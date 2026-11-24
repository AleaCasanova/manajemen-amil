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
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class TransaksiKeuanganForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Jenis, Tanggal & Referensi Voucher')
                    ->icon('heroicon-o-arrows-right-left')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(4)
                            ->components([
                                Radio::make('jenis')
                                    ->label('Arah Arus Kas')
                                    ->options([
                                        'masuk' => 'Kas Masuk (Debet)',
                                        'keluar' => 'Kas Keluar (Kredit)',
                                    ])
                                    ->default('masuk')
                                    ->inline()
                                    ->live()
                                    ->required(),

                                DatePicker::make('tgl_transaksi')
                                    ->label('Tanggal Transaksi')
                                    ->default(today())
                                    ->required(),

                                TextInput::make('no_voucher')
                                    ->label('No. Voucher (Opsional)')
                                    ->placeholder('Contoh: BKM-001 / BKK-012'),

                                TextInput::make('reff')
                                    ->label('Reff (Opsional)')
                                    ->placeholder('Contoh: Flip / QRIS / LinkAja'),
                            ]),

                        TextInput::make('kode_transaksi')
                            ->label('Kode Transaksi Sistem')
                            ->placeholder('Otomatis (contoh: TRX-202611-0001)')
                            ->disabled()
                            ->dehydrated(false),
                    ]),

                Section::make('Akun Dompet & Akun COA')
                    ->icon('heroicon-o-building-library')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(2)
                            ->components([
                                Select::make('akun_keuangan_id')
                                    ->label('Akun Kas / Rekening Bank')
                                    ->options(function () {
                                        return AkunKeuangan::withMutasiTotals()
                                            ->where('is_active', true)
                                            ->orderBy('kode_coa')
                                            ->orderBy('id')
                                            ->get()
                                            ->mapWithKeys(function (AkunKeuangan $a): array {
                                                $saldoFormatted = number_format($a->saldo_sekarang, 0, ',', '.');
                                                $kode = $a->kode_coa ? "[{$a->kode_coa}] " : '';
                                                $rek = $a->nomor_rekening ? " ({$a->nomor_rekening})" : '';

                                                return [$a->id => "{$kode}{$a->nama}{$rek} — Saldo: Rp {$saldoFormatted}"];
                                            });
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->createOptionForm([
                                        TextInput::make('kode_coa')
                                            ->label('Kode COA (Opsional)')
                                            ->placeholder('Contoh: 101.02.009'),
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
                                    ->helperText('Pilih kas tunai atau rekening bank sumber/tujuan transaksi.'),

                                Select::make('kategori_keuangan_id')
                                    ->label('Akun COA (Pos Perkiraan)')
                                    ->options(function (Get $get) {
                                        $jenis = $get('jenis') ?? 'masuk';

                                        return KategoriKeuangan::active()
                                            ->postable()
                                            ->untukArahKas((string) $jenis)
                                            ->orderBy('kode_coa')
                                            ->orderBy('nama')
                                            ->get()
                                            ->mapWithKeys(fn (KategoriKeuangan $coa): array => [
                                                $coa->id => $coa->label_lengkap,
                                            ]);
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->createOptionForm([
                                        Grid::make(2)
                                            ->components([
                                                TextInput::make('kode_coa')
                                                    ->label('Kode COA (Opsional)')
                                                    ->placeholder('Contoh: 503.03.019'),
                                                TextInput::make('nama')
                                                    ->label('Nama Pos COA')
                                                    ->placeholder('Contoh: Biaya Perlengkapan Event')
                                                    ->required(),
                                            ]),
                                        Grid::make(2)
                                            ->components([
                                                Select::make('jenis')
                                                    ->label('Jenis Arus Kas')
                                                    ->options([
                                                        'masuk' => 'Pemasukan (Kas Masuk)',
                                                        'keluar' => 'Pengeluaran (Kas Keluar)',
                                                        'netral' => 'Netral / Neraca',
                                                    ])
                                                    ->default(fn (Get $get) => $get('../../jenis') ?? 'masuk')
                                                    ->required(),
                                                Select::make('pos_dana')
                                                    ->label('Pos Dana Terkait')
                                                    ->options(KategoriKeuangan::POS_DANA_LABELS)
                                                    ->default('amil'),
                                            ]),
                                        Textarea::make('deskripsi')
                                            ->label('Deskripsi Pos'),
                                    ])
                                    ->createOptionUsing(function (array $data): int {
                                        $data['level'] = 3;
                                        $data['is_postable'] = true;
                                        $data['is_active'] = true;
                                        $kategori = KategoriKeuangan::create($data);

                                        return $kategori->id;
                                    })
                                    ->helperText('Hanya menampilkan akun COA detail (postable) yang sesuai dengan arah kas.'),
                            ]),
                    ]),

                Section::make('Rincian Nominal, Program & Penanggung Jawab')
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
                                    ->label('Uraian Transaksi / Pihak Terkait')
                                    ->placeholder('Contoh: Hamba Allah / SPBU / Abata')
                                    ->helperText('Nama donatur, vendor, atau penerima dana'),

                                TextInput::make('sub_program')
                                    ->label('Keterangan Sub-Program (Opsional)')
                                    ->placeholder('Contoh: RUSING / OTA / FUN RUN / AIR BERSIH'),
                            ]),

                        Grid::make(3)
                            ->components([
                                Select::make('program_id')
                                    ->label('Alokasi Program Terkait (Opsional)')
                                    ->relationship('program', 'nama')
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Pilih jika terkait program tertentu'),

                                Select::make('pj_amil_id')
                                    ->label('PJ / Penanggung Jawab (Opsional)')
                                    ->relationship('pjAmil', 'nama')
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Pilih amil penanggung jawab'),

                                Select::make('divisi_id')
                                    ->label('Departemen / Divisi (Opsional)')
                                    ->relationship('divisi', 'nama')
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Pilih divisi terkait'),
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
