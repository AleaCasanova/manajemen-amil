<?php

namespace App\Filament\Resources\Donasis\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DonasiInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Rincian Transaksi Donasi')
                    ->icon('heroicon-o-document-currency-dollar')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(4)
                            ->components([
                                TextEntry::make('no_kwitansi')
                                    ->label('Nomor Kwitansi')
                                    ->badge()
                                    ->color('primary')
                                    ->copyable(),
                                TextEntry::make('tgl_donasi')
                                    ->label('Tanggal & Waktu Transaksi')
                                    ->dateTime('d M Y, H:i')
                                    ->suffix(' WIB')
                                    ->icon('heroicon-o-calendar'),
                                TextEntry::make('status')
                                    ->label('Status Verifikasi')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'terverifikasi' => 'success',
                                        'menunggu_verifikasi' => 'warning',
                                        'ditolak' => 'danger',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn ($state) => strtoupper(str_replace('_', ' ', $state))),
                                TextEntry::make('jumlah')
                                    ->label('Jumlah Nominal')
                                    ->money('IDR', locale: 'id')
                                    ->weight('bold')
                                    ->color('success'),
                            ]),
                        Grid::make(3)
                            ->components([
                                TextEntry::make('jenisDana.nama')
                                    ->label('Jenis Dana')
                                    ->badge()
                                    ->color('info'),
                                TextEntry::make('program.nama')
                                    ->label('Program Alokasi')
                                    ->placeholder('Program Umum / Bebas'),
                                TextEntry::make('metode_bayar')
                                    ->label('Metode Pembayaran')
                                    ->badge()
                                    ->formatStateUsing(fn ($state, $record) => ucfirst($state).($record->bank ? " ({$record->bank})" : '')),
                            ]),
                    ]),

                Section::make('Pihak Terkait (Donatur & Petugas Amil)')
                    ->icon('heroicon-o-user-group')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(2)
                            ->components([
                                Grid::make(1)
                                    ->components([
                                        TextEntry::make('donatur.nama')
                                            ->label('Nama Donatur (Muzakki / Munfiq)')
                                            ->weight('bold')
                                            ->formatStateUsing(fn ($state, $record) => $record->donatur?->is_anonim ? 'Hamba Allah (Anonim)' : $state),
                                        TextEntry::make('donatur.no_hp')
                                            ->label('WhatsApp Donatur')
                                            ->icon('heroicon-o-phone')
                                            ->url(fn (?string $state) => $state ? 'https://wa.me/'.preg_replace('/[^0-9]/', '', $state) : null, true)
                                            ->placeholder('-'),
                                        TextEntry::make('atas_nama')
                                            ->label('Diniatkan Atas Nama')
                                            ->placeholder('-'),
                                    ]),
                                Grid::make(1)
                                    ->components([
                                        TextEntry::make('amil.nama')
                                            ->label('Petugas Amil Penghimpun')
                                            ->weight('bold')
                                            ->placeholder('Penghimpunan Langsung'),
                                        TextEntry::make('amil.nip')
                                            ->label('NIP Amil')
                                            ->placeholder('-'),
                                        TextEntry::make('amil.divisi.nama')
                                            ->label('Divisi Amil')
                                            ->badge()
                                            ->placeholder('-'),
                                    ]),
                            ]),
                    ]),

                Section::make('Berkas Bukti Transaksi & Foto Dokumentasi')
                    ->icon('heroicon-o-photo')
                    ->description('Dokumentasi resmi transfer perbankan dan serah terima dana')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(2)
                            ->components([
                                ImageEntry::make('bukti_transfer')
                                    ->label('Bukti Transfer / Struk Bank')
                                    ->disk('public')
                                    ->height(240)
                                    ->placeholder('Tidak ada berkas bukti transfer'),
                                ImageEntry::make('lampiran_foto')
                                    ->label('Foto Dokumentasi Penyerahan / Kuitansi')
                                    ->disk('public')
                                    ->height(240)
                                    ->placeholder('Tidak ada foto dokumentasi'),
                            ]),
                    ]),

                Section::make('Niat, Doa, dan Catatan Transaksi')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(2)
                            ->components([
                                TextEntry::make('niat')
                                    ->label('Doa & Niat Khusus Donatur')
                                    ->placeholder('Tidak ada niat khusus')
                                    ->columnSpanFull(),
                                TextEntry::make('pesan')
                                    ->label('Pesan Donatur')
                                    ->placeholder('-'),
                                TextEntry::make('catatan')
                                    ->label('Catatan Internal Admin / Amil')
                                    ->placeholder('-'),
                            ]),
                    ]),

                Section::make('Informasi Verifikasi & Audit')
                    ->icon('heroicon-o-shield-check')
                    ->collapsed()
                    ->columnSpanFull()
                    ->components([
                        Grid::make(3)
                            ->components([
                                TextEntry::make('verifiedByUser.name')
                                    ->label('Diverifikasi Oleh')
                                    ->placeholder('Belum diverifikasi'),
                                TextEntry::make('verified_at')
                                    ->label('Waktu Verifikasi')
                                    ->dateTime('d M Y, H:i')
                                    ->suffix(' WIB')
                                    ->placeholder('-'),
                                TextEntry::make('created_at')
                                    ->label('Waktu Input Sistem')
                                    ->dateTime('d M Y, H:i')
                                    ->suffix(' WIB'),
                            ]),
                    ]),
            ]);
    }
}
