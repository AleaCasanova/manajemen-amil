<?php

namespace Tests\Feature;

use App\Models\AkunKeuangan;
use App\Models\Amil;
use App\Models\KategoriKeuangan;
use App\Models\PengajuanDana;
use App\Models\Program;
use App\Models\User;
use App\Services\PengajuanDanaPdfService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PengajuanDanaTest extends TestCase
{
    use DatabaseTransactions;

    protected User $adminUser;

    protected User $amilUser;

    protected Amil $amil;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'admin']);
        Role::firstOrCreate(['name' => 'Amil', 'guard_name' => 'web']);

        $this->adminUser = User::firstOrCreate(
            ['email' => 'admin_pengajuan_test@example.com'],
            ['name' => 'Manager Admin', 'password' => bcrypt('password'), 'is_active' => true, 'role_id' => 1]
        );
        $this->adminUser->syncRoles(['Admin']);

        $this->amilUser = User::firstOrCreate(
            ['email' => 'amil_pengajuan_test@example.com'],
            ['name' => 'Amil Lapangan Test', 'password' => bcrypt('password'), 'is_active' => true, 'role_id' => 2]
        );
        $this->amilUser->syncRoles(['Amil']);

        $this->amil = Amil::firstOrCreate(
            ['user_id' => $this->amilUser->id],
            [
                'nip' => 'AML-TEST-0099',
                'nama' => 'Amil Lapangan Test',
                'jabatan' => 'Staf Program & Penyaluran',
                'status' => 'aktif',
            ]
        );
    }

    public function test_amil_can_submit_pengajuan_dana_with_official_reference(): void
    {
        $program = Program::first() ?? Program::create([
            'nama' => 'Santunan Yatim Pengujian',
            'target_nominal' => 10000000,
            'status' => 'aktif',
        ]);

        $kategori = KategoriKeuangan::where('jenis', 'keluar')->first() ?? KategoriKeuangan::create([
            'nama' => 'Penyaluran Program Uji',
            'jenis' => 'keluar',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->amilUser)
            ->post(route('amil.pengajuan-dana.store'), [
                'judul' => 'Pengadaan Paket Santunan Mustahik',
                'nominal_diajukan' => 2500000,
                'tgl_kebutuhan' => now()->addDays(2)->toDateString(),
                'program_id' => $program->id,
                'kategori_keuangan_id' => $kategori->id,
                'nomor_dokumen_referensi' => 'MEMO/GSC/2026/088',
                'deskripsi' => "Rincian RAB:\n1. 25 Paket Sembako @ Rp 100.000 = Rp 2.500.000",
            ]);

        $this->assertDatabaseHas('pengajuan_danas', [
            'amil_id' => $this->amil->id,
            'judul' => 'Pengadaan Paket Santunan Mustahik',
            'nominal_diajukan' => 2500000,
            'nomor_dokumen_referensi' => 'MEMO/GSC/2026/088',
            'status' => 'diajukan',
        ]);

        $pengajuan = PengajuanDana::where('nomor_dokumen_referensi', 'MEMO/GSC/2026/088')->first();
        $this->assertNotNull($pengajuan);
        $this->assertStringStartsWith('PR-'.now()->format('Ym').'-', $pengajuan->nomor_pengajuan);
    }

    public function test_bendahara_and_manager_workflow_approval(): void
    {
        $pengajuan = PengajuanDana::create([
            'amil_id' => $this->amil->id,
            'judul' => 'BBM & Servis Ambulans',
            'nominal_diajukan' => 750000,
            'tgl_kebutuhan' => now()->toDateString(),
            'deskripsi' => 'Penggantian oli mesin & BBM antar pasien',
            'status' => 'diajukan',
        ]);

        // 1. Verifikasi Bendahara
        $pengajuan->verifikasiBendahara($this->adminUser->id, 'Saldo kas operasional mencukupi. Teruskan ke Manager.');
        $this->assertEquals('diverifikasi_bendahara', $pengajuan->fresh()->status);
        $this->assertEquals($this->adminUser->id, $pengajuan->fresh()->bendahara_id);

        // 2. ACC Manager
        $pengajuan->accManager($this->adminUser->id, 700000, 'Disetujui disesuaikan menjadi Rp 700.000.');
        $this->assertEquals('disetujui', $pengajuan->fresh()->status);
        $this->assertEquals(700000, (float) $pengajuan->fresh()->nominal_disetujui);
        $this->assertEquals($this->adminUser->id, $pengajuan->fresh()->manager_id);
    }

    public function test_cairkan_dana_automatically_records_to_buku_kas(): void
    {
        $akun = AkunKeuangan::firstOrCreate(
            ['nama' => 'Kas Brankas Uji'],
            ['tipe' => 'kas', 'saldo_awal' => 5000000, 'is_active' => true]
        );

        $kategori = KategoriKeuangan::where('jenis', 'keluar')->first() ?? KategoriKeuangan::create([
            'nama' => 'Operasional Uji',
            'jenis' => 'keluar',
            'is_active' => true,
        ]);

        $pengajuan = PengajuanDana::create([
            'amil_id' => $this->amil->id,
            'judul' => 'Pengadaan ATK Kegiatan',
            'nominal_diajukan' => 500000,
            'nominal_disetujui' => 500000,
            'tgl_kebutuhan' => now()->toDateString(),
            'kategori_keuangan_id' => $kategori->id,
            'deskripsi' => 'Kertas, map, dan tinta printer',
            'status' => 'disetujui',
        ]);

        $transaksi = $pengajuan->cairkan($this->adminUser->id, $akun->id, 'tunai');

        $this->assertEquals('dicairkan', $pengajuan->fresh()->status);
        $this->assertNotNull($pengajuan->fresh()->transaksi_keuangan_id);

        // Pastikan transaksi keluar tercatat di tabel Buku Kas (transaksi_keuangans)
        $this->assertDatabaseHas('transaksi_keuangans', [
            'id' => $transaksi->id,
            'jenis' => 'keluar',
            'akun_keuangan_id' => $akun->id,
            'nominal' => 500000,
            'pihak_terkait' => $this->amil->nama,
        ]);
    }

    public function test_pdf_service_can_generate_official_pengajuan_form(): void
    {
        $pengajuan = PengajuanDana::create([
            'amil_id' => $this->amil->id,
            'judul' => 'Pengujian Cetak Dokumen PDF Resmi',
            'nominal_diajukan' => 1200000,
            'tgl_kebutuhan' => now()->toDateString(),
            'nomor_dokumen_referensi' => 'REF-PDF-TEST-01',
            'deskripsi' => 'Pengujian cetak formulir resmi BKK',
            'status' => 'diajukan',
        ]);

        $service = new PengajuanDanaPdfService;
        $pdf = $service->generatePdf($pengajuan);

        $this->assertNotNull($pdf->output());
    }

    public function test_admin_can_access_filament_pengajuan_dana_resource(): void
    {
        $adminPath = config('filament.admin_path', 'portal-internal-gsc');
        $response = $this->actingAs($this->adminUser, 'admin')
            ->get('/'.$adminPath.'/pengajuan-danas');

        $response->assertStatus(200);
    }
}
