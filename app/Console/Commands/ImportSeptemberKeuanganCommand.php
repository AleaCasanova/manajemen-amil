<?php

namespace App\Console\Commands;

use App\Models\AkunKeuangan;
use App\Models\KategoriKeuangan;
use App\Models\TransaksiKeuangan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ImportSeptemberKeuanganCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'import:september-keuangan
                            {--file= : Path file Excel pembukuan September 2026}
                            {--fresh : Kosongkan mutasi September sebelum import ulang}';

    /**
     * The console command description.
     */
    protected $description = 'Import mutasi keuangan bulan September 2026 dari Excel bendahara ke Buku Kas & Bank';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $filePath = $this->option('file') ?: 'C:/Users/acer/Downloads/Copy of SEPTEMBER 2026.xlsx';

        if (! file_exists($filePath)) {
            $this->error("File Excel tidak ditemukan pada path: {$filePath}");

            return Command::FAILURE;
        }

        $this->info('========================================================================');
        $this->info('  IMPORT DATA KEUANGAN BULAN SEPTEMBER 2026 — LAZ GERAK SEDEKAH CILACAP');
        $this->info('========================================================================');
        $this->line("Membaca berkas: {$filePath}");

        $spreadsheet = IOFactory::load($filePath);

        $adminUser = User::whereHas('roles', fn ($q) => $q->where('name', 'Admin'))->first() ?? User::first();
        $adminId = $adminUser?->id ?? 1;

        $sheetConfigs = [
            'BKH' => [
                'kode_coa' => '101.01.001',
                'start_row' => 4,
                'saldo_awal' => 15356000,
                'prefix' => 'BKH',
            ],
            'BRI PENERIMAAN' => [
                'kode_coa' => '101.02.001',
                'start_row' => 5,
                'saldo_awal' => 21551693,
                'prefix' => 'BRITERIMA',
            ],
            'BRI PROGRAM' => [
                'kode_coa' => '101.02.004',
                'start_row' => 5,
                'saldo_awal' => 574819,
                'prefix' => 'BRIPROG',
            ],
            'MANDIRI' => [
                'kode_coa' => '101.02.006',
                'start_row' => 5,
                'saldo_awal' => 5683442,
                'prefix' => 'MANDIRI',
            ],
            'BSI PENERIMAAN' => [
                'kode_coa' => '101.02.007',
                'start_row' => 5,
                'saldo_awal' => 7438993,
                'prefix' => 'BSI',
            ],
            'MUAMALAT PENERIMAAN' => [
                'kode_coa' => '101.02.008',
                'start_row' => 5,
                'saldo_awal' => 9314179,
                'prefix' => 'MUAMALAT',
            ],
            'BRI OPERASIONAL' => [
                'kode_coa' => '101.02.003',
                'start_row' => 5,
                'saldo_awal' => 1431638,
                'prefix' => 'BRIOPR',
            ],
            'BRI PENAMPUNG' => [
                'kode_coa' => '101.02.002',
                'start_row' => 5,
                'saldo_awal' => 0,
                'prefix' => 'BRITAMPUNG',
            ],
            'BRI ZAKAT' => [
                'kode_coa' => '101.02.005',
                'start_row' => 5,
                'saldo_awal' => 479729,
                'prefix' => 'BRIZAKAT',
            ],
        ];

        // 1. Update Saldo Awal pada 9 Akun Kas & Bank
        $this->comment('Langkah 1: Mengatur Saldo Awal per 1 September 2026...');
        foreach ($sheetConfigs as $cfg) {
            AkunKeuangan::where('kode_coa', $cfg['kode_coa'])->update([
                'saldo_awal' => $cfg['saldo_awal'],
            ]);
        }

        $coasInDb = KategoriKeuangan::pluck('id', 'kode_coa')->all();
        $akunsInDb = AkunKeuangan::pluck('id', 'kode_coa')->all();

        // 2. Bersihkan mutasi September jika fresh
        if ($this->option('fresh')) {
            $this->comment('Langkah 2: Menghapus data transaksi September sebelumnya (--fresh)...');
            TransaksiKeuangan::where('kode_transaksi', 'like', 'TRX-202609%')->forceDelete();
        }

        $totalInserted = 0;

        $this->comment('Langkah 3: Membaca sheet dan menyimpan transaksi...');

        DB::beginTransaction();

        try {
            foreach ($sheetConfigs as $sheetName => $cfg) {
                $sheet = $spreadsheet->getSheetByName($sheetName);
                if (! $sheet) {
                    continue;
                }

                $akunId = $akunsInDb[$cfg['kode_coa']] ?? null;
                if (! $akunId) {
                    $this->warn("Akun {$cfg['kode_coa']} tidak ditemukan di database!");

                    continue;
                }

                $highestRow = $sheet->getHighestRow();
                $sheetCount = 0;

                for ($r = $cfg['start_row']; $r <= $highestRow; $r++) {
                    $rawDate = $sheet->getCell('C'.$r)->getValue();
                    $coa = trim((string) $sheet->getCell('E'.$r)->getValue());
                    $uraian = trim((string) $sheet->getCell('G'.$r)->getValue());

                    $debitCell = $sheet->getCell('I'.$r);
                    $debitRaw = $debitCell->getValue();
                    $debit = 0.0;
                    if ($debitRaw !== null && $debitRaw !== '') {
                        $debit = (float) (str_starts_with((string) $debitRaw, '=') ? $debitCell->getCalculatedValue() : $debitRaw);
                    }

                    $kreditCell = $sheet->getCell('J'.$r);
                    $kreditRaw = $kreditCell->getValue();
                    $kredit = 0.0;
                    if ($kreditRaw !== null && $kreditRaw !== '') {
                        $kredit = (float) (str_starts_with((string) $kreditRaw, '=') ? $kreditCell->getCalculatedValue() : $kreditRaw);
                    }

                    if ($debit <= 0 && $kredit <= 0) {
                        continue;
                    }

                    // Fallback COA Uang Muka di BKH
                    if ($coa === '' && str_starts_with(strtolower($uraian), 'ca ')) {
                        $coa = '101.09.000';
                    }

                    $kategoriId = $coasInDb[$coa] ?? null;

                    // Parse tanggal transaksi secara tangguh
                    $tgl = '2026-09-30';
                    if (is_numeric($rawDate)) {
                        $tgl = Carbon::instance(Date::excelToDateTimeObject($rawDate))->toDateString();
                    } elseif (! empty($rawDate)) {
                        $rawStr = trim((string) $rawDate);
                        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{2,4})$/', $rawStr, $m)) {
                            $day = (int) $m[1];
                            $month = (int) $m[2];
                            $year = 2026; // Normalisasi ke September 2026
                            $tgl = sprintf('%04d-%02d-%02d', $year, $month, $day);
                        } else {
                            try {
                                $parsed = Carbon::parse(str_replace('/', '-', $rawStr));
                                $tgl = sprintf('2026-%02d-%02d', $parsed->month, $parsed->day);
                            } catch (\Throwable) {
                                $tgl = '2026-09-30';
                            }
                        }
                    }

                    $noVoucher = trim((string) $sheet->getCell('D'.$r)->getValue()) ?: null;
                    $reff = trim((string) $sheet->getCell('H'.$r)->getValue()) ?: null;
                    $keteranganCell = trim((string) $sheet->getCell('L'.$r)->getValue());
                    $pjCell = trim((string) $sheet->getCell('M'.$r)->getValue());
                    $deptCell = trim((string) $sheet->getCell('N'.$r)->getValue());

                    $isTransfer = str_starts_with($coa, '101.01')
                        || str_starts_with($coa, '101.02')
                        || str_contains(strtolower($uraian), 'pindah saldo')
                        || str_contains(strtolower($uraian), 'setor tunai');

                    $jenis = $debit > 0 ? 'masuk' : 'keluar';
                    $nominal = $debit > 0 ? $debit : $kredit;

                    $tipeTransaksi = 'reguler';
                    if ($isTransfer) {
                        $tipeTransaksi = 'transfer';
                    } elseif ($coa === '101.09.000') {
                        $tipeTransaksi = $jenis === 'keluar' ? 'cash_advance_keluar' : 'cash_advance_kembali';
                    } elseif ($coa === '101.06.001') {
                        $tipeTransaksi = $jenis === 'keluar' ? 'cash_bon_keluar' : 'cash_bon_kembali';
                    }

                    $keteranganFull = [];
                    if ($keteranganCell !== '') {
                        $keteranganFull[] = $keteranganCell;
                    }
                    if ($pjCell !== '') {
                        $keteranganFull[] = "PJ: {$pjCell}";
                    }
                    if ($deptCell !== '') {
                        $keteranganFull[] = "Dept: {$deptCell}";
                    }

                    $seq = str_pad((string) ($sheetCount + 1), 4, '0', STR_PAD_LEFT);
                    $kodeTrx = "TRX-202609-{$cfg['prefix']}-{$seq}";
                    $finalKeterangan = ! empty($keteranganFull) ? implode(' | ', $keteranganFull) : ($uraian ?: '-');

                    TransaksiKeuangan::create([
                        'kode_transaksi' => $kodeTrx,
                        'no_voucher' => $noVoucher,
                        'reff' => $reff,
                        'jenis' => $jenis,
                        'tipe_transaksi' => $tipeTransaksi,
                        'is_transfer' => $isTransfer,
                        'kategori_keuangan_id' => $kategoriId,
                        'akun_keuangan_id' => $akunId,
                        'nominal' => $nominal,
                        'tgl_transaksi' => $tgl,
                        'pihak_terkait' => $uraian ?: '-',
                        'sub_program' => $keteranganCell ?: null,
                        'keterangan' => $finalKeterangan,
                        'created_by' => $adminId,
                    ]);

                    $sheetCount++;
                    $totalInserted++;
                }

                $this->line("  ✓ Sheet <comment>{$sheetName}</comment>: {$sheetCount} transaksi diimport.");
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Gagal import: '.$e->getMessage().' di '.$e->getFile().':'.$e->getLine());

            return Command::FAILURE;
        }

        $this->newLine();
        $this->info("BERHASIL! Total {$totalInserted} transaksi telah tersimpan di Buku Kas & Bank.");
        $this->newLine();

        // 4. Verifikasi saldo akhir
        $akuns = AkunKeuangan::withMutasiTotals()->where('is_active', true)->orderBy('kode_coa')->get();

        $rows = [];
        foreach ($akuns as $a) {
            $rows[] = [
                "[{$a->kode_coa}] {$a->nama}",
                'Rp '.number_format((float) $a->saldo_awal, 0, ',', '.'),
                'Rp '.number_format($a->total_masuk_computed, 0, ',', '.'),
                'Rp '.number_format($a->total_keluar_computed, 0, ',', '.'),
                'Rp '.number_format($a->saldo_sekarang, 0, ',', '.'),
            ];
        }

        $this->table(
            ['Akun Kas / Bank', 'Saldo Awal', 'Total Masuk', 'Total Keluar', 'Saldo Akhir Database'],
            $rows
        );

        return Command::SUCCESS;
    }
}
