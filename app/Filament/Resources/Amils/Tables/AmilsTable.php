<?php

namespace App\Filament\Resources\Amils\Tables;

use App\Models\Amil;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class AmilsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto')
                    ->label('Foto')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(fn () => 'https://ui-avatars.com/api/?name=Amil&background=0D8ABC&color=fff'),
                TextColumn::make('nip')
                    ->label('NIP')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),
                TextColumn::make('nama')
                    ->label('Nama Amil')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Amil $record): string => $record->user?->email ?? '-'),
                TextColumn::make('divisi.nama')
                    ->label('Divisi')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jabatan')
                    ->label('Jabatan')
                    ->searchable(),
                TextColumn::make('no_hp')
                    ->label('WhatsApp / Kontak')
                    ->searchable()
                    ->url(fn ($record) => $record->no_hp ? 'https://wa.me/'.preg_replace('/[^0-9]/', '', $record->no_hp) : null, true),
                TextColumn::make('target_bulanan')
                    ->label('Target Bulanan')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'success' => 'aktif',
                        'danger' => 'nonaktif',
                        'warning' => 'cuti',
                        'info' => 'mutasi',
                    ]),
                TextColumn::make('tgl_bergabung')
                    ->label('Tgl Bergabung')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'aktif' => 'Aktif',
                        'nonaktif' => 'Nonaktif',
                        'cuti' => 'Cuti',
                        'mutasi' => 'Mutasi',
                    ]),
                SelectFilter::make('divisi_id')
                    ->label('Divisi')
                    ->relationship('divisi', 'nama'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('resetPassword')
                    ->label('Reset Password')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Reset Password Akun Amil')
                    ->modalDescription('Masukkan password baru untuk akun amil ini, atau gunakan default password123.')
                    ->form([
                        TextInput::make('new_password')
                            ->label('Password Baru')
                            ->password()
                            ->revealable()
                            ->default('password123')
                            ->required(),
                    ])
                    ->action(function (Amil $record, array $data): void {
                        if ($record->user) {
                            $record->user->update([
                                'password' => Hash::make($data['new_password']),
                            ]);

                            Notification::make()
                                ->title('Password Berhasil Direset')
                                ->body("Password akun {$record->nama} berhasil diperbarui.")
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('User Tidak Ditemukan')
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('toggleActive')
                    ->label(fn (Amil $record): string => $record->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan')
                    ->icon(fn (Amil $record): string => $record->status === 'aktif' ? 'heroicon-o-no-symbol' : 'heroicon-o-check-circle')
                    ->color(fn (Amil $record): string => $record->status === 'aktif' ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->action(function (Amil $record): void {
                        $newStatus = $record->status === 'aktif' ? 'nonaktif' : 'aktif';
                        $record->update(['status' => $newStatus]);

                        if ($record->user) {
                            $record->user->update(['is_active' => ($newStatus === 'aktif')]);
                        }

                        Notification::make()
                            ->title('Status Amil Diperbarui')
                            ->body("Akun {$record->nama} sekarang berstatus ".strtoupper($newStatus))
                            ->success()
                            ->send();
                    }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
