<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\Agenda;
use App\Models\AktivitasHarian;
use App\Models\Amil;
use App\Models\Divisi;
use App\Models\Donasi;
use App\Models\DonasiBarang;
use App\Models\Donatur;
use App\Models\DonaturKategori;
use App\Models\IzinCuti;
use App\Models\JamKerja;
use App\Models\JenisDana;
use App\Models\LokasiKantor;
use App\Models\Mustahik;
use App\Models\Notifikasi;
use App\Models\Pengumuman;
use App\Models\Penyaluran;
use App\Models\Program;
use App\Models\SaldoCuti;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DummyDataSeeder extends Seeder
{
    /**
     * Seed dummy data: minimal 5 data untuk setiap tabel.
     */
    public function run(): void
    {
        $amilRole = Role::firstOrCreate(['name' => 'Amil']);

        // 1. DIVISI (Minimal 5)
        $divisiData = [
            ['nama' => 'Penghimpunan / Fundraising', 'deskripsi' => 'Divisi penghimpunan dana ZISWAF dan kemitraan donatur'],
            ['nama' => 'Pendayagunaan & Penyaluran', 'deskripsi' => 'Divisi penyaluran manfaat dan program ke mustahik'],
            ['nama' => 'Operasional & Keuangan', 'deskripsi' => 'Divisi administrasi operasional, SDM, dan keuangan'],
            ['nama' => 'Media & Publikasi Dakwah', 'deskripsi' => 'Divisi konten dakwah, media sosial, dan kampanye program'],
            ['nama' => 'Pengembangan Kemitraan & CSR', 'deskripsi' => 'Divisi hubungan korporasi, instansi, dan program CSR'],
        ];

        $divisis = [];
        foreach ($divisiData as $data) {
            $divisis[] = Divisi::firstOrCreate(['nama' => $data['nama']], $data);
        }

        // 2. JAM KERJA (Minimal 5)
        $jamKerjaData = [
            ['nama' => 'Jam Kerja Reguler', 'jam_masuk' => '08:00:00', 'jam_pulang' => '16:30:00', 'toleransi_menit' => 15, 'is_active' => true],
            ['nama' => 'Jam Kerja Ramadhan', 'jam_masuk' => '08:00:00', 'jam_pulang' => '15:00:00', 'toleransi_menit' => 15, 'is_active' => true],
            ['nama' => 'Jam Kerja Tim Siaga Lapangan', 'jam_masuk' => '07:00:00', 'jam_pulang' => '15:30:00', 'toleransi_menit' => 30, 'is_active' => true],
            ['nama' => 'Jam Kerja Shift Weekend', 'jam_masuk' => '09:00:00', 'jam_pulang' => '16:00:00', 'toleransi_menit' => 15, 'is_active' => true],
            ['nama' => 'Jam Kerja Posko Siaga Bencana', 'jam_masuk' => '06:00:00', 'jam_pulang' => '18:00:00', 'toleransi_menit' => 30, 'is_active' => true],
        ];

        foreach ($jamKerjaData as $data) {
            JamKerja::firstOrCreate(['nama' => $data['nama']], $data);
        }

        // 3. LOKASI KANTOR (Minimal 5)
        $lokasiData = [
            ['nama_lokasi' => 'Kantor Pusat GSC', 'alamat' => 'Jl. Gerakan Sedekah Cepat No. 01, Jawa Barat', 'lat' => -6.20876340, 'lng' => 106.84559900, 'radius_meter' => 100, 'is_active' => true],
            ['nama_lokasi' => 'Kantor Cabang Bandung', 'alamat' => 'Jl. Asia Afrika No. 45, Bandung', 'lat' => -6.92147700, 'lng' => 107.60749000, 'radius_meter' => 100, 'is_active' => true],
            ['nama_lokasi' => 'Pos Layanan Umat Bekasi', 'alamat' => 'Jl. Ir. H. Juanda No. 88, Bekasi', 'lat' => -6.23827000, 'lng' => 106.97557100, 'radius_meter' => 150, 'is_active' => true],
            ['nama_lokasi' => 'Gerai Donasi Mall Kelapa Gading', 'alamat' => 'Mall Kelapa Gading Lt. Ground, Jakarta Utara', 'lat' => -6.15783300, 'lng' => 106.90833300, 'radius_meter' => 80, 'is_active' => true],
            ['nama_lokasi' => 'Posko Penyaluran Dhuafa Bogor', 'alamat' => 'Jl. Raya Pajajaran No. 23, Bogor', 'lat' => -6.59762900, 'lng' => 106.80536400, 'radius_meter' => 120, 'is_active' => true],
        ];

        foreach ($lokasiData as $data) {
            LokasiKantor::firstOrCreate(['nama_lokasi' => $data['nama_lokasi']], $data);
        }

        // 4. USERS & AMILS (Minimal 5 Petugas Amil)
        $amilUsersData = [
            [
                'email' => 'amil@amilgsc.com',
                'name' => 'Ahmad Fauzi',
                'nip' => 'AML-202601',
                'nama_lengkap' => 'Ahmad Fauzi, S.Sos',
                'jabatan' => 'Petugas Fundraising Senior',
                'divisi_id' => $divisis[0]->id,
                'no_hp' => '081234567891',
                'alamat' => 'Jl. Sedekah No. 12, Bandung',
                'target_bulanan' => 25000000,
            ],
            [
                'email' => 'siti@amilgsc.com',
                'name' => 'Siti Nurhaliza',
                'nip' => 'AML-202602',
                'nama_lengkap' => 'Siti Nurhaliza, S.E',
                'jabatan' => 'Petugas Layanan Mustahik Lapangan',
                'divisi_id' => $divisis[1]->id,
                'no_hp' => '081234567892',
                'alamat' => 'Jl. Barokah No. 45, Bandung',
                'target_bulanan' => 15000000,
            ],
            [
                'email' => 'budi@amilgsc.com',
                'name' => 'Budi Santoso',
                'nip' => 'AML-202603',
                'nama_lengkap' => 'Budi Santoso, S.Kom',
                'jabatan' => 'Petugas Administrasi & IT Amil',
                'divisi_id' => $divisis[2]->id,
                'no_hp' => '081234567893',
                'alamat' => 'Jl. Terusan Buah Batu No. 18, Bandung',
                'target_bulanan' => 10000000,
            ],
            [
                'email' => 'dewi@amilgsc.com',
                'name' => 'Dewi Lestari',
                'nip' => 'AML-202604',
                'nama_lengkap' => 'Dewi Lestari, S.Pd',
                'jabatan' => 'Koordinator Penyaluran Beasiswa',
                'divisi_id' => $divisis[1]->id,
                'no_hp' => '081234567894',
                'alamat' => 'Jl. Dipatiukur No. 89, Bandung',
                'target_bulanan' => 20000000,
            ],
            [
                'email' => 'ridwan@amilgsc.com',
                'name' => 'Muhammad Ridwan',
                'nip' => 'AML-202605',
                'nama_lengkap' => 'Muhammad Ridwan, S.Ag',
                'jabatan' => 'Petugas Kemitraan Komunitas & CSR',
                'divisi_id' => $divisis[4]->id,
                'no_hp' => '081234567895',
                'alamat' => 'Jl. Soekarno Hatta No. 102, Bandung',
                'target_bulanan' => 30000000,
            ],
        ];

        $amils = [];
        foreach ($amilUsersData as $item) {
            $user = User::firstOrCreate(
                ['email' => $item['email']],
                [
                    'name' => $item['name'],
                    'password' => Hash::make('password'),
                    'is_active' => true,
                ]
            );
            $user->syncRoles([$amilRole]);

            $amil = Amil::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'nip' => $item['nip'],
                    'nama' => $item['nama_lengkap'],
                    'jabatan' => $item['jabatan'],
                    'divisi_id' => $item['divisi_id'],
                    'no_hp' => $item['no_hp'],
                    'alamat' => $item['alamat'],
                    'tgl_bergabung' => now()->subMonths(6),
                    'target_bulanan' => $item['target_bulanan'],
                    'status' => 'aktif',
                ]
            );
            $amils[] = $amil;

            // Pastikan SALDO CUTI (Minimal 5)
            SaldoCuti::firstOrCreate(
                ['amil_id' => $amil->id, 'tahun' => now()->year],
                ['jatah' => 12, 'terpakai' => rand(0, 3)]
            );
        }

        // 5. DONATUR KATEGORI (Minimal 5)
        $kategoriData = [
            ['nama' => 'Perorangan / Retail', 'deskripsi' => 'Donatur individu / perorangan masyarakat umum'],
            ['nama' => 'Korporat / Lembaga / Perusahaan', 'deskripsi' => 'Donatur dari perusahaan, CSR, atau instansi'],
            ['nama' => 'Komunitas / Komunitas Mitra', 'deskripsi' => 'Donatur dari kelompok hobi, komunitas, atau paguyuban'],
            ['nama' => 'Organisasi Sosial & Masjid', 'deskripsi' => 'Donatur dari DKM masjid, majelis taklim, dan yayasan'],
            ['nama' => 'Alumni & Paguyuban Kampus', 'deskripsi' => 'Donatur dari ikatan alumni sekolah dan kampus mitra'],
        ];

        $donaturKategoris = [];
        foreach ($kategoriData as $data) {
            $donaturKategoris[] = DonaturKategori::firstOrCreate(['nama' => $data['nama']], $data);
        }

        // 6. PROGRAM (Minimal 5)
        $programData = [
            [
                'nama' => 'Layanan Ambulans Siaga Gratis',
                'deskripsi' => 'Operasional ambulans gratis 24 jam untuk pasien dhuafa dan jenazah',
                'target_dana' => 150000000,
                'tgl_mulai' => now()->startOfYear(),
                'tgl_selesai' => now()->endOfYear(),
                'status' => 'aktif',
            ],
            [
                'nama' => 'Beasiswa Generasi Cerdas Mandiri',
                'deskripsi' => 'Bantuan pendidikan dan pembinaan karakter anak yatim & dhuafa',
                'target_dana' => 100000000,
                'tgl_mulai' => now()->startOfYear(),
                'tgl_selesai' => now()->endOfYear(),
                'status' => 'aktif',
            ],
            [
                'nama' => 'Pemberdayaan UMKM Barokah',
                'deskripsi' => 'Bantuan modal usaha bergulir tanpa bunga untuk mustahik produktif',
                'target_dana' => 75000000,
                'tgl_mulai' => now()->startOfYear(),
                'tgl_selesai' => now()->endOfYear(),
                'status' => 'aktif',
            ],
            [
                'nama' => 'Pangan Berkah Lansia Dhuafa',
                'deskripsi' => 'Paket sembako dan makanan siap saji bulanan bagi lansia sebatang kara',
                'target_dana' => 60000000,
                'tgl_mulai' => now()->startOfYear(),
                'tgl_selesai' => now()->endOfYear(),
                'status' => 'aktif',
            ],
            [
                'nama' => 'Renovasi Sarana Ibadah Pelosok',
                'deskripsi' => 'Penyediaan sarana air bersih, karpet, dan renovasi musholla pedesaan',
                'target_dana' => 120000000,
                'tgl_mulai' => now()->startOfYear(),
                'tgl_selesai' => now()->endOfYear(),
                'status' => 'aktif',
            ],
        ];

        $programs = [];
        foreach ($programData as $data) {
            $programs[] = Program::firstOrCreate(['nama' => $data['nama']], $data);
        }

        $jenisDanas = JenisDana::all();

        // 7. DONATUR (Minimal 5)
        $donaturList = [
            [
                'kode_donatur' => 'DNT-2026001',
                'nama' => 'H. Hendra Wijaya',
                'jenis' => 'individu',
                'kategori_id' => $donaturKategoris[0]->id,
                'no_hp' => '081298765431',
                'email' => 'hendra.wijaya@gmail.com',
                'alamat' => 'Jl. Merdeka No. 10, Jakarta Selatan',
                'kelurahan' => 'Kebayoran Baru',
                'kecamatan' => 'Kebayoran Baru',
                'kota' => 'Jakarta Selatan',
                'provinsi' => 'DKI Jakarta',
                'nik' => '3174012305800001',
                'is_anonim' => false,
                'amil_pic_id' => $amils[0]->id,
            ],
            [
                'kode_donatur' => 'DNT-2026002',
                'nama' => 'Hj. Fatimah Azzahra',
                'jenis' => 'individu',
                'kategori_id' => $donaturKategoris[0]->id,
                'no_hp' => '081311223344',
                'email' => 'fatimah.azzahra@yahoo.com',
                'alamat' => 'Jl. Riau No. 15, Bandung',
                'kelurahan' => 'Cihapit',
                'kecamatan' => 'Bandung Wetan',
                'kota' => 'Bandung',
                'provinsi' => 'Jawa Barat',
                'nik' => '3273014508850002',
                'is_anonim' => false,
                'amil_pic_id' => $amils[1]->id,
            ],
            [
                'kode_donatur' => 'DNT-2026003',
                'nama' => 'PT. Berkah Sinergi Mandiri',
                'jenis' => 'lembaga',
                'kategori_id' => $donaturKategoris[1]->id,
                'no_hp' => '081199887766',
                'email' => 'csr@berkahsinergi.co.id',
                'alamat' => 'Gedung Sovereign Plaza Lt. 8 TB Simatupang',
                'kelurahan' => 'Cilandak Barat',
                'kecamatan' => 'Cilandak',
                'kota' => 'Jakarta Selatan',
                'provinsi' => 'DKI Jakarta',
                'npwp' => '01.234.567.8-012.000',
                'is_anonim' => false,
                'amil_pic_id' => $amils[0]->id,
            ],
            [
                'kode_donatur' => 'DNT-2026004',
                'nama' => 'Komunitas Sahabat Berbagi',
                'jenis' => 'lembaga',
                'kategori_id' => $donaturKategoris[2]->id,
                'no_hp' => '085712345678',
                'email' => 'sahabatberbagi@gmail.com',
                'alamat' => 'Jl. Margonda Raya No. 12, Depok',
                'kelurahan' => 'Pondok Cina',
                'kecamatan' => 'Beji',
                'kota' => 'Depok',
                'provinsi' => 'Jawa Barat',
                'is_anonim' => false,
                'amil_pic_id' => $amils[1]->id,
            ],
            [
                'kode_donatur' => 'DNT-2026005',
                'nama' => 'Dr. Irwan Susanto, Sp.A',
                'jenis' => 'individu',
                'kategori_id' => $donaturKategoris[0]->id,
                'no_hp' => '081234509876',
                'email' => 'irwan.pediatri@gmail.com',
                'alamat' => 'Komplek Duta Mas Blok B3 No. 7',
                'kelurahan' => 'Wijaya Kusuma',
                'kecamatan' => 'Grogol Petamburan',
                'kota' => 'Jakarta Barat',
                'provinsi' => 'DKI Jakarta',
                'nik' => '3674011210780003',
                'is_anonim' => false,
                'amil_pic_id' => $amils[0]->id,
            ],
        ];

        $donaturs = [];
        foreach ($donaturList as $d) {
            $donaturs[] = Donatur::firstOrCreate(['kode_donatur' => $d['kode_donatur']], $d);
        }

        // 8. DONASI (Minimal 5)
        $adminUser = User::role('Admin')->first();

        $donasiList = [
            [
                'no_kwitansi' => 'KWT-GSC-202601-001',
                'donatur_id' => $donaturs[0]->id,
                'amil_id' => $amils[0]->id,
                'jenis_dana_id' => $jenisDanas[0]->id, // Zakat Maal
                'program_id' => null,
                'jumlah' => 5000000,
                'metode_bayar' => 'transfer',
                'bank' => 'Bank Syariah Indonesia (BSI)',
                'tgl_donasi' => now()->subDays(4),
                'niat' => 'Niat zakat maal keluarga untuk pembersihan harta',
                'atas_nama' => 'H. Hendra Wijaya & Keluarga',
                'peruntukan' => 'bebas',
                'status' => 'terverifikasi',
                'verified_by' => $adminUser?->id,
                'verified_at' => now()->subDays(4),
            ],
            [
                'no_kwitansi' => 'KWT-GSC-202601-002',
                'donatur_id' => $donaturs[2]->id,
                'amil_id' => $amils[0]->id,
                'jenis_dana_id' => $jenisDanas[2]->id, // Infak Terikat
                'program_id' => $programs[1]->id, // Beasiswa
                'jumlah' => 10000000,
                'metode_bayar' => 'transfer',
                'bank' => 'Bank Central Asia (BCA)',
                'tgl_donasi' => now()->subDays(3),
                'niat' => 'Penyaluran dana CSR pendidikan yatim semester genap',
                'atas_nama' => 'PT. Berkah Sinergi Mandiri',
                'peruntukan' => 'program',
                'status' => 'terverifikasi',
                'verified_by' => $adminUser?->id,
                'verified_at' => now()->subDays(3),
            ],
            [
                'no_kwitansi' => 'KWT-GSC-202601-003',
                'donatur_id' => $donaturs[1]->id,
                'amil_id' => $amils[1]->id,
                'jenis_dana_id' => $jenisDanas[1]->id, // Zakat Fitrah
                'program_id' => null,
                'jumlah' => 750000,
                'metode_bayar' => 'qris',
                'bank' => 'QRIS GSC',
                'tgl_donasi' => now()->subDays(2),
                'niat' => 'Zakat fitrah untuk 5 jiwa',
                'atas_nama' => 'Hj. Fatimah Azzahra',
                'peruntukan' => 'bebas',
                'status' => 'terverifikasi',
                'verified_by' => $adminUser?->id,
                'verified_at' => now()->subDays(2),
            ],
            [
                'no_kwitansi' => 'KWT-GSC-202601-004',
                'donatur_id' => $donaturs[3]->id,
                'amil_id' => $amils[1]->id,
                'jenis_dana_id' => $jenisDanas[3]->id, // Infak Umum
                'program_id' => null,
                'jumlah' => 1500000,
                'metode_bayar' => 'tunai',
                'bank' => 'Kas Lapangan Amil',
                'tgl_donasi' => now()->subDay(),
                'niat' => 'Sedekah subuh dan infak kemaslahatan umat',
                'atas_nama' => 'Komunitas Sahabat Berbagi',
                'peruntukan' => 'bebas',
                'status' => 'terverifikasi',
                'verified_by' => $adminUser?->id,
                'verified_at' => now()->subDay(),
            ],
            [
                'no_kwitansi' => 'KWT-GSC-202601-005',
                'donatur_id' => $donaturs[4]->id,
                'amil_id' => $amils[0]->id,
                'jenis_dana_id' => $jenisDanas[2]->id, // Infak Terikat
                'program_id' => $programs[0]->id, // Ambulans
                'jumlah' => 2500000,
                'metode_bayar' => 'transfer',
                'bank' => 'Bank Mandiri',
                'tgl_donasi' => now(),
                'niat' => 'Infak operasional bahan bakar & servis armada ambulans gratis',
                'atas_nama' => 'Dr. Irwan Susanto, Sp.A',
                'peruntukan' => 'program',
                'status' => 'menunggu_verifikasi',
            ],
        ];

        $donasis = [];
        foreach ($donasiList as $d) {
            $donasis[] = Donasi::firstOrCreate(['no_kwitansi' => $d['no_kwitansi']], $d);
        }

        // 9. DONASI BARANG (Minimal 5)
        $barangList = [
            ['donasi_id' => $donasis[0]->id, 'deskripsi' => '10 Karung Beras Premium @25kg untuk Posko Pangan Dhuafa', 'estimasi_nilai' => 3500000],
            ['donasi_id' => $donasis[1]->id, 'deskripsi' => '5 Dus Paket Sembako Lengkap (Minyak Goreng, Gula, Tepung, Sarden)', 'estimasi_nilai' => 1250000],
            ['donasi_id' => $donasis[2]->id, 'deskripsi' => '1 Unit Kursi Roda Standar Medis Baru untuk Pasien Lansia Stroke', 'estimasi_nilai' => 1800000],
            ['donasi_id' => $donasis[3]->id, 'deskripsi' => '2 Unit Laptop Core i5 Rekondisi Siap Pakai untuk Rumah Belajar Qur\'an', 'estimasi_nilai' => 6000000],
            ['donasi_id' => $donasis[4]->id, 'deskripsi' => '50 Lembar Kain Sarung & Mukena Katun Baru untuk Jamaah Pelosok', 'estimasi_nilai' => 2500000],
        ];

        foreach ($barangList as $b) {
            DonasiBarang::firstOrCreate(
                ['donasi_id' => $b['donasi_id'], 'deskripsi' => $b['deskripsi']],
                $b
            );
        }

        // 10. ABSENSI (Minimal 5)
        $absensiData = [
            [
                'amil_id' => $amils[0]->id,
                'tanggal' => now()->toDateString(),
                'jam_masuk' => '07:55:00',
                'jam_pulang' => '16:35:00',
                'lat_masuk' => -6.20876340,
                'lng_masuk' => 106.84559900,
                'lat_pulang' => -6.20876340,
                'lng_pulang' => 106.84559900,
                'status' => 'hadir',
                'keterangan' => 'Hadir tepat waktu di Kantor Pusat GSC',
            ],
            [
                'amil_id' => $amils[0]->id,
                'tanggal' => now()->subDay()->toDateString(),
                'jam_masuk' => '07:58:00',
                'jam_pulang' => '16:30:00',
                'lat_masuk' => -6.20876340,
                'lng_masuk' => 106.84559900,
                'status' => 'hadir',
                'keterangan' => 'Hadir tepat waktu',
            ],
            [
                'amil_id' => $amils[1]->id,
                'tanggal' => now()->toDateString(),
                'jam_masuk' => '08:12:00',
                'jam_pulang' => '16:40:00',
                'lat_masuk' => -6.20876340,
                'lng_masuk' => 106.84559900,
                'status' => 'terlambat',
                'keterangan' => 'Terlambat 12 menit karena kemacetan jalan raya',
            ],
            [
                'amil_id' => $amils[2]->id,
                'tanggal' => now()->toDateString(),
                'jam_masuk' => '07:50:00',
                'jam_pulang' => '16:30:00',
                'lat_masuk' => -6.92147700,
                'lng_masuk' => 107.60749000,
                'status' => 'hadir',
                'keterangan' => 'Presensi di Kantor Cabang Bandung',
            ],
            [
                'amil_id' => $amils[3]->id,
                'tanggal' => now()->toDateString(),
                'jam_masuk' => '08:00:00',
                'jam_pulang' => '16:30:00',
                'lat_masuk' => -6.23827000,
                'lng_masuk' => 106.97557100,
                'status' => 'hadir',
                'keterangan' => 'Presensi di Pos Layanan Bekasi',
            ],
        ];

        foreach ($absensiData as $ab) {
            Absensi::updateOrCreate(
                ['amil_id' => $ab['amil_id'], 'tanggal' => $ab['tanggal']],
                $ab
            );
        }

        // 11. IZIN & CUTI (Minimal 5)
        $izinCutiList = [
            [
                'amil_id' => $amils[0]->id,
                'jenis' => 'izin',
                'tgl_mulai' => now()->subDays(10)->toDateString(),
                'tgl_selesai' => now()->subDays(10)->toDateString(),
                'alasan' => 'Izin setengah hari mengantar orang tua kontrol rutin ke RSUD',
                'status' => 'disetujui',
                'approved_by' => $adminUser?->id,
                'catatan_admin' => 'Disetujui, semoga lekas sehat',
            ],
            [
                'amil_id' => $amils[1]->id,
                'jenis' => 'sakit',
                'tgl_mulai' => now()->subDays(7)->toDateString(),
                'tgl_selesai' => now()->subDays(6)->toDateString(),
                'alasan' => 'Sakit demam tipes, istirahat dokter selama 2 hari kerja',
                'status' => 'disetujui',
                'approved_by' => $adminUser?->id,
                'catatan_admin' => 'Surat keterangan dokter lengkap dan valid',
            ],
            [
                'amil_id' => $amils[0]->id,
                'jenis' => 'cuti',
                'tgl_mulai' => now()->subDays(20)->toDateString(),
                'tgl_selesai' => now()->subDays(19)->toDateString(),
                'alasan' => 'Cuti tahunan menghadiri pernikahan adik kandung di Cirebon',
                'status' => 'disetujui',
                'approved_by' => $adminUser?->id,
                'catatan_admin' => 'Disetujui potong saldo cuti tahunan',
            ],
            [
                'amil_id' => $amils[2]->id,
                'jenis' => 'izin',
                'tgl_mulai' => now()->subDays(2)->toDateString(),
                'tgl_selesai' => now()->subDays(2)->toDateString(),
                'alasan' => 'Izin mengurus perpanjangan SIM dan administrasi kependudukan',
                'status' => 'disetujui',
                'approved_by' => $adminUser?->id,
                'catatan_admin' => 'Disetujui',
            ],
            [
                'amil_id' => $amils[3]->id,
                'jenis' => 'cuti',
                'tgl_mulai' => now()->addDays(5)->toDateString(),
                'tgl_selesai' => now()->addDays(7)->toDateString(),
                'alasan' => 'Pengajuan cuti tahunan 3 hari untuk keperluan keluarga',
                'status' => 'diajukan',
            ],
        ];

        foreach ($izinCutiList as $iz) {
            IzinCuti::firstOrCreate(
                ['amil_id' => $iz['amil_id'], 'tgl_mulai' => $iz['tgl_mulai'], 'alasan' => $iz['alasan']],
                $iz
            );
        }

        // 12. AKTIVITAS HARIAN AMIL (Minimal 5)
        $aktivitasData = [
            [
                'amil_id' => $amils[0]->id,
                'tanggal' => now()->toDateString(),
                'judul' => 'Sosialisasi Program Zakat Maal & Kemitraan CSR di Perkantoran',
                'deskripsi' => 'Melakukan presentasi program unggulan beasiswa dan ambulans gratis kepada manajemen PT. Berkah Sinergi Mandiri',
                'lokasi' => 'TB Simatupang, Jakarta Selatan',
                'status' => 'disetujui',
            ],
            [
                'amil_id' => $amils[0]->id,
                'tanggal' => now()->subDay()->toDateString(),
                'judul' => 'Penjemputan Donasi ZISWAF & Penyerahan Kwitansi Resmi',
                'deskripsi' => 'Mengunjungi kediaman Bapak H. Hendra untuk silaturahmi berkala serta serah terima kwitansi resmi donasi zakat maal',
                'lokasi' => 'Kebayoran Baru, Jakarta Selatan',
                'status' => 'disetujui',
            ],
            [
                'amil_id' => $amils[1]->id,
                'tanggal' => now()->toDateString(),
                'judul' => 'Survey Verifikasi Faktual Calon Mustahik Fakir Miskin',
                'deskripsi' => 'Kunjungan home visit ke kediaman Ibu Aminah untuk meninjau kelayakan bantuan beasiswa anak yatim dan sembako',
                'lokasi' => 'Kp. Sukamaju RT 02/08',
                'status' => 'disetujui',
            ],
            [
                'amil_id' => $amils[2]->id,
                'tanggal' => now()->toDateString(),
                'judul' => 'Rekonsiliasi Bank & Audit Pembukuan Kas ZISWAF',
                'deskripsi' => 'Pengecekan mutasi rekening donasi masuk, pencocokan bukti transfer donatur, dan update database sistem',
                'lokasi' => 'Ruang Administrasi Kantor Pusat GSC',
                'status' => 'disetujui',
            ],
            [
                'amil_id' => $amils[3]->id,
                'tanggal' => now()->toDateString(),
                'judul' => 'Penyaluran Paket Pangan Berkah & Layanan Ambulans Siaga',
                'deskripsi' => 'Mengantar pasien dhuafa lansia kontrol ke poli jantung RSUD serta mendistribusikan sembako di sekitar posko',
                'lokasi' => 'RSUD Kota Bekasi & Sekitarnya',
                'status' => 'disetujui',
            ],
        ];

        foreach ($aktivitasData as $ak) {
            AktivitasHarian::firstOrCreate(
                ['amil_id' => $ak['amil_id'], 'tanggal' => $ak['tanggal'], 'judul' => $ak['judul']],
                $ak
            );
        }

        // 13. AGENDAS & AGENDA AMIL (Minimal 5)
        $agendaData = [
            [
                'judul' => 'Briefing Mingguan Amil & Evaluasi Penghimpunan ZISWAF',
                'deskripsi' => 'Rapat koordinasi rutin seluruh amil lapangan dan manajemen kantor untuk monitoring capaian target donasi bulanan',
                'lokasi' => 'Aula Utama Kantor Pusat GSC',
                'mulai' => now()->setTime(8, 30),
                'selesai' => now()->setTime(10, 0),
                'status' => 'selesai',
                'created_by' => $adminUser?->id,
            ],
            [
                'judul' => 'Kunjungan Silaturahmi Kemitraan Donatur Korporat',
                'deskripsi' => 'Pertemuan audiensi kemitraan CSR dengan perwakilan direksi perusahaan mitra',
                'lokasi' => 'Menara BTPN Lt. 15, Mega Kuningan Jakarta',
                'mulai' => now()->addDay()->setTime(13, 0),
                'selesai' => now()->addDay()->setTime(15, 30),
                'status' => 'terjadwal',
                'created_by' => $adminUser?->id,
            ],
            [
                'judul' => 'Distribusi Paket Pangan & Sembako Jumat Berkah',
                'deskripsi' => 'Penyaluran langsung 100 paket bahan makanan pokok ke perkampungan dhuafa binaan',
                'lokasi' => 'Wilayah Binaan Kp. Cileunyi Wetan',
                'mulai' => now()->addDays(2)->setTime(9, 0),
                'selesai' => now()->addDays(2)->setTime(11, 30),
                'status' => 'terjadwal',
                'created_by' => $adminUser?->id,
            ],
            [
                'judul' => 'Layanan Siaga Ambulans Pasien Dhuafa Luar Kota',
                'deskripsi' => 'Penjemputan dan rujukan darurat pasien anak dhuafa menuju rumah sakit rujukan provinsi',
                'lokasi' => 'RSUP Dr. Hasan Sadikin Bandung',
                'mulai' => now()->addDays(3)->setTime(8, 0),
                'selesai' => now()->addDays(3)->setTime(13, 0),
                'status' => 'terjadwal',
                'created_by' => $adminUser?->id,
            ],
            [
                'judul' => 'Pelatihan & Standarisasi Fiqih Zakat Kontemporer',
                'deskripsi' => 'Workshop peningkatan kapasitas syariah amil dalam perhitungan zakat saham, reksadana, dan profesi',
                'lokasi' => 'Hotel Grand Asrilia Bandung',
                'mulai' => now()->addDays(5)->setTime(9, 0),
                'selesai' => now()->addDays(5)->setTime(16, 0),
                'status' => 'terjadwal',
                'created_by' => $adminUser?->id,
            ],
        ];

        foreach ($agendaData as $idx => $ag) {
            $agenda = Agenda::firstOrCreate(['judul' => $ag['judul']], $ag);
            // Hubungkan ke amils
            $agenda->amils()->syncWithoutDetaching([$amils[$idx % count($amils)]->id, $amils[($idx + 1) % count($amils)]->id]);
        }

        // 14. PENGUMUMAN & PENGUMUMAN DIBACA (Minimal 5)
        $pengumumanData = [
            [
                'judul' => 'Target Penghimpunan ZISWAF Kuartal Ini & Strategi Lapangan',
                'isi' => 'Diberitahukan kepada seluruh amil fundraising dan operasional bahwa target penghimpunan ZISWAF kuartal ini difokuskan pada program beasiswa yatim dan layanan ambulans gratis. Mari perkuat silaturahmi dengan para donatur tetap.',
                'target' => 'semua',
                'published_at' => now()->subDays(3),
                'created_by' => $adminUser?->id,
            ],
            [
                'judul' => 'Standar Operasional Presensi GPS & Ketepatan Jam Kerja',
                'isi' => 'Seluruh petugas amil wajib melakukan presensi masuk dan pulang melalui Portal Amil dengan memastikan GPS lokasi aktif di dalam radius kantor atau area tugas masing-masing.',
                'target' => 'semua',
                'published_at' => now()->subDays(2),
                'created_by' => $adminUser?->id,
            ],
            [
                'judul' => 'Panduan Teknis Input Donatur Baru & Cetak Kwitansi Resmi',
                'isi' => 'Mulai pekan ini setiap penerimaan donasi zakat dan infak dapat langsung diterbitkan kwitansi resmi digital ber-QR Code untuk diverifikasi keasliannya secara transparan oleh donatur.',
                'target' => 'Penghimpunan / Fundraising',
                'published_at' => now()->subDay(),
                'created_by' => $adminUser?->id,
            ],
            [
                'judul' => 'Jadwal Siaga Ambulans Gratis Kaum Dhuafa Pekan Ini',
                'isi' => 'Armada ambulans siaga standby 24 jam di posko pusat. Petugas divisi penyaluran harap memastikan kondisi bahan bakar, tabung oksigen, dan kelengkapan P3K selalu siap operasional.',
                'target' => 'Pendayagunaan & Penyaluran',
                'published_at' => now()->subHours(12),
                'created_by' => $adminUser?->id,
            ],
            [
                'judul' => 'Pemberitahuan Rekonsiliasi Kas Operasional & Penutupan Buku Akhir Bulan',
                'isi' => 'Seluruh pelaporan bukti setor donasi tunai lapangan paling lambat diserahkan ke kasir bendahara kantor setiap hari Jumat pukul 16:00 WIB untuk verifikasi sistem.',
                'target' => 'Operasional & Keuangan',
                'published_at' => now()->subHours(4),
                'created_by' => $adminUser?->id,
            ],
        ];

        foreach ($pengumumanData as $peng) {
            $p = Pengumuman::firstOrCreate(['judul' => $peng['judul']], $peng);
            // Tandai dibaca oleh user amil 1
            $p->pembacaUsers()->syncWithoutDetaching([$amils[0]->user_id => ['read_at' => now()]]);
        }

        // 15. MUSTAHIK (Minimal 5)
        $mustahikData = [
            [
                'nama' => 'Bapak Sukardi',
                'nik' => '3204120101550001',
                'alamat' => 'Kp. Cileunyi Wetan RT 03/05, Kab. Bandung',
                'asnaf' => 'fakir',
                'kondisi' => 'Lansia usia 70 tahun sebatang kara, sakit stroke ringan menahun, tidak memiliki sumber penghasilan apapun',
                'status_verifikasi' => 'terverifikasi',
            ],
            [
                'nama' => 'Ibu Aminah',
                'nik' => '3204154508720002',
                'alamat' => 'Kp. Sukamaju RT 02/08, Kab. Bandung',
                'asnaf' => 'miskin',
                'kondisi' => 'Ibu tunggal (janda) menanggung 4 orang anak usia sekolah dasar, bekerja serabutan sebagai buruh cuci keliling',
                'status_verifikasi' => 'terverifikasi',
            ],
            [
                'nama' => 'Ustadz Ahmad Baihaqi',
                'nik' => '3204181203800003',
                'alamat' => 'Kp. Mekarwangi RT 01/02, Kab. Bandung',
                'asnaf' => 'fisabilillah',
                'kondisi' => 'Guru ngaji sukarela anak-anak pelosok desa tanpa gaji tetap, mengabdi mengajar Al-Qur\'an lebih dari 15 tahun',
                'status_verifikasi' => 'terverifikasi',
            ],
            [
                'nama' => 'M. Syarifuddin',
                'nik' => '3204200507920004',
                'alamat' => 'Singgah Sementara di Halte Terminal Leuwipanjang',
                'asnaf' => 'ibnu_sabil',
                'kondisi' => 'Musafir asal Sumatra Barat yang mengalami musibah kehilangan dompet dan bekal tiket saat perjalanan transit',
                'status_verifikasi' => 'terverifikasi',
            ],
            [
                'nama' => 'David Pratama (Mualaf)',
                'nik' => '3204221109960005',
                'alamat' => 'Jl. Sindang Laya No. 14, Bandung',
                'asnaf' => 'mualaf',
                'kondisi' => 'Mualaf baru berikrar 6 bulan, diasingkan keluarga, membutuhkan bimbingan agama dan modal kerja mandiri',
                'status_verifikasi' => 'terverifikasi',
            ],
        ];

        $mustahiks = [];
        foreach ($mustahikData as $m) {
            $mustahiks[] = Mustahik::firstOrCreate(['nik' => $m['nik']], $m);
        }

        // 16. PENYALURAN (Minimal 5)
        $penyaluranData = [
            [
                'mustahik_id' => $mustahiks[0]->id,
                'program_id' => $programs[3]->id, // Pangan Lansia
                'jenis_dana_id' => $jenisDanas[0]->id, // Zakat Maal
                'jumlah' => 1500000,
                'tgl_salur' => now()->subDays(5)->toDateString(),
                'amil_id' => $amils[1]->id,
                'catatan' => 'Penyaluran santunan tunai biaya obat dan paket beras bulanan di kediaman mustahik',
            ],
            [
                'mustahik_id' => $mustahiks[1]->id,
                'program_id' => $programs[1]->id, // Beasiswa
                'jenis_dana_id' => $jenisDanas[2]->id, // Infak Terikat
                'jumlah' => 2000000,
                'tgl_salur' => now()->subDays(4)->toDateString(),
                'amil_id' => $amils[1]->id,
                'catatan' => 'Bantuan beasiswa pendidikan, pelunasan SPP dan seragam untuk 2 anak sekolah',
            ],
            [
                'mustahik_id' => $mustahiks[2]->id,
                'program_id' => null,
                'jenis_dana_id' => $jenisDanas[0]->id, // Zakat Maal
                'jumlah' => 1750000,
                'tgl_salur' => now()->subDays(3)->toDateString(),
                'amil_id' => $amils[0]->id,
                'catatan' => 'Kafalah kehormatan da\'i pelosok serta pengadaan buku Iqra dan Juz Amma untuk santri',
            ],
            [
                'mustahik_id' => $mustahiks[3]->id,
                'program_id' => null,
                'jenis_dana_id' => $jenisDanas[3]->id, // Infak Umum
                'jumlah' => 750000,
                'tgl_salur' => now()->subDays(2)->toDateString(),
                'amil_id' => $amils[0]->id,
                'catatan' => 'Bantuan darurat tiket bus antar provinsi dan bekal perjalanan menuju kampung halaman',
            ],
            [
                'mustahik_id' => $mustahiks[4]->id,
                'program_id' => $programs[2]->id, // UMKM Barokah
                'jenis_dana_id' => $jenisDanas[0]->id, // Zakat Maal
                'jumlah' => 2500000,
                'tgl_salur' => now()->subDay()->toDateString(),
                'amil_id' => $amils[1]->id,
                'catatan' => 'Modal usaha gerobak kuliner berkah dan perlengkapan ibadah mualaf',
            ],
        ];

        foreach ($penyaluranData as $p) {
            Penyaluran::firstOrCreate(
                ['mustahik_id' => $p['mustahik_id'], 'tgl_salur' => $p['tgl_salur'], 'jumlah' => $p['jumlah']],
                $p
            );
        }

        // 17. NOTIFIKASI (Minimal 5)
        $notifikasiData = [
            [
                'user_id' => $amils[0]->user_id,
                'judul' => 'Donasi Terverifikasi',
                'pesan' => 'Alhamdulillah, donasi Zakat Maal Rp 5.000.000 dari H. Hendra Wijaya telah berhasil diverifikasi oleh Administrator.',
                'tipe' => 'success',
                'url' => '/amil/donasi',
                'is_read' => false,
            ],
            [
                'user_id' => $amils[0]->user_id,
                'judul' => 'Agenda Kerja Mendatang',
                'pesan' => 'Anda dijadwalkan menghadiri agenda "Briefing Mingguan Amil & Evaluasi Penghimpunan ZISWAF" pukul 08:30 WIB.',
                'tipe' => 'info',
                'url' => '/amil/agenda',
                'is_read' => false,
            ],
            [
                'user_id' => $amils[1]->user_id,
                'judul' => 'Pengajuan Izin Disetujui',
                'pesan' => 'Surat permohonan izin/sakit Anda untuk tanggal yang diajukan telah disetujui oleh Administrator.',
                'tipe' => 'success',
                'url' => '/amil/izin-cuti',
                'is_read' => true,
                'read_at' => now()->subDay(),
            ],
            [
                'user_id' => $amils[0]->user_id,
                'judul' => 'Pengumuman Lembaga Baru',
                'pesan' => 'Terdapat pengumuman baru mengenai "Target Penghimpunan ZISWAF Kuartal Ini & Strategi Lapangan".',
                'tipe' => 'info',
                'url' => '/amil/pengumuman',
                'is_read' => false,
            ],
            [
                'user_id' => $amils[1]->user_id,
                'judul' => 'Presensi Harian Berhasil',
                'pesan' => 'Data presensi masuk dan foto selfie GPS Anda hari ini telah berhasil terekam ke sistem.',
                'tipe' => 'success',
                'url' => '/amil/absensi',
                'is_read' => false,
            ],
        ];

        foreach ($notifikasiData as $notif) {
            Notifikasi::firstOrCreate(
                ['user_id' => $notif['user_id'], 'judul' => $notif['judul'], 'pesan' => $notif['pesan']],
                $notif
            );
        }
    }
}
