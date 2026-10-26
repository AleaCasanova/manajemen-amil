<?php

namespace App\Filament\Resources\Donaturs\Tables;

use App\Models\Donatur;
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

class DonatursTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_donatur')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),
                TextColumn::make('nama')
                    ->label('Nama Donatur')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Donatur $record): string => $record->email ?? ($record->kota ? $record->kota.', '.$record->provinsi : '')),
                TextColumn::make('kategori.nama')
                    ->label('Kategori')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jenis')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn ($state) => $state === 'lembaga' ? 'info' : 'gray')
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                TextColumn::make('no_hp')
                    ->label('WhatsApp')
                    ->searchable()
                    ->url(fn ($record) => $record->no_hp ? 'https://wa.me/'.preg_replace('/[^0-9]/', '', $record->no_hp) : null, true),
                TextColumn::make('amilPic.nama')
                    ->label('Amil PIC')
                    ->default('-')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('donasis_count')
                    ->label('Frekuensi Donasi')
                    ->counts('donasis')
                    ->badge()
                    ->color('success')
                    ->sortable(),
                TextColumn::make('donasis_sum_jumlah')
                    ->label('Total Donasi (Rp)')
                    ->sum('donasis', 'jumlah')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Tgl Bergabung')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('kategori_id')
                    ->label('Kategori')
                    ->relationship('kategori', 'nama'),
                SelectFilter::make('jenis')
                    ->options([
                        'individu' => 'Individu',
                        'lembaga' => 'Lembaga / Perusahaan',
                    ]),
                SelectFilter::make('amil_pic_id')
                    ->label('Amil PIC')
                    ->relationship('amilPic', 'nama'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->before(function (Donatur $record, DeleteAction $action) {
                        if ($record->donasis()->exists()) {
                            Notification::make()
                                ->title('Aksi Ditolak: Perlindungan Audit Finansial')
                                ->body("Donatur {$record->nama} tidak dapat dihapus karena memiliki riwayat transaksi donasi tercatat.")
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
                            $hasDonations = $records->filter(fn (Donatur $record) => $record->donasis()->exists());
                            if ($hasDonations->isNotEmpty()) {
                                Notification::make()
                                    ->title('Aksi Ditolak: Perlindungan Audit Finansial')
                                    ->body('Beberapa donatur yang dipilih ('.$hasDonations->pluck('nama')->take(3)->join(', ').') memiliki riwayat transaksi donasi dan tidak dapat dihapus.')
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
