<?php

namespace Database\Seeders;

use App\Models\Amil;
use App\Models\Divisi;
use App\Models\SaldoCuti;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RealDataSeeder extends Seeder
{
    /**
     * Bersihkan seluruh data transaksi dummy dan masukkan data pegawai/relawan/magang real.
     */
    public function run(): void
    {
        // 1. Nonaktifkan foreign key checks sementara untuk pembersihan bersih
        Schema::disableForeignKeyConstraints();

        // Bersihkan tabel transaksi & aktivitas dummy
        $tablesToTruncate = [
            'donasi_barangs',
            'donasis',
            'donaturs',
            'absensis',
            'izin_cutis',
            'saldo_cutis',
            'aktivitas_harians',
            'agenda_amil',
            'agendas',
            'pengumuman_dibaca',
            'pengumumans',
            'penyalurans',
            'mustahiks',
            'notifikasis',
            'amils',
            'model_has_roles',
            'model_has_permissions',
        ];

        foreach ($tablesToTruncate as $tableName) {
            if (Schema::hasTable($tableName)) {
                DB::table($tableName)->truncate();
            }
        }

        // Hapus user lama yang bukan admin baru
        if (Schema::hasTable('users')) {
            // Hapus semua user dummy
            DB::table('users')->delete();
        }

        Schema::enableForeignKeyConstraints();

        // 2. Roles & Permissions Spatie
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

        $adminRole->syncPermissions(Permission::all());
        $amilRole->syncPermissions([
            'absen',
            'input-donatur',
            'input-donasi',
            'laporan-aktivitas',
            'ajukan-izin-cuti',
        ]);

        // 3. Buat Akun Super Admin baru: admin@gmail.com / password123
        $adminUser = User::create([
            'name' => 'Administrator Amil',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password123'),
            'role_id' => $adminRole->id,
            'is_active' => true,
        ]);
        $adminUser->syncRoles([$adminRole]);

        // 4. Siapkan Master Data Divisi Real Yayasan GSC
        $divisiDefs = [
            'keuangan' => [
                'nama' => 'Keuangan',
                'deskripsi' => 'Divisi pengelolaan keuangan, akuntansi, dan pembukuan yayasan',
            ],
            'fundraising' => [
                'nama' => 'Fundraising',
                'deskripsi' => 'Divisi penghimpunan dana ZISWAF dan kemitraan donatur',
            ],
            'kesehatan' => [
                'nama' => 'Kesehatan',
                'deskripsi' => 'Divisi pelayanan ambulans siaga dan kesehatan umat',
            ],
            'program' => [
                'nama' => 'Program',
                'deskripsi' => 'Divisi pendistribusian, penyaluran program, dan pemberdayaan umat',
            ],
            'manajemen' => [
                'nama' => 'Manajemen',
                'deskripsi' => 'Divisi pimpinan eksekutif dan manajerial yayasan',
            ],
            'donasi' => [
                'nama' => 'Donasi',
                'deskripsi' => 'Divisi penerimaan donasi dan relawan lapangan',
            ],
            'it' => [
                'nama' => 'IT',
                'deskripsi' => 'Divisi teknologi informasi, sistem aplikasi, dan digitalisasi',
            ],
        ];

        $divisiModels = [];
        $allowedNames = [];
        foreach ($divisiDefs as $key => $dData) {
            $allowedNames[] = $dData['nama'];
            $divisiModels[$key] = Divisi::updateOrCreate(
                ['nama' => $dData['nama']],
                ['deskripsi' => $dData['deskripsi']]
            );
        }

        // Hapus divisi lama dummy yang tidak ada di daftar 7 divisi real resmi
        Divisi::whereNotIn('nama', $allowedNames)->delete();

        // 5. Data 24 Pegawai Real GSC
        $pegawaiList = [
            [
                'nip' => 'AML-001',
                'nama' => 'Kholifatun Mualfiyah',
                'jabatan' => 'staff',
                'divisi' => 'keuangan',
                'no_wa' => '085799538544',
                'alamat' => 'Jl. Satria Rt 04 Rw 10, kel. Tritih kulon',
                'email' => 'kholifatun@gmail.com',
                'target_bulanan' => 10000000,
            ],
            [
                'nip' => 'AML-002',
                'nama' => 'Wiji Susanti',
                'jabatan' => 'staff',
                'divisi' => 'fundraising',
                'no_wa' => '085894374285',
                'alamat' => 'Jl. Damar',
                'email' => 'wiji@gmail.com',
                'target_bulanan' => 20000000,
            ],
            [
                'nip' => 'AML-003',
                'nama' => 'Ujang',
                'jabatan' => 'driver',
                'divisi' => 'kesehatan',
                'no_wa' => '0882005532646',
                'alamat' => 'Patimuan',
                'email' => 'ujang@gmail.com',
                'target_bulanan' => 0,
            ],
            [
                'nip' => 'AML-004',
                'nama' => 'Fahri Azzuhair',
                'jabatan' => 'staff',
                'divisi' => 'fundraising',
                'no_wa' => '085642196488',
                'alamat' => 'Jl. Thamrin Gg. Juliet no 66',
                'email' => 'fahri@gmail.com',
                'target_bulanan' => 20000000,
            ],
            [
                'nip' => 'AML-005',
                'nama' => 'Ahmad Robbani Izzal',
                'jabatan' => 'staff',
                'divisi' => 'kesehatan',
                'no_wa' => '087848792503',
                'alamat' => 'Jl. Abdul Syukur Gg. Blanak 14, Kel. Margadana, Kec. Margadana Kota Tegal',
                'email' => 'ahmad@gmail.com',
                'target_bulanan' => 10000000,
            ],
            [
                'nip' => 'AML-006',
                'nama' => 'Naffisa Putri Azzahra',
                'jabatan' => 'staff',
                'divisi' => 'fundraising',
                'no_wa' => '085878216139',
                'alamat' => 'jl bali gang ujung pandang no 19',
                'email' => 'naffisa@gmail.com',
                'target_bulanan' => 20000000,
            ],
            [
                'nip' => 'AML-007',
                'nama' => 'Rafi Ramadhan',
                'jabatan' => 'driver',
                'divisi' => 'kesehatan',
                'no_wa' => '085712438351',
                'alamat' => 'Jl. ganggeng timur no 24 mertasinga Cilacap utara',
                'email' => 'rafi@gmail.com',
                'target_bulanan' => 0,
            ],
            [
                'nip' => 'AML-008',
                'nama' => 'Rista Amalia Salsabila',
                'jabatan' => 'staff',
                'divisi' => 'program',
                'no_wa' => '08772871193',
                'alamat' => 'Perum Grand Mahkota Sultan Blok L 75 RT 05/RW 15 , Desa Tritih Lor , Kecamatan Jeruklegi',
                'email' => 'rista@gmail.com',
                'target_bulanan' => 15000000,
            ],
            [
                'nip' => 'AML-009',
                'nama' => 'Endang Kuswanto',
                'jabatan' => 'ketua',
                'divisi' => 'manajemen',
                'no_wa' => '081315173737',
                'alamat' => 'Jl. Laban No.32 Rt 04 Rw 10 Cilacap Tengah',
                'email' => 'endang@gmail.com',
                'target_bulanan' => 25000000,
            ],
            [
                'nip' => 'AML-010',
                'nama' => 'Faishal Hanif',
                'jabatan' => 'staff',
                'divisi' => 'program',
                'no_wa' => '085782114624',
                'alamat' => 'Jl. Sulawesi Perum Puri Tanjung Intan Cilacap Tengah',
                'email' => 'faishal@gmail.com',
                'target_bulanan' => 15000000,
            ],
            [
                'nip' => 'AML-011',
                'nama' => 'Mini',
                'jabatan' => 'relawan',
                'divisi' => 'donasi',
                'no_wa' => '0895337295056',
                'alamat' => 'Jl. Wayuno 39 rt 02 rw 01 Tritih Kulon, Cilacap Utara',
                'email' => 'mini@gmail.com',
                'target_bulanan' => 0,
            ],
            [
                'nip' => 'AML-012',
                'nama' => 'Yeti Lufanti',
                'jabatan' => 'relawan',
                'divisi' => 'donasi',
                'no_wa' => '088233154527',
                'alamat' => 'Brebeg rt.01/rw.08',
                'email' => 'yeti@gmail.com',
                'target_bulanan' => 0,
            ],
            [
                'nip' => 'AML-013',
                'nama' => 'Bu Kukuh',
                'jabatan' => 'relawan',
                'divisi' => 'donasi',
                'no_wa' => '0895325606277',
                'alamat' => 'Jl. Lombok GG. Seribu RT.01 RW.XI Gunung Simping',
                'email' => 'bukukuh@gmail.com',
                'target_bulanan' => 0,
            ],
            [
                'nip' => 'AML-014',
                'nama' => 'Samijo',
                'jabatan' => 'relawan',
                'divisi' => 'donasi',
                'no_wa' => '0815581115506',
                'alamat' => 'Jl. Jendral Sudirman, RT 03/07, Kroya, Cilacap',
                'email' => 'samijo@gmail.com',
                'target_bulanan' => 0,
            ],
            [
                'nip' => 'AML-015',
                'nama' => 'Umi Salamah',
                'jabatan' => 'relawan',
                'divisi' => 'donasi',
                'no_wa' => '085877213709',
                'alamat' => 'Jl. Kendeng, RT.03/07, Kuripan, Kesugihan, Cilacap',
                'email' => 'umi@gmail.com',
                'target_bulanan' => 0,
            ],
            [
                'nip' => 'AML-016',
                'nama' => 'Solimah',
                'jabatan' => 'relawan',
                'divisi' => 'donasi',
                'no_wa' => '081314179473',
                'alamat' => 'Jl. Tancang 2 Rt 06/13 c',
                'email' => 'solimah@gmail.com',
                'target_bulanan' => 0,
            ],
            [
                'nip' => 'AML-017',
                'nama' => 'Siti Nur Habibah',
                'jabatan' => 'relawan',
                'divisi' => 'donasi',
                'no_wa' => '0895416940409',
                'alamat' => 'Jl. Serayu Raya, Rt 03/06, Kesugihan Kidul',
                'email' => 'siti@gmail.com',
                'target_bulanan' => 0,
            ],
            [
                'nip' => 'AML-018',
                'nama' => 'Jenita BIMBA',
                'jabatan' => 'relawan',
                'divisi' => 'donasi',
                'no_wa' => '088227357180',
                'alamat' => 'Jalan Pramuka Bakti Rt 2 Rw 3 Mulyadadi',
                'email' => 'jenita@gmail.com',
                'target_bulanan' => 0,
            ],
            [
                'nip' => 'AML-019',
                'nama' => 'Tri Rustini',
                'jabatan' => 'relawan',
                'divisi' => 'donasi',
                'no_wa' => '088902824863',
                'alamat' => 'Jalan Blekok Timur Rt 5 Rw 2 Tegalreja Cilacap Selatan',
                'email' => 'tri@gmail.com',
                'target_bulanan' => 0,
            ],
            [
                'nip' => 'AML-020',
                'nama' => 'Alea Casanova',
                'jabatan' => 'magang',
                'divisi' => 'it',
                'no_wa' => '085974165996',
                'alamat' => 'Jl. Raya Kubangkangkung, Kawunganten, Cilacap',
                'email' => 'alea@gmail.com',
                'target_bulanan' => 0,
            ],
            [
                'nip' => 'AML-021',
                'nama' => 'Rifka Febiani',
                'jabatan' => 'magang',
                'divisi' => 'it',
                'no_wa' => '0895358011008',
                'alamat' => 'Jl. Truntung Rt 02 Rw 13 Tritih Kulon, Cilacap Utara, Cilacap',
                'email' => 'rifka@gmail.com',
                'target_bulanan' => 0,
            ],
            [
                'nip' => 'AML-022',
                'nama' => 'Annisa Nur Afdila',
                'jabatan' => 'magang',
                'divisi' => 'it',
                'no_wa' => '085176906860',
                'alamat' => 'Jl. Kendeng, Kuripan, Kesugihan',
                'email' => 'annisa@gmail.com',
                'target_bulanan' => 0,
            ],
            [
                'nip' => 'AML-023',
                'nama' => 'Nuke Zahra Alifia',
                'jabatan' => 'magang',
                'divisi' => 'it',
                'no_wa' => '089669651069',
                'alamat' => 'Jalan Letnan Sutrisno Rt 01/04 Pesanggaran Kesugihan Cilacap',
                'email' => 'nuke@gmail.com',
                'target_bulanan' => 0,
            ],
            [
                'nip' => 'AML-024',
                'nama' => 'Nazril Andhika Aulia Muhendi',
                'jabatan' => 'magang',
                'divisi' => 'it',
                'no_wa' => '08956346789045',
                'alamat' => 'Jl. Flores No. 4, Cilacap Tengah',
                'email' => 'nazril@gmail.com',
                'target_bulanan' => 0,
            ],
        ];

        foreach ($pegawaiList as $p) {
            // 5a. Buat Akun User
            $user = User::create([
                'name' => $p['nama'],
                'email' => $p['email'],
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]);
            $user->syncRoles([$amilRole]);

            // 5b. Buat Profil Amil
            $divisiModel = $divisiModels[$p['divisi']] ?? null;

            $amil = Amil::create([
                'user_id' => $user->id,
                'nip' => $p['nip'],
                'nama' => $p['nama'],
                'jabatan' => $p['jabatan'],
                'divisi_id' => $divisiModel?->id,
                'no_hp' => $p['no_wa'],
                'alamat' => $p['alamat'],
                'tgl_bergabung' => '2026-01-01',
                'target_bulanan' => $p['target_bulanan'] ?? 0,
                'status' => 'aktif',
            ]);

            // 5c. Saldo Cuti 12 Hari untuk tahun berjalan
            SaldoCuti::create([
                'amil_id' => $amil->id,
                'tahun' => now()->year,
                'jatah' => 12,
                'terpakai' => 0,
            ]);
        }
    }
}
