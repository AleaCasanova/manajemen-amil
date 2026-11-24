<?php

namespace App\Filament\Resources\CashAdvances\Schemas;

use App\Models\CashAdvance;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CashAdvanceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informasi Cash Advance / Cash Bon')
                    ->icon('heroicon-o-credit-card')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(4)
                            ->components([
                                TextEntry::make('kode_ca')
                                    ->label('Kode')
                                    ->badge()
                                    ->color('primary')
                                    ->copyable(),

                                TextEntry::make('tipe')
                                    ->label('Jenis')
                                    ->badge()
                                    ->color(fn (CashAdvance $record): string => $record->tipe === 'cash_bon' ? 'gray' : 'info')
                                    ->formatStateUsing(fn (string $state): string => $state === 'cash_bon' ? 'Cash Bon (101.06.001)' : 'Cash Advance (101.09.000)'),

                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn (CashAdvance $record): string => $record->status_color)
                                    ->formatStateUsing(fn (CashAdvance $record): string => $record->status_label),

                                TextEntry::make('tgl_pencairan')
                                    ->label('Tanggal Pencairan')
                                    ->date('d M Y'),
                            ]),

                        Grid::make(4)
                            ->components([
                                TextEntry::make('nama_pj')
                                    ->label('Penanggung Jawab')
                                    ->weight('bold'),

                                TextEntry::make('divisi.nama')
                                    ->label('Departemen / Divisi')
                                    ->default('-'),

                                TextEntry::make('program.nama')
                                    ->label('Program Terkait')
                                    ->default('-'),

                                TextEntry::make('tgl_jatuh_tempo')
                                    ->label('Jatuh Tempo')
                                    ->date('d M Y')
                                    ->default('-'),
                            ]),

                        Grid::make(5)
                            ->components([
                                TextEntry::make('nominal_dicairkan')
                                    ->label('Dicairkan')
                                    ->money('IDR', locale: 'id')
                                    ->weight('bold')
                                    ->color('primary'),

                                TextEntry::make('nominal_realisasi')
                                    ->label('Total Realisasi LPJ')
                                    ->money('IDR', locale: 'id')
                                    ->color('info'),

                                TextEntry::make('nominal_dikembalikan')
                                    ->label('Dikembalikan')
                                    ->money('IDR', locale: 'id')
                                    ->color('success'),

                                TextEntry::make('nominal_reimburse')
                                    ->label('Reimburse / Kurang Bayar')
                                    ->money('IDR', locale: 'id')
                                    ->color('warning'),

                                TextEntry::make('sisa_tanggungan')
                                    ->label('Sisa Tanggungan')
                                    ->money('IDR', locale: 'id')
                                    ->weight('bold')
                                    ->color(fn (CashAdvance $record): string => $record->sisa_tanggungan > 0 ? 'danger' : 'success'),
                            ]),

                        Grid::make(3)
                            ->components([
                                TextEntry::make('akunKeuangan.nama')
                                    ->label('Akun Sumber Pencairan')
                                    ->badge()
                                    ->color('info'),

                                TextEntry::make('akunPenyelesaian.nama')
                                    ->label('Akun Penyelesaian / LPJ')
                                    ->badge()
                                    ->default('-'),

                                TextEntry::make('tgl_selesai')
                                    ->label('Tanggal Selesai / Lunas')
                                    ->date('d M Y')
                                    ->default('-'),
                            ]),

                        TextEntry::make('keperluan')
                            ->label('Keperluan')
                            ->columnSpanFull(),

                        TextEntry::make('catatan_penyelesaian')
                            ->label('Catatan Penyelesaian')
                            ->default('-')
                            ->visible(fn (CashAdvance $record): bool => filled($record->catatan_penyelesaian))
                            ->columnSpanFull(),

                        ImageEntry::make('bukti_pencairan')
                            ->label('Bukti Pencairan')
                            ->disk('public')
                            ->visible(fn (CashAdvance $record): bool => filled($record->bukti_pencairan))
                            ->columnSpanFull(),
                    ]),

                Section::make('Rincian Item Realisasi LPJ')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->visible(fn (CashAdvance $record): bool => $record->tipe === 'cash_advance')
                    ->columnSpanFull()
                    ->components([
                        RepeatableEntry::make('realisasis')
                            ->label('')
                            ->columns(4)
                            ->components([
                                TextEntry::make('tgl_realisasi')
                                    ->label('Tanggal Nota')
                                    ->date('d M Y'),
                                TextEntry::make('kategoriKeuangan.nama')
                                    ->label('Akun COA Beban')
                                    ->formatStateUsing(fn ($record): string => $record->kategoriKeuangan?->label_lengkap ?? '-'),
                                TextEntry::make('uraian')
                                    ->label('Uraian Belanja'),
                                TextEntry::make('nominal')
                                    ->label('Nominal')
                                    ->money('IDR', locale: 'id')
                                    ->weight('bold'),
                            ]),
                    ]),

                Section::make('Jejak Mutasi di Buku Kas & Bank')
                    ->icon('heroicon-o-book-open')
                    ->columnSpanFull()
                    ->components([
                        RepeatableEntry::make('transaksiKeuangans')
                            ->label('')
                            ->columns(5)
                            ->components([
                                TextEntry::make('kode_transaksi')
                                    ->label('Kode TRX')
                                    ->badge()
                                    ->color('primary'),
                                TextEntry::make('tgl_transaksi')
                                    ->label('Tanggal')
                                    ->date('d M Y'),
                                TextEntry::make('jenis')
                                    ->label('Arah')
                                    ->badge()
                                    ->color(fn (string $state): string => $state === 'masuk' ? 'success' : 'danger')
                                    ->formatStateUsing(fn (string $state): string => strtoupper($state)),
                                TextEntry::make('akunKeuangan.nama')
                                    ->label('Kas / Bank'),
                                TextEntry::make('nominal')
                                    ->label('Nominal')
                                    ->money('IDR', locale: 'id')
                                    ->weight('bold'),
                            ]),
                    ]),
            ]);
    }
}
