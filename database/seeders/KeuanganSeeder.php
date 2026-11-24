<?php

namespace Database\Seeders;

use App\Models\AkunKeuangan;
use App\Models\KategoriKeuangan;
use Illuminate\Database\Seeder;

class KeuanganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedChartOfAccounts();
        $this->seedAkunKeuangans();
    }

    private function seedChartOfAccounts(): void
    {
        /**
         * Format: [kode_coa, parent_kode, level, nama, kelompok, pos_dana, jenis, saldo_normal, is_postable, deskripsi]
         */
        $coaRows = [
            // =========================================================================
            // 100.00.000 — ASET
            // =========================================================================
            ['100.00.000', null, 1, 'ASET', 'aset', 'netral', 'netral', 'debit', false, null],
            ['101.00.000', '100.00.000', 2, 'Aset Lancar', 'aset', 'netral', 'netral', 'debit', false, null],

            ['101.01.000', '101.00.000', 2, 'Kas', 'aset', 'netral', 'netral', 'debit', false, null],
            ['101.01.001', '101.01.000', 3, 'Kas', 'aset', 'netral', 'netral', 'debit', true, 'Untuk transaksi Via Cash'],

            ['101.02.000', '101.00.000', 2, 'Bank', 'aset', 'netral', 'netral', 'debit', false, 'Untuk transaksi Via Bank'],
            ['101.02.001', '101.02.000', 3, 'BRI PENERIMAAN 10601001125306', 'aset', 'netral', 'netral', 'debit', true, 'Rekening BRI Penerimaan Donasi'],
            ['101.02.002', '101.02.000', 3, 'BRI PENAMPUNG 10601002368305', 'aset', 'netral', 'netral', 'debit', true, 'Rekening BRI Penampung'],
            ['101.02.003', '101.02.000', 3, 'BRI OPERASIONAL 10601002365307', 'aset', 'netral', 'netral', 'debit', true, 'Rekening BRI Operasional Amil'],
            ['101.02.004', '101.02.000', 3, 'BRI PROGRAM 10601002366303', 'aset', 'netral', 'netral', 'debit', true, 'Rekening BRI Penyaluran Program'],
            ['101.02.005', '101.02.000', 3, 'BRI ZAKAT 10601002367309', 'aset', 'netral', 'netral', 'debit', true, 'Rekening BRI Zakat & Wakaf'],
            ['101.02.006', '101.02.000', 3, 'MANDIRI PENERIMAAN 1390016405635', 'aset', 'netral', 'netral', 'debit', true, 'Rekening Mandiri Penerimaan'],
            ['101.02.007', '101.02.000', 3, 'BRIS/BSI PENERIMAAN 1029299688', 'aset', 'netral', 'netral', 'debit', true, 'Rekening BSI Penerimaan'],
            ['101.02.008', '101.02.000', 3, 'MUAMALAT PENERIMAAN', 'aset', 'netral', 'netral', 'debit', true, 'Rekening Bank Muamalat Penerimaan'],

            ['101.03.000', '101.00.000', 3, 'Deposito', 'aset', 'netral', 'netral', 'debit', true, null],
            ['101.04.000', '101.00.000', 3, 'Investasi Jangka Pendek', 'aset', 'netral', 'netral', 'debit', true, null],
            ['101.05.000', '101.00.000', 3, 'Investasi Jangka Panjang', 'aset', 'netral', 'netral', 'debit', true, null],

            ['101.06.000', '101.00.000', 2, 'Piutang', 'aset', 'netral', 'netral', 'debit', false, null],
            ['101.06.001', '101.06.000', 3, 'Cash Bon', 'aset', 'netral', 'netral', 'debit', true, 'Piutang kasbon / salah transfer sementara'],
            ['101.06.002', '101.06.000', 3, 'Piutang Cicilan', 'aset', 'netral', 'netral', 'debit', true, null],
            ['101.06.003', '101.06.000', 3, 'Piutang lainnya', 'aset', 'netral', 'netral', 'debit', true, null],
            ['101.06.004', '101.06.000', 3, 'Cadangan Penyisihan Piutang lain lain', 'aset', 'netral', 'netral', 'kredit', true, null],

            ['101.07.000', '101.00.000', 3, 'Persediaan', 'aset', 'netral', 'netral', 'debit', true, 'Untuk transaksi Via Natura / Non Cash'],

            ['101.08.000', '101.00.000', 2, 'Beban Dibayar Dimuka', 'aset', 'netral', 'netral', 'debit', false, null],
            ['101.08.001', '101.08.000', 3, 'Sewa Dibayar Dimuka', 'aset', 'netral', 'netral', 'debit', true, null],
            ['101.08.002', '101.08.000', 3, 'Asuransi dibayar dimuka', 'aset', 'netral', 'netral', 'debit', true, null],
            ['101.08.003', '101.08.000', 3, 'Biaya dibayar dimuka lainnya', 'aset', 'netral', 'netral', 'debit', true, null],

            ['101.09.000', '101.00.000', 3, 'Cash Advance', 'aset', 'netral', 'netral', 'debit', true, 'Uang muka kerja operasional & program'],

            ['102.00.000', '100.00.000', 2, 'Aset Tidak Lancar', 'aset', 'netral', 'netral', 'debit', false, null],
            ['102.01.000', '102.00.000', 2, 'Aset Tetap', 'aset', 'amil', 'netral', 'debit', false, 'Aset yang bersumber dari dana Amil'],
            ['102.01.001', '102.01.000', 3, 'Tanah', 'aset', 'amil', 'netral', 'debit', true, null],
            ['102.01.002', '102.01.000', 3, 'Bangunan', 'aset', 'amil', 'netral', 'debit', true, null],
            ['102.01.003', '102.01.000', 3, 'Kendaraan', 'aset', 'amil', 'netral', 'debit', true, null],
            ['102.01.004', '102.01.000', 3, 'Peralatan', 'aset', 'amil', 'netral', 'debit', true, null],
            ['102.01.005', '102.01.000', 3, 'Aset Murabahah', 'aset', 'amil', 'netral', 'debit', true, null],
            ['102.01.006', '102.01.000', 3, 'Akumulasi Penyusutan Bangunan', 'aset', 'amil', 'netral', 'kredit', true, null],
            ['102.01.007', '102.01.000', 3, 'Akumulasi Penyusutan Kendaraan', 'aset', 'amil', 'netral', 'kredit', true, null],
            ['102.01.008', '102.01.000', 3, 'Akumulasi Penyusutan Peralatan', 'aset', 'amil', 'netral', 'kredit', true, null],

            ['102.02.000', '102.00.000', 2, 'Hak Paten & Aset Tak Berwujud', 'aset', 'amil', 'netral', 'debit', false, null],
            ['102.02.001', '102.02.000', 3, 'Nilai Perolehan Hak Paten', 'aset', 'amil', 'netral', 'debit', true, null],
            ['102.02.002', '102.02.000', 3, 'Amortisasi Hak Paten', 'aset', 'amil', 'netral', 'kredit', true, null],
            ['102.02.003', '102.02.000', 3, 'Software', 'aset', 'amil', 'netral', 'debit', true, null],
            ['102.02.004', '102.02.000', 3, 'Nilai Perolehan Software', 'aset', 'amil', 'netral', 'debit', true, null],
            ['102.02.005', '102.02.000', 3, 'Amortisasi Software', 'aset', 'amil', 'netral', 'kredit', true, null],
            ['102.02.006', '102.02.000', 3, 'Goodwill', 'aset', 'amil', 'netral', 'debit', true, null],

            ['102.03.000', '102.00.000', 2, 'Aset Tidak Lancar Kelolaan', 'aset', 'infak_terikat', 'netral', 'debit', false, 'Aset yang bersumber dari dana Salur'],
            ['102.03.001', '102.03.000', 3, 'Tanah Kelolaan', 'aset', 'infak_terikat', 'netral', 'debit', true, null],
            ['102.03.002', '102.03.000', 3, 'Bangunan Kelolaan', 'aset', 'infak_terikat', 'netral', 'debit', true, null],
            ['102.03.003', '102.03.000', 3, 'Kendaraan Kelolaan', 'aset', 'infak_terikat', 'netral', 'debit', true, null],
            ['102.03.004', '102.03.000', 3, 'Peralatan Kelolaan', 'aset', 'infak_terikat', 'netral', 'debit', true, null],
            ['102.03.005', '102.03.000', 3, 'Akumulasi Penyusutan Bangunan Kelolaan', 'aset', 'infak_terikat', 'netral', 'kredit', true, null],
            ['102.03.006', '102.03.000', 3, 'Akumulasi Penyusutan Kendaraan Kelolaan', 'aset', 'infak_terikat', 'netral', 'kredit', true, null],
            ['102.03.007', '102.03.000', 3, 'Akumulasi Penyusutan Peralatan Kelolaan', 'aset', 'infak_terikat', 'netral', 'kredit', true, null],

            // =========================================================================
            // 200.00.000 — KEWAJIBAN
            // =========================================================================
            ['200.00.000', null, 1, 'KEWAJIBAN', 'kewajiban', 'netral', 'netral', 'kredit', false, null],
            ['201.00.000', '200.00.000', 2, 'Kewajiban Jangka Pendek', 'kewajiban', 'netral', 'netral', 'kredit', false, null],
            ['201.01.000', '201.00.000', 3, 'Hutang pada Pihak Ketiga', 'kewajiban', 'netral', 'netral', 'kredit', true, null],
            ['201.02.000', '201.00.000', 2, 'Hutang Pajak', 'kewajiban', 'netral', 'netral', 'kredit', false, null],
            ['201.02.001', '201.02.000', 3, 'PPH 21', 'kewajiban', 'netral', 'netral', 'kredit', true, null],
            ['201.02.002', '201.02.000', 3, 'PPH 4 ayat 2', 'kewajiban', 'netral', 'netral', 'kredit', true, null],
            ['201.02.003', '201.02.000', 3, 'PPH 23', 'kewajiban', 'netral', 'netral', 'kredit', true, null],
            ['201.03.000', '201.00.000', 3, 'Beban YMH Dibayar', 'kewajiban', 'netral', 'netral', 'kredit', true, null],
            ['201.04.000', '201.00.000', 3, 'Kewajiban jangka Pendek lainnya', 'kewajiban', 'netral', 'netral', 'kredit', true, null],
            ['201.05.000', '201.00.000', 2, 'Hutang Wakaf', 'kewajiban', 'wakaf', 'netral', 'kredit', false, null],
            ['201.05.001', '201.05.000', 3, 'Hutang Wakaf Quran', 'kewajiban', 'wakaf', 'netral', 'kredit', true, null],
            ['201.05.002', '201.05.000', 3, 'Hutang Wakaf Rumah Singgah', 'kewajiban', 'wakaf', 'netral', 'kredit', true, null],
            ['201.06.000', '201.00.000', 3, 'Kewajiban Dana Kebajikan (Non Halal)', 'kewajiban', 'non_halal', 'netral', 'kredit', true, 'Bunga bank konvensional / jasa giro non-halal'],

            ['202.00.000', '200.00.000', 2, 'Kewajiban Jangka Panjang', 'kewajiban', 'netral', 'netral', 'kredit', false, null],
            ['202.01.000', '202.00.000', 2, 'Pinjaman Jangka Panjang', 'kewajiban', 'netral', 'netral', 'kredit', false, null],
            ['202.01.001', '202.01.000', 3, 'Pinjaman Bank', 'kewajiban', 'netral', 'netral', 'kredit', true, null],
            ['202.01.002', '202.01.000', 3, 'Hutang Murabahah', 'kewajiban', 'netral', 'netral', 'kredit', true, null],
            ['202.02.000', '202.00.000', 3, 'Kewajiban Imbalan Kerja', 'kewajiban', 'netral', 'netral', 'kredit', true, null],
            ['202.03.000', '202.00.000', 3, 'Kewajiban Jangka Panjang Lainnya', 'kewajiban', 'netral', 'netral', 'kredit', true, null],

            // =========================================================================
            // 300.00.000 — SALDO DANA
            // =========================================================================
            ['300.00.000', null, 1, 'SALDO DANA', 'saldo_dana', 'netral', 'netral', 'kredit', false, null],
            ['301.00.000', '300.00.000', 2, 'Saldo Dana Zakat', 'saldo_dana', 'zakat', 'netral', 'kredit', false, null],
            ['301.00.001', '301.00.000', 3, 'Dana Zakat Profesi', 'saldo_dana', 'zakat', 'netral', 'kredit', true, null],
            ['301.00.002', '301.00.000', 3, 'Dana Zakat Mall', 'saldo_dana', 'zakat', 'netral', 'kredit', true, null],
            ['301.00.003', '301.00.000', 3, 'Dana Zakat Fitrah', 'saldo_dana', 'zakat', 'netral', 'kredit', true, null],
            ['302.00.000', '300.00.000', 2, 'Saldo Dana Infak Terikat', 'saldo_dana', 'infak_terikat', 'netral', 'kredit', false, null],
            ['302.00.001', '302.00.000', 3, 'Dana Infak Terikat Cash', 'saldo_dana', 'infak_terikat', 'netral', 'kredit', true, null],
            ['302.00.002', '302.00.000', 3, 'Dana Infak Terikat Non Cash', 'saldo_dana', 'infak_terikat', 'netral', 'kredit', true, null],
            ['303.00.000', '300.00.000', 3, 'Dana Infak Umum', 'saldo_dana', 'infak_umum', 'netral', 'kredit', true, null],
            ['304.00.000', '300.00.000', 2, 'Saldo Dana Amil', 'saldo_dana', 'amil', 'netral', 'kredit', false, null],
            ['304.00.001', '304.00.000', 3, 'Dana Amil Cash', 'saldo_dana', 'amil', 'netral', 'kredit', true, null],
            ['304.00.002', '304.00.000', 3, 'Dana Amil Non Cash', 'saldo_dana', 'amil', 'netral', 'kredit', true, null],

            // =========================================================================
            // 400.00.000 — PENERIMAAN
            // =========================================================================
            ['400.00.000', null, 1, 'Penerimaan', 'penerimaan', 'netral', 'masuk', 'kredit', false, null],
            ['401.00.000', '400.00.000', 2, 'Penerimaan Dana Zakat', 'penerimaan', 'zakat', 'masuk', 'kredit', false, null],
            ['401.00.001', '401.00.000', 3, 'ZAKAT PROFESI', 'penerimaan', 'zakat', 'masuk', 'kredit', true, null],
            ['401.00.002', '401.00.000', 3, 'ZAKAT MAAL', 'penerimaan', 'zakat', 'masuk', 'kredit', true, null],
            ['401.00.003', '401.00.000', 3, 'ZAKAT FITRAH', 'penerimaan', 'zakat', 'masuk', 'kredit', true, null],

            ['402.00.000', '400.00.000', 2, 'Penerimaan Dana Infak/Sedekah', 'penerimaan', 'infak_terikat', 'masuk', 'kredit', false, null],
            ['402.01.000', '402.00.000', 2, 'Penerimaan Infak Terikat', 'penerimaan', 'infak_terikat', 'masuk', 'kredit', false, null],
            ['402.01.001', '402.01.000', 3, 'Penerimaan Dana Pendidikan', 'penerimaan', 'infak_terikat', 'masuk', 'kredit', true, null],
            ['402.01.002', '402.01.000', 3, 'Penerimaan Dana Kesehatan', 'penerimaan', 'infak_terikat', 'masuk', 'kredit', true, null],
            ['402.01.003', '402.01.000', 3, 'Penerimaan Dana Kemanusiaan', 'penerimaan', 'infak_terikat', 'masuk', 'kredit', true, null],
            ['402.01.004', '402.01.000', 3, 'Penerimaan Dana Sosial Ekonomi', 'penerimaan', 'infak_terikat', 'masuk', 'kredit', true, null],
            ['402.01.005', '402.01.000', 3, 'Penerimaan Dana Dakwah', 'penerimaan', 'infak_terikat', 'masuk', 'kredit', true, null],
            ['402.01.006', '402.01.000', 3, 'Penerimaan Dana Qurban', 'penerimaan', 'infak_terikat', 'masuk', 'kredit', true, null],
            ['402.01.007', '402.01.000', 3, 'Penerimaan Dana Fidyah', 'penerimaan', 'infak_terikat', 'masuk', 'kredit', true, null],
            ['402.01.008', '402.01.000', 3, 'Penerimaan Dana Kafarat', 'penerimaan', 'infak_terikat', 'masuk', 'kredit', true, null],
            ['402.01.009', '402.01.000', 3, 'Penerimaan Dana Hibah', 'penerimaan', 'infak_terikat', 'masuk', 'kredit', true, null],
            ['402.01.010', '402.01.000', 3, 'Penerimaan non Cash Terikat Pendidikan', 'penerimaan', 'infak_terikat', 'masuk', 'kredit', true, null],
            ['402.01.011', '402.01.000', 3, 'Penerimaan non Cash Terikat Kesehatan', 'penerimaan', 'infak_terikat', 'masuk', 'kredit', true, null],
            ['402.01.012', '402.01.000', 3, 'Penerimaan non Cash Terikat Kemanusiaan', 'penerimaan', 'infak_terikat', 'masuk', 'kredit', true, null],
            ['402.01.013', '402.01.000', 3, 'Penerimaan non Cash Terikat Sosial Ekonomi', 'penerimaan', 'infak_terikat', 'masuk', 'kredit', true, null],
            ['402.01.014', '402.01.000', 3, 'Penerimaan non Cash Terikat Dakwah', 'penerimaan', 'infak_terikat', 'masuk', 'kredit', true, null],
            ['402.01.015', '402.01.000', 3, 'Reklase', 'penerimaan', 'infak_terikat', 'masuk', 'kredit', true, null],

            ['402.02.000', '402.00.000', 2, 'Penerimaan Infak Umum', 'penerimaan', 'infak_umum', 'masuk', 'kredit', false, null],
            ['402.02.001', '402.02.000', 3, 'Penerimaan Infak Umum Donatur Perorangan', 'penerimaan', 'infak_umum', 'masuk', 'kredit', true, null],
            ['402.02.002', '402.02.000', 3, 'Penerimaan Infak Umum Tabung Kebaikan', 'penerimaan', 'infak_umum', 'masuk', 'kredit', true, null],
            ['402.02.003', '402.02.000', 3, 'Penerimaan Kotak Infak', 'penerimaan', 'infak_umum', 'masuk', 'kredit', true, null],
            ['402.02.004', '402.02.000', 3, 'Penerimaan Infak Umum Komunitas', 'penerimaan', 'infak_umum', 'masuk', 'kredit', true, null],
            ['402.02.005', '402.02.000', 3, 'Penerimaan non Cash Umum', 'penerimaan', 'infak_umum', 'masuk', 'kredit', true, null],

            ['403.00.000', '400.00.000', 2, 'Penerimaan Dana Amil', 'penerimaan', 'amil', 'masuk', 'kredit', false, null],
            ['403.01.000', '403.00.000', 3, 'Bagian Amil dari Dana Zakat', 'penerimaan', 'amil', 'masuk', 'kredit', true, null],
            ['403.02.000', '403.00.000', 3, 'Bagian Amil dari Dana Infak/Sedekah Terikat', 'penerimaan', 'amil', 'masuk', 'kredit', true, null],
            ['403.03.000', '403.00.000', 3, 'Bagian Amil dari Dana Infak/Sedekah Tidak Terikat', 'penerimaan', 'amil', 'masuk', 'kredit', true, null],
            ['403.04.000', '403.00.000', 3, 'Penerimaan Bagi Hasil Rek Amil', 'penerimaan', 'amil', 'masuk', 'kredit', true, null],
            ['403.05.000', '403.00.000', 3, 'Penerimaan Amil Lain-lain', 'penerimaan', 'amil', 'masuk', 'kredit', true, null],

            ['404.00.000', '400.00.000', 2, 'Penerimaan Lainnya', 'penerimaan', 'non_halal', 'masuk', 'kredit', false, null],
            ['404.01.000', '404.00.000', 3, 'Bagi hasil Tabungan', 'penerimaan', 'non_halal', 'masuk', 'kredit', true, null],
            ['404.02.000', '404.00.000', 3, 'Bagi hasil Deposito', 'penerimaan', 'non_halal', 'masuk', 'kredit', true, null],

            ['405.00.000', '400.00.000', 3, 'Penerimaan non Cash Operasional', 'penerimaan', 'amil', 'masuk', 'kredit', true, null],

            // =========================================================================
            // 500.00.000 — PENYALURAN & BEBAN
            // =========================================================================
            ['500.00.000', null, 1, 'PENYALURAN', 'penyaluran', 'netral', 'keluar', 'debit', false, null],
            ['501.00.000', '500.00.000', 2, 'Penyaluran Dana Zakat', 'penyaluran', 'zakat', 'keluar', 'debit', false, null],
            ['501.01.000', '501.00.000', 3, 'Penyaluran Zakat - Fakir Miskin', 'penyaluran', 'zakat', 'keluar', 'debit', true, null],
            ['501.02.000', '501.00.000', 3, 'Penyaluran Zakat - Amil', 'penyaluran', 'zakat', 'keluar', 'debit', true, null],
            ['501.03.000', '501.00.000', 3, 'Penyaluran Zakat - Muallaf', 'penyaluran', 'zakat', 'keluar', 'debit', true, null],
            ['501.04.000', '501.00.000', 3, 'Penyaluran Zakat - Gharimin', 'penyaluran', 'zakat', 'keluar', 'debit', true, null],
            ['501.05.000', '501.00.000', 3, 'Penyaluran Zakat - Fisabilillah', 'penyaluran', 'zakat', 'keluar', 'debit', true, null],
            ['501.06.000', '501.00.000', 3, 'Penyaluran Zakat - Ibnu Sabil', 'penyaluran', 'zakat', 'keluar', 'debit', true, null],
            ['501.07.000', '501.00.000', 3, 'Penyaluran Zakat - Fitrah', 'penyaluran', 'zakat', 'keluar', 'debit', true, null],

            ['502.00.000', '500.00.000', 2, 'Penyaluran Dana Infak', 'penyaluran', 'infak_terikat', 'keluar', 'debit', false, null],
            ['502.01.000', '502.00.000', 2, 'Penyaluran Infak Terikat', 'penyaluran', 'infak_terikat', 'keluar', 'debit', false, null],
            ['502.01.001', '502.01.000', 3, 'Penyaluran Dana Pendidikan', 'penyaluran', 'infak_terikat', 'keluar', 'debit', true, null],
            ['502.01.002', '502.01.000', 3, 'Penyaluran Dana Kesehatan', 'penyaluran', 'infak_terikat', 'keluar', 'debit', true, null],
            ['502.01.003', '502.01.000', 3, 'Penyaluran Dana Kemanusiaan', 'penyaluran', 'infak_terikat', 'keluar', 'debit', true, null],
            ['502.01.004', '502.01.000', 3, 'Penyaluran Dana Sosial Ekonomi', 'penyaluran', 'infak_terikat', 'keluar', 'debit', true, null],
            ['502.01.005', '502.01.000', 3, 'Penyaluran Dana Dakwah', 'penyaluran', 'infak_terikat', 'keluar', 'debit', true, null],
            ['502.01.006', '502.01.000', 3, 'Pengeluaran Dana Qurban', 'penyaluran', 'infak_terikat', 'keluar', 'debit', true, null],
            ['502.01.007', '502.01.000', 3, 'Pengeluaran Dana Fidyah', 'penyaluran', 'infak_terikat', 'keluar', 'debit', true, null],
            ['502.01.008', '502.01.000', 3, 'Pengeluaran Dana Kafarat', 'penyaluran', 'infak_terikat', 'keluar', 'debit', true, null],
            ['502.01.009', '502.01.000', 3, 'Pengeluaran Dana Hibah', 'penyaluran', 'infak_terikat', 'keluar', 'debit', true, null],
            ['502.01.010', '502.01.000', 3, 'Penyaluran non Cash Terikat Pendidikan', 'penyaluran', 'infak_terikat', 'keluar', 'debit', true, null],
            ['502.01.011', '502.01.000', 3, 'Penyaluran non Cash Terikat Kesehatan', 'penyaluran', 'infak_terikat', 'keluar', 'debit', true, null],
            ['502.01.012', '502.01.000', 3, 'Penyaluran non Cash Terikat Kemanusiaan', 'penyaluran', 'infak_terikat', 'keluar', 'debit', true, null],
            ['502.01.013', '502.01.000', 3, 'Penyaluran non Cash Terikat Sosial Ekonomi', 'penyaluran', 'infak_terikat', 'keluar', 'debit', true, null],
            ['502.01.014', '502.01.000', 3, 'Penyaluran non Cash Terikat Dakwah', 'penyaluran', 'infak_terikat', 'keluar', 'debit', true, null],

            ['502.02.000', '502.00.000', 2, 'Penyaluran Infak Umum', 'penyaluran', 'infak_umum', 'keluar', 'debit', false, null],
            ['502.02.001', '502.02.000', 3, 'Penyaluran Infak Umum', 'penyaluran', 'infak_umum', 'keluar', 'debit', true, null],
            ['502.02.002', '502.02.000', 3, 'Penyaluran non Cash Umum', 'penyaluran', 'infak_umum', 'keluar', 'debit', true, null],
            ['502.02.003', '502.02.000', 3, 'Penyaluran Dana Infak tidak terikat Amil', 'penyaluran', 'infak_umum', 'keluar', 'debit', true, null],

            ['503.00.000', '500.00.000', 2, 'Penyaluran Amil', 'penyaluran', 'amil', 'keluar', 'debit', false, null],
            ['503.01.000', '503.00.000', 2, 'Beban SDM', 'penyaluran', 'amil', 'keluar', 'debit', false, null],
            ['503.01.001', '503.01.000', 3, 'Biaya Gaji', 'penyaluran', 'amil', 'keluar', 'debit', true, null],
            ['503.01.002', '503.01.000', 3, 'Bonus/THR', 'penyaluran', 'amil', 'keluar', 'debit', true, null],
            ['503.01.003', '503.01.000', 3, 'Tunjangan', 'penyaluran', 'amil', 'keluar', 'debit', true, null],
            ['503.01.004', '503.01.000', 3, 'Pesangon', 'penyaluran', 'amil', 'keluar', 'debit', true, null],
            ['503.01.005', '503.01.000', 3, 'Pelatihan dan Pengembangan', 'penyaluran', 'amil', 'keluar', 'debit', true, null],
            ['503.01.006', '503.01.000', 3, 'Beban SDM Lainnya', 'penyaluran', 'amil', 'keluar', 'debit', true, null],

            ['503.02.000', '503.00.000', 2, 'Beban Fundraising', 'penyaluran', 'amil', 'keluar', 'debit', false, null],
            ['503.02.001', '503.02.000', 3, 'Biaya Amil Korlap', 'penyaluran', 'amil', 'keluar', 'debit', true, 'Amil Korlap'],
            ['503.02.002', '503.02.000', 3, 'Bonus/THR Fundraising', 'penyaluran', 'amil', 'keluar', 'debit', true, null],
            ['503.02.003', '503.02.000', 3, 'Tunjangan Fundraising', 'penyaluran', 'amil', 'keluar', 'debit', true, null],
            ['503.02.004', '503.02.000', 3, 'Pesangon Fundraising', 'penyaluran', 'amil', 'keluar', 'debit', true, null],
            ['503.02.005', '503.02.000', 3, 'Pelatihan dan Pengembangan Fundraising', 'penyaluran', 'amil', 'keluar', 'debit', true, null],
            ['503.02.006', '503.02.000', 3, 'Beban SDM Lainnya Fundraising', 'penyaluran', 'amil', 'keluar', 'debit', true, null],
            ['503.02.007', '503.02.000', 3, 'Beban Fee Selling', 'penyaluran', 'amil', 'keluar', 'debit', true, null],

            ['503.03.000', '503.00.000', 2, 'Beban Operasional', 'penyaluran', 'amil', 'keluar', 'debit', false, null],
            ['503.03.001', '503.03.000', 3, 'BIAYA LISTRIK', 'penyaluran', 'amil', 'keluar', 'debit', true, null],
            ['503.03.002', '503.03.000', 3, 'BIAYA SPEEDY/KOMUNIKASI', 'penyaluran', 'amil', 'keluar', 'debit', true, null],
            ['503.03.003', '503.03.000', 3, 'IURAN LINGKUNGAN', 'penyaluran', 'amil', 'keluar', 'debit', true, null],
            ['503.03.004', '503.03.000', 3, 'IURAN KELEMBAGAAN', 'penyaluran', 'amil', 'keluar', 'debit', true, null],
            ['503.03.005', '503.03.000', 3, 'BIAYA TRANSPORTASI/BBM', 'penyaluran', 'amil', 'keluar', 'debit', true, null],
            ['503.03.006', '503.03.000', 3, 'BIAYA SERVIS KENDARAAN OPRSL', 'penyaluran', 'amil', 'keluar', 'debit', true, null],
            ['503.03.007', '503.03.000', 3, 'BIAYA BELANJA RUMAHTANGGA', 'penyaluran', 'amil', 'keluar', 'debit', true, null],
            ['503.03.008', '503.03.000', 3, 'BIAYA KONSUMSI', 'penyaluran', 'amil', 'keluar', 'debit', true, null],
            ['503.03.009', '503.03.000', 3, 'BIAYA TRANSFER/ADM BANK', 'penyaluran', 'amil', 'keluar', 'debit', true, null],
            ['503.03.010', '503.03.000', 3, 'BIAYA KEBERSIHAN', 'penyaluran', 'amil', 'keluar', 'debit', true, null],
            ['503.03.011', '503.03.000', 3, 'BIAYA ATK', 'penyaluran', 'amil', 'keluar', 'debit', true, null],
            ['503.03.012', '503.03.000', 3, 'BIAYA FOTOCOPY DAN CETAK', 'penyaluran', 'amil', 'keluar', 'debit', true, null],
            ['503.03.013', '503.03.000', 3, 'BIAYA PAJAK KENDARAAN', 'penyaluran', 'amil', 'keluar', 'debit', true, null],
            ['503.03.014', '503.03.000', 3, 'BIAYA IURAN BPJS', 'penyaluran', 'amil', 'keluar', 'debit', true, null],
            ['503.03.015', '503.03.000', 3, 'BIAYA PELATIHAN', 'penyaluran', 'amil', 'keluar', 'debit', true, null],
            ['503.03.016', '503.03.000', 3, 'BIAYA PERLENGKAPAN KANTOR', 'penyaluran', 'amil', 'keluar', 'debit', true, null],
            ['503.03.017', '503.03.000', 3, 'MARKOM', 'penyaluran', 'amil', 'keluar', 'debit', true, null],
            ['503.03.018', '503.03.000', 3, 'BIAYA LAIN-LAIN', 'penyaluran', 'amil', 'keluar', 'debit', true, null],

            ['503.04.000', '503.00.000', 3, 'Penyaluran non Cash Operasional', 'penyaluran', 'amil', 'keluar', 'debit', true, null],

            ['504.00.000', '500.00.000', 2, 'Beban Marketing', 'penyaluran', 'amil', 'keluar', 'debit', false, null],
            ['504.01.000', '504.00.000', 3, 'Tools Marketing', 'penyaluran', 'amil', 'keluar', 'debit', true, null],
            ['504.02.000', '504.00.000', 3, 'Biaya Komunikasi', 'penyaluran', 'amil', 'keluar', 'debit', true, null],
            ['504.03.000', '504.00.000', 3, 'Website', 'penyaluran', 'amil', 'keluar', 'debit', true, null],
        ];

        $coaMap = [];

        foreach ($coaRows as $row) {
            [
                $kodeCoa,
                $parentKode,
                $level,
                $nama,
                $kelompok,
                $posDana,
                $jenis,
                $saldoNormal,
                $isPostable,
                $deskripsi,
            ] = $row;

            $parentId = $parentKode ? ($coaMap[$parentKode] ?? KategoriKeuangan::where('kode_coa', $parentKode)->value('id')) : null;

            $coa = KategoriKeuangan::updateOrCreate(
                ['kode_coa' => $kodeCoa],
                [
                    'parent_id' => $parentId,
                    'level' => $level,
                    'nama' => $nama,
                    'kelompok' => $kelompok,
                    'pos_dana' => $posDana,
                    'jenis' => $jenis,
                    'saldo_normal' => $saldoNormal,
                    'is_postable' => $isPostable,
                    'deskripsi' => $deskripsi,
                    'is_active' => true,
                ]
            );

            $coaMap[$kodeCoa] = $coa->id;
        }
    }

    private function seedAkunKeuangans(): void
    {
        $akuns = [
            [
                'kode_coa' => '101.01.001',
                'nama' => 'Kas Tunai (Teller)',
                'nama_bank' => 'Tunai',
                'nomor_rekening' => null,
                'atas_nama' => 'Yayasan Gerak Sedekah Cilacap',
                'tipe' => 'kas',
                'fungsi' => 'kas_teller',
                'saldo_awal' => 0,
                'saldo_minimal' => 0,
                'keterangan' => 'Kas tunai fisik operasional & teller kantor',
            ],
            [
                'kode_coa' => '101.02.001',
                'nama' => 'BRI PENERIMAAN',
                'nama_bank' => 'BRI',
                'nomor_rekening' => '10601001125306',
                'atas_nama' => 'Yayasan Gerak Sedekah Cilacap',
                'tipe' => 'bank',
                'fungsi' => 'penerimaan',
                'saldo_awal' => 0,
                'saldo_minimal' => 0,
                'keterangan' => 'Rekening utama penerimaan donasi transfer via BRI',
            ],
            [
                'kode_coa' => '101.02.002',
                'nama' => 'BRI PENAMPUNG',
                'nama_bank' => 'BRI',
                'nomor_rekening' => '10601002368305',
                'atas_nama' => 'Yayasan Gerak Sedekah Cilacap',
                'tipe' => 'bank',
                'fungsi' => 'penampung',
                'saldo_awal' => 0,
                'saldo_minimal' => 0,
                'keterangan' => 'Rekening BRI penampung dana',
            ],
            [
                'kode_coa' => '101.02.003',
                'nama' => 'BRI OPERASIONAL',
                'nama_bank' => 'BRI',
                'nomor_rekening' => '10601002365307',
                'atas_nama' => 'Yayasan Gerak Sedekah Cilacap',
                'tipe' => 'bank',
                'fungsi' => 'operasional',
                'saldo_awal' => 0,
                'saldo_minimal' => 0,
                'keterangan' => 'Rekening khusus belanja operasional kantor, gaji amil, dan utilitas',
            ],
            [
                'kode_coa' => '101.02.004',
                'nama' => 'BRI PROGRAM',
                'nama_bank' => 'BRI',
                'nomor_rekening' => '10601002366303',
                'atas_nama' => 'Yayasan Gerak Sedekah Cilacap',
                'tipe' => 'bank',
                'fungsi' => 'program',
                'saldo_awal' => 0,
                'saldo_minimal' => 0,
                'keterangan' => 'Rekening khusus penyaluran program sosial, pendidikan, kesehatan, dan dakwah',
            ],
            [
                'kode_coa' => '101.02.005',
                'nama' => 'BRI ZAKAT / WAKAF',
                'nama_bank' => 'BRI',
                'nomor_rekening' => '10601002367309',
                'atas_nama' => 'Yayasan Gerak Sedekah Cilacap',
                'tipe' => 'bank',
                'fungsi' => 'zakat_wakaf',
                'saldo_awal' => 0,
                'saldo_minimal' => 0,
                'keterangan' => 'Rekening BRI khusus dana zakat & wakaf',
            ],
            [
                'kode_coa' => '101.02.006',
                'nama' => 'MANDIRI PENERIMAAN',
                'nama_bank' => 'Bank Mandiri',
                'nomor_rekening' => '1390016405635',
                'atas_nama' => 'Yayasan Gerak Sedekah Cilacap',
                'tipe' => 'bank',
                'fungsi' => 'penerimaan',
                'saldo_awal' => 0,
                'saldo_minimal' => 0,
                'keterangan' => 'Rekening penerimaan donasi via Bank Mandiri',
            ],
            [
                'kode_coa' => '101.02.007',
                'nama' => 'BRIS/BSI PENERIMAAN',
                'nama_bank' => 'BSI',
                'nomor_rekening' => '1029299688',
                'atas_nama' => 'Yayasan Gerak Sedekah Cilacap',
                'tipe' => 'bank',
                'fungsi' => 'penerimaan',
                'saldo_awal' => 0,
                'saldo_minimal' => 0,
                'keterangan' => 'Rekening penerimaan donasi via Bank Syariah Indonesia (BSI)',
            ],
            [
                'kode_coa' => '101.02.008',
                'nama' => 'MUAMALAT PENERIMAAN',
                'nama_bank' => 'Bank Muamalat',
                'nomor_rekening' => null,
                'atas_nama' => 'Yayasan Gerak Sedekah Cilacap',
                'tipe' => 'bank',
                'fungsi' => 'penerimaan',
                'saldo_awal' => 0,
                'saldo_minimal' => 0,
                'keterangan' => 'Rekening penerimaan donasi via Bank Muamalat',
            ],
        ];

        foreach ($akuns as $akun) {
            $coaId = KategoriKeuangan::where('kode_coa', $akun['kode_coa'])->value('id');

            AkunKeuangan::updateOrCreate(
                ['kode_coa' => $akun['kode_coa']],
                array_merge($akun, [
                    'kategori_keuangan_id' => $coaId,
                    'is_active' => true,
                ])
            );
        }
    }
}
