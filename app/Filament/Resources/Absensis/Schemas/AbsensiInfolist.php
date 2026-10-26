<?php

namespace App\Filament\Resources\Absensis\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AbsensiInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Kehadiran Amil')
                    ->icon('heroicon-o-check-badge')
                    ->components([
                        Grid::make(4)
                            ->components([
                                TextEntry::make('amil.nama')
                                    ->label('Petugas Amil')
                                    ->weight('bold'),
                                TextEntry::make('tanggal')
                                    ->label('Tanggal Presensi')
                                    ->date('l, d F Y')
                                    ->icon('heroicon-o-calendar'),
                                TextEntry::make('status')
                                    ->label('Status Kehadiran')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'hadir' => 'success',
                                        'terlambat' => 'danger',
                                        'izin', 'cuti' => 'info',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn ($state) => strtoupper($state)),
                                TextEntry::make('keterangan')
                                    ->label('Catatan Kehadiran')
                                    ->placeholder('Tidak ada catatan khusus'),
                            ]),
                    ]),

                Section::make('Waktu Kerja & Titik Koordinat GPS')
                    ->icon('heroicon-o-map-pin')
                    ->components([
                        Grid::make(2)
                            ->components([
                                Grid::make(2)
                                    ->components([
                                        TextEntry::make('jam_masuk')
                                            ->label('Jam Masuk')
                                            ->time('H:i:s')
                                            ->suffix(' WIB')
                                            ->weight('bold')
                                            ->color('success')
                                            ->placeholder('-'),
                                        TextEntry::make('lat_masuk')
                                            ->label('GPS Masuk (Lat, Lng)')
                                            ->formatStateUsing(fn ($state, $record) => $state ? "{$record->lat_masuk}, {$record->lng_masuk}" : '-')
                                            ->placeholder('-'),
                                    ]),
                                Grid::make(2)
                                    ->components([
                                        TextEntry::make('jam_pulang')
                                            ->label('Jam Pulang')
                                            ->time('H:i:s')
                                            ->suffix(' WIB')
                                            ->weight('bold')
                                            ->color('danger')
                                            ->placeholder('Belum clock out'),
                                        TextEntry::make('lat_pulang')
                                            ->label('GPS Pulang (Lat, Lng)')
                                            ->formatStateUsing(fn ($state, $record) => $state ? "{$record->lat_pulang}, {$record->lng_pulang}" : '-')
                                            ->placeholder('-'),
                                    ]),
                            ]),
                    ]),

                Section::make('Foto Selfie Bukti Kehadiran')
                    ->icon('heroicon-o-camera')
                    ->description('Foto selfie amil saat melakukan presensi masuk dan pulang')
                    ->components([
                        Grid::make(2)
                            ->components([
                                ImageEntry::make('foto_masuk')
                                    ->label('Foto Selfie Clock In (Masuk)')
                                    ->disk('public')
                                    ->height(240)
                                    ->placeholder('Belum ada foto selfie masuk'),
                                ImageEntry::make('foto_pulang')
                                    ->label('Foto Selfie Clock Out (Pulang)')
                                    ->disk('public')
                                    ->height(240)
                                    ->placeholder('Belum ada foto selfie pulang'),
                            ]),
                    ]),

                Section::make('Informasi Log Sistem')
                    ->icon('heroicon-o-clock')
                    ->collapsed()
                    ->components([
                        Grid::make(2)
                            ->components([
                                TextEntry::make('created_at')
                                    ->label('Tercatat di Sistem')
                                    ->dateTime('d M Y, H:i:s')
                                    ->suffix(' WIB'),
                                TextEntry::make('updated_at')
                                    ->label('Pembaruan Terakhir')
                                    ->dateTime('d M Y, H:i:s')
                                    ->suffix(' WIB'),
                            ]),
                    ]),
            ]);
    }
}
