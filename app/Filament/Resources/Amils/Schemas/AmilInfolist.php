<?php

namespace App\Filament\Resources\Amils\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AmilInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Biodata & Identitas Petugas Amil')
                    ->icon('heroicon-o-user-circle')
                    ->description('Informasi kontak dan biodata resmi amil GSC')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(3)
                            ->components([
                                ImageEntry::make('foto')
                                    ->label('Foto Profil')
                                    ->disk('public')
                                    ->circular()
                                    ->height(130)
                                    ->placeholder('Tidak ada foto profil'),
                                Grid::make(2)
                                    ->columnSpan(2)
                                    ->components([
                                        TextEntry::make('nama')
                                            ->label('Nama Lengkap')
                                            ->weight('bold'),
                                        TextEntry::make('nip')
                                            ->label('Nomor Induk Pegawai (NIP)')
                                            ->badge()
                                            ->color('gray')
                                            ->copyable(),
                                        TextEntry::make('jabatan')
                                            ->label('Jabatan / Peran')
                                            ->badge()
                                            ->formatStateUsing(fn ($record) => $record->jabatan_label)
                                            ->color(fn ($state) => match (strtolower(trim($state ?? ''))) {
                                                'ketua' => 'warning',
                                                'staff' => 'success',
                                                'driver' => 'info',
                                                'relawan' => 'purple',
                                                'magang' => 'teal',
                                                default => 'gray',
                                            }),
                                        TextEntry::make('divisi.nama')
                                            ->label('Divisi Kerja')
                                            ->badge()
                                            ->color('info')
                                            ->placeholder('-'),
                                    ]),
                            ]),
                        Grid::make(3)
                            ->components([
                                TextEntry::make('user.email')
                                    ->label('Email Akun Login')
                                    ->icon('heroicon-o-envelope')
                                    ->placeholder('-'),
                                TextEntry::make('no_hp')
                                    ->label('WhatsApp / Nomor HP')
                                    ->icon('heroicon-o-phone')
                                    ->url(fn (?string $state) => $state ? 'https://wa.me/'.preg_replace('/[^0-9]/', '', $state) : null, true)
                                    ->color('primary')
                                    ->placeholder('-'),
                                TextEntry::make('alamat')
                                    ->label('Alamat Domisili')
                                    ->icon('heroicon-o-map-pin')
                                    ->placeholder('-'),
                            ]),
                    ]),

                Section::make('Status Kepegawaian & Target Donasi')
                    ->icon('heroicon-o-briefcase')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(3)
                            ->components([
                                TextEntry::make('status')
                                    ->label('Status Keaktifan')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'aktif' => 'success',
                                        'nonaktif' => 'danger',
                                        'cuti' => 'warning',
                                        'mutasi' => 'info',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn ($state) => strtoupper($state)),
                                TextEntry::make('target_bulanan')
                                    ->label('Target Donasi Bulanan')
                                    ->money('IDR', locale: 'id')
                                    ->weight('bold')
                                    ->color('success'),
                                TextEntry::make('tgl_bergabung')
                                    ->label('Tanggal Bergabung')
                                    ->date('d F Y')
                                    ->icon('heroicon-o-calendar'),
                            ]),
                    ]),

                Section::make('Informasi Log Sistem')
                    ->icon('heroicon-o-clock')
                    ->collapsed()
                    ->columnSpanFull()
                    ->components([
                        Grid::make(2)
                            ->components([
                                TextEntry::make('created_at')
                                    ->label('Waktu Pendaftaran')
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
