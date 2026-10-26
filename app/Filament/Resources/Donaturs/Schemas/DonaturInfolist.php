<?php

namespace App\Filament\Resources\Donaturs\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DonaturInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas Profil Donatur')
                    ->icon('heroicon-o-user')
                    ->components([
                        Grid::make(4)
                            ->components([
                                TextEntry::make('kode_donatur')
                                    ->label('Kode Donatur')
                                    ->badge()
                                    ->color('primary')
                                    ->copyable(),
                                TextEntry::make('nama')
                                    ->label('Nama Lengkap / Lembaga')
                                    ->weight('bold'),
                                TextEntry::make('jenis')
                                    ->label('Jenis Donatur')
                                    ->badge()
                                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                                TextEntry::make('kategori.nama')
                                    ->label('Kategori Donatur')
                                    ->badge()
                                    ->color('info')
                                    ->placeholder('-'),
                            ]),
                        Grid::make(3)
                            ->components([
                                TextEntry::make('no_hp')
                                    ->label('WhatsApp / Nomor HP')
                                    ->icon('heroicon-o-phone')
                                    ->url(fn (?string $state) => $state ? 'https://wa.me/'.preg_replace('/[^0-9]/', '', $state) : null, true)
                                    ->color('primary')
                                    ->placeholder('-'),
                                TextEntry::make('email')
                                    ->label('Alamat Email')
                                    ->icon('heroicon-o-envelope')
                                    ->placeholder('-'),
                                TextEntry::make('amilPic.nama')
                                    ->label('Amil Pendamping (PIC)')
                                    ->weight('bold')
                                    ->placeholder('Tidak ada PIC'),
                            ]),
                    ]),

                Section::make('Alamat & Domisili')
                    ->icon('heroicon-o-map-pin')
                    ->components([
                        TextEntry::make('alamat')
                            ->label('Alamat Lengkap')
                            ->placeholder('Alamat belum diatur'),
                        Grid::make(4)
                            ->components([
                                TextEntry::make('kelurahan')
                                    ->label('Kelurahan / Desa')
                                    ->placeholder('-'),
                                TextEntry::make('kecamatan')
                                    ->label('Kecamatan')
                                    ->placeholder('-'),
                                TextEntry::make('kota')
                                    ->label('Kota / Kabupaten')
                                    ->placeholder('-'),
                                TextEntry::make('provinsi')
                                    ->label('Provinsi')
                                    ->placeholder('-'),
                            ]),
                    ]),

                Section::make('Identitas Kependudukan & Pajak')
                    ->icon('heroicon-o-identification')
                    ->collapsed()
                    ->components([
                        Grid::make(4)
                            ->components([
                                TextEntry::make('nik')
                                    ->label('NIK KTP')
                                    ->placeholder('-'),
                                TextEntry::make('npwp')
                                    ->label('Nomor Pokok Wajib Pajak (NPWP)')
                                    ->placeholder('-'),
                                TextEntry::make('tgl_lahir')
                                    ->label('Tanggal Lahir / Pendirian')
                                    ->date('d F Y')
                                    ->placeholder('-'),
                                IconEntry::make('is_anonim')
                                    ->label('Status Hamba Allah (Anonim)')
                                    ->boolean(),
                            ]),
                    ]),

                Section::make('Informasi Log Sistem')
                    ->icon('heroicon-o-clock')
                    ->collapsed()
                    ->components([
                        Grid::make(2)
                            ->components([
                                TextEntry::make('created_at')
                                    ->label('Terdaftar Pada')
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
