<?php

namespace Tests\Feature;

use App\Exports\TransaksiKeuanganExport;
use App\Filament\Resources\TransaksiKeuangans\Pages\CreateTransaksiKeuangan;
use App\Models\AkunKeuangan;
use App\Models\KategoriKeuangan;
use App\Models\TransaksiKeuangan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BukuKasKeuanganTest extends TestCase
{
    use DatabaseTransactions;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'admin']);

        $this->adminUser = User::firstOrCreate(
            ['email' => 'admin_keuangan_test@example.com'],
            ['name' => 'Admin Keuangan Test', 'password' => bcrypt('password'), 'is_active' => true, 'role_id' => 1]
        );
        $this->adminUser->syncRoles(['Admin']);
    }

    public function test_akun_keuangan_saldo_calculation(): void
    {
        $akun = AkunKeuangan::create([
            'nama' => 'Kas Uji Coba',
            'tipe' => 'kas',
            'saldo_awal' => 500000,
            'is_active' => true,
        ]);

        $kategoriMasuk = KategoriKeuangan::create([
            'nama' => 'Pemasukan Uji',
            'jenis' => 'masuk',
            'is_active' => true,
        ]);

        $kategoriKeluar = KategoriKeuangan::create([
            'nama' => 'Pengeluaran Uji',
            'jenis' => 'keluar',
            'is_active' => true,
        ]);

        // Catat Masuk 200.000
        TransaksiKeuangan::create([
            'jenis' => 'masuk',
            'kategori_keuangan_id' => $kategoriMasuk->id,
            'akun_keuangan_id' => $akun->id,
            'nominal' => 200000,
            'tgl_transaksi' => now()->toDateString(),
            'keterangan' => 'Donasi Tunai Masuk',
        ]);

        // Catat Keluar 50.000
        TransaksiKeuangan::create([
            'jenis' => 'keluar',
            'kategori_keuangan_id' => $kategoriKeluar->id,
            'akun_keuangan_id' => $akun->id,
            'nominal' => 50000,
            'tgl_transaksi' => now()->toDateString(),
            'keterangan' => 'Beli Bensin',
        ]);

        // Saldo sekarang: 500.000 + 200.000 - 50.000 = 650.000
        $this->assertEquals(650000, $akun->fresh()->saldo_sekarang);
    }

    public function test_auto_generate_kode_transaksi(): void
    {
        $akun = AkunKeuangan::first() ?? AkunKeuangan::create([
            'nama' => 'Kas Otomatis Kode',
            'tipe' => 'kas',
            'saldo_awal' => 0,
            'is_active' => true,
        ]);

        $kategori = KategoriKeuangan::first() ?? KategoriKeuangan::create([
            'nama' => 'Pos Otomatis',
            'jenis' => 'masuk',
            'is_active' => true,
        ]);

        $trx1 = TransaksiKeuangan::create([
            'jenis' => 'masuk',
            'kategori_keuangan_id' => $kategori->id,
            'akun_keuangan_id' => $akun->id,
            'nominal' => 100000,
            'tgl_transaksi' => now()->toDateString(),
            'keterangan' => 'Transaksi Tes 1',
        ]);

        $trx2 = TransaksiKeuangan::create([
            'jenis' => 'masuk',
            'kategori_keuangan_id' => $kategori->id,
            'akun_keuangan_id' => $akun->id,
            'nominal' => 150000,
            'tgl_transaksi' => now()->toDateString(),
            'keterangan' => 'Transaksi Tes 2',
        ]);

        $prefix = 'TRX-'.now()->format('Ym').'-';
        $this->assertStringStartsWith($prefix, $trx1->kode_transaksi);
        $this->assertStringStartsWith($prefix, $trx2->kode_transaksi);
        $this->assertNotEquals($trx1->kode_transaksi, $trx2->kode_transaksi);
    }

    public function test_admin_can_access_buku_kas_resource(): void
    {
        $adminPath = config('filament.admin_path', 'portal-internal-gsc');
        $response = $this->actingAs($this->adminUser, 'admin')
            ->get('/'.$adminPath.'/transaksi-keuangans');

        $response->assertStatus(200);
    }

    public function test_transaksi_keuangan_export(): void
    {
        $export = new TransaksiKeuanganExport;
        $headings = $export->headings();

        $this->assertContains('Kode Transaksi', $headings);
        $this->assertContains('Nominal Masuk (Rp)', $headings);
        $this->assertContains('Nominal Keluar (Rp)', $headings);
    }

    public function test_admin_can_create_transaksi_keuangan_via_livewire(): void
    {
        $akun = AkunKeuangan::firstOrCreate(
            ['nama' => 'Kas Livewire Test'],
            ['tipe' => 'kas', 'saldo_awal' => 1000000, 'is_active' => true]
        );

        $kategori = KategoriKeuangan::firstOrCreate(
            ['nama' => 'Pemasukan Livewire Test', 'jenis' => 'masuk'],
            ['deskripsi' => 'Pos uji coba livewire', 'is_active' => true]
        );

        $this->actingAs($this->adminUser, 'admin');

        Livewire::test(CreateTransaksiKeuangan::class)
            ->set('data.jenis', 'masuk')
            ->set('data.akun_keuangan_id', $akun->id)
            ->set('data.kategori_keuangan_id', $kategori->id)
            ->set('data.nominal', 350000)
            ->set('data.tgl_transaksi', now()->toDateString())
            ->set('data.pihak_terkait', 'Donatur Hamba Allah')
            ->set('data.keterangan', 'Pemasukan Kas ZISWAF langsung di kasir')
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('transaksi_keuangans', [
            'nominal' => 350000,
            'keterangan' => 'Pemasukan Kas ZISWAF langsung di kasir',
        ]);
    }
}
