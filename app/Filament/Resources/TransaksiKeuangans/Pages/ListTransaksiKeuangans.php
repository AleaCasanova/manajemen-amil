<?php

namespace App\Filament\Resources\TransaksiKeuangans\Pages;

use App\Exports\TransaksiKeuanganExport;
use App\Filament\Resources\TransaksiKeuangans\TransaksiKeuanganResource;
use App\Filament\Resources\TransaksiKeuangans\Widgets\BukuKasOverviewWidget;
use App\Models\AkunKeuangan;
use App\Models\CashOpname;
use App\Models\TransaksiKeuangan;
use App\Services\TransaksiKeuanganService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ListTransaksiKeuangans extends ListRecords
{
    protected static string $resource = TransaksiKeuanganResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            BukuKasOverviewWidget::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua Transaksi')
                ->badge(TransaksiKeuangan::count()),
            'masuk' => Tab::make('Kas Masuk')
                ->modifyQueryUsing(fn ($query) => $query->where('jenis', 'masuk')->where('is_transfer', false))
                ->badge(TransaksiKeuangan::where('jenis', 'masuk')->where('is_transfer', false)->count())
                ->badgeColor('success'),
            'keluar' => Tab::make('Kas Keluar')
                ->modifyQueryUsing(fn ($query) => $query->where('jenis', 'keluar')->where('is_transfer', false))
                ->badge(TransaksiKeuangan::where('jenis', 'keluar')->where('is_transfer', false)->count())
                ->badgeColor('danger'),
            'transfer' => Tab::make('Transfer Antar Rekening')
                ->modifyQueryUsing(fn ($query) => $query->where('is_transfer', true))
                ->badge(TransaksiKeuangan::where('is_transfer', true)->count())
                ->badgeColor('info'),
            'cash_advance' => Tab::make('Mutasi CA & Kasbon')
                ->modifyQueryUsing(fn ($query) => $query->whereNotNull('cash_advance_id'))
                ->badge(TransaksiKeuangan::whereNotNull('cash_advance_id')->count())
                ->badgeColor('warning'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('ringkasanAkun')
                ->label('Saldo Kas & Bank')
                ->icon('heroicon-o-building-library')
                ->color('gray')
                ->modalHeading('Daftar Rekening & Akun Kas Yayasan')
                ->modalDescription('Pantauan rincian saldo buku, mutasi, dan rekonsiliasi saldo riil setiap dompet / rekening bank yayasan.')
                ->modalWidth('5xl')
                ->modalContent(fn () => view('filament.modals.akun-keuangan-modal', [
                    'akuns' => AkunKeuangan::withMutasiTotals()
                        ->where('is_active', true)
                        ->orderBy('kode_coa')
                        ->orderBy('id')
                        ->get(),
                ]))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Tutup'),

            Action::make('transferAntarAkun')
                ->label('Transfer / Pindah Saldo')
                ->icon('heroicon-o-arrows-right-left')
                ->color('info')
                ->modalHeading('Transfer / Pindah Saldo Antar Rekening')
                ->modalDescription('Mencatat pindah saldo antar kas/bank secara berpasangan (keluar dari rekening asal & masuk ke rekening tujuan).')
                ->modalWidth('2xl')
                ->form([
                    Grid::make(2)
                        ->components([
                            Select::make('akun_asal_id')
                                ->label('Dari Rekening / Kas Asal')
                                ->options(fn () => $this->getAkunOptions())
                                ->searchable()
                                ->preload()
                                ->required()
                                ->different('akun_tujuan_id'),

                            Select::make('akun_tujuan_id')
                                ->label('Ke Rekening / Kas Tujuan')
                                ->options(fn () => $this->getAkunOptions())
                                ->searchable()
                                ->preload()
                                ->required()
                                ->different('akun_asal_id'),
                        ]),

                    Grid::make(3)
                        ->components([
                            TextInput::make('nominal')
                                ->label('Nominal Transfer (Rp)')
                                ->prefix('Rp')
                                ->numeric()
                                ->minValue(1)
                                ->required(),

                            TextInput::make('biaya_admin')
                                ->label('Biaya Admin Bank / Flip (Rp)')
                                ->prefix('Rp')
                                ->numeric()
                                ->minValue(0)
                                ->default(0)
                                ->helperText('Opsional, otomatis tercatat ke COA 503.03.009'),

                            DatePicker::make('tgl_transaksi')
                                ->label('Tanggal Transfer')
                                ->default(today())
                                ->required(),
                        ]),

                    Grid::make(2)
                        ->components([
                            TextInput::make('no_voucher')
                                ->label('No. Voucher (Opsional)')
                                ->placeholder('Contoh: TRF-001'),

                            TextInput::make('reff')
                                ->label('Reff / No. Referensi Bank')
                                ->placeholder('Contoh: Flip / CMS BRI'),
                        ]),

                    Textarea::make('keterangan')
                        ->label('Keterangan Pindah Saldo')
                        ->rows(2)
                        ->placeholder('Contoh: Pindah saldo untuk operasional / penyaluran program'),

                    FileUpload::make('bukti_transaksi')
                        ->label('Bukti Transfer (Opsional)')
                        ->disk('public')
                        ->directory('keuangan-bukti')
                        ->image()
                        ->maxSize(3072),
                ])
                ->action(function (array $data): void {
                    $result = app(TransaksiKeuanganService::class)->buatTransfer($data, Auth::id());

                    Notification::make()
                        ->title('Transfer Antar Rekening Berhasil Dicatat')
                        ->body("Kode Grup: {$result['group_kode']}. Saldo rekening asal & tujuan telah diperbarui.")
                        ->success()
                        ->send();
                }),

            Action::make('cashOpname')
                ->label('Cash Opname Kas')
                ->icon('heroicon-o-calculator')
                ->color('warning')
                ->modalHeading('Cash Opname Fisik Kas Tunai')
                ->modalDescription('Hitung fisik uang kertas & koin brankas lalu bandingkan dengan saldo Buku Kas Harian (BKH).')
                ->modalWidth('4xl')
                ->form([
                    Grid::make(3)
                        ->components([
                            Select::make('akun_keuangan_id')
                                ->label('Akun Kas Tunai')
                                ->options(fn () => AkunKeuangan::where('is_active', true)
                                    ->where('tipe', 'kas')
                                    ->orderBy('kode_coa')
                                    ->pluck('nama', 'id'))
                                ->default(fn () => AkunKeuangan::where('is_active', true)->where('tipe', 'kas')->value('id'))
                                ->live()
                                ->required(),

                            DatePicker::make('tgl_opname')
                                ->label('Tanggal Opname')
                                ->default(today())
                                ->live()
                                ->required(),

                            TextEntry::make('saldo_bkh_preview')
                                ->label('Saldo Buku Kas (BKH)')
                                ->state(function (Get $get): string {
                                    $akunId = $get('akun_keuangan_id');
                                    $tgl = $get('tgl_opname') ?: now()->toDateString();
                                    if (! $akunId) {
                                        return 'Rp 0';
                                    }
                                    $akun = AkunKeuangan::find($akunId);
                                    $saldo = $akun ? $akun->hitungSaldoSampaiTanggal((string) $tgl) : 0;

                                    return 'Rp '.number_format($saldo, 0, ',', '.');
                                }),
                        ]),

                    Section::make('Pecahan Uang Kertas (Jumlah Lembar)')
                        ->compact()
                        ->components([
                            Grid::make(4)
                                ->components(
                                    collect(CashOpname::DENOMINASI_KERTAS)->map(function (int $denom) {
                                        return TextInput::make("pecahan.kertas_{$denom}")
                                            ->label('Rp '.number_format($denom, 0, ',', '.'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->default(0)
                                            ->live(onBlur: true)
                                            ->suffix('lbr');
                                    })->all()
                                ),
                        ]),

                    Section::make('Pecahan Uang Koin & Cek/Wesel')
                        ->compact()
                        ->components([
                            Grid::make(4)
                                ->components(array_merge(
                                    collect(CashOpname::DENOMINASI_KOIN)->map(function (int $denom) {
                                        return TextInput::make("pecahan.koin_{$denom}")
                                            ->label('Koin Rp '.number_format($denom, 0, ',', '.'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->default(0)
                                            ->live(onBlur: true)
                                            ->suffix('kpg');
                                    })->all(),
                                    [
                                        TextInput::make('pecahan.cek_wesel')
                                            ->label('Cek + Wesel (Rp)')
                                            ->numeric()
                                            ->minValue(0)
                                            ->default(0)
                                            ->live(onBlur: true)
                                            ->prefix('Rp'),
                                    ]
                                )),
                        ]),

                    Grid::make(2)
                        ->components([
                            TextEntry::make('total_fisik_preview')
                                ->label('Total Uang Fisik')
                                ->state(function (Get $get): string {
                                    $total = CashOpname::hitungTotalFisik($get('pecahan') ?? []);

                                    return 'Rp '.number_format($total, 0, ',', '.');
                                }),

                            TextEntry::make('selisih_preview')
                                ->label('Selisih (Fisik - BKH)')
                                ->state(function (Get $get): string {
                                    $akunId = $get('akun_keuangan_id');
                                    $tgl = $get('tgl_opname') ?: now()->toDateString();
                                    $akun = $akunId ? AkunKeuangan::find($akunId) : null;
                                    $saldoBuku = $akun ? $akun->hitungSaldoSampaiTanggal((string) $tgl) : 0;
                                    $totalFisik = CashOpname::hitungTotalFisik($get('pecahan') ?? []);
                                    $selisih = $totalFisik - $saldoBuku;

                                    $prefix = $selisih > 0 ? '+' : '';

                                    return $prefix.'Rp '.number_format($selisih, 0, ',', '.');
                                }),
                        ]),

                    Textarea::make('catatan')
                        ->label('Catatan Opname')
                        ->rows(2)
                        ->placeholder('Keterangan hasil hitung fisik brankas/kasir...'),
                ])
                ->action(function (array $data): void {
                    $akun = AkunKeuangan::findOrFail((int) $data['akun_keuangan_id']);
                    $opname = app(TransaksiKeuanganService::class)->simpanCashOpname(
                        $akun,
                        (string) $data['tgl_opname'],
                        $data['pecahan'] ?? [],
                        $data['catatan'] ?? null,
                        Auth::id()
                    );

                    Notification::make()
                        ->title('Cash Opname Berhasil Disimpan')
                        ->body('Total Fisik: Rp '.number_format((float) $opname->total_fisik, 0, ',', '.').' | Selisih: Rp '.number_format((float) $opname->selisih, 0, ',', '.'))
                        ->success()
                        ->send();
                }),

            Action::make('exportExcel')
                ->label('Ekspor Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    return Excel::download(
                        new TransaksiKeuanganExport($this->getFilteredTableQuery()),
                        'buku-kas-keuangan-'.date('Ymd-His').'.xlsx'
                    );
                }),

            CreateAction::make()
                ->label('+ Catat Transaksi')
                ->icon('heroicon-o-plus'),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function getAkunOptions(): array
    {
        return AkunKeuangan::withMutasiTotals()
            ->where('is_active', true)
            ->orderBy('kode_coa')
            ->orderBy('id')
            ->get()
            ->mapWithKeys(function (AkunKeuangan $a): array {
                $saldo = number_format($a->saldo_sekarang, 0, ',', '.');
                $kode = $a->kode_coa ? "[{$a->kode_coa}] " : '';
                $rek = $a->nomor_rekening ? " ({$a->nomor_rekening})" : '';

                return [$a->id => "{$kode}{$a->nama}{$rek} — Saldo: Rp {$saldo}"];
            })
            ->all();
    }
}
