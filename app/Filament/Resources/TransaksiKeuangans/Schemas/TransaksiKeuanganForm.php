<?php

namespace App\Filament\Resources\TransaksiKeuangans\Schemas;

use App\Models\AkunKeuangan;
use App\Models\KategoriKeuangan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TransaksiKeuanganForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Jenis & Tanggal Transaksi')
                    ->icon('heroicon-o-arrows-right-left')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(3)
                            ->components([
                                Radio::make('jenis')
                                    ->label('Arah Arus Kas')
                                    ->options([
                                        'masuk' => 'Kas Masuk (Pemasukan)',
                                        'keluar' => 'Kas Keluar (Pengeluaran)',
                                    ])
                                    ->default('masuk')
                                    ->inline()
                                    ->live()
                                    ->required(),
                                DatePicker::make('tgl_transaksi')
                                    ->label('Tanggal Transaksi')
                                    ->default(today())
                                    ->required(),
                                TextInput::make('kode_transaksi')
                                    ->label('Kode Transaksi')
                                    ->placeholder('Otomatis (contoh: TRX-202611-0001)')
                                    ->disabled()
                                    ->dehydrated(false),
                            ]),
                    ]),

                Section::make('Akun Dompet & Pos Keuangan')
                    ->icon('heroicon-o-building-library')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(2)
                            ->components([
                                Select::make('akun_keuangan_id')
                                    ->label('Akun Kas / Rekening Bank')
                                    ->options(function () {
                                        return AkunKeuangan::where('is_active', true)
                                            ->get()
                                            ->mapWithKeys(function (AkunKeuangan $a) {
                                                $saldoFormatted = number_format($a->saldo_sekarang, 0, ',', '.');
                                                $rek = $a->nomor_rekening ? " ({$a->nomor_rekening})" : '';

                                                return [$a->id => "{$a->nama}{$rek} — Saldo: Rp {$saldoFormatted}"];
                                            });
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->createOptionForm([
                                        TextInput::make('nama')
                                            ->label('Nama Akun / Bank')
                                            ->placeholder('Contoh: Kas Kecil Kantor / Bank Syariah')
                                            ->required(),
                                        Select::make('tipe')
                                            ->label('Tipe Akun')
                                            ->options([
                                                'kas' => 'Kas Tunai / Fisik',
                                                'bank' => 'Rekening Bank',
                                                'e_wallet' => 'E-Wallet / QRIS',
                                            ])
                                            ->default('kas')
                                            ->required(),
                                        TextInput::make('nomor_rekening')
                                            ->label('Nomor Rekening (Opsional)'),
                                        TextInput::make('atas_nama')
                                            ->label('Atas Nama Rekening'),
                                        TextInput::make('saldo_awal')
                                            ->label('Saldo Awal (Rp)')
                                            ->numeric()
                                            ->default(0),
                                        Textarea::make('keterangan')
                                            ->label('Keterangan Tambahan'),
                                    ])
                                    ->createOptionUsing(function (array $data): int {
                                        $account = AkunKeuangan::create($data);

                                        return $account->id;
                                    })
                                    ->helperText('Pilih kas tunai atau rekening bank sumber/tujuan transaksi. Anda juga dapat menambah akun baru langsung di sini.'),

                                Select::make('kategori_keuangan_id')
                                    ->label('Kategori Pos Anggaran')
                                    ->options(function (callable $get) {
                                        $jenis = $get('jenis');
                                        $query = KategoriKeuangan::where('is_active', true);
                                        if ($jenis) {
                                            $query->where('jenis', $jenis);
                                        }

                                        return $query->pluck('nama', 'id');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->createOptionForm([
                                        TextInput::make('nama')
                                            ->label('Nama Pos Kategori')
                                            ->placeholder('Contoh: Honor Pengajar / Servis Ambulans')
                                            ->required(),
                                        Select::make('jenis')
                                            ->label('Jenis Kategori')
                                            ->options([
                                                'masuk' => 'Pemasukan (Kas Masuk)',
                                                'keluar' => 'Pengeluaran (Kas Keluar)',
                                            ])
                                            ->default(fn (callable $get) => $get('../../jenis') ?? 'masuk')
                                            ->required(),
                                        Textarea::make('deskripsi')
                                            ->label('Deskripsi Pos'),
                                    ])
                                    ->createOptionUsing(function (array $data): int {
                                        $kategori = KategoriKeuangan::create($data);

                                        return $kategori->id;
                                    })
                                    ->helperText('Kategori pos keuangan (otomatis terfilter berdasarkan Masuk/Keluar)'),
                            ]),
                    ]),

                Section::make('Rincian Nominal & Keterangan')
                    ->icon('heroicon-o-banknotes')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(3)
                            ->components([
                                TextInput::make('nominal')
                                    ->label('Nominal Transaksi (Rp)')
                                    ->prefix('Rp')
                                    ->required()
                                    ->numeric()
                                    ->minValue(1)
                                    ->placeholder('100000'),

                                TextInput::make('pihak_terkait')
                                    ->label('Pihak Terkait / Vendor / Penyetor')
                                    ->placeholder('Contoh: SPBU Pertamina / Donatur PT ABC / Amil Fulan')
                                    ->helperText('Nama penyetor dana atau pihak ketiga/toko penerima uang'),

                                Select::make('program_id')
                                    ->label('Alokasi Program Terkait (Opsional)')
                                    ->relationship('program', 'nama')
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Pilih jika terkait program tertentu'),
                            ]),

                        Textarea::make('keterangan')
                            ->label('Uraian / Keterangan Keperluan')
                            ->rows(3)
                            ->required()
                            ->placeholder('Jelaskan detail pengeluaran/pemasukan ini secara lengkap...'),

                        FileUpload::make('bukti_transaksi')
                            ->label('Bukti Kwitansi / Struk / Nota / Bukti Transfer')
                            ->disk('public')
                            ->directory('keuangan-bukti')
                            ->image()
                            ->maxSize(3072)
                            ->openable()
                            ->downloadable()
                            ->helperText('Format JPG/PNG/WEBP, maksimal ukuran 3MB'),
                    ]),
            ]);
    }
}
