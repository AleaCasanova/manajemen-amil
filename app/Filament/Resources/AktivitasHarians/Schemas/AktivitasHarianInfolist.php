<?php

namespace App\Filament\Resources\AktivitasHarians\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AktivitasHarianInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Rincian Kegiatan Lapangan Amil')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->components([
                        Grid::make(4)
                            ->components([
                                TextEntry::make('judul')
                                    ->label('Judul Aktivitas')
                                    ->weight('bold'),
                                TextEntry::make('amil.nama')
                                    ->label('Petugas Amil')
                                    ->weight('bold'),
                                TextEntry::make('tanggal')
                                    ->label('Tanggal Pelaksanaan')
                                    ->date('l, d F Y')
                                    ->icon('heroicon-o-calendar'),
                                TextEntry::make('status')
                                    ->label('Status Review')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'disetujui' => 'success',
                                        'ditolak' => 'danger',
                                        default => 'warning',
                                    })
                                    ->formatStateUsing(fn ($state) => strtoupper($state)),
                            ]),
                        TextEntry::make('lokasi')
                            ->label('Lokasi / Instansi Terkait')
                            ->icon('heroicon-o-map-pin')
                            ->placeholder('Tidak disebutkan'),
                    ]),

                Section::make('Uraian Narasi & Hasil Kegiatan')
                    ->icon('heroicon-o-document-text')
                    ->components([
                        TextEntry::make('deskripsi')
                            ->label('Deskripsi Lengkap Laporan')
                            ->columnSpanFull(),
                    ]),

                Section::make('Foto Dokumentasi Lapangan')
                    ->icon('heroicon-o-camera')
                    ->components([
                        ImageEntry::make('foto')
                            ->label('Dokumentasi Kunjungan / Tugas Lapangan')
                            ->disk('public')
                            ->height(280)
                            ->placeholder('Tidak ada foto dokumentasi'),
                    ]),

                Section::make('Informasi Log Sistem')
                    ->icon('heroicon-o-clock')
                    ->collapsed()
                    ->components([
                        Grid::make(2)
                            ->components([
                                TextEntry::make('created_at')
                                    ->label('Waktu Pelaporan')
                                    ->dateTime('d M Y, H:i:s')
                                    ->suffix(' WIB'),
                                TextEntry::make('updated_at')
                                    ->label('Pembaruan Status')
                                    ->dateTime('d M Y, H:i:s')
                                    ->suffix(' WIB'),
                            ]),
                    ]),
            ]);
    }
}
