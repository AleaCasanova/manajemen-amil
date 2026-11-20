<?php

namespace Tests\Feature;

use App\Filament\Widgets\DonasiProgramChartWidget;
use App\Models\Amil;
use App\Models\Divisi;
use App\Models\Donasi;
use App\Models\Donatur;
use App\Models\DonaturKategori;
use App\Models\JenisDana;
use App\Models\Program;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DonasiProgramChartWidgetTest extends TestCase
{
    protected User $adminUser;

    protected Amil $amil;

    protected Donatur $donatur;

    protected JenisDana $jenisDana;

    protected function setUp(): void
    {
        parent::setUp();

        Donasi::query()->forceDelete();
        Program::query()->forceDelete();

        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $this->adminUser = User::firstOrCreate(
            ['email' => 'admin_test_widget@example.com'],
            ['name' => 'Admin Test Widget', 'password' => bcrypt('password'), 'is_active' => true, 'role_id' => 1]
        );
        $this->adminUser->syncRoles(['Admin']);

        $divisi = Divisi::firstOrCreate(['nama' => 'Fundraising'], ['deskripsi' => 'Divisi Fundraising']);
        $amilUser = User::firstOrCreate(
            ['email' => 'amil_widget@example.com'],
            ['name' => 'Amil Widget', 'password' => bcrypt('password'), 'is_active' => true, 'role_id' => 2]
        );
        $this->amil = Amil::firstOrCreate(
            ['user_id' => $amilUser->id],
            ['nama' => 'Amil Widget', 'divisi_id' => $divisi->id, 'nip' => 'AML-WDG-01', 'status' => 'aktif']
        );

        $kategori = DonaturKategori::firstOrCreate(['nama' => 'Perorangan'], ['deskripsi' => 'Donatur Individu']);
        $this->donatur = Donatur::firstOrCreate(
            ['no_hp' => '08999999901'],
            [
                'kode_donatur' => 'DNT-WDG-001',
                'nama' => 'Donatur Program Test',
                'jenis' => 'individu',
                'kategori_id' => $kategori->id,
            ]
        );

        $this->jenisDana = JenisDana::firstOrCreate(['nama' => 'Infak / Sedekah Terikat (Program)']);
    }

    public function test_widget_renders_empty_state_when_no_program_donations(): void
    {
        $this->actingAs($this->adminUser);

        $currentMonthName = now()->translatedFormat('F Y');

        Livewire::test(DonasiProgramChartWidget::class)
            ->assertSuccessful()
            ->assertSee('Donasi per program')
            ->assertSee("Belum ada donasi program ({$currentMonthName})");
    }

    public function test_widget_displays_program_donations_aggregated_and_sorted(): void
    {
        $this->actingAs($this->adminUser);

        $programA = Program::create(['nama' => 'Ambulans Siaga']);
        $programB = Program::create(['nama' => 'Beasiswa Yatim']);

        // Donasi Program A (Total Rp 3.000.000)
        Donasi::create([
            'no_kwitansi' => 'KWT-TST-001',
            'donatur_id' => $this->donatur->id,
            'amil_id' => $this->amil->id,
            'jenis_dana_id' => $this->jenisDana->id,
            'program_id' => $programA->id,
            'jumlah' => 3000000,
            'metode_bayar' => 'transfer',
            'tgl_donasi' => now()->toDateString(),
            'status' => 'terverifikasi',
        ]);

        // Donasi Program B (Total Rp 5.000.000)
        Donasi::create([
            'no_kwitansi' => 'KWT-TST-002',
            'donatur_id' => $this->donatur->id,
            'amil_id' => $this->amil->id,
            'jenis_dana_id' => $this->jenisDana->id,
            'program_id' => $programB->id,
            'jumlah' => 5000000,
            'metode_bayar' => 'transfer',
            'tgl_donasi' => now()->toDateString(),
            'status' => 'terverifikasi',
        ]);

        // Donasi belum terverifikasi (tidak boleh masuk hitungan)
        Donasi::create([
            'no_kwitansi' => 'KWT-TST-003',
            'donatur_id' => $this->donatur->id,
            'amil_id' => $this->amil->id,
            'jenis_dana_id' => $this->jenisDana->id,
            'program_id' => $programA->id,
            'jumlah' => 10000000,
            'metode_bayar' => 'transfer',
            'tgl_donasi' => now()->toDateString(),
            'status' => 'menunggu_verifikasi',
        ]);

        $component = Livewire::test(DonasiProgramChartWidget::class)
            ->assertSuccessful();

        $ref = new \ReflectionMethod($component->instance(), 'getData');
        $data = $ref->invoke($component->instance());

        $this->assertCount(2, $data['labels']);
        // Program B harus paling atas karena Rp 5.000.000 > Rp 3.000.000
        $this->assertEquals('Beasiswa Yatim', $data['labels'][0]);
        $this->assertEquals(5000000.0, $data['datasets'][0]['data'][0]);

        $this->assertEquals('Ambulans Siaga', $data['labels'][1]);
        $this->assertEquals(3000000.0, $data['datasets'][0]['data'][1]);
    }

    public function test_widget_synchronizes_with_dashboard_filter_updated_event(): void
    {
        $this->actingAs($this->adminUser);

        $program = Program::create(['nama' => 'Renovasi Masjid']);

        // Donasi bulan lalu
        $targetDate = Carbon::create(2026, 5, 10);
        Donasi::create([
            'no_kwitansi' => 'KWT-TST-004',
            'donatur_id' => $this->donatur->id,
            'amil_id' => $this->amil->id,
            'jenis_dana_id' => $this->jenisDana->id,
            'program_id' => $program->id,
            'jumlah' => 1500000,
            'metode_bayar' => 'transfer',
            'tgl_donasi' => $targetDate->toDateString(),
            'status' => 'terverifikasi',
        ]);

        // Default: bulan ini (belum ada donasi untuk bulan ini)
        $component = Livewire::test(DonasiProgramChartWidget::class)
            ->assertSuccessful();
        $ref = new \ReflectionMethod($component->instance(), 'getData');
        $dataDefault = $ref->invoke($component->instance());
        $this->assertStringContainsString('Belum ada donasi program', $dataDefault['labels'][0]);

        // Kirim event sinkronisasi bulan ke-5 (Mei) tahun 2026
        $component->dispatch('dashboardFilterUpdated', month: 5, year: 2026);

        $dataUpdated = $ref->invoke($component->instance());
        $this->assertEquals('Renovasi Masjid', $dataUpdated['labels'][0]);
        $this->assertEquals(1500000.0, $dataUpdated['datasets'][0]['data'][0]);
    }
}
