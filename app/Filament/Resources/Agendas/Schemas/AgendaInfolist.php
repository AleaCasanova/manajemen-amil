<?php

namespace App\Filament\Resources\Agendas\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AgendaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi & Jadwal Kegiatan')
                    ->icon('heroicon-o-calendar-days')
                    ->components([
                        Grid::make(3)
                            ->components([
                                TextEntry::make('judul')
                                    ->label('Nama Agenda / Kegiatan')
                                    ->weight('bold')
                                    ->size('lg'),
                                TextEntry::make('status')
                                    ->label('Status Agenda')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'terjadwal' => 'info',
                                        'berlangsung' => 'warning',
                                        'selesai' => 'gray',
                                        'dibatalkan' => 'danger',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                                TextEntry::make('lokasi')
                                    ->label('Lokasi / Tempat Pelaksanaan')
                                    ->icon('heroicon-o-map-pin')
                                    ->placeholder('Tidak ditentukan'),
                            ]),
                        Grid::make(3)
                            ->components([
                                TextEntry::make('mulai')
                                    ->label('Waktu Mulai')
                                    ->dateTime('l, d F Y, H:i')
                                    ->suffix(' WIB')
                                    ->icon('heroicon-o-clock'),
                                TextEntry::make('selesai')
                                    ->label('Waktu Selesai')
                                    ->dateTime('l, d F Y, H:i')
                                    ->suffix(' WIB')
                                    ->icon('heroicon-o-clock'),
                                TextEntry::make('creator.name')
                                    ->label('Dibuat Oleh')
                                    ->icon('heroicon-o-user')
                                    ->placeholder('-'),
                            ]),
                    ]),

                Section::make('Petugas Amil Terlibat')
                    ->icon('heroicon-o-users')
                    ->components([
                        TextEntry::make('amils.nama')
                            ->label('Daftar Petugas Amil yang Ditugaskan')
                            ->badge()
                            ->separator(', ')
                            ->placeholder('Terbuka untuk semua petugas'),
                    ]),

                Section::make('Rincian Deskripsi Kegiatan')
                    ->icon('heroicon-o-document-text')
                    ->components([
                        TextEntry::make('deskripsi')
                            ->label('Catatan & Jadwal Acara')
                            ->placeholder('Tidak ada rincian agenda.')
                            ->columnSpanFull(),
                    ]),

                Section::make('Informasi Log Sistem')
                    ->icon('heroicon-o-clock')
                    ->collapsed()
                    ->components([
                        Grid::make(2)
                            ->components([
                                TextEntry::make('created_at')
                                    ->label('Dibuat Pada')
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
