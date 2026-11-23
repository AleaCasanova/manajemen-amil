<?php

namespace App\Filament\Resources\TransaksiKeuangans\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TransaksiKeuanganInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Rincian Transaksi Keuangan')
                    ->icon('heroicon-o-document-text')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(4)
                            ->components([
                                TextEntry::make('kode_transaksi')
                                    ->label('Kode Transaksi')
                                    ->badge()
                                    ->color('primary')
                                    ->copyable(),

                                TextEntry::make('jenis')
                                    ->label('Arah Arus Kas')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'masuk' => 'success',
                                        'keluar' => 'danger',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn ($state) => $state === 'masuk' ? 'KAS MASUK' : 'KAS KELUAR'),

                                TextEntry::make('nominal')
                                    ->label('Nominal Transaksi')
                                    ->money('IDR', locale: 'id')
                                    ->weight('bold')
                                    ->color(fn ($record) => $record->jenis === 'masuk' ? 'success' : 'danger'),

                                TextEntry::make('tgl_transaksi')
                                    ->label('Tanggal Transaksi')
                                    ->date('d M Y')
                                    ->icon('heroicon-o-calendar'),
                            ]),

                        Grid::make(3)
                            ->components([
                                TextEntry::make('akunKeuangan.nama')
                                    ->label('Akun Kas / Bank')
                                    ->badge()
                                    ->color('info')
                                    ->description(fn ($record) => $record->akunKeuangan?->nomor_rekening ? 'Rek: '.$record->akunKeuangan->nomor_rekening : null),

                                TextEntry::make('kategoriKeuangan.nama')
                                    ->label('Kategori Pos')
                                    ->badge(),

                                TextEntry::make('pihak_terkait')
                                    ->label('Pihak Terkait (Penyetor / Vendor)')
                                    ->default('-'),
                            ]),

                        Grid::make(2)
                            ->components([
                                TextEntry::make('program.nama')
                                    ->label('Program Terkait')
                                    ->default('-'),

                                TextEntry::make('creator.name')
                                    ->label('Diinput Oleh Petugas')
                                    ->default('Sistem / Admin'),
                            ]),

                        TextEntry::make('keterangan')
                            ->label('Uraian Keterangan')
                            ->columnSpanFull(),

                        ImageEntry::make('bukti_transaksi')
                            ->label('Lampiran Bukti Nota / Transfer')
                            ->disk('public')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
