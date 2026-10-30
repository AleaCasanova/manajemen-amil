<?php

namespace Database\Seeders;

use App\Models\Absensi;
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
     * Jalankan penambahan data operasional minimal 10 baris per tabel.
     * Mengaitkan seluruh data ke 24 Pegawai Asli GSC dan Super Admin tanpa merusak akun mereka.
     */
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@gmail.com')->first() ?? User::first();
        $adminId = $adminUser?->id ?? 1;

        $amils = Amil::all();
        if ($amils->isEmpty()) {
            return;
        }

        $amil1 = $amils->get(0) ?? $amils->first(); // Kholifatun Mualfiyah (Keuangan)
        $amil2 = $amils->get(1) ?? $amils->first(); // Wiji Susanti (Fundraising)
        $amil3 = $amils->get(2) ?? $amils->first(); // Ujang (Driver / Kesehatan)
        $amil4 = $amils->get(3) ?? $amils->first(); // Fahri Azzuhair (Fundraising)
        $amil5 = $amils->get(4) ?? $amils->first(); // Ahmad Robbani Izzal (Kesehatan)
        $amil6 = $amils->get(5) ?? $amils->first(); // Naffisa Putri Azzahra (Fundraising)
        $amil7 = $amils->get(6) ?? $amils->first(); // Rafi Ramadhan (Driver)
        $amil8 = $amils->get(7) ?? $amils->first(); // Rista Amalia Salsabila (Program)
        $amil9 = $amils->get(8) ?? $amils->first(); // Endang Kuswanto (Ketua)
        $amil10 = $amils->get(9) ?? $amils->first(); // Faishal Hanif (Program)
        $amilAlea = $amils->firstWhere('nama', 'Alea Casanova') ?? ($amils->get(19) ?? $amils->first());

        $kategoriRetail = DonaturKategori::firstOrCreate(['nama' => 'Perorangan / Retail']);
        $kategoriKorporat = DonaturKategori::firstOrCreate(['nama' => 'Korporat / Lembaga / Perusahaan']);
        $kategoriKomunitas = DonaturKategori::firstOrCreate(['nama' => 'Komunitas / Komunitas Mitra']);

        $jenisZakatMaal = JenisDana::where('nama', 'LIKE', '%Zakat Maal%')->first() ?? JenisDana::firstOrCreate(['nama' => 'Zakat Maal', 'kode' => 'ZM', 'tipe' => 'zakat']);
        $jenisZakatFitrah = JenisDana::where('nama', 'LIKE', '%Zakat Fitrah%')->first() ?? JenisDana::firstOrCreate(['nama' => 'Zakat Fitrah', 'kode' => 'ZF', 'tipe' => 'zakat']);
        $jenisInfakTerikat = JenisDana::where('nama', 'LIKE', '%Terikat%')->first() ?? JenisDana::firstOrCreate(['nama' => 'Infak Terikat', 'kode' => 'IT', 'tipe' => 'infak']);
        $jenisInfakUmum = JenisDana::where('nama', 'LIKE', '%Umum%')->first() ?? JenisDana::firstOrCreate(['nama' => 'Infak Umum', 'kode' => 'IU', 'tipe' => 'infak']);
        $jenisDskl = JenisDana::where('nama', 'LIKE', '%DSKL%')->first() ?? JenisDana::firstOrCreate(['nama' => 'DSKL', 'kode' => 'DSKL', 'tipe' => 'dskl']);

        // ========================================================
        // 1. PROGRAMS (Minimal 10 Data Program)
        // ========================================================
        $programsData = [
            [
                'nama' => 'Pangan Berkah Lansia Dhuafa',
                'deskripsi' => 'Program pembagian sembako bulanan untuk lansia dhuafa dan keluarga prasejahtera di Cilacap.',
                'target_dana' => 75000000,
                'tgl_mulai' => Carbon::create(2026, 1, 1),
                'tgl_selesai' => Carbon::create(2026, 12, 31),
                'status' => 'aktif',
            ],
            [
                'nama' => 'Beasiswa Generasi Cerdas Mandiri',
                'deskripsi' => 'Bantuan biaya SPP, perlengkapan sekolah, dan pembinaan karakter anak yatim berprestasi.',
                'target_dana' => 100000000,
                'tgl_mulai' => Carbon::create(2026, 1, 1),
                'tgl_selesai' => Carbon::create(2026, 12, 31),
                'status' => 'aktif',
            ],
            [
                'nama' => 'Layanan Ambulans Siaga Gratis',
                'deskripsi' => 'Operasional armada ambulans 24 jam gratis untuk pasien dhuafa dan korban kecelakaan.',
                'target_dana' => 60000000,
                'tgl_mulai' => Carbon::create(2026, 1, 1),
                'tgl_selesai' => Carbon::create(2026, 12, 31),
                'status' => 'aktif',
            ],
            [
                'nama' => 'Pemberdayaan UMKM Barokah',
                'deskripsi' => 'Bantuan permodalan usaha mikro tanpa bunga dan pendampingan manajemen usaha mustahik.',
                'target_dana' => 85000000,
                'tgl_mulai' => Carbon::create(2026, 1, 1),
                'tgl_selesai' => Carbon::create(2026, 12, 31),
                'status' => 'aktif',
            ],
            [
                'nama' => 'Renovasi Rumah Tidak Layak Huni (RTLH)',
                'deskripsi' => 'Bedah rumah roboh, perbaikan atap bocor, dan pembuatan sanitasi layak bagi warga miskin.',
                'target_dana' => 150000000,
                'tgl_mulai' => Carbon::create(2026, 1, 1),
                'tgl_selesai' => Carbon::create(2026, 12, 31),
                'status' => 'aktif',
            ],
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
        // 2. MUSTAHIK (10 Data Mustahik)
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
            [
                'nama' => 'Pak Slamet Riyadi',
                'nik' => '3301072006760002',
                'alamat' => 'Desa Karangmangu RT 02/RW 03, Kec. Kroya, Kab. Cilacap',
                'asnaf' => 'gharim',
                'kondisi' => 'Terlilit utang pengobatan cuci darah istri yang mendesak dan telah menunggak.',
                'status_verifikasi' => 'terverifikasi',
            ],
            [
                'nama' => 'Ahmad Muzaki (Ibnu Sabil)',
                'nik' => '3301081109010007',
                'alamat' => 'Singgah di Masjid Agung Darussalam Cilacap asal Jawa Timur',
                'asnaf' => 'ibnu_sabil',
                'kondisi' => 'Musafir kehabisan ongkos perjalanan saat perjalanan pulang pasca menyelesaikan studi tahfidz.',
                'status_verifikasi' => 'terverifikasi',
            ],
            [
                'nama' => 'Ibu Tarwiyah',
                'nik' => '3301095507680003',
                'alamat' => 'Kelurahan Tambakreja RT 03/RW 06, Kec. Cilacap Selatan, Kab. Cilacap',
                'asnaf' => 'miskin',
                'kondisi' => 'Buruh cuci pakaian dengan rumah terancam abrasi pasang laut, menanggung 1 cucu yatim.',
                'status_verifikasi' => 'terverifikasi',
            ],
            [
                'nama' => 'Mbah Sutarmi',
                'nik' => '3301104803420005',
                'alamat' => 'Desa Kesugihan Kidul RT 04/RW 01, Kec. Kesugihan, Kab. Cilacap',
                'asnaf' => 'fakir',
                'kondisi' => 'Lansia renta usia 84 tahun hidup sendiri tanpa keluarga, bergantung pada bantuan tetangga.',
                'status_verifikasi' => 'terverifikasi',
            ],
        ];

        foreach ($mustahikData as $m) {
            Mustahik::firstOrCreate(['nik' => $m['nik']], $m);
        }

        $allMustahiks = Mustahik::all();

        // ========================================================
        // 3. PENYALURAN (10 Data Penyaluran)
        // ========================================================
        $penyaluranData = [
            [
                'mustahik_id' => $allMustahiks->get(0)?->id ?? 1,
                'program_id' => $allPrograms->where('nama', 'Pangan Berkah Lansia Dhuafa')->first()?->id ?? 1,
                'jenis_dana_id' => $jenisInfakUmum->id,
                'jumlah' => 750000,
                'tgl_salur' => Carbon::create(2026, 10, 15),
                'amil_id' => $amil1->id,
                'catatan' => 'Penyaluran paket sembako lengkap dan santunan tunai kebutuhan harian lansia dhuafa.',
            ],
            [
                'mustahik_id' => $allMustahiks->get(1)?->id ?? 1,
                'program_id' => $allPrograms->where('nama', 'Pemberdayaan UMKM Barokah')->first()?->id ?? 1,
                'jenis_dana_id' => $jenisInfakTerikat->id,
                'jumlah' => 2000000,
                'tgl_salur' => Carbon::create(2026, 10, 18),
                'amil_id' => $amil2->id,
                'catatan' => 'Bantuan modal usaha dagang sayur keliling dan timbangan digital.',
            ],
            [
                'mustahik_id' => $allMustahiks->get(2)?->id ?? 1,
                'program_id' => $allPrograms->where('nama', 'Sedekah Al-Qur\'an & Santri Penghafal Kitab')->first()?->id ?? 1,
                'jenis_dana_id' => $jenisZakatMaal->id,
                'jumlah' => 1500000,
                'tgl_salur' => Carbon::create(2026, 10, 20),
                'amil_id' => $amil4->id,
                'catatan' => 'Santunan pembinaan asnaf mualaf dan paket perlengkapan ibadah sholat.',
            ],
            [
                'mustahik_id' => $allMustahiks->get(3)?->id ?? 1,
                'program_id' => $allPrograms->where('nama', 'Sedekah Al-Qur\'an & Santri Penghafal Kitab')->first()?->id ?? 1,
                'jenis_dana_id' => $jenisZakatMaal->id,
                'jumlah' => 1250000,
                'tgl_salur' => Carbon::create(2026, 10, 22),
                'amil_id' => $amil8->id,
                'catatan' => 'Kafalah guru ngaji pelosok TPQ dan bantuan 20 mushaf Al-Qur\'an santri.',
            ],
            [
                'mustahik_id' => $allMustahiks->get(4)?->id ?? 1,
                'program_id' => $allPrograms->where('nama', 'Layanan Ambulans Siaga Gratis')->first()?->id ?? 1,
                'jenis_dana_id' => $jenisInfakTerikat->id,
                'jumlah' => 1000000,
                'tgl_salur' => Carbon::create(2026, 10, 24),
                'amil_id' => $amil3->id,
                'catatan' => 'Bantuan operasional pengantaran kontrol rutin pasien stroke ke RSUD Cilacap.',
            ],
            [
                'mustahik_id' => $allMustahiks->get(5)?->id ?? 1,
                'program_id' => $allPrograms->where('nama', 'Pangan Berkah Lansia Dhuafa')->first()?->id ?? 1,
                'jenis_dana_id' => $jenisInfakUmum->id,
                'jumlah' => 800000,
                'tgl_salur' => Carbon::create(2026, 10, 26),
                'amil_id' => $amil6->id,
                'catatan' => 'Santunan tunai bulanan dan popok dewasa medis lansia dhuafa.',
            ],
            [
                'mustahik_id' => $allMustahiks->get(6)?->id ?? 1,
                'program_id' => $allPrograms->where('nama', 'Santunan Yatim & Piatu Bahagia')->first()?->id ?? 1,
                'jenis_dana_id' => $jenisZakatMaal->id,
                'jumlah' => 2500000,
                'tgl_salur' => Carbon::create(2026, 10, 27),
                'amil_id' => $amil10->id,
                'catatan' => 'Bantuan pelunasan utang biaya tindakan medis darurat di rumah sakit.',
            ],
            [
                'mustahik_id' => $allMustahiks->get(7)?->id ?? 1,
                'program_id' => $allPrograms->where('nama', 'Pangan Berkah Lansia Dhuafa')->first()?->id ?? 1,
                'jenis_dana_id' => $jenisInfakUmum->id,
                'jumlah' => 600000,
                'tgl_salur' => Carbon::create(2026, 10, 28),
                'amil_id' => $amil5->id,
                'catatan' => 'Bantuan bekal perjalanan tiket bus antar provinsi dan uang saku darurat.',
            ],
            [
                'mustahik_id' => $allMustahiks->get(8)?->id ?? 1,
                'program_id' => $allPrograms->where('nama', 'Renovasi Rumah Tidak Layak Huni (RTLH)')->first()?->id ?? 1,
                'jenis_dana_id' => $jenisInfakTerikat->id,
                'jumlah' => 1750000,
                'tgl_salur' => Carbon::create(2026, 10, 28),
                'amil_id' => $amil8->id,
                'catatan' => 'Pengadaan material seng asbes dan semen untuk perbaikan dinding rumah abrasi.',
            ],
            [
                'mustahik_id' => $allMustahiks->get(9)?->id ?? 1,
                'program_id' => $allPrograms->where('nama', 'Pangan Berkah Lansia Dhuafa')->first()?->id ?? 1,
                'jenis_dana_id' => $jenisInfakUmum->id,
                'jumlah' => 700000,
                'tgl_salur' => Carbon::create(2026, 10, 29),
                'amil_id' => $amilAlea->id,
                'catatan' => 'Paket sembako dan santunan lansia sebatang kara Kesugihan Kidul.',
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
        // 4. DONATUR (12 Data Donatur Baru)
        // ========================================================
        $donatursData = [
            [
                'kode_donatur' => 'DNT-202610-001',
                'nama' => 'H. Bambang Soeprapto',
                'jenis' => 'individu',
                'kategori_id' => $kategoriRetail->id,
                'no_hp' => '081227189001',
                'email' => 'bambang.soeprapto@gmail.com',
                'alamat' => 'Jl. Gatot Subroto No. 45, Cilacap Tengah',
                'kota' => 'Cilacap',
                'amil_pic_id' => $amil2->id,
                'created_by' => $adminId,
            ],
            [
                'kode_donatur' => 'DNT-202610-002',
                'nama' => 'Hj. Ratna Sulistiyowati',
                'jenis' => 'individu',
                'kategori_id' => $kategoriRetail->id,
                'no_hp' => '081328901234',
                'email' => 'ratna.sulistiyo@gmail.com',
                'alamat' => 'Jl. Rinjani No. 12, Kel. Sidanegara, Cilacap Tengah',
                'kota' => 'Cilacap',
                'amil_pic_id' => $amil4->id,
                'created_by' => $adminId,
            ],
            [
                'kode_donatur' => 'DNT-202610-003',
                'nama' => 'CV. Samudra Jaya Mandiri',
                'jenis' => 'lembaga',
                'kategori_id' => $kategoriKorporat->id,
                'no_hp' => '0282534890',
                'email' => 'csr@samudrajaya.co.id',
                'alamat' => 'Kawasan Industri Pelabuhan Tanjung Intan Cilacap',
                'kota' => 'Cilacap',
                'amil_pic_id' => $amil2->id,
                'created_by' => $adminId,
            ],
            [
                'kode_donatur' => 'DNT-202610-004',
                'nama' => 'Komunitas Gowes Sehat Barokah',
                'jenis' => 'lembaga',
                'kategori_id' => $kategoriKomunitas->id,
                'no_hp' => '085876543210',
                'email' => 'gowessehat.clp@gmail.com',
                'alamat' => 'Sekretariat GOR Wijayakusuma Cilacap',
                'kota' => 'Cilacap',
                'amil_pic_id' => $amil6->id,
                'created_by' => $adminId,
            ],
            [
                'kode_donatur' => 'DNT-202610-005',
                'nama' => 'dr. Hendro Wicaksono, Sp.B',
                'jenis' => 'individu',
                'kategori_id' => $kategoriRetail->id,
                'no_hp' => '081123456789',
                'email' => 'hendro.wicaksono@yahoo.com',
                'alamat' => 'Jl. Dr. Soetomo No. 88, Sidakaya, Cilacap Selatan',
                'kota' => 'Cilacap',
                'amil_pic_id' => $amil4->id,
                'created_by' => $adminId,
            ],
            [
                'kode_donatur' => 'DNT-202610-006',
                'nama' => 'PT. Cilacap Samudra Perkasa',
                'jenis' => 'lembaga',
                'kategori_id' => $kategoriKorporat->id,
                'no_hp' => '0282521199',
                'email' => 'finance@cilacapperksa.com',
                'alamat' => 'Jl. MT. Haryono Kav. 5, Cilacap Tengah',
                'kota' => 'Cilacap',
                'amil_pic_id' => $amil2->id,
                'created_by' => $adminId,
            ],
            [
                'kode_donatur' => 'DNT-202610-007',
                'nama' => 'Ibu Sri Wahyuningsih',
                'jenis' => 'individu',
                'kategori_id' => $kategoriRetail->id,
                'no_hp' => '087834567812',
                'email' => 'sri.wahyuni67@gmail.com',
                'alamat' => 'Jl. Juanda No. 102, Cilacap Utara',
                'kota' => 'Cilacap',
                'amil_pic_id' => $amil6->id,
                'created_by' => $adminId,
            ],
            [
                'kode_donatur' => 'DNT-202610-008',
                'nama' => 'Keluarga Besar Alm. H. Sudirman',
                'jenis' => 'individu',
                'kategori_id' => $kategoriRetail->id,
                'no_hp' => '082134567890',
                'email' => 'sudirman.fam@gmail.com',
                'alamat' => 'Jl. Ahmad Yani No. 33, Cilacap Selatan',
                'kota' => 'Cilacap',
                'amil_pic_id' => $amil9->id,
                'created_by' => $adminId,
            ],
            [
                'kode_donatur' => 'DNT-202610-009',
                'nama' => 'Toko Bangunan Berkah Logam',
                'jenis' => 'lembaga',
                'kategori_id' => $kategoriKorporat->id,
                'no_hp' => '081391234567',
                'email' => 'berkahlogam.clp@gmail.com',
                'alamat' => 'Jl. DI. Panjaitan No. 15, Cilacap',
                'kota' => 'Cilacap',
                'amil_pic_id' => $amil4->id,
                'created_by' => $adminId,
            ],
            [
                'kode_donatur' => 'DNT-202610-010',
                'nama' => 'Bapak Agus Setiawan',
                'jenis' => 'individu',
                'kategori_id' => $kategoriRetail->id,
                'no_hp' => '085747123987',
                'email' => 'agus.setiawan.eng@gmail.com',
                'alamat' => 'Perum Pertamina Gunung Simping Blok B No. 9',
                'kota' => 'Cilacap',
                'amil_pic_id' => $amil2->id,
                'created_by' => $adminId,
            ],
            [
                'kode_donatur' => 'DNT-202610-011',
                'nama' => 'Ibu Nurul Hidayati',
                'jenis' => 'individu',
                'kategori_id' => $kategoriRetail->id,
                'no_hp' => '081234112233',
                'email' => 'nurul.hidayati@gmail.com',
                'alamat' => 'Jl. Kendeng No. 17, Kesugihan, Cilacap',
                'kota' => 'Cilacap',
                'amil_pic_id' => $amil6->id,
                'created_by' => $adminId,
            ],
            [
                'kode_donatur' => 'DNT-202610-012',
                'nama' => 'Apotek Sehat Barokah',
                'jenis' => 'lembaga',
                'kategori_id' => $kategoriKorporat->id,
                'no_hp' => '0282531980',
                'email' => 'sehatbarokah.apt@gmail.com',
                'alamat' => 'Jl. Gatot Subroto No. 89, Cilacap',
                'kota' => 'Cilacap',
                'amil_pic_id' => $amil5->id,
                'created_by' => $adminId,
            ],
        ];

        foreach ($donatursData as $d) {
            Donatur::firstOrCreate(['kode_donatur' => $d['kode_donatur']], $d);
        }

        $allDonaturs = Donatur::all();

        // ========================================================
        // 5. DONASI (12 Data Donasi Transaksi Uang)
        // ========================================================
        $donasiData = [
            [
                'no_kwitansi' => 'KWT-202602-001',
                'donatur_id' => $allDonaturs->get(0)?->id ?? 1,
                'jenis_dana_id' => $jenisZakatMaal->id,
                'program_id' => $allPrograms->where('nama', 'Pemberdayaan UMKM Barokah')->first()?->id ?? 1,
                'jumlah' => 12500000,
                'tgl_donasi' => Carbon::create(2026, 2, 10, 10, 30),
                'metode_bayar' => 'transfer',
                'bank' => 'BSI',
                'status' => 'terverifikasi',
                'amil_id' => $amil2->id,
                'catatan' => 'Tunaikan Zakat Maal perdagangan perikanan kuartal awal 2026.',
                'verified_at' => Carbon::create(2026, 2, 10, 11, 0),
                'verified_by' => $adminId,
            ],
            [
                'no_kwitansi' => 'KWT-202603-002',
                'donatur_id' => $allDonaturs->get(1)?->id ?? 1,
                'jenis_dana_id' => $jenisInfakTerikat->id,
                'program_id' => $allPrograms->where('nama', 'Beasiswa Generasi Cerdas Mandiri')->first()?->id ?? 1,
                'jumlah' => 3000000,
                'tgl_donasi' => Carbon::create(2026, 3, 15, 14, 0),
                'metode_bayar' => 'qris',
                'bank' => null,
                'status' => 'terverifikasi',
                'amil_id' => $amil4->id,
                'catatan' => 'Infak beasiswa pendidikan 3 santri yatim binaan.',
                'verified_at' => Carbon::create(2026, 3, 15, 14, 15),
                'verified_by' => $adminId,
            ],
            [
                'no_kwitansi' => 'KWT-202604-003',
                'donatur_id' => $allDonaturs->get(2)?->id ?? 1,
                'jenis_dana_id' => $jenisZakatMaal->id,
                'program_id' => $allPrograms->where('nama', 'Beasiswa Generasi Cerdas Mandiri')->first()?->id ?? 1,
                'jumlah' => 15000000,
                'tgl_donasi' => Carbon::create(2026, 4, 20, 9, 15),
                'metode_bayar' => 'transfer',
                'bank' => 'Mandiri',
                'status' => 'terverifikasi',
                'amil_id' => $amil2->id,
                'catatan' => 'CSR zakal perusahaan untuk program pendidikan cerdas mandiri.',
                'verified_at' => Carbon::create(2026, 4, 20, 10, 0),
                'verified_by' => $adminId,
            ],
            [
                'no_kwitansi' => 'KWT-202605-004',
                'donatur_id' => $allDonaturs->get(3)?->id ?? 1,
                'jenis_dana_id' => $jenisInfakUmum->id,
                'program_id' => $allPrograms->where('nama', 'Layanan Ambulans Siaga Gratis')->first()?->id ?? 1,
                'jumlah' => 2500000,
                'tgl_donasi' => Carbon::create(2026, 5, 25, 16, 45),
                'metode_bayar' => 'tunai',
                'bank' => null,
                'status' => 'terverifikasi',
                'amil_id' => $amil6->id,
                'catatan' => 'Donasi patungan komunitas gowes untuk operasional BBM ambulans siaga.',
                'verified_at' => Carbon::create(2026, 5, 25, 17, 0),
                'verified_by' => $adminId,
            ],
            [
                'no_kwitansi' => 'KWT-202606-005',
                'donatur_id' => $allDonaturs->get(4)?->id ?? 1,
                'jenis_dana_id' => $jenisInfakTerikat->id,
                'program_id' => $allPrograms->where('nama', 'Operasi Katarak & Pengobatan Lansia Gratis')->first()?->id ?? 1,
                'jumlah' => 5000000,
                'tgl_donasi' => Carbon::create(2026, 6, 12, 11, 20),
                'metode_bayar' => 'transfer',
                'bank' => 'BCA',
                'status' => 'terverifikasi',
                'amil_id' => $amil4->id,
                'catatan' => 'Bantuan pengobatan katarak lansia mustahik tidak mampu.',
                'verified_at' => Carbon::create(2026, 6, 12, 11, 45),
                'verified_by' => $adminId,
            ],
            [
                'no_kwitansi' => 'KWT-202607-006',
                'donatur_id' => $allDonaturs->get(5)?->id ?? 1,
                'jenis_dana_id' => $jenisZakatMaal->id,
                'program_id' => $allPrograms->where('nama', 'Renovasi Rumah Tidak Layak Huni (RTLH)')->first()?->id ?? 1,
                'jumlah' => 20000000,
                'tgl_donasi' => Carbon::create(2026, 7, 18, 13, 0),
                'metode_bayar' => 'transfer',
                'bank' => 'BSI',
                'status' => 'terverifikasi',
                'amil_id' => $amil2->id,
                'catatan' => 'Program kemitraan bedah rumah dhuafa pesisir Cilacap.',
                'verified_at' => Carbon::create(2026, 7, 18, 14, 0),
                'verified_by' => $adminId,
            ],
            [
                'no_kwitansi' => 'KWT-202608-007',
                'donatur_id' => $allDonaturs->get(6)?->id ?? 1,
                'jenis_dana_id' => $jenisInfakUmum->id,
                'program_id' => $allPrograms->where('nama', 'Pangan Berkah Lansia Dhuafa')->first()?->id ?? 1,
                'jumlah' => 1000000,
                'tgl_donasi' => Carbon::create(2026, 8, 22, 10, 0),
                'metode_bayar' => 'qris',
                'bank' => null,
                'status' => 'terverifikasi',
                'amil_id' => $amil6->id,
                'catatan' => 'Infak sedekah jumat berkah untuk paket sembako lansia.',
                'verified_at' => Carbon::create(2026, 8, 22, 10, 15),
                'verified_by' => $adminId,
            ],
            [
                'no_kwitansi' => 'KWT-202609-008',
                'donatur_id' => $allDonaturs->get(7)?->id ?? 1,
                'jenis_dana_id' => $jenisDskl->id,
                'program_id' => $allPrograms->where('nama', 'Sedekah Al-Qur\'an & Santri Penghafal Kitab')->first()?->id ?? 1,
                'jumlah' => 7500000,
                'tgl_donasi' => Carbon::create(2026, 9, 5, 15, 30),
                'metode_bayar' => 'transfer',
                'bank' => 'Bank Jateng',
                'status' => 'terverifikasi',
                'amil_id' => $amil9->id,
                'catatan' => 'Wakaf Al-Qur\'an dan mushaf santri pelosok Cilacap.',
                'verified_at' => Carbon::create(2026, 9, 5, 16, 0),
                'verified_by' => $adminId,
            ],
            [
                'no_kwitansi' => 'KWT-202610-009',
                'donatur_id' => $allDonaturs->get(8)?->id ?? 1,
                'jenis_dana_id' => $jenisInfakTerikat->id,
                'program_id' => $allPrograms->where('nama', 'Santunan Yatim & Piatu Bahagia')->first()?->id ?? 1,
                'jumlah' => 4500000,
                'tgl_donasi' => Carbon::create(2026, 10, 1, 9, 0),
                'metode_bayar' => 'transfer',
                'bank' => 'BRI',
                'status' => 'terverifikasi',
                'amil_id' => $amil4->id,
                'catatan' => 'Sedekah rutin bulanan santunan yatim piatu dhuafa.',
                'verified_at' => Carbon::create(2026, 10, 1, 9, 30),
                'verified_by' => $adminId,
            ],
            [
                'no_kwitansi' => 'KWT-202610-010',
                'donatur_id' => $allDonaturs->get(9)?->id ?? 1,
                'jenis_dana_id' => $jenisInfakUmum->id,
                'program_id' => $allPrograms->where('nama', 'Tanggap Bencana & Dapur Umum Darurat')->first()?->id ?? 1,
                'jumlah' => 2000000,
                'tgl_donasi' => Carbon::create(2026, 10, 14, 14, 10),
                'metode_bayar' => 'tunai',
                'bank' => null,
                'status' => 'terverifikasi',
                'amil_id' => $amil2->id,
                'catatan' => 'Donasi kesiapsiagaan bencana hidrometeorologi musim hujan.',
                'verified_at' => Carbon::create(2026, 10, 14, 14, 30),
                'verified_by' => $adminId,
            ],
            [
                'no_kwitansi' => 'KWT-202610-011',
                'donatur_id' => $allDonaturs->get(10)?->id ?? 1,
                'jenis_dana_id' => $jenisZakatMaal->id,
                'program_id' => $allPrograms->where('nama', 'Beasiswa Generasi Cerdas Mandiri')->first()?->id ?? 1,
                'jumlah' => 3500000,
                'tgl_donasi' => Carbon::create(2026, 10, 20, 11, 0),
                'metode_bayar' => 'transfer',
                'bank' => 'BSI',
                'status' => 'terverifikasi',
                'amil_id' => $amil6->id,
                'catatan' => 'Zakat profesi dosen UNU untuk beasiswa mahasiswa dhuafa.',
                'verified_at' => Carbon::create(2026, 10, 20, 11, 20),
                'verified_by' => $adminId,
            ],
            [
                'no_kwitansi' => 'KWT-202610-012',
                'donatur_id' => $allDonaturs->get(11)?->id ?? 1,
                'jenis_dana_id' => $jenisInfakTerikat->id,
                'program_id' => $allPrograms->where('nama', 'Layanan Ambulans Siaga Gratis')->first()?->id ?? 1,
                'jumlah' => 1800000,
                'tgl_donasi' => Carbon::create(2026, 10, 25, 16, 0),
                'metode_bayar' => 'tunai',
                'bank' => null,
                'status' => 'terverifikasi',
                'amil_id' => $amil5->id,
                'catatan' => 'Donasi operasional P3K armada ambulans.',
                'verified_at' => Carbon::create(2026, 10, 25, 16, 30),
                'verified_by' => $adminId,
            ],
        ];

        foreach ($donasiData as $dn) {
            Donasi::firstOrCreate(['no_kwitansi' => $dn['no_kwitansi']], $dn);
        }

        $allDonasis = Donasi::all();

        // ========================================================
        // 6. DONASI BARANG (10 Data Donasi Barang)
        // ========================================================
        $donasiBarangData = [
            [
                'donasi_id' => $allDonasis->get(0)?->id ?? 1,
                'deskripsi' => 'Semen Gresik 50 Sak untuk Program Bedah Rumah RTLH',
                'estimasi_nilai' => 3500000,
            ],
            [
                'donasi_id' => $allDonasis->get(1)?->id ?? 1,
                'deskripsi' => 'Beras Organik 20 Karung @25kg Logistik Sembako Lansia Dhuafa',
                'estimasi_nilai' => 7000000,
            ],
            [
                'donasi_id' => $allDonasis->get(2)?->id ?? 1,
                'deskripsi' => 'Tabung Oksigen Medis 1m3 + Regulator Siaga Ambulans GSC (2 Unit)',
                'estimasi_nilai' => 3000000,
            ],
            [
                'donasi_id' => $allDonasis->get(3)?->id ?? 1,
                'deskripsi' => 'Mushaf Al-Qur\'an Terjemahan Standar Kemenag (100 Eksemplar)',
                'estimasi_nilai' => 8000000,
            ],
            [
                'donasi_id' => $allDonasis->get(4)?->id ?? 1,
                'deskripsi' => 'Sepeda Gunung Anak Layak Pakai Hadiah Santri Berprestasi (3 Unit)',
                'estimasi_nilai' => 3600000,
            ],
            [
                'donasi_id' => $allDonasis->get(5)?->id ?? 1,
                'deskripsi' => 'Timbangan Badan Digital & Tensimeter Omron Posyandu Lansia (3 Set)',
                'estimasi_nilai' => 2400000,
            ],
            [
                'donasi_id' => $allDonasis->get(6)?->id ?? 1,
                'deskripsi' => 'Perahu Karet Rescue Kapasitas 6 Orang Kesiapsiagaan Banjir',
                'estimasi_nilai' => 12000000,
            ],
            [
                'donasi_id' => $allDonasis->get(7)?->id ?? 1,
                'deskripsi' => 'Laptop Pelatihan IT Santri Dhuafa Core i5 Siap Pakai (2 Unit)',
                'estimasi_nilai' => 7000000,
            ],
            [
                'donasi_id' => $allDonasis->get(8)?->id ?? 1,
                'deskripsi' => 'Kain Sarung Wadimor & Mukena Dewasa Kado Hari Santri (50 Pasang)',
                'estimasi_nilai' => 4750000,
            ],
            [
                'donasi_id' => $allDonasis->get(9)?->id ?? 1,
                'deskripsi' => 'Kasur Busa Medis & Kursi Roda Lipat Layanan Pasien Stroke Dhuafa',
                'estimasi_nilai' => 3800000,
            ],
        ];

        foreach ($donasiBarangData as $bg) {
            DonasiBarang::firstOrCreate(
                [
                    'donasi_id' => $bg['donasi_id'],
                    'deskripsi' => $bg['deskripsi'],
                ],
                $bg
            );
        }

        // ========================================================
        // 7. AGENDAS (10 Data Agenda Penugasan Amil)
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
                'amils' => [$amil1->id, $amil2->id, $amil4->id, $amil6->id],
            ],
            [
                'judul' => 'Pemeriksaan Kesehatan Lansia & Screening Katarak',
                'deskripsi' => 'Pelayanan tensi, cek gula darah, dan screening awal mata untuk lansia mustahik.',
                'lokasi' => 'Masjid Baiturrahman, Desa Kesugihan Kidul',
                'mulai' => Carbon::create(2026, 11, 7, 8, 0),
                'selesai' => Carbon::create(2026, 11, 7, 12, 0),
                'status' => 'terjadwal',
                'created_by' => $adminId,
                'amils' => [$amil3->id, $amil5->id, $amil7->id],
            ],
            [
                'judul' => 'Pelatihan Digitalisasi Zakat & CRM Donatur Amil',
                'deskripsi' => 'Workshop internal penggunaan aplikasi manajemen amil GSC dan teknik followup donatur korporat.',
                'lokasi' => 'Auditorium Diklat GSC',
                'mulai' => Carbon::create(2026, 11, 10, 13, 0),
                'selesai' => Carbon::create(2026, 11, 10, 16, 30),
                'status' => 'terjadwal',
                'created_by' => $adminId,
                'amils' => [$amil1->id, $amil2->id, $amil8->id, $amil10->id, $amilAlea->id],
            ],
            [
                'judul' => 'Jemput Zakat Maal & Kunjungan Kemitraan Donatur',
                'deskripsi' => 'Layanan jemput donasi ke kantor donatur mitra dan silaturahmi penyerahan laporan program.',
                'lokasi' => 'Kawasan Industri Cilacap & Perkantoran Cilacap Tengah',
                'mulai' => Carbon::create(2026, 11, 12, 9, 30),
                'selesai' => Carbon::create(2026, 11, 12, 15, 0),
                'status' => 'terjadwal',
                'created_by' => $adminId,
                'amils' => [$amil2->id, $amil4->id],
            ],
            [
                'judul' => 'Survei Faktual Kelayakan Mustahik Bedah Rumah',
                'deskripsi' => 'Peninjauan langsung kondisi fisik rumah roboh dan status tanah calon penerima bantuan RTLH di Kawunganten.',
                'lokasi' => 'Desa Bojong RT 05/RW 02, Kec. Kawunganten',
                'mulai' => Carbon::create(2026, 10, 15, 8, 0),
                'selesai' => Carbon::create(2026, 10, 15, 12, 0),
                'status' => 'selesai',
                'created_by' => $adminId,
                'amils' => [$amil8->id, $amil10->id],
            ],
            [
                'judul' => 'Penyaluran Santunan Yatim & Doa Bersama Donatur',
                'deskripsi' => 'Pemberian santunan uang tunai, perlengkapan sekolah, dan doa bersama 50 anak yatim.',
                'lokasi' => 'Aula Yayasan Gerak Sedekah Cilacap',
                'mulai' => Carbon::create(2026, 10, 20, 15, 30),
                'selesai' => Carbon::create(2026, 10, 20, 17, 30),
                'status' => 'selesai',
                'created_by' => $adminId,
                'amils' => [$amil2->id, $amil6->id, $amil8->id],
            ],
            [
                'judul' => 'Kunjungan Silaturahmi BAZNAS Kabupaten Cilacap',
                'deskripsi' => 'Koordinasi sinkronisasi data mustahik terpadu dan sinergi program pengentasan kemiskinan daerah.',
                'lokasi' => 'Kantor BAZNAS Cilacap',
                'mulai' => Carbon::create(2026, 10, 22, 10, 0),
                'selesai' => Carbon::create(2026, 10, 22, 12, 0),
                'status' => 'selesai',
                'created_by' => $adminId,
                'amils' => [$amil9->id, $amil1->id],
            ],
            [
                'judul' => 'Posko Layanan Pengecekan Kesehatan Gratis CFD',
                'deskripsi' => 'Membuka tenda kesehatan gratis tensi, gula darah, dan informasi program sedekah saat Car Free Day.',
                'lokasi' => 'Alun-alun Kabupaten Cilacap',
                'mulai' => Carbon::create(2026, 10, 25, 6, 0),
                'selesai' => Carbon::create(2026, 10, 25, 9, 30),
                'status' => 'selesai',
                'created_by' => $adminId,
                'amils' => [$amil5->id, $amil7->id, $amilAlea->id],
            ],
            [
                'judul' => 'Pengantaran Pasien Rujukan Dhuafa ke RS Margono Banyumas',
                'deskripsi' => 'Layanan ambulans antar kabupaten untuk pasien kanker rujukan operasi dari RSUD Cilacap.',
                'lokasi' => 'RSUD Margono Soekarjo Purwokerto',
                'mulai' => Carbon::create(2026, 10, 27, 7, 0),
                'selesai' => Carbon::create(2026, 10, 27, 13, 0),
                'status' => 'selesai',
                'created_by' => $adminId,
                'amils' => [$amil3->id, $amil5->id],
            ],
        ];

        foreach ($agendasData as $ag) {
            $amilIds = $ag['amils'];
            unset($ag['amils']);

            $agendaObj = Agenda::firstOrCreate(['judul' => $ag['judul']], $ag);
            $agendaObj->amils()->syncWithoutDetaching($amilIds);
        }

        // ========================================================
        // 8. PENGUMUMAN (10 Data Pengumuman Lembaga)
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
                'judul' => 'Undangan Rapat Pleno Koordinasi Akhir Bulan Oktober 2026',
                'isi' => 'Seluruh jajaran amil diundang menghadiri Rapat Pleno Evaluasi Kinerja Bulanan pada Kamis, 31 Oktober 2026 pukul 09.00 WIB di Ruang Rapat Yayasan GSC. Kehadiran tepat waktu sangat diharapkan.',
                'target' => 'semua',
                'published_at' => Carbon::create(2026, 10, 29, 16, 0),
                'created_by' => $adminId,
            ],
            [
                'judul' => 'Panduan Penggunaan Radius Presensi Absensi Kantor 100 Meter',
                'isi' => 'Bagi amil yang melakukan clock-in absensi masuk, pastikan GPS ponsel dalam mode akurasi tinggi dan berada dalam radius geofencing 100 meter dari titik koordinat kantor yayasan.',
                'target' => 'semua',
                'published_at' => Carbon::create(2026, 10, 20, 8, 30),
                'created_by' => $adminId,
            ],
            [
                'judul' => 'Pengumuman Jadwal Libur Nasional & Cuti Bersama',
                'isi' => 'Menindaklanjuti SKB 3 Menteri, pelayanan kantor yayasan akan libur sesuai kalender nasional. Namun tim ambulans darurat tetap siaga bergilir secara on-call 24 jam.',
                'target' => 'semua',
                'published_at' => Carbon::create(2026, 10, 18, 10, 0),
                'created_by' => $adminId,
            ],
            [
                'judul' => 'Sosialisasi Standar Kebersihan dan Sterilisasi Armada Ambulans',
                'isi' => 'Seluruh armada ambulans yang telah selesai melayani pengantaran pasien wajib disemprot disinfektan dan diganti sprei brankar demi keselamatan pasien berikutnya.',
                'target' => 'semua',
                'published_at' => Carbon::create(2026, 10, 15, 13, 0),
                'created_by' => $adminId,
            ],
            [
                'judul' => 'Penyaluran Zakat Maal Fitur Baru Penyaluran Digital',
                'isi' => 'Modul penyaluran kini telah dilengkapi foto penyerahan bantuan dan tanda tangan digital mustahik untuk menjamin akuntabilitas penyaluran dana zakat umat.',
                'target' => 'semua',
                'published_at' => Carbon::create(2026, 10, 12, 11, 15),
                'created_by' => $adminId,
            ],
            [
                'judul' => 'Kajian Rutin Pekanan Pegawai & Amil Yayasan GSC',
                'isi' => 'Mengundang segenap keluarga besar amil dan staf untuk mengikuti kajian tholabul ilmi setiap hari Jumat pagi pukul 07.30 - 08.30 WIB di Musholla Kantor.',
                'target' => 'semua',
                'published_at' => Carbon::create(2026, 10, 10, 15, 0),
                'created_by' => $adminId,
            ],
        ];

        foreach ($pengumumansData as $pg) {
            Pengumuman::firstOrCreate(['judul' => $pg['judul']], $pg);
        }

        // ========================================================
        // 9. AKTIVITAS HARIAN (10 Data Aktivitas Logbook Amil)
        // ========================================================
        $aktivitasData = [
            [
                'amil_id' => $amil1->id,
                'tanggal' => Carbon::create(2026, 10, 25),
                'judul' => 'Rekonsiliasi Bank & Verifikasi Laporan Donasi Masuk',
                'deskripsi' => 'Melakukan pencocokan mutasi rekening koran BSI dan Mandiri dengan transaksi donasi yang diinput sistem.',
                'lokasi' => 'Kantor Yayasan GSC Cilacap',
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
                'judul' => 'Kunjungan dan Edukasi Zakat Maal Perusahaan Mitra',
                'deskripsi' => 'Melakukan presentasi sosialisasi perhitungan nisab zakat maal dan program kemitraan CSR di CV. Samudra Jaya Mandiri.',
                'lokasi' => 'Kawasan Industri Pelabuhan Cilacap',
                'status' => 'disetujui',
            ],
            [
                'amil_id' => $amil5->id,
                'tanggal' => Carbon::create(2026, 10, 29),
                'judul' => 'Pemeriksaan Kesehatan Berkala Lansia Dhuafa',
                'deskripsi' => 'Melakukan pengecekan tensi dan kadar gula darah bagi 20 lansia mustahik di posko binaan.',
                'lokasi' => 'Posyandu Gumilir, Cilacap Utara',
                'status' => 'disetujui',
            ],
            [
                'amil_id' => $amil6->id,
                'tanggal' => Carbon::create(2026, 10, 26),
                'judul' => 'Followup Donatur Komunitas Gowes & Penyerahan Kwitansi',
                'deskripsi' => 'Menyerahkan bukti kwitansi resmi donasi ambulans siaga dan laporan progres pemanfaatan armada.',
                'lokasi' => 'GOR Wijayakusuma Cilacap',
                'status' => 'disetujui',
            ],
            [
                'amil_id' => $amil7->id,
                'tanggal' => Carbon::create(2026, 10, 27),
                'judul' => 'Pengecekan Teknis & Servis Rutin Armada Ambulans 02',
                'deskripsi' => 'Penggantian oli mesin, servis rem, dan pengecekan tabung oksigen ambulans di bengkel resmi.',
                'lokasi' => 'Bengkel Mobil Cilacap Tengah',
                'status' => 'disetujui',
            ],
            [
                'amil_id' => $amil8->id,
                'tanggal' => Carbon::create(2026, 10, 28),
                'judul' => 'Penyaluran Paket Sembako & Santunan Tunai Lansia',
                'deskripsi' => 'Menyerahkan langsung paket beras, minyak, susu, dan uang tunai ke rumah Mbah Sutarmi.',
                'lokasi' => 'Desa Kesugihan Kidul',
                'status' => 'disetujui',
            ],
            [
                'amil_id' => $amil9->id,
                'tanggal' => Carbon::create(2026, 10, 29),
                'judul' => 'Audiensi Sinergi Program Kemiskinan dengan BAZNAS Cilacap',
                'deskripsi' => 'Membahas pemetaan wilayah kantong kemiskinan dan harmonisasi program beasiswa dhuafa tahun 2027.',
                'lokasi' => 'Kantor BAZNAS Cilacap',
                'status' => 'disetujui',
            ],
            [
                'amil_id' => $amilAlea->id,
                'tanggal' => Carbon::create(2026, 10, 29),
                'judul' => 'Maintenance Sistem Aplikasi Manajemen Amil & Monitoring DB',
                'deskripsi' => 'Melakukan testing fitur kwitansi, validasi perhitungan donasi, dan pengujian sinkronisasi data presensi.',
                'lokasi' => 'Ruang IT Kantor GSC',
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
        // 10. IZIN CUTI (10 Data Izin & Cuti)
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
                'catatan_admin' => 'Disetujui. Pastikan laporan keuangan periode berjalan telah didelegasikan.',
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
                'alasan' => 'Sakit demam dan radang tenggorokan dengan surat anjuran istirahat dokter klinik.',
                'status' => 'disetujui',
                'approved_by' => $adminId,
                'catatan_admin' => 'Semoga lekas sembuh dan pulih kembali.',
            ],
            [
                'amil_id' => $amil4->id,
                'jenis' => 'cuti',
                'tgl_mulai' => Carbon::create(2026, 12, 21),
                'tgl_selesai' => Carbon::create(2026, 12, 23),
                'alasan' => 'Cuti tahunan keperluan acara silaturahmi keluarga besar akhir tahun.',
                'status' => 'diajukan',
                'approved_by' => null,
                'catatan_admin' => null,
            ],
            [
                'amil_id' => $amil5->id,
                'jenis' => 'izin',
                'tgl_mulai' => Carbon::create(2026, 10, 12),
                'tgl_selesai' => Carbon::create(2026, 10, 12),
                'alasan' => 'Izin mengurus sertifikasi kompetensi tenaga kesehatan di Semarang.',
                'status' => 'disetujui',
                'approved_by' => $adminId,
                'catatan_admin' => 'Disetujui. Sukses untuk sertifikasinya.',
            ],
            [
                'amil_id' => $amil6->id,
                'jenis' => 'cuti',
                'tgl_mulai' => Carbon::create(2026, 11, 23),
                'tgl_selesai' => Carbon::create(2026, 11, 24),
                'alasan' => 'Cuti tahunan untuk keperluan mengurus renovasi rumah orang tua.',
                'status' => 'diajukan',
                'approved_by' => null,
                'catatan_admin' => null,
            ],
            [
                'amil_id' => $amil7->id,
                'jenis' => 'sakit',
                'tgl_mulai' => Carbon::create(2026, 10, 8),
                'tgl_selesai' => Carbon::create(2026, 10, 9),
                'alasan' => 'Sakit gejala flu berat dan pusing pasca dinas malam pengantaran ambulans.',
                'status' => 'disetujui',
                'approved_by' => $adminId,
                'catatan_admin' => 'Istirahat yang cukup agar segera fit kembali.',
            ],
            [
                'amil_id' => $amil8->id,
                'jenis' => 'izin',
                'tgl_mulai' => Carbon::create(2026, 10, 16),
                'tgl_selesai' => Carbon::create(2026, 10, 16),
                'alasan' => 'Izin keperluan wisuda kerabat dekat di Purwokerto.',
                'status' => 'disetujui',
                'approved_by' => $adminId,
                'catatan_admin' => 'Disetujui.',
            ],
            [
                'amil_id' => $amil10->id,
                'jenis' => 'cuti',
                'tgl_mulai' => Carbon::create(2026, 11, 9),
                'tgl_selesai' => Carbon::create(2026, 11, 10),
                'alasan' => 'Cuti tahunan keperluan khitanan anak kandung.',
                'status' => 'disetujui',
                'approved_by' => $adminId,
                'catatan_admin' => 'Disetujui. Semoga acaranya lancar dan barokah.',
            ],
            [
                'amil_id' => $amilAlea->id,
                'jenis' => 'izin',
                'tgl_mulai' => Carbon::create(2026, 10, 23),
                'tgl_selesai' => Carbon::create(2026, 10, 23),
                'alasan' => 'Izin bimbingan laporan magang dengan dosen pembimbing kampus.',
                'status' => 'disetujui',
                'approved_by' => $adminId,
                'catatan_admin' => 'Disetujui. Semangat bimbingan laporannya.',
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
        // 11. ABSENSI (12 Data Absensi Amil)
        // ========================================================
        $absensiData = [
            [
                'amil_id' => $amil1->id,
                'tanggal' => Carbon::create(2026, 10, 28),
                'jam_masuk' => '07:55:00',
                'jam_pulang' => '16:30:00',
                'lat_masuk' => -7.71804200,
                'lng_masuk' => 109.01543200,
                'lat_pulang' => -7.71804500,
                'lng_pulang' => 109.01543800,
                'status' => 'hadir',
                'keterangan' => 'Hadir tepat waktu kantor yayasan.',
            ],
            [
                'amil_id' => $amil2->id,
                'tanggal' => Carbon::create(2026, 10, 28),
                'jam_masuk' => '07:50:00',
                'jam_pulang' => '16:35:00',
                'lat_masuk' => -7.71804100,
                'lng_masuk' => 109.01543000,
                'lat_pulang' => -7.71804300,
                'lng_pulang' => 109.01543500,
                'status' => 'hadir',
                'keterangan' => 'Hadir tepat waktu.',
            ],
            [
                'amil_id' => $amil3->id,
                'tanggal' => Carbon::create(2026, 10, 28),
                'jam_masuk' => '07:45:00',
                'jam_pulang' => '16:40:00',
                'lat_masuk' => -7.71804400,
                'lng_masuk' => 109.01543300,
                'lat_pulang' => -7.71804600,
                'lng_pulang' => 109.01544000,
                'status' => 'hadir',
                'keterangan' => 'Siaga ambulans shift pagi.',
            ],
            [
                'amil_id' => $amil4->id,
                'tanggal' => Carbon::create(2026, 10, 28),
                'jam_masuk' => '08:05:00',
                'jam_pulang' => '16:30:00',
                'lat_masuk' => -7.71804000,
                'lng_masuk' => 109.01543100,
                'lat_pulang' => -7.71804200,
                'lng_pulang' => 109.01543600,
                'status' => 'terlambat',
                'keterangan' => 'Terlambat 5 menit karena kendala cuaca hujan deras.',
            ],
            [
                'amil_id' => $amil5->id,
                'tanggal' => Carbon::create(2026, 10, 28),
                'jam_masuk' => '07:58:00',
                'jam_pulang' => '16:32:00',
                'lat_masuk' => -7.71804300,
                'lng_masuk' => 109.01543400,
                'lat_pulang' => -7.71804500,
                'lng_pulang' => 109.01543700,
                'status' => 'hadir',
                'keterangan' => 'Hadir tepat waktu.',
            ],
            [
                'amil_id' => $amil6->id,
                'tanggal' => Carbon::create(2026, 10, 28),
                'jam_masuk' => '07:52:00',
                'jam_pulang' => '16:30:00',
                'lat_masuk' => -7.71804200,
                'lng_masuk' => 109.01543200,
                'lat_pulang' => -7.71804400,
                'lng_pulang' => 109.01543900,
                'status' => 'hadir',
                'keterangan' => 'Hadir tepat waktu.',
            ],
            [
                'amil_id' => $amil1->id,
                'tanggal' => Carbon::create(2026, 10, 29),
                'jam_masuk' => '07:48:00',
                'jam_pulang' => '16:35:00',
                'lat_masuk' => -7.71804200,
                'lng_masuk' => 109.01543200,
                'lat_pulang' => -7.71804500,
                'lng_pulang' => 109.01543800,
                'status' => 'hadir',
                'keterangan' => 'Hadir tepat waktu.',
            ],
            [
                'amil_id' => $amil2->id,
                'tanggal' => Carbon::create(2026, 10, 29),
                'jam_masuk' => '07:50:00',
                'jam_pulang' => '16:30:00',
                'lat_masuk' => -7.71804100,
                'lng_masuk' => 109.01543000,
                'lat_pulang' => -7.71804300,
                'lng_pulang' => 109.01543500,
                'status' => 'hadir',
                'keterangan' => 'Hadir tepat waktu.',
            ],
            [
                'amil_id' => $amil7->id,
                'tanggal' => Carbon::create(2026, 10, 29),
                'jam_masuk' => '07:40:00',
                'jam_pulang' => '16:45:00',
                'lat_masuk' => -7.71804400,
                'lng_masuk' => 109.01543300,
                'lat_pulang' => -7.71804600,
                'lng_pulang' => 109.01544000,
                'status' => 'hadir',
                'keterangan' => 'Piket darurat ambulans siaga.',
            ],
            [
                'amil_id' => $amil8->id,
                'tanggal' => Carbon::create(2026, 10, 29),
                'jam_masuk' => '07:55:00',
                'jam_pulang' => '16:30:00',
                'lat_masuk' => -7.71804200,
                'lng_masuk' => 109.01543200,
                'lat_pulang' => -7.71804400,
                'lng_pulang' => 109.01543900,
                'status' => 'hadir',
                'keterangan' => 'Hadir dinas penyaluran lapangan.',
            ],
            [
                'amil_id' => $amil10->id,
                'tanggal' => Carbon::create(2026, 10, 29),
                'jam_masuk' => '07:57:00',
                'jam_pulang' => '16:30:00',
                'lat_masuk' => -7.71804300,
                'lng_masuk' => 109.01543400,
                'lat_pulang' => -7.71804500,
                'lng_pulang' => 109.01543700,
                'status' => 'hadir',
                'keterangan' => 'Hadir tepat waktu.',
            ],
            [
                'amil_id' => $amilAlea->id,
                'tanggal' => Carbon::create(2026, 10, 29),
                'jam_masuk' => '07:51:00',
                'jam_pulang' => '16:33:00',
                'lat_masuk' => -7.71804200,
                'lng_masuk' => 109.01543200,
                'lat_pulang' => -7.71804500,
                'lng_pulang' => 109.01543800,
                'status' => 'hadir',
                'keterangan' => 'Hadir shift IT & digitalisasi.',
            ],
        ];

        foreach ($absensiData as $ab) {
            Absensi::firstOrCreate(
                [
                    'amil_id' => $ab['amil_id'],
                    'tanggal' => $ab['tanggal'],
                ],
                $ab
            );
        }

        // ========================================================
        // 12. NOTIFIKASI (10 Data Notifikasi Sistem)
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
                'pesan' => 'Kholifatun Mualfiyah mengajukan cuti tahunan tanggal 16-18 November 2026 menunggu persetujuan.',
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
                'pesan' => 'Wiji Susanti telah menginput laporan aktivitas survei lapangan mustahik hari ini.',
                'tipe' => 'aktivitas',
                'url' => '/admin/aktivitas-harians',
                'is_read' => false,
                'read_at' => null,
            ],
            [
                'user_id' => $adminId,
                'judul' => 'Layanan Ambulans Siaga Darurat Dilaporkan',
                'pesan' => 'Armada ambulans siaga 01 telah kembali dari RSUD Margono Purwokerto dengan kondisi aman.',
                'tipe' => 'aktivitas',
                'url' => '/admin/aktivitas-harians',
                'is_read' => true,
                'read_at' => Carbon::create(2026, 10, 27, 14, 0),
            ],
            [
                'user_id' => $adminId,
                'judul' => 'Donasi Barang Masuk: 50 Sak Semen Bedah Rumah',
                'pesan' => 'Toko Bangunan Berkah Logam telah menyerahkan bantuan material 50 sak semen.',
                'tipe' => 'donasi',
                'url' => '/admin/donasi-barangs',
                'is_read' => true,
                'read_at' => Carbon::create(2026, 10, 28, 10, 0),
            ],
            [
                'user_id' => $adminId,
                'judul' => 'Mustahik Baru Asnaf Mualaf Terdaftar',
                'pesan' => 'Data Bapak Nurhasan telah diverifikasi tim lapangan sebagai penerima manfaat asnaf mualaf.',
                'tipe' => 'mustahik',
                'url' => '/admin/mustahiks',
                'is_read' => false,
                'read_at' => null,
            ],
            [
                'user_id' => $adminId,
                'judul' => 'Pengajuan Izin Setengah Hari Masuk',
                'pesan' => 'Fahri Azzuhair mengajukan izin urusan administrasi tanggal 5 November 2026.',
                'tipe' => 'izin_cuti',
                'url' => '/admin/izin-cutis',
                'is_read' => false,
                'read_at' => null,
            ],
            [
                'user_id' => $adminId,
                'judul' => 'Rekapitulasi Presensi Amil Bulan Oktober Siap Diunduh',
                'pesan' => 'Rekap absensi bulanan seluruh pegawai yayasan telah terbit dan dapat diekspor ke Excel.',
                'tipe' => 'absensi',
                'url' => '/admin/absensis',
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
