<?php

namespace Database\Seeders;

use App\Models\Agenda;
use App\Models\AktivitasHarian;
use App\Models\Amil;
use App\Models\Donasi;
use App\Models\DonasiBarang;
use App\Models\Donatur;
use App\Models\DonaturKategori;
use App\Models\IzinCuti;
use App\Models\JenisDana;
use App\Models\Mustahik;
use App\Models\Notifikasi;
use App\Models\Pengumuman;
use App\Models\Penyaluran;
use App\Models\Program;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    /**
     * Jalankan penambahan data dummy untuk tabel-tabel operasional.
     * CATATAN PENTING: Sesuai instruksi, data amil dan user TIDAK DIUBAH / DIOTAK-ATIK SAMA SEKALI.
     */
    public function run(): void
    {
        $adminUser = User::first();
        $adminId = $adminUser?->id ?? 1;

        $amils = Amil::all();
        if ($amils->isEmpty()) {
            return;
        }

        $amil1 = $amils->first();
        $amil2 = $amils->skip(1)->first() ?? $amil1;
        $amil3 = $amils->skip(2)->first() ?? $amil1;
        $amil4 = $amils->skip(3)->first() ?? $amil1;
        $amil5 = $amils->skip(4)->first() ?? $amil1;
        $amil6 = $amils->skip(5)->first() ?? $amil1;

        $kategoriRetail = DonaturKategori::firstOrCreate(['nama' => 'Perorangan / Retail']);
        $kategoriKorporat = DonaturKategori::firstOrCreate(['nama' => 'Korporat / Lembaga / Perusahaan']);
        $kategoriKomunitas = DonaturKategori::firstOrCreate(['nama' => 'Komunitas / Komunitas Mitra']);

        $jenisZakatMaal = JenisDana::where('nama', 'LIKE', '%Zakat Maal%')->first() ?? JenisDana::first();
        $jenisZakatFitrah = JenisDana::where('nama', 'LIKE', '%Zakat Fitrah%')->first() ?? JenisDana::first();
        $jenisInfakTerikat = JenisDana::where('nama', 'LIKE', '%Terikat%')->first() ?? JenisDana::first();
        $jenisInfakUmum = JenisDana::where('nama', 'LIKE', '%Umum%')->first() ?? JenisDana::first();
        $jenisDskl = JenisDana::where('nama', 'LIKE', '%DSKL%')->first() ?? JenisDana::first();

        // ========================================================
        // 1. PROGRAMS (Pastikan minimal 10 program)
        // ========================================================
        $programsData = [
            [
                'nama' => 'Sedekah Al-Qur\'an & Santri Penghafal Kitab',
                'deskripsi' => 'Pengadaan mushaf Al-Qur\'an standar dan santunan biaya hidup santri dhuafa penghafal Al-Qur\'an di pelosok Cilacap.',
                'target_dana' => 50000000,
                'tgl_mulai' => Carbon::create(2026, 1, 1),
                'tgl_selesai' => Carbon::create(2026, 12, 31),
                'status' => 'aktif',
            ],
            [
                'nama' => 'Tanggap Bencana & Dapur Umum Darurat',
                'deskripsi' => 'Kesiapsiagaan logistik, perahu karet, dan dapur umum darurat untuk bencana banjir dan tanah longsor di wilayah Cilacap.',
                'target_dana' => 80000000,
                'tgl_mulai' => Carbon::create(2026, 1, 1),
                'tgl_selesai' => Carbon::create(2026, 12, 31),
                'status' => 'aktif',
            ],
            [
                'nama' => 'Bantuan Pangan & Gizi Balita Stunting',
                'deskripsi' => 'Penyaluran asupan nutrisi protein tinggi, telur, susu, dan multivitamin untuk balita terindikasi stunting dari keluarga prasejahtera.',
                'target_dana' => 60000000,
                'tgl_mulai' => Carbon::create(2026, 2, 1),
                'tgl_selesai' => Carbon::create(2026, 12, 31),
                'status' => 'aktif',
            ],
            [
                'nama' => 'Operasi Katarak & Pengobatan Lansia Gratis',
                'deskripsi' => 'Pemeriksaan mata berkala, kacamata gratis, dan pembiayaan tindakan operasi katarak untuk lansia mustahik tidak mampu.',
                'target_dana' => 120000000,
                'tgl_mulai' => Carbon::create(2026, 3, 1),
                'tgl_selesai' => Carbon::create(2026, 12, 31),
                'status' => 'aktif',
            ],
            [
                'nama' => 'Santunan Yatim & Piatu Bahagia',
                'deskripsi' => 'Program santunan uang saku bulanan, pembinaan karakter, dan bingkisan hari raya untuk 100 anak yatim piatu di Cilacap.',
                'target_dana' => 90000000,
                'tgl_mulai' => Carbon::create(2026, 1, 1),
                'tgl_selesai' => Carbon::create(2026, 12, 31),
                'status' => 'aktif',
            ],
        ];

        foreach ($programsData as $p) {
            Program::firstOrCreate(['nama' => $p['nama']], $p);
        }

        $allPrograms = Program::all();

        // ========================================================
        // 2. MUSTAHIK (Pastikan minimal 10 mustahik)
        // ========================================================
        $mustahikData = [
            [
                'nama' => 'Mbah Waryono',
                'nik' => '3301015508470001',
                'alamat' => 'Desa Maos Kidul RT 03/RW 02, Kec. Maos, Kab. Cilacap',
                'asnaf' => 'fakir',
                'kondisi' => 'Lansia sebatang kara usia 79 tahun, tidak memiliki penghasilan tetap, rumah berdinding bambu.',
                'status_verifikasi' => 'terverifikasi',
            ],
            [
                'nama' => 'Ibu Samini',
                'nik' => '3301026104780003',
                'alamat' => 'Desa Adipala RT 01/RW 04, Kec. Adipala, Kab. Cilacap',
                'asnaf' => 'miskin',
                'kondisi' => 'Janda anak 3, bekerja serabutan sebagai buruh kupas kelapa dengan penghasilan Rp 25.000/hari.',
                'status_verifikasi' => 'terverifikasi',
            ],
            [
                'nama' => 'Bapak Nurhasan (Mualaf)',
                'nik' => '3301031205850002',
                'alamat' => 'Kelurahan Gumilir RT 04/RW 01, Kec. Cilacap Utara, Kab. Cilacap',
                'asnaf' => 'mualaf',
                'kondisi' => 'Baru memeluk Islam 8 bulan lalu, membutuhkan penguatan ekonomi dan pembinaan keagamaan.',
                'status_verifikasi' => 'terverifikasi',
            ],
            [
                'nama' => 'Ustadz Abdul Wahid',
                'nik' => '3301041903820005',
                'alamat' => 'Desa Kunci RT 02/RW 05, Kec. Sidareja, Kab. Cilacap',
                'asnaf' => 'fisabilillah',
                'kondisi' => 'Guru ngaji TPQ pelosok desa membina 45 santri tanpa meminta iuran bayaran.',
                'status_verifikasi' => 'terverifikasi',
            ],
            [
                'nama' => 'Ibu Darsih',
                'nik' => '3301054811790004',
                'alamat' => 'Desa Bojong RT 05/RW 02, Kec. Kawunganten, Kab. Cilacap',
                'asnaf' => 'miskin',
                'kondisi' => 'Suami menderita stroke menahun, menanggung 2 anak sekolah di tingkat SMP dan SD.',
                'status_verifikasi' => 'terverifikasi',
            ],
            [
                'nama' => 'Mbah Karyo Sukarto',
                'nik' => '3301061502440001',
                'alamat' => 'Desa Padangsari RT 01/RW 03, Kec. Majenang, Kab. Cilacap',
                'asnaf' => 'fakir',
                'kondisi' => 'Tunanetra dan lumpuh ringan, dirawat oleh tetangga secara gotong royong.',
                'status_verifikasi' => 'terverifikasi',
            ],
        ];

        foreach ($mustahikData as $m) {
            Mustahik::firstOrCreate(['nik' => $m['nik']], $m);
        }

        $allMustahiks = Mustahik::all();

        // ========================================================
        // 3. PENYALURAN (Pastikan minimal 10 penyaluran)
        // ========================================================
        $penyaluranData = [
            [
                'mustahik_id' => $allMustahiks->where('nama', 'Mbah Waryono')->first()?->id ?? $allMustahiks->first()->id,
                'program_id' => $allPrograms->where('nama', 'Pangan Berkah Lansia Dhuafa')->first()?->id ?? $allPrograms->first()->id,
                'jenis_dana_id' => $jenisInfakUmum->id,
                'jumlah' => 750000,
                'tgl_salur' => Carbon::create(2026, 10, 15),
                'amil_id' => $amil1->id,
                'catatan' => 'Penyaluran paket sembako lengkap dan santunan tunai kebutuhan harian lansia dhuafa.',
            ],
            [
                'mustahik_id' => $allMustahiks->where('nama', 'Ibu Samini')->first()?->id ?? $allMustahiks->first()->id,
                'program_id' => $allPrograms->where('nama', 'Pemberdayaan UMKM Barokah')->first()?->id ?? $allPrograms->first()->id,
                'jenis_dana_id' => $jenisInfakTerikat->id,
                'jumlah' => 2000000,
                'tgl_salur' => Carbon::create(2026, 10, 18),
                'amil_id' => $amil2->id,
                'catatan' => 'Bantuan modal usaha dagang sayur keliling dan timbangan digital.',
            ],
            [
                'mustahik_id' => $allMustahiks->where('nama', 'Bapak Nurhasan (Mualaf)')->first()?->id ?? $allMustahiks->first()->id,
                'program_id' => $allPrograms->where('nama', 'Sedekah Al-Qur\'an & Santri Penghafal Kitab')->first()?->id ?? $allPrograms->first()->id,
                'jenis_dana_id' => $jenisZakatMaal->id,
                'jumlah' => 1500000,
                'tgl_salur' => Carbon::create(2026, 10, 20),
                'amil_id' => $amil5->id,
                'catatan' => 'Santunan pembinaan asnaf mualaf dan paket perlengkapan ibadah sholat.',
            ],
            [
                'mustahik_id' => $allMustahiks->where('nama', 'Ustadz Abdul Wahid')->first()?->id ?? $allMustahiks->first()->id,
                'program_id' => $allPrograms->where('nama', 'Sedekah Al-Qur\'an & Santri Penghafal Kitab')->first()?->id ?? $allPrograms->first()->id,
                'jenis_dana_id' => $jenisZakatMaal->id,
                'jumlah' => 1250000,
                'tgl_salur' => Carbon::create(2026, 10, 22),
                'amil_id' => $amil3->id,
                'catatan' => 'Kafalah guru ngaji pelosok TPQ dan bantuan 20 mushaf Al-Qur\'an santri.',
            ],
            [
                'mustahik_id' => $allMustahiks->where('nama', 'Ibu Darsih')->first()?->id ?? $allMustahiks->first()->id,
                'program_id' => $allPrograms->where('nama', 'Layanan Ambulans Siaga Gratis')->first()?->id ?? $allPrograms->first()->id,
                'jenis_dana_id' => $jenisInfakTerikat->id,
                'jumlah' => 1000000,
                'tgl_salur' => Carbon::create(2026, 10, 24),
                'amil_id' => $amil4->id,
                'catatan' => 'Bantuan operasional pengantaran kontrol rutin pasien stroke ke RSUD Cilacap.',
            ],
            [
                'mustahik_id' => $allMustahiks->where('nama', 'Mbah Karyo Sukarto')->first()?->id ?? $allMustahiks->first()->id,
                'program_id' => $allPrograms->where('nama', 'Pangan Berkah Lansia Dhuafa')->first()?->id ?? $allPrograms->first()->id,
                'jenis_dana_id' => $jenisInfakUmum->id,
                'jumlah' => 800000,
                'tgl_salur' => Carbon::create(2026, 10, 26),
                'amil_id' => $amil6->id,
                'catatan' => 'Santunan tunai bulanan dan popok dewasa medis lansia dhuafa.',
            ],
        ];

        foreach ($penyaluranData as $peny) {
            Penyaluran::firstOrCreate(
                [
                    'mustahik_id' => $peny['mustahik_id'],
                    'tgl_salur' => $peny['tgl_salur'],
                    'jumlah' => $peny['jumlah'],
                ],
                $peny
            );
        }

        // ========================================================
        // 4. DONATUR (Tambahkan 10 donatur baru)
        // ========================================================
        $donatursData = [
            [
                'kode_donatur' => 'DNT-202610-007',
                'nama' => 'H. Bambang Soeprapto',
                'jenis' => 'individu',
                'kategori_id' => $kategoriRetail->id,
                'no_hp' => '081227189001',
                'email' => 'bambang.soeprapto@gmail.com',
                'alamat' => 'Jl. Gatot Subroto No. 45',
                'kelurahan' => 'Sidanegara',
                'kecamatan' => 'Cilacap Tengah',
                'kota' => 'Cilacap',
                'provinsi' => 'Jawa Tengah',
                'is_anonim' => false,
                'consent' => true,
                'amil_pic_id' => $amil1->id,
                'created_by' => $adminId,
            ],
            [
                'kode_donatur' => 'DNT-202610-008',
                'nama' => 'dr. Ratna Wulandari, Sp.A',
                'jenis' => 'individu',
                'kategori_id' => $kategoriRetail->id,
                'no_hp' => '081391223002',
                'email' => 'dr.ratna.w@gmail.com',
                'alamat' => 'Jl. Dr. Soetomo No. 12',
                'kelurahan' => 'Sidakaya',
                'kecamatan' => 'Cilacap Selatan',
                'kota' => 'Cilacap',
                'provinsi' => 'Jawa Tengah',
                'is_anonim' => false,
                'consent' => true,
                'amil_pic_id' => $amil2->id,
                'created_by' => $adminId,
            ],
            [
                'kode_donatur' => 'DNT-202610-009',
                'nama' => 'CV. Samudra Jaya Mandiri',
                'jenis' => 'lembaga',
                'kategori_id' => $kategoriKorporat->id,
                'no_hp' => '082134556003',
                'email' => 'csr@samudrajaya.co.id',
                'alamat' => 'Kawasan Industri Cilacap Kav. 18',
                'kelurahan' => 'Karangkandri',
                'kecamatan' => 'Kesugihan',
                'kota' => 'Cilacap',
                'provinsi' => 'Jawa Tengah',
                'is_anonim' => false,
                'consent' => true,
                'amil_pic_id' => $amil3->id,
                'created_by' => $adminId,
            ],
            [
                'kode_donatur' => 'DNT-202610-010',
                'nama' => 'Hj. Maryam Sulaiman',
                'jenis' => 'individu',
                'kategori_id' => $kategoriRetail->id,
                'no_hp' => '085747889004',
                'email' => 'maryam.sulaiman@yahoo.co.id',
                'alamat' => 'Jl. Diponegoro No. 88',
                'kelurahan' => 'Adipala',
                'kecamatan' => 'Adipala',
                'kota' => 'Cilacap',
                'provinsi' => 'Jawa Tengah',
                'is_anonim' => false,
                'consent' => true,
                'amil_pic_id' => $amil4->id,
                'created_by' => $adminId,
            ],
            [
                'kode_donatur' => 'DNT-202610-011',
                'nama' => 'Ir. Eko Prasetyo, M.T.',
                'jenis' => 'individu',
                'kategori_id' => $kategoriRetail->id,
                'no_hp' => '087836112005',
                'email' => 'eko.prasetyo@eng.co.id',
                'alamat' => 'Perum Griya Praja Mukti Blok D-5',
                'kelurahan' => 'Kroya',
                'kecamatan' => 'Kroya',
                'kota' => 'Cilacap',
                'provinsi' => 'Jawa Tengah',
                'is_anonim' => false,
                'consent' => true,
                'amil_pic_id' => $amil5->id,
                'created_by' => $adminId,
            ],
            [
                'kode_donatur' => 'DNT-202610-012',
                'nama' => 'Komunitas Motor Trail Peduli',
                'jenis' => 'lembaga',
                'kategori_id' => $kategoriKomunitas->id,
                'no_hp' => '085290113006',
                'email' => 'trail.cilacap.peduli@gmail.com',
                'alamat' => 'Sekretariat Trail Adventure Cilacap',
                'kelurahan' => 'Jeruklegi Wetan',
                'kecamatan' => 'Jeruklegi',
                'kota' => 'Cilacap',
                'provinsi' => 'Jawa Tengah',
                'is_anonim' => false,
                'consent' => true,
                'amil_pic_id' => $amil6->id,
                'created_by' => $adminId,
            ],
            [
                'kode_donatur' => 'DNT-202610-013',
                'nama' => 'H. Suwandi & Keluarga',
                'jenis' => 'individu',
                'kategori_id' => $kategoriRetail->id,
                'no_hp' => '081328994007',
                'email' => 'h.suwandi@outlook.com',
                'alamat' => 'Jl. Jenderal Sudirman No. 102',
                'kelurahan' => 'Maos Lor',
                'kecamatan' => 'Maos',
                'kota' => 'Cilacap',
                'provinsi' => 'Jawa Tengah',
                'is_anonim' => false,
                'consent' => true,
                'amil_pic_id' => $amil1->id,
                'created_by' => $adminId,
            ],
            [
                'kode_donatur' => 'DNT-202610-014',
                'nama' => 'Agus Priyanto, S.E.',
                'jenis' => 'individu',
                'kategori_id' => $kategoriRetail->id,
                'no_hp' => '085876445008',
                'email' => 'agus.priyanto@finance.id',
                'alamat' => 'Jl. Perintis Kemerdekaan No. 23',
                'kelurahan' => 'Gumilir',
                'kecamatan' => 'Cilacap Utara',
                'kota' => 'Cilacap',
                'provinsi' => 'Jawa Tengah',
                'is_anonim' => false,
                'consent' => true,
                'amil_pic_id' => $amil2->id,
                'created_by' => $adminId,
            ],
            [
                'kode_donatur' => 'DNT-202610-015',
                'nama' => 'Koperasi Bahari Sejahtera',
                'jenis' => 'lembaga',
                'kategori_id' => $kategoriKorporat->id,
                'no_hp' => '081290334009',
                'email' => 'koperasi.bahari.clp@gmail.com',
                'alamat' => 'Kompleks Pelabuhan Perikanan Samudera',
                'kelurahan' => 'Tegal Kamulyan',
                'kecamatan' => 'Cilacap Selatan',
                'kota' => 'Cilacap',
                'provinsi' => 'Jawa Tengah',
                'is_anonim' => false,
                'consent' => true,
                'amil_pic_id' => $amil3->id,
                'created_by' => $adminId,
            ],
            [
                'kode_donatur' => 'DNT-202610-016',
                'nama' => 'Hamba Allah Anonim Bersyukur',
                'jenis' => 'individu',
                'kategori_id' => $kategoriRetail->id,
                'no_hp' => '087711229010',
                'email' => null,
                'alamat' => 'Cilacap Tengah',
                'kelurahan' => 'Gunungsimping',
                'kecamatan' => 'Cilacap Tengah',
                'kota' => 'Cilacap',
                'provinsi' => 'Jawa Tengah',
                'is_anonim' => true,
                'consent' => true,
                'amil_pic_id' => $amil4->id,
                'created_by' => $adminId,
            ],
        ];

        foreach ($donatursData as $dnt) {
            Donatur::firstOrCreate(['kode_donatur' => $dnt['kode_donatur']], $dnt);
        }

        // ========================================================
        // 5. DONASI (Tambahkan 10 transaksi donasi baru)
        // ========================================================
        $donaturBambang = Donatur::where('kode_donatur', 'DNT-202610-007')->first();
        $donaturRatna = Donatur::where('kode_donatur', 'DNT-202610-008')->first();
        $donaturSamudra = Donatur::where('kode_donatur', 'DNT-202610-009')->first();
        $donaturMaryam = Donatur::where('kode_donatur', 'DNT-202610-010')->first();
        $donaturEko = Donatur::where('kode_donatur', 'DNT-202610-011')->first();
        $donaturTrail = Donatur::where('kode_donatur', 'DNT-202610-012')->first();
        $donaturSuwandi = Donatur::where('kode_donatur', 'DNT-202610-013')->first();
        $donaturAgus = Donatur::where('kode_donatur', 'DNT-202610-014')->first();
        $donaturKoperasi = Donatur::where('kode_donatur', 'DNT-202610-015')->first();
        $donaturAnonim = Donatur::where('kode_donatur', 'DNT-202610-016')->first();

        $progAmbulans = $allPrograms->where('nama', 'Layanan Ambulans Siaga Gratis')->first();
        $progBeasiswa = $allPrograms->where('nama', 'Beasiswa Generasi Cerdas Mandiri')->first();
        $progUmkm = $allPrograms->where('nama', 'Pemberdayaan UMKM Barokah')->first();
        $progQuran = $allPrograms->where('nama', 'Sedekah Al-Qur\'an & Santri Penghafal Kitab')->first();
        $progKatarak = $allPrograms->where('nama', 'Operasi Katarak & Pengobatan Lansia Gratis')->first();

        $donasisData = [
            [
                'no_kwitansi' => 'KWT-GSC/2026/10/0010',
                'donatur_id' => $donaturBambang->id,
                'amil_id' => $amil1->id,
                'jenis_dana_id' => $jenisZakatMaal->id,
                'program_id' => null,
                'jumlah' => 7500000,
                'metode_bayar' => 'transfer',
                'bank' => 'BSI - Bank Syariah Indonesia',
                'tgl_donasi' => Carbon::create(2026, 10, 24, 9, 30),
                'niat' => 'Zakat penghasilan profesi tahunan keluarga',
                'atas_nama' => 'H. Bambang Soeprapto & Keluarga',
                'status' => 'terverifikasi',
                'verified_by' => $adminId,
                'verified_at' => Carbon::create(2026, 10, 24, 10, 0),
            ],
            [
                'no_kwitansi' => 'KWT-GSC/2026/10/0011',
                'donatur_id' => $donaturRatna->id,
                'amil_id' => $amil2->id,
                'jenis_dana_id' => $jenisInfakTerikat->id,
                'program_id' => $progAmbulans?->id,
                'jumlah' => 2000000,
                'metode_bayar' => 'qris',
                'bank' => null,
                'tgl_donasi' => Carbon::create(2026, 10, 25, 11, 15),
                'niat' => 'Sedekah operasional ambulans siaga darurat',
                'atas_nama' => 'dr. Ratna Wulandari, Sp.A',
                'status' => 'terverifikasi',
                'verified_by' => $adminId,
                'verified_at' => Carbon::create(2026, 10, 25, 11, 45),
            ],
            [
                'no_kwitansi' => 'KWT-GSC/2026/10/0012',
                'donatur_id' => $donaturSamudra->id,
                'amil_id' => $amil3->id,
                'jenis_dana_id' => $jenisInfakTerikat->id,
                'program_id' => $progBeasiswa?->id,
                'jumlah' => 15000000,
                'metode_bayar' => 'transfer',
                'bank' => 'BCA - Bank Central Asia',
                'tgl_donasi' => Carbon::create(2026, 10, 26, 14, 0),
                'niat' => 'Penyaluran CSR pendidikan beasiswa anak yatim',
                'atas_nama' => 'CV. Samudra Jaya Mandiri',
                'status' => 'terverifikasi',
                'verified_by' => $adminId,
                'verified_at' => Carbon::create(2026, 10, 26, 14, 30),
            ],
            [
                'no_kwitansi' => 'KWT-GSC/2026/10/0013',
                'donatur_id' => $donaturMaryam->id,
                'amil_id' => $amil4->id,
                'jenis_dana_id' => $jenisInfakUmum->id,
                'program_id' => null,
                'jumlah' => 1000000,
                'metode_bayar' => 'tunai',
                'bank' => null,
                'tgl_donasi' => Carbon::create(2026, 10, 27, 10, 20),
                'niat' => 'Sedekah subuh berkah untuk umat',
                'atas_nama' => 'Hj. Maryam Sulaiman',
                'status' => 'terverifikasi',
                'verified_by' => $adminId,
                'verified_at' => Carbon::create(2026, 10, 27, 10, 40),
            ],
            [
                'no_kwitansi' => 'KWT-GSC/2026/10/0014',
                'donatur_id' => $donaturEko->id,
                'amil_id' => $amil5->id,
                'jenis_dana_id' => $jenisZakatMaal->id,
                'program_id' => null,
                'jumlah' => 4500000,
                'metode_bayar' => 'transfer',
                'bank' => 'Mandiri',
                'tgl_donasi' => Carbon::create(2026, 10, 27, 15, 10),
                'niat' => 'Zakat perniagaan dan simpanan',
                'atas_nama' => 'Ir. Eko Prasetyo & Istri',
                'status' => 'terverifikasi',
                'verified_by' => $adminId,
                'verified_at' => Carbon::create(2026, 10, 27, 15, 30),
            ],
            [
                'no_kwitansi' => 'KWT-GSC/2026/10/0015',
                'donatur_id' => $donaturTrail->id,
                'amil_id' => $amil6->id,
                'jenis_dana_id' => $jenisInfakTerikat->id,
                'program_id' => $progQuran?->id,
                'jumlah' => 3500000,
                'metode_bayar' => 'qris',
                'bank' => null,
                'tgl_donasi' => Carbon::create(2026, 10, 28, 8, 45),
                'niat' => 'Donasi pengadaan Al-Quran santri pelosok',
                'atas_nama' => 'Komunitas Motor Trail Peduli Sesama',
                'status' => 'terverifikasi',
                'verified_by' => $adminId,
                'verified_at' => Carbon::create(2026, 10, 28, 9, 15),
            ],
            [
                'no_kwitansi' => 'KWT-GSC/2026/10/0016',
                'donatur_id' => $donaturSuwandi->id,
                'amil_id' => $amil1->id,
                'jenis_dana_id' => $jenisInfakUmum->id,
                'program_id' => null,
                'jumlah' => 1500000,
                'metode_bayar' => 'tunai',
                'bank' => null,
                'tgl_donasi' => Carbon::create(2026, 10, 28, 13, 0),
                'niat' => 'Infak jariyah keluarga besar',
                'atas_nama' => 'H. Suwandi',
                'status' => 'terverifikasi',
                'verified_by' => $adminId,
                'verified_at' => Carbon::create(2026, 10, 28, 13, 20),
            ],
            [
                'no_kwitansi' => 'KWT-GSC/2026/10/0017',
                'donatur_id' => $donaturAgus->id,
                'amil_id' => $amil2->id,
                'jenis_dana_id' => $jenisZakatFitrah->id,
                'program_id' => null,
                'jumlah' => 450000,
                'metode_bayar' => 'qris',
                'bank' => null,
                'tgl_donasi' => Carbon::create(2026, 10, 28, 16, 20),
                'niat' => 'Zakat fitrah anggota keluarga (9 jiwa)',
                'atas_nama' => 'Agus Priyanto, S.E.',
                'status' => 'terverifikasi',
                'verified_by' => $adminId,
                'verified_at' => Carbon::create(2026, 10, 28, 16, 40),
            ],
            [
                'no_kwitansi' => 'KWT-GSC/2026/10/0018',
                'donatur_id' => $donaturKoperasi->id,
                'amil_id' => $amil3->id,
                'jenis_dana_id' => $jenisInfakTerikat->id,
                'program_id' => $progKatarak?->id,
                'jumlah' => 8000000,
                'metode_bayar' => 'transfer',
                'bank' => 'BRI',
                'tgl_donasi' => Carbon::create(2026, 10, 29, 9, 10),
                'niat' => 'Bantuan operasi katarak lansia pesisir',
                'atas_nama' => 'Koperasi Bahari Sejahtera',
                'status' => 'terverifikasi',
                'verified_by' => $adminId,
                'verified_at' => Carbon::create(2026, 10, 29, 9, 30),
            ],
            [
                'no_kwitansi' => 'KWT-GSC/2026/10/0019',
                'donatur_id' => $donaturAnonim->id,
                'amil_id' => $amil4->id,
                'jenis_dana_id' => $jenisInfakUmum->id,
                'program_id' => null,
                'jumlah' => 500000,
                'metode_bayar' => 'tunai',
                'bank' => null,
                'tgl_donasi' => Carbon::create(2026, 10, 29, 11, 0),
                'niat' => 'Sedekah jumat berkah ikhlas lillahi ta\'ala',
                'atas_nama' => null,
                'status' => 'terverifikasi',
                'verified_by' => $adminId,
                'verified_at' => Carbon::create(2026, 10, 29, 11, 20),
            ],
        ];

        foreach ($donasisData as $dns) {
            Donasi::firstOrCreate(['no_kwitansi' => $dns['no_kwitansi']], $dns);
        }

        // ========================================================
        // 6. DONASI BARANG (Pastikan minimal 10 donasi barang)
        // ========================================================
        $allDonasis = Donasi::all();
        $donasi10 = $allDonasis->where('no_kwitansi', 'KWT-GSC/2026/10/0010')->first() ?? $allDonasis->first();
        $donasi11 = $allDonasis->where('no_kwitansi', 'KWT-GSC/2026/10/0011')->first() ?? $allDonasis->first();
        $donasi12 = $allDonasis->where('no_kwitansi', 'KWT-GSC/2026/10/0012')->first() ?? $allDonasis->first();
        $donasi15 = $allDonasis->where('no_kwitansi', 'KWT-GSC/2026/10/0015')->first() ?? $allDonasis->first();
        $donasi18 = $allDonasis->where('no_kwitansi', 'KWT-GSC/2026/10/0018')->first() ?? $allDonasis->first();

        $barangData = [
            [
                'donasi_id' => $donasi10->id,
                'deskripsi' => 'Beras Ramos Super 5 Karung (@25 Kg)',
                'estimasi_nilai' => 1750000,
            ],
            [
                'donasi_id' => $donasi11->id,
                'deskripsi' => '2 Unit Tabung Oksigen Medis 1m3 Lengkap Regulator Ambulans',
                'estimasi_nilai' => 2200000,
            ],
            [
                'donasi_id' => $donasi12->id,
                'deskripsi' => '30 Paket Tas & Perlengkapan Belajar Siswa Yatim',
                'estimasi_nilai' => 3000000,
            ],
            [
                'donasi_id' => $donasi15->id,
                'deskripsi' => '50 Mushaf Al-Qur\'an Rasm Utsmani Standar Kemenag',
                'estimasi_nilai' => 4500000,
            ],
            [
                'donasi_id' => $donasi18->id,
                'deskripsi' => '20 Paket Sembako Ikan Kaleng & Minyak Goreng Higienis',
                'estimasi_nilai' => 2500000,
            ],
        ];

        foreach ($barangData as $bg) {
            DonasiBarang::firstOrCreate(
                [
                    'donasi_id' => $bg['donasi_id'],
                    'deskripsi' => $bg['deskripsi'],
                ],
                $bg
            );
        }

        // ========================================================
        // 7. AGENDAS (Pastikan minimal 10 agenda penugasan)
        // ========================================================
        $agendasData = [
            [
                'judul' => 'Distribusi Sembako Akbar Pesisir Cilacap',
                'deskripsi' => 'Penyaluran 100 paket bantuan sembako untuk nelayan kecil dan janda dhuafa di pesisir Teluk Penyu.',
                'lokasi' => 'Balai Nelayan Kelurahan Pandanaran, Cilacap Selatan',
                'mulai' => Carbon::create(2026, 11, 2, 8, 30),
                'selesai' => Carbon::create(2026, 11, 2, 14, 0),
                'status' => 'terjadwal',
                'created_by' => $adminId,
                'amils' => [$amil1->id, $amil3->id, $amil4->id],
            ],
            [
                'judul' => 'Rapat Koordinasi Evaluasi Fundraising Q4',
                'deskripsi' => 'Evaluasi pencapaian target penghimpunan ZISWAF bulan Oktober dan strategi jemput zakat akhir tahun.',
                'lokasi' => 'Ruang Rapat Utama Kantor Yayasan GSC',
                'mulai' => Carbon::create(2026, 11, 4, 9, 0),
                'selesai' => Carbon::create(2026, 11, 4, 11, 30),
                'status' => 'terjadwal',
                'created_by' => $adminId,
                'amils' => [$amil1->id, $amil2->id, $amil5->id],
            ],
            [
                'judul' => 'Pemeriksaan Kesehatan Lansia & Screening Katarak',
                'deskripsi' => 'Pelayanan tensi, cek gula darah, dan screening awal mata untuk lansia mustahik.',
                'lokasi' => 'Masjid Baiturrahman, Desa Kesugihan Kidul',
                'mulai' => Carbon::create(2026, 11, 7, 8, 0),
                'selesai' => Carbon::create(2026, 11, 7, 12, 0),
                'status' => 'terjadwal',
                'created_by' => $adminId,
                'amils' => [$amil2->id, $amil4->id, $amil6->id],
            ],
            [
                'judul' => 'Pelatihan Digitalisasi Zakat & CRM Donatur Amil',
                'deskripsi' => 'Workshop internal penggunaan aplikasi manajemen amil GSC dan teknik followup donatur korporat.',
                'lokasi' => 'Auditorium Diklat GSC',
                'mulai' => Carbon::create(2026, 11, 10, 13, 0),
                'selesai' => Carbon::create(2026, 11, 10, 16, 30),
                'status' => 'terjadwal',
                'created_by' => $adminId,
                'amils' => [$amil1->id, $amil2->id, $amil3->id, $amil4->id, $amil5->id, $amil6->id],
            ],
            [
                'judul' => 'Jemput Zakat Maal & Kunjungan Kemitraan Donatur',
                'deskripsi' => 'Layanan jemput donasi ke kantor donatur mitra dan silaturahmi penyerahan laporan program.',
                'lokasi' => 'Kawasan Industri Cilacap & Perkantoran Cilacap Tengah',
                'mulai' => Carbon::create(2026, 11, 12, 9, 30),
                'selesai' => Carbon::create(2026, 11, 12, 15, 0),
                'status' => 'terjadwal',
                'created_by' => $adminId,
                'amils' => [$amil1->id, $amil6->id],
            ],
        ];

        foreach ($agendasData as $ag) {
            $amilIds = $ag['amils'];
            unset($ag['amils']);

            $agendaObj = Agenda::firstOrCreate(['judul' => $ag['judul']], $ag);
            $agendaObj->amils()->syncWithoutDetaching($amilIds);
        }

        // ========================================================
        // 8. PENGUMUMAN (Pastikan minimal 10 pengumuman)
        // ========================================================
        $pengumumansData = [
            [
                'judul' => 'Pemberlakuan SOP Baru Penyerahan Kwitansi Donasi Digital GSC',
                'isi' => 'Diberitahukan kepada seluruh amil penghimpun dana zakat, infak, dan sedekah, terhitung mulai 1 November 2026 seluruh kwitansi wajib digenerate langsung dari sistem aplikasi Amil GSC dengan scan QR Code verifikasi resmi.',
                'target' => 'semua',
                'published_at' => Carbon::create(2026, 10, 25, 8, 0),
                'created_by' => $adminId,
            ],
            [
                'judul' => 'Jadwal Siaga Pelayanan Ambulans Gratis 24 Jam Bulan November',
                'isi' => 'Divisi Kesehatan telah merilis jadwal piket driver ambulans siaga darurat untuk bulan November 2026. Mohon bagi amil yang bertugas dapat selalu mengaktifkan ponsel dan stand by di posko.',
                'target' => 'semua',
                'published_at' => Carbon::create(2026, 10, 26, 9, 0),
                'created_by' => $adminId,
            ],
            [
                'judul' => 'Pembaruan Batas Waktu Pengisian Aktivitas Harian Amil',
                'isi' => 'Mengingatkan seluruh rekan-rekan staf amil lapangan dan kantor untuk melakukan pengisian laporan aktivitas harian (logbook) maksimal pukul 20.00 WIB setiap harinya guna keperluan rekap performa bulanan.',
                'target' => 'semua',
                'published_at' => Carbon::create(2026, 10, 27, 11, 0),
                'created_by' => $adminId,
            ],
            [
                'judul' => 'Apresiasi Pencapaian Target Donasi Program Beasiswa Yatim Q3',
                'isi' => 'Alhamdulillah, berkat kerja keras tim fundraising dan kemurahan hati para donatur, target penghimpunan program Beasiswa Generasi Cerdas Mandiri telah terlampaui 110%. Jazakumullahu khairan katsiran.',
                'target' => 'semua',
                'published_at' => Carbon::create(2026, 10, 28, 14, 0),
                'created_by' => $adminId,
            ],
            [
                'judul' => 'Himbauan Kesiapsiagaan Cuaca Ekstrem dan Banjir Musim Penghujan',
                'isi' => 'Menyikapi peringatan BMKG Cilacap terkait potensi cuaca ekstrem di pesisir selatan, divisi kebencanaan dan seluruh amil diminta mengecek kesiapan logistik darurat dan perahu karet operasional.',
                'target' => 'semua',
                'published_at' => Carbon::create(2026, 10, 29, 8, 30),
                'created_by' => $adminId,
            ],
        ];

        foreach ($pengumumansData as $pg) {
            Pengumuman::firstOrCreate(['judul' => $pg['judul']], $pg);
        }

        // ========================================================
        // 9. AKTIVITAS HARIAN (Pastikan minimal 10 aktivitas)
        // ========================================================
        $aktivitasData = [
            [
                'amil_id' => $amil1->id,
                'tanggal' => Carbon::create(2026, 10, 25),
                'judul' => 'Kunjungan dan Edukasi Zakat Maal Perusahaan Mitra',
                'deskripsi' => 'Melakukan presentasi sosialisasi perhitungan nisab zakat maal dan program kemitraan CSR di CV. Samudra Jaya Mandiri.',
                'lokasi' => 'Kawasan Industri Cilacap',
                'status' => 'disetujui',
            ],
            [
                'amil_id' => $amil2->id,
                'tanggal' => Carbon::create(2026, 10, 26),
                'judul' => 'Survei Faktual Kelayakan Mustahik Asnaf Fakir',
                'deskripsi' => 'Melakukan home visit dan verifikasi kondisi rumah serta kelayakan ekonomi Mbah Waryono di Desa Maos Kidul.',
                'lokasi' => 'Kec. Maos, Kab. Cilacap',
                'status' => 'disetujui',
            ],
            [
                'amil_id' => $amil3->id,
                'tanggal' => Carbon::create(2026, 10, 27),
                'judul' => 'Layanan Pengantaran Pasien Dhuafa Ambulans Siaga GSC',
                'deskripsi' => 'Mendampingi dan mengantar pasien rujukan darurat dari Puskesmas Kesugihan menuju RSUD Cilacap.',
                'lokasi' => 'RSUD Cilacap',
                'status' => 'disetujui',
            ],
            [
                'amil_id' => $amil4->id,
                'tanggal' => Carbon::create(2026, 10, 28),
                'judul' => 'Penyaluran Paket Nutrisi Balita Stunting',
                'deskripsi' => 'Menyerahkan paket susu, telur, dan madu kepada 15 keluarga balita stunting di posyandu binaan.',
                'lokasi' => 'Posyandu Melati Gumilir',
                'status' => 'disetujui',
            ],
            [
                'amil_id' => $amil5->id,
                'tanggal' => Carbon::create(2026, 10, 29),
                'judul' => 'Pencatatan & Rekonsiliasi Donasi Kotak Infak Toko Mitra',
                'deskripsi' => 'Melakukan pembukaan dan penghitungan hasil kotak infak di 8 titik mini market mitra wilayah Cilacap Tengah.',
                'lokasi' => 'Cilacap Tengah',
                'status' => 'disetujui',
            ],
        ];

        foreach ($aktivitasData as $akt) {
            AktivitasHarian::firstOrCreate(
                [
                    'amil_id' => $akt['amil_id'],
                    'tanggal' => $akt['tanggal'],
                    'judul' => $akt['judul'],
                ],
                $akt
            );
        }

        // ========================================================
        // 10. IZIN CUTI (Pastikan minimal 10 data izin & cuti)
        // ========================================================
        $izinData = [
            [
                'amil_id' => $amil1->id,
                'jenis' => 'cuti',
                'tgl_mulai' => Carbon::create(2026, 11, 16),
                'tgl_selesai' => Carbon::create(2026, 11, 18),
                'alasan' => 'Cuti tahunan untuk keperluan mendampingi acara wisuda adik kandung di Yogyakarta.',
                'status' => 'disetujui',
                'approved_by' => $adminId,
                'catatan_admin' => 'Disetujui. Pastikan tugas lapangan didelegasikan ke rekan tim selama cuti.',
            ],
            [
                'amil_id' => $amil2->id,
                'jenis' => 'izin',
                'tgl_mulai' => Carbon::create(2026, 11, 5),
                'tgl_selesai' => Carbon::create(2026, 11, 5),
                'alasan' => 'Izin setengah hari keperluan perpanjangan SIM dan administrasi kependudukan di Samsat.',
                'status' => 'disetujui',
                'approved_by' => $adminId,
                'catatan_admin' => 'Disetujui.',
            ],
            [
                'amil_id' => $amil3->id,
                'jenis' => 'sakit',
                'tgl_mulai' => Carbon::create(2026, 10, 19),
                'tgl_selesai' => Carbon::create(2026, 10, 20),
                'alasan' => 'Sakit demam dan radang tenggorokan dengan anjuran istirahat dokter.',
                'status' => 'disetujui',
                'approved_by' => $adminId,
                'catatan_admin' => 'Semoga lekas sembuh dan pulih kembali.',
            ],
            [
                'amil_id' => $amil4->id,
                'jenis' => 'cuti',
                'tgl_mulai' => Carbon::create(2026, 12, 21),
                'tgl_selesai' => Carbon::create(2026, 12, 23),
                'alasan' => 'Cuti tahunan keperluan keluarga akhir tahun.',
                'status' => 'diajukan',
                'approved_by' => null,
                'catatan_admin' => null,
            ],
        ];

        foreach ($izinData as $iz) {
            IzinCuti::firstOrCreate(
                [
                    'amil_id' => $iz['amil_id'],
                    'tgl_mulai' => $iz['tgl_mulai'],
                    'jenis' => $iz['jenis'],
                ],
                $iz
            );
        }

        // ========================================================
        // 11. NOTIFIKASI (Pastikan minimal 10 notifikasi)
        // ========================================================
        $notifikasiData = [
            [
                'user_id' => $adminId,
                'judul' => 'Donasi Baru Terverifikasi: Rp 15.000.000',
                'pesan' => 'CV. Samudra Jaya Mandiri telah menyalurkan donasi program beasiswa sebesar Rp 15.000.000.',
                'tipe' => 'donasi',
                'url' => '/admin/donasis',
                'is_read' => true,
                'read_at' => Carbon::create(2026, 10, 26, 15, 0),
            ],
            [
                'user_id' => $adminId,
                'judul' => 'Penyaluran Sembako Terjadwal Berhasil Dilaksanakan',
                'pesan' => 'Penyaluran bantuan sembako dan modal usaha di Kecamatan Kesugihan telah selesai diinput oleh amil.',
                'tipe' => 'penyaluran',
                'url' => '/admin/penyalurans',
                'is_read' => true,
                'read_at' => Carbon::create(2026, 10, 27, 9, 30),
            ],
            [
                'user_id' => $adminId,
                'judul' => 'Pengajuan Cuti Tahunan Baru',
                'pesan' => 'Ahmad Fauzi mengajukan cuti tahunan tanggal 16-18 November 2026 menunggu persetujuan.',
                'tipe' => 'izin_cuti',
                'url' => '/admin/izin-cutis',
                'is_read' => false,
                'read_at' => null,
            ],
            [
                'user_id' => $adminId,
                'judul' => 'Target Donasi Beasiswa Tercapai 100%',
                'pesan' => 'Selamat! Program Beasiswa Generasi Cerdas Mandiri telah memenuhi 100% target dana tahun 2026.',
                'tipe' => 'program',
                'url' => '/admin/programs',
                'is_read' => false,
                'read_at' => null,
            ],
            [
                'user_id' => $adminId,
                'judul' => 'Laporan Aktivitas Harian Masuk',
                'pesan' => 'Muhammad Ridwan telah menginput laporan aktivitas survei lapangan mustahik hari ini.',
                'tipe' => 'aktivitas',
                'url' => '/admin/aktivitas-harians',
                'is_read' => false,
                'read_at' => null,
            ],
        ];

        foreach ($notifikasiData as $notif) {
            Notifikasi::firstOrCreate(
                [
                    'user_id' => $notif['user_id'],
                    'judul' => $notif['judul'],
                ],
                $notif
            );
        }
    }
}
