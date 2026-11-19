<?php

namespace App\Filament\Resources\Amils\Tables;

use App\Models\Amil;
use App\Models\User;
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
use Spatie\Permission\Models\Role;

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
                    ->description(function (Amil $record): string {
                        $email = $record->user?->email ?? '-';
                        if ($record->isAdmin()) {
                            return "🛡️ Admin • {$email}";
                        }

                        return $email;
                    }),
                TextColumn::make('divisi.nama')
                    ->label('Divisi')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jabatan')
                    ->label('Jabatan')
                    ->badge()
                    ->formatStateUsing(fn (?string $state, Amil $record): string => $record->jabatan_label)
                    ->color(fn (?string $state): string => match (strtolower(trim($state ?? ''))) {
                        'ketua' => 'warning',
                        'staff' => 'success',
                        'driver' => 'info',
                        'relawan' => 'purple',
                        'magang' => 'teal',
                        default => 'gray',
                    })
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
                SelectFilter::make('jabatan')
                    ->label('Jabatan / Peran')
                    ->options(Amil::getJabatanOptions()),
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
                Action::make('toggleAdmin')
                    ->label(fn (Amil $record): string => $record->isAdmin() ? 'Cabut Admin' : 'Jadikan Admin')
                    ->icon(fn (Amil $record): string => $record->isAdmin() ? 'heroicon-o-shield-exclamation' : 'heroicon-o-shield-check')
                    ->color(fn (Amil $record): string => $record->isAdmin() ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->modalHeading(fn (Amil $record): string => $record->isAdmin() ? 'Cabut Hak Akses Administrator' : 'Jadikan Administrator')
                    ->modalDescription(fn (Amil $record): string => $record->isAdmin()
                        ? "Apakah Anda yakin ingin mencabut hak akses Admin untuk {$record->nama}? Petugas ini tidak akan dapat lagi mengakses Panel Manajemen Admin Filament."
                        : "Apakah Anda yakin ingin memberikan hak akses Admin kepada {$record->nama}? Petugas ini akan dapat membuka Panel Manajemen Admin Filament dan mengelola seluruh data yayasan.")
                    ->action(function (Amil $record): void {
                        $currentUser = auth()->user();

                        // Cegah mencabut akses admin untuk akun sendiri
                        if ($record->user_id && $currentUser && $record->user_id === $currentUser->id) {
                            Notification::make()
                                ->title('Aksi Ditolak')
                                ->body('Anda tidak dapat mencabut hak akses Admin untuk akun Anda sendiri!')
                                ->danger()
                                ->send();

                            return;
                        }

                        $roleAdmin = Role::firstOrCreate(['name' => 'Admin']);
                        $roleAmil = Role::firstOrCreate(['name' => 'Amil']);

                        $user = $record->user;

                        // Jika amil belum memiliki user, buatkan otomatis
                        if (! $user) {
                            $user = User::create([
                                'name' => $record->nama,
                                'email' => strtolower(str_replace(' ', '', $record->nama)).'@amilgsc.com',
                                'password' => Hash::make('password123'),
                                'role_id' => $roleAdmin->id,
                                'is_active' => true,
                            ]);
                            $user->assignRole($roleAdmin);
                            $user->assignRole($roleAmil);
                            $record->update(['user_id' => $user->id]);

                            Notification::make()
                                ->title('Akun Dibuat & Menjadi Admin')
                                ->body("Akun user {$record->nama} berhasil dibuat dan resmi menjadi Administrator.")
                                ->success()
                                ->send();

                            return;
                        }

                        if ($user->isAdmin()) {
                            // Cabut akses Admin
                            $user->removeRole('Admin');
                            $user->assignRole($roleAmil);
                            $user->update(['role_id' => $roleAmil->id]);

                            Notification::make()
                                ->title('Akses Admin Dicabut')
                                ->body("Hak akses Administrator untuk {$record->nama} berhasil dicabut.")
                                ->warning()
                                ->send();
                        } else {
                            // Berikan akses Admin
                            $user->assignRole($roleAdmin);
                            $user->update(['role_id' => $roleAdmin->id]);

                            Notification::make()
                                ->title('Berhasil Menjadi Admin')
                                ->body("{$record->nama} sekarang resmi memiliki hak akses Administrator Panel Admin.")
                                ->success()
                                ->send();
                        }
                    }),
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
