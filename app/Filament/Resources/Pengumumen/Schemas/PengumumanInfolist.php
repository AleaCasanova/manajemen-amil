<?php

namespace App\Filament\Resources\Pengumumen\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PengumumanInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informasi Pengumuman Resmi')
                    ->icon('heroicon-o-megaphone')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(3)
                            ->components([
                                TextEntry::make('judul')
                                    ->label('Judul Pengumuman')
                                    ->weight('bold')
                                    ->size('lg'),
                                TextEntry::make('target')
                                    ->label('Target Pembaca')
                                    ->badge()
                                    ->color('info')
                                    ->formatStateUsing(fn (?string $state): string => ucfirst($state ?? 'Semua Petugas')),
                                TextEntry::make('published_at')
                                    ->label('Jadwal Tayang / Terbit')
                                    ->dateTime('l, d F Y, H:i')
                                    ->suffix(' WIB')
                                    ->icon('heroicon-o-calendar')
                                    ->placeholder('Segera'),
                            ]),
                    ]),

                Section::make('Isi Pengumuman')
                    ->icon('heroicon-o-document-text')
                    ->columnSpanFull()
                    ->components([
                        TextEntry::make('isi')
                            ->label('Uraian Pengumuman')
                            ->columnSpanFull(),
                    ]),

                Section::make('Poster & Berkas Banner')
                    ->icon('heroicon-o-photo')
                    ->columnSpanFull()
                    ->components([
                        ImageEntry::make('lampiran')
                            ->label('Poster Resmi Pengumuman')
                            ->disk('public')
                            ->height(320)
                            ->placeholder('Tidak ada poster visual'),
                    ]),

                Section::make('Informasi Log Sistem')
                    ->icon('heroicon-o-clock')
                    ->collapsed()
                    ->columnSpanFull()
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
