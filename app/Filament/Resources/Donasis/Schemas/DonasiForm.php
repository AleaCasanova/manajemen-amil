<?php

namespace App\Filament\Resources\Donasis\Schemas;

use App\Services\DonasiService;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DonasiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informasi Transaksi Donasi')
                    ->icon('heroicon-o-document-currency-dollar')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(3)
                            ->components([
                                TextInput::make('no_kwitansi')
                                    ->label('Nomor Kwitansi')
                                    ->default(fn () => app(DonasiService::class)->generateNoKwitansi())
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->readOnly(),
                                DateTimePicker::make('tgl_donasi')
                                    ->label('Tanggal & Waktu Transaksi')
                                    ->default(now())
                                    ->required(),
                                Select::make('status')
                                    ->label('Status Verifikasi')
                                    ->options([
                                        'menunggu_verifikasi' => 'Menunggu Verifikasi',
                                        'terverifikasi' => 'Terverifikasi',
                                        'ditolak' => 'Ditolak',
                                    ])
                                    ->default('terverifikasi')
                                    ->required(),
                            ]),
                    ]),

                Section::make('Penyetor (Donatur) & Penerima (Amil)')
                    ->icon('heroicon-o-user-group')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(2)
                            ->components([
                                Select::make('donatur_id')
                                    ->label('Donatur')
                                    ->relationship('donatur', 'nama')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->helperText('Pilih donatur terdaftar atau buat baru di menu Donatur'),
                                Select::make('amil_id')
                                    ->label('Petugas Amil Penerima')
                                    ->relationship('amil', 'nama')
                                    ->searchable()
                                    ->preload()
                                    ->helperText('Amil yang menghimpun donasi ini'),
                            ]),
                    ]),

                Section::make('Rincian Dana & Alokasi')
                    ->icon('heroicon-o-banknotes')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(3)
                            ->components([
                                Select::make('jenis_dana_id')
                                    ->label('Jenis Dana')
                                    ->relationship('jenisDana', 'nama')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('program_id')
                                    ->label('Program / Campaign Tujuan')
                                    ->relationship('program', 'nama')
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Opsional (Program Umum jika kosong)'),
                                TextInput::make('jumlah')
                                    ->label('Jumlah Nominal (Rp)')
                                    ->prefix('Rp')
                                    ->numeric()
                                    ->required()
                                    ->minValue(1000),
                            ]),
                        Grid::make(3)
                            ->components([
                                Select::make('metode_bayar')
                                    ->label('Metode Pembayaran')
                                    ->options([
                                        'transfer' => 'Transfer Bank',
                                        'tunai' => 'Tunai / Cash',
                                        'qris' => 'QRIS',
                                        'ewallet' => 'E-Wallet',
                                    ])
                                    ->default('transfer')
                                    ->reactive()
                                    ->required(),
                                TextInput::make('bank')
                                    ->label('Nama Bank / Dompet')
                                    ->placeholder('BSI, Mandiri, BCA, GoPay, dll')
                                    ->visible(fn ($get) => in_array($get('metode_bayar'), ['transfer', 'ewallet'])),
                                TextInput::make('atas_nama')
                                    ->label('Atas Nama (Niat Khusus)')
                                    ->placeholder('Contoh: Alm. H. Ahmad bin Fulan'),
                            ]),
                        Grid::make(2)
                            ->components([
                                FileUpload::make('bukti_transfer')
                                    ->label('Bukti Transfer / Struk / QRIS')
                                    ->image()
                                    ->disk('public')
                                    ->directory('donasis/bukti')
                                    ->openable()
                                    ->downloadable()
                                    ->maxSize(5120),
                                FileUpload::make('lampiran_foto')
                                    ->label('Foto Dokumentasi Penyerahan')
                                    ->image()
                                    ->disk('public')
                                    ->directory('donasis/dokumentasi')
                                    ->openable()
                                    ->downloadable()
                                    ->maxSize(5120),
                            ]),
                        Grid::make(2)
                            ->components([
                                Textarea::make('pesan')
                                    ->label('Doa / Pesan dari Donatur')
                                    ->rows(2)
                                    ->placeholder('Doa donatur yang akan dicantumkan di kwitansi'),
                                Textarea::make('catatan')
                                    ->label('Catatan Internal Admin / Amil')
                                    ->rows(2),
                            ]),
                    ]),
            ]);
    }
}
