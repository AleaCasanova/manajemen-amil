<?php

namespace App\Filament\Resources\TransaksiKeuangans\Schemas;

use App\Models\KategoriKeuangan;
use App\Models\TransaksiKeuangan;
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

                                TextEntry::make('no_voucher')
                                    ->label('No. Voucher')
                                    ->default('-'),

                                TextEntry::make('tipe_transaksi')
                                    ->label('Tipe & Arah Kas')
                                    ->badge()
                                    ->color(fn (TransaksiKeuangan $record): string => $record->is_transfer ? 'info' : ($record->jenis === 'masuk' ? 'success' : 'danger'))
                                    ->formatStateUsing(fn (string $state, TransaksiKeuangan $record): string => (TransaksiKeuangan::TIPE_LABELS[$state] ?? ucfirst($state)).' ('.($record->jenis === 'masuk' ? 'MASUK' : 'KELUAR').')'),

                                TextEntry::make('tgl_transaksi')
                                    ->label('Tanggal Transaksi')
                                    ->date('d M Y')
                                    ->icon('heroicon-o-calendar'),
                            ]),

                        Grid::make(3)
                            ->components([
                                TextEntry::make('nominal')
                                    ->label('Nominal Transaksi')
                                    ->money('IDR', locale: 'id')
                                    ->weight('bold')
                                    ->color(fn (TransaksiKeuangan $record): string => $record->jenis === 'masuk' ? 'success' : 'danger'),

                                TextEntry::make('akunKeuangan.nama')
                                    ->label('Akun Kas / Bank')
                                    ->badge()
                                    ->color('info')
                                    ->helperText(fn (TransaksiKeuangan $record): ?string => $record->akunKeuangan?->kode_coa
                                        ? "{$record->akunKeuangan->kode_coa}".($record->akunKeuangan->nomor_rekening ? " • Rek: {$record->akunKeuangan->nomor_rekening}" : '')
                                        : null),

                                TextEntry::make('kategoriKeuangan.nama')
                                    ->label('Akun COA')
                                    ->badge()
                                    ->formatStateUsing(fn (TransaksiKeuangan $record): string => $record->kategoriKeuangan?->label_lengkap ?? '-')
                                    ->helperText(fn (TransaksiKeuangan $record): ?string => $record->kategoriKeuangan?->pos_dana
                                        ? 'Pos: '.(KategoriKeuangan::POS_DANA_LABELS[$record->kategoriKeuangan->pos_dana] ?? $record->kategoriKeuangan->pos_dana)
                                        : null),
                            ]),

                        Grid::make(3)
                            ->visible(fn (TransaksiKeuangan $record): bool => $record->is_transfer || $record->cash_advance_id !== null)
                            ->components([
                                TextEntry::make('transfer_group_kode')
                                    ->label('Kode Grup Transfer')
                                    ->badge()
                                    ->color('info')
                                    ->visible(fn (TransaksiKeuangan $record): bool => $record->is_transfer),

                                TextEntry::make('transferPasangan.akunKeuangan.nama')
                                    ->label('Rekening Pasangan Transfer')
                                    ->default('-')
                                    ->visible(fn (TransaksiKeuangan $record): bool => $record->is_transfer),

                                TextEntry::make('cashAdvance.kode_ca')
                                    ->label('Dokumen Cash Advance / Kasbon')
                                    ->badge()
                                    ->color('warning')
                                    ->visible(fn (TransaksiKeuangan $record): bool => $record->cash_advance_id !== null),
                            ]),

                        Grid::make(3)
                            ->components([
                                TextEntry::make('pihak_terkait')
                                    ->label('Uraian / Pihak Terkait')
                                    ->default('-'),

                                TextEntry::make('sub_program')
                                    ->label('Sub-Program')
                                    ->default('-'),

                                TextEntry::make('reff')
                                    ->label('Reff')
                                    ->default('-'),
                            ]),

                        Grid::make(4)
                            ->components([
                                TextEntry::make('program.nama')
                                    ->label('Program Terkait')
                                    ->default('-'),

                                TextEntry::make('pjAmil.nama')
                                    ->label('PJ Amil')
                                    ->default('-'),

                                TextEntry::make('divisi.nama')
                                    ->label('Departemen')
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
