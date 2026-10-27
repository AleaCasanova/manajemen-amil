<?php

namespace Database\Seeders;

use App\Models\Divisi;
use App\Models\DonaturKategori;
use App\Models\JamKerja;
use App\Models\JenisDana;
use App\Models\LokasiKantor;
use App\Models\Program;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Roles & Permissions (Spatie)
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $amilRole = Role::firstOrCreate(['name' => 'Amil']);

        $permissions = [
            'kelola-semua',
            'kelola-amil',
            'kelola-divisi',
            'kelola-jam-kerja',
            'kelola-lokasi',
            'absen',
            'input-donatur',
            'input-donasi',
            'verifikasi-donasi',
            'laporan-aktivitas',
            'ajukan-izin-cuti',
            'persetujuan-izin-cuti',
            'kelola-agenda',
            'kelola-pengumuman',
            'kelola-mustahik',
            'kelola-penyaluran',
            'kelola-settings',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Admin mendapatkan semua permission
        $adminRole->syncPermissions(Permission::all());

        // Amil mendapatkan permission spesifik amil
        $amilRole->syncPermissions([
            'absen',
            'input-donatur',
            'input-donasi',
            'laporan-aktivitas',
            'ajukan-izin-cuti',
        ]);

        // 2. Default User Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Administrator Amil',
                'password' => Hash::make('password123'),
                'role_id' => $adminRole->id,
                'is_active' => true,
            ]
        );
        $admin->syncRoles([$adminRole]);

        // 3. Default Divisi
        $divisiPenghimpunan = Divisi::firstOrCreate(
            ['nama' => 'Penghimpunan / Fundraising'],
            ['deskripsi' => 'Divisi penghimpunan dana ZISWAF dan kemitraan donatur']
        );
        $divisiPenyaluran = Divisi::firstOrCreate(
            ['nama' => 'Pendayagunaan & Penyaluran'],
            ['deskripsi' => 'Divisi penyaluran manfaat dan program ke mustahik']
        );
        $divisiOperasional = Divisi::firstOrCreate(
            ['nama' => 'Operasional & Keuangan'],
            ['deskripsi' => 'Divisi administrasi operasional, SDM, dan keuangan']
        );

        // 4. Default Jam Kerja & Lokasi Kantor
        JamKerja::firstOrCreate(
            ['nama' => 'Jam Kerja Reguler'],
            [
                'jam_masuk' => '08:00:00',
                'jam_pulang' => '16:30:00',
                'toleransi_menit' => 15,
                'is_active' => true,
            ]
        );

        LokasiKantor::firstOrCreate(
            ['nama_lokasi' => 'Kantor Pusat GSC'],
            [
                'alamat' => 'Jl. Gerak Sedekah Cilacap No. 01, Cilacap, Jawa Tengah',
                'lat' => -6.20876340,
                'lng' => 106.84559900,
                'radius_meter' => 100,
                'is_active' => true,
            ]
        );

        // 5. Default Donatur Kategori
        DonaturKategori::firstOrCreate(
            ['nama' => 'Perorangan / Retail'],
            ['deskripsi' => 'Donatur individu / perorangan masyarakat umum']
        );
        DonaturKategori::firstOrCreate(
            ['nama' => 'Korporat / Lembaga / Perusahaan'],
            ['deskripsi' => 'Donatur dari perusahaan, CSR, atau instansi']
        );
        DonaturKategori::firstOrCreate(
            ['nama' => 'Komunitas / Komunitas Mitra'],
            ['deskripsi' => 'Donatur dari kelompok hobi, komunitas, atau paguyuban']
        );

        // 6. Default Jenis Dana
        JenisDana::firstOrCreate(
            ['nama' => 'Zakat Maal'],
            [
                'kelompok' => 'zakat',
                'hak_amil_persen' => 12.50,
                'deskripsi' => 'Zakat harta, tabungan, perniagaan, emas, dll',
            ]
        );
        JenisDana::firstOrCreate(
            ['nama' => 'Zakat Fitrah'],
            [
                'kelompok' => 'zakat',
                'hak_amil_persen' => 12.50,
                'deskripsi' => 'Zakat fitrah Ramadhan',
            ]
        );
        JenisDana::firstOrCreate(
            ['nama' => 'Infak / Sedekah Terikat (Program)'],
            [
                'kelompok' => 'infak',
                'hak_amil_persen' => 20.00,
                'deskripsi' => 'Infak yang dialokasikan khusus untuk program tertentu',
            ]
        );
        JenisDana::firstOrCreate(
            ['nama' => 'Infak / Sedekah Umum'],
            [
                'kelompok' => 'infak',
                'hak_amil_persen' => 20.00,
                'deskripsi' => 'Infak tidak terikat untuk kemaslahatan umum',
            ]
        );
        JenisDana::firstOrCreate(
            ['nama' => 'DSKL (Dana Sosial Keagamaan Lainnya)'],
            [
                'kelompok' => 'dskl',
                'hak_amil_persen' => 10.00,
                'deskripsi' => 'Dana sosial keagamaan di luar zakat dan infak',
            ]
        );

        // 7. Default Program
        Program::firstOrCreate(
            ['nama' => 'Layanan Ambulans Siaga Gratis'],
            [
                'deskripsi' => 'Penyediaan dan operasional ambulans gratis untuk kaum dhuafa dan pasien darurat',
                'target_dana' => 150000000,
                'tgl_mulai' => now()->startOfYear(),
                'tgl_selesai' => now()->endOfYear(),
                'status' => 'aktif',
            ]
        );
        Program::firstOrCreate(
            ['nama' => 'Beasiswa Generasi Cerdas Mandiri'],
            [
                'deskripsi' => 'Bantuan beasiswa pendidikan anak yatim dan dhuafa',
                'target_dana' => 100000000,
                'tgl_mulai' => now()->startOfYear(),
                'tgl_selesai' => now()->endOfYear(),
                'status' => 'aktif',
            ]
        );
        Program::firstOrCreate(
            ['nama' => 'Pemberdayaan UMKM Barokah'],
            [
                'deskripsi' => 'Program modal usaha bergulir dan pembinaan usaha mustahik',
                'target_dana' => 75000000,
                'tgl_mulai' => now()->startOfYear(),
                'tgl_selesai' => now()->endOfYear(),
                'status' => 'aktif',
            ]
        );

        // 8. Default Settings
        $settings = [
            ['key' => 'app_name', 'value' => 'Sistem Manajemen Amil GSC', 'grup' => 'general', 'deskripsi' => 'Nama Aplikasi'],
            ['key' => 'app_institution', 'value' => 'Yayasan Gerak Sedekah Cilacap (GSC)', 'grup' => 'general', 'deskripsi' => 'Nama Lembaga'],
            ['key' => 'app_phone', 'value' => '0852-9000-xxxx', 'grup' => 'contact', 'deskripsi' => 'No Telepon / WhatsApp Resmi'],
            ['key' => 'app_email', 'value' => 'info@geraksedekahcilacap.org', 'grup' => 'contact', 'deskripsi' => 'Email Resmi Lembaga'],
            ['key' => 'app_address', 'value' => 'Jalan Sulawesi, Perum Puri Tanjung Intan No.B2, Karang Lor, Gunungsimping, Cilacap Tengah 53224', 'grup' => 'contact', 'deskripsi' => 'Alamat Kantor'],
            ['key' => 'default_annual_leave_days', 'value' => '12', 'grup' => 'hrd', 'deskripsi' => 'Jatah Cuti Tahunan Amil per Tahun'],
            ['key' => 'receipt_prefix', 'value' => 'KWT-GSC', 'grup' => 'donation', 'deskripsi' => 'Prefix Nomor Kwitansi'],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'grup' => $setting['grup'],
                    'deskripsi' => $setting['deskripsi'],
                ]
            );
        }

        // 9. Data Pegawai Real GSC & Pembersihan Data Dummy
        $this->call([
            RealDataSeeder::class,
        ]);
    }
}
