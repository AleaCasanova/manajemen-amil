<?php

namespace App\Filament\Resources\Mustahiks\Tables;

use App\Models\Mustahik;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class MustahiksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama Mustahik')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('nik')
                    ->label('NIK')
                    ->fontFamily('mono')
                    ->searchable()
                    ->copyable()
                    ->placeholder('-'),

                TextColumn::make('asnaf')
                    ->label('Kategori Asnaf')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'fakir' => 'danger',
                        'miskin' => 'warning',
                        'amil' => 'info',
                        'mualaf' => 'success',
                        'riqab' => 'gray',
                        'gharim' => 'rose',
                        'fisabilillah' => 'primary',
                        'ibnu_sabil' => 'teal',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'ibnu_sabil' => 'Ibnu Sabil',
                        default => ucfirst($state),
                    }),

                TextColumn::make('alamat')
                    ->label('Alamat')
                    ->limit(35)
                    ->placeholder('-'),

                TextColumn::make('penyalurans_count')
                    ->counts('penyalurans')
                    ->label('Penyaluran')
                    ->badge()
                    ->color('emerald')
                    ->formatStateUsing(fn ($state): string => "{$state}x Salur"),

                TextColumn::make('status_verifikasi')
                    ->label('Status Kelayakan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'terverifikasi' => 'success',
                        'ditolak' => 'danger',
                        default => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'terverifikasi' => 'Terverifikasi Layak',
                        'ditolak' => 'Tidak Layak',
                        default => 'Menunggu Verifikasi',
                    }),

                TextColumn::make('created_at')
                    ->label('Terdaftar Pada')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('asnaf')
                    ->label('Asnaf Zakat')
                    ->options([
                        'fakir' => 'Fakir',
                        'miskin' => 'Miskin',
                        'amil' => 'Amil',
                        'mualaf' => 'Mualaf',
                        'riqab' => 'Riqab',
                        'gharim' => 'Gharim',
                        'fisabilillah' => 'Fisabilillah',
                        'ibnu_sabil' => 'Ibnu Sabil',
                    ]),
                SelectFilter::make('status_verifikasi')
                    ->label('Status Verifikasi')
                    ->options([
                        'menunggu' => 'Menunggu Verifikasi',
                        'terverifikasi' => 'Terverifikasi Layak',
                        'ditolak' => 'Tidak Layak',
                    ]),
                TrashedFilter::make(),
            ])
            ->recordActions([
                // Quick Action: Verifikasi Layak
                Action::make('verifikasi')
                    ->label('Verifikasi Layak')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Mustahik $record): bool => $record->status_verifikasi !== 'terverifikasi')
                    ->action(function (Mustahik $record): void {
                        $record->update(['status_verifikasi' => 'terverifikasi']);

                        Notification::make()
                            ->title('Mustahik Terverifikasi')
                            ->body("Data {$record->nama} telah diverifikasi sebagai penerima manfaat layak.")
                            ->success()
                            ->send();
                    }),

                // Quick Action: Tolak / Tidak Layak
                Action::make('tolak')
                    ->label('Tidak Layak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Mustahik $record): bool => $record->status_verifikasi === 'menunggu')
                    ->action(function (Mustahik $record): void {
                        $record->update(['status_verifikasi' => 'ditolak']);

                        Notification::make()
                            ->title('Status Mustahik Diubah')
                            ->body("Data {$record->nama} dinyatakan tidak memenuhi kriteria penerima zakat.")
                            ->warning()
                            ->send();
                    }),

                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->before(function (Mustahik $record, DeleteAction $action) {
                        if ($record->penyalurans()->exists()) {
                            Notification::make()
                                ->title('Aksi Ditolak: Perlindungan Penyaluran ZISWAF')
                                ->body("Mustahik {$record->nama} tidak dapat dihapus karena telah tercatat menerima penyaluran dana ZISWAF.")
                                ->danger()
                                ->send();

                            $action->cancel();
                        }
                    }),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->before(function (Collection $records, DeleteBulkAction $action) {
                            $hasDisbursements = $records->filter(fn (Mustahik $record) => $record->penyalurans()->exists());
                            if ($hasDisbursements->isNotEmpty()) {
                                Notification::make()
                                    ->title('Aksi Ditolak: Perlindungan Penyaluran ZISWAF')
                                    ->body('Beberapa mustahik yang dipilih ('.$hasDisbursements->pluck('nama')->take(3)->join(', ').') telah menerima penyaluran dana dan tidak dapat dihapus.')
                                    ->danger()
                                    ->send();

                                $action->cancel();
                            }
                        }),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
