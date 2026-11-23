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
        // 1. Akun Kas & Bank
        $akuns = [
            [
                'nama' => 'Kas Tunai (Brankas Kantor)',
                'nomor_rekening' => null,
                'atas_nama' => 'Yayasan GSC',
                'tipe' => 'kas',
                'saldo_awal' => 0,
                'keterangan' => 'Kas tunai fisik operasional di brankas kantor',
            ],
            [
                'nama' => 'Bank BSI (Rekening Utama ZISWAF)',
                'nomor_rekening' => '7188899912',
                'atas_nama' => 'Yayasan GSC - ZISWAF',
                'tipe' => 'bank',
                'saldo_awal' => 0,
                'keterangan' => 'Rekening penerimaan donasi, zakat, infak, shadaqah',
            ],
            [
                'nama' => 'Bank Mandiri (Rekening Operasional)',
                'nomor_rekening' => '1370001234567',
                'atas_nama' => 'Yayasan GSC - Operasional',
                'tipe' => 'bank',
                'saldo_awal' => 0,
                'keterangan' => 'Rekening pengeluaran rutin operasional & gaji amil',
            ],
        ];

        foreach ($akuns as $akun) {
            AkunKeuangan::firstOrCreate(
                ['nama' => $akun['nama']],
                $akun
            );
        }

        // 2. Kategori Pos Keuangan
        $kategoris = [
            // Pemasukan
            [
                'nama' => 'Penerimaan Donasi & ZISWAF',
                'jenis' => 'masuk',
                'deskripsi' => 'Dana donasi, zakat, infak, dan shadaqah masuk',
            ],
            [
                'nama' => 'Hibah & Bantuan CSR Lembaga',
                'jenis' => 'masuk',
                'deskripsi' => 'Penerimaan hibah pemerintah atau bantuan CSR perusahaan',
            ],
            [
                'nama' => 'Bagi Hasil & Jasa Giro Bank',
                'jenis' => 'masuk',
                'deskripsi' => 'Bagi hasil dari rekening bank lembaga',
            ],
            [
                'nama' => 'Pengembalian Kasbon / Panjar Amil',
                'jenis' => 'masuk',
                'deskripsi' => 'Sisa dana kegiatan / panjar yang dikembalikan ke kas',
            ],
            [
                'nama' => 'Pemasukan Lain-lain',
                'jenis' => 'masuk',
                'deskripsi' => 'Pemasukan di luar pos-pos utama',
            ],

            // Pengeluaran
            [
                'nama' => 'Penyaluran Program & Mustahik',
                'jenis' => 'keluar',
                'deskripsi' => 'Santunan dhuafa, beasiswa yatim, bantuan tanggap bencana, program sosial',
            ],
            [
                'nama' => 'Armada & Transportasi (Ambulans, BBM, Servis)',
                'jenis' => 'keluar',
                'deskripsi' => 'BBM ambulans, antar jemput pasien, servis berkala, dan e-toll',
            ],
            [
                'nama' => 'Operasional Kantor (Listrik, Wifi, Air, ATK)',
                'jenis' => 'keluar',
                'deskripsi' => 'Tagihan bulanan kantor dan pembelian alat tulis kantor',
            ],
            [
                'nama' => 'Gaji, Tunjangan & Hak Amil',
                'jenis' => 'keluar',
                'deskripsi' => 'Honorarium, kafalah amil bulanan, dan insentif',
            ],
            [
                'nama' => 'Promosi, Brosur & Fundraising',
                'jenis' => 'keluar',
                'deskripsi' => 'Cetak banner, spanduk, brosur program donasi, dan iklan medsos',
            ],
            [
                'nama' => 'Konsumsi Rapat & Kegiatan',
                'jenis' => 'keluar',
                'deskripsi' => 'Makan & minum rapat koordinasi, kajian, dan kegiatan lapangan',
            ],
            [
                'nama' => 'Perawatan Gedung & Inventaris',
                'jenis' => 'keluar',
                'deskripsi' => 'Perbaikan AC, renovasi ringan sarana kantor yayasan',
            ],
            [
                'nama' => 'Pengeluaran Lain-lain',
                'jenis' => 'keluar',
                'deskripsi' => 'Pengeluaran darurat atau tidak terduga',
            ],
        ];

        foreach ($kategoris as $kategori) {
            KategoriKeuangan::firstOrCreate(
                ['nama' => $kategori['nama'], 'jenis' => $kategori['jenis']],
                $kategori
            );
        }
    }
}
