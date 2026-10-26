<?php

namespace App\Filament\Resources\Mustahiks\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MustahikInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas Mustahik & Asnaf')
                    ->icon('heroicon-o-user')
                    ->components([
                        Grid::make(4)
                            ->components([
                                TextEntry::make('nama')
                                    ->label('Nama Lengkap')
                                    ->weight('bold'),
                                TextEntry::make('nik')
                                    ->label('NIK KTP')
                                    ->placeholder('-'),
                                TextEntry::make('asnaf')
                                    ->label('Golongan Asnaf')
                                    ->badge()
                                    ->color('info')
                                    ->formatStateUsing(fn ($state) => strtoupper($state)),
                                TextEntry::make('status_verifikasi')
                                    ->label('Status Kelayakan')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'terverifikasi' => 'success',
                                        'ditolak' => 'danger',
                                        default => 'warning',
                                    })
                                    ->formatStateUsing(fn ($state) => ucfirst(str_replace('_', ' ', $state))),
                            ]),
                    ]),

                Section::make('Domisili & Kondisi Ekonomi Mustahik')
                    ->icon('heroicon-o-home')
                    ->components([
                        TextEntry::make('alamat')
                            ->label('Alamat Lengkap')
                            ->icon('heroicon-o-map-pin')
                            ->placeholder('Alamat belum diatur'),
                        TextEntry::make('kondisi')
                            ->label('Uraian Kondisi & Kebutuhan Mendesak')
                            ->placeholder('Tidak ada catatan kondisi khusus')
                            ->columnSpanFull(),
                    ]),

                Section::make('Informasi Log Sistem')
                    ->icon('heroicon-o-clock')
                    ->collapsed()
                    ->components([
                        Grid::make(2)
                            ->components([
                                TextEntry::make('created_at')
                                    ->label('Waktu Terdaftar')
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
