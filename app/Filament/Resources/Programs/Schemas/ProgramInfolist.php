<?php

namespace App\Filament\Resources\Programs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProgramInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informasi Program & Campaign ZISWAF')
                    ->icon('heroicon-o-sparkles')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(4)
                            ->components([
                                TextEntry::make('nama')
                                    ->label('Nama Program')
                                    ->weight('bold'),
                                TextEntry::make('target_dana')
                                    ->label('Target Penghimpunan')
                                    ->money('IDR', locale: 'id')
                                    ->weight('bold')
                                    ->color('success'),
                                TextEntry::make('status')
                                    ->label('Status Program')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'aktif' => 'success',
                                        'selesai' => 'info',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn ($state) => strtoupper($state)),
                                TextEntry::make('periode')
                                    ->label('Periode Pelaksanaan')
                                    ->state(function ($record) {
                                        if (! $record->tgl_mulai) {
                                            return 'Program Rutin Berjalan';
                                        }

                                        return $record->tgl_mulai->format('d M Y').' s/d '.($record->tgl_selesai ? $record->tgl_selesai->format('d M Y') : 'Seterusnya');
                                    })
                                    ->icon('heroicon-o-calendar'),
                            ]),
                    ]),

                Section::make('Deskripsi & Rincian Program')
                    ->icon('heroicon-o-document-text')
                    ->columnSpanFull()
                    ->components([
                        TextEntry::make('deskripsi')
                            ->label('Uraian Lengkap Program')
                            ->placeholder('Belum ada deskripsi program')
                            ->columnSpanFull(),
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
