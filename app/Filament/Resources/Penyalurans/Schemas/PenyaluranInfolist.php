<?php

namespace App\Filament\Resources\Penyalurans\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PenyaluranInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Rincian Bantuan Penyaluran ZISWAF')
                    ->icon('heroicon-o-gift')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(4)
                            ->components([
                                TextEntry::make('mustahik.nama')
                                    ->label('Penerima Manfaat (Mustahik)')
                                    ->weight('bold')
                                    ->description(fn ($record) => 'Asnaf: '.ucfirst($record->mustahik?->asnaf ?? '-')),
                                TextEntry::make('jumlah')
                                    ->label('Nominal Bantuan')
                                    ->money('IDR', locale: 'id')
                                    ->weight('bold')
                                    ->color('success'),
                                TextEntry::make('jenisDana.nama')
                                    ->label('Sumber Jenis Dana')
                                    ->badge()
                                    ->color('info'),
                                TextEntry::make('tgl_salur')
                                    ->label('Tanggal Penyaluran')
                                    ->date('l, d F Y')
                                    ->icon('heroicon-o-calendar'),
                            ]),
                        Grid::make(2)
                            ->components([
                                TextEntry::make('program.nama')
                                    ->label('Program Penyaluran')
                                    ->placeholder('Program Umum / Penyaluran Bebas'),
                                TextEntry::make('amil.nama')
                                    ->label('Petugas Amil Penyalur')
                                    ->weight('bold')
                                    ->placeholder('-'),
                            ]),
                    ]),

                Section::make('Foto Berita Acara & Dokumentasi Penyerahan')
                    ->icon('heroicon-o-camera')
                    ->description('Dokumentasi langsung penyerahan amanah dana donatur kepada mustahik')
                    ->columnSpanFull()
                    ->components([
                        ImageEntry::make('bukti')
                            ->label('Dokumentasi Serah Terima / Kwitansi Tanda Terima')
                            ->disk('public')
                            ->height(280)
                            ->placeholder('Belum ada foto dokumentasi penyerahan'),
                    ]),

                Section::make('Rincian Bantuan & Catatan Tambahan')
                    ->icon('heroicon-o-document-text')
                    ->columnSpanFull()
                    ->components([
                        TextEntry::make('catatan')
                            ->label('Catatan Rincian Bantuan')
                            ->placeholder('Tidak ada catatan tambahan')
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
                                    ->label('Waktu Dicatat')
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
