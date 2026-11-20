<?php

namespace Tests\Feature;

use App\Filament\Widgets\DonasiChartWidget;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DonasiChartWidgetInteractiveTest extends TestCase
{
    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $this->adminUser = User::firstOrCreate(
            ['email' => 'admin_chart_test@example.com'],
            ['name' => 'Admin Chart Test', 'password' => bcrypt('password'), 'is_active' => true, 'role_id' => 1]
        );
        $this->adminUser->syncRoles(['Admin']);
    }

    public function test_line_chart_mounts_with_current_month_and_year(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test(DonasiChartWidget::class)
            ->assertSuccessful()
            ->assertSee('Tren Penerimaan Donasi Bulanan')
            ->assertSee('Klik titik bulan pada grafik tren');
    }

    public function test_selecting_month_dispatches_dashboard_filter_updated(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test(DonasiChartWidget::class)
            ->call('selectMonth', 8) // Bulan Agustus
            ->assertDispatched('dashboardFilterUpdated', month: 8, year: (int) now()->year);
    }

    public function test_changing_year_filter_dispatches_dashboard_filter_updated(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test(DonasiChartWidget::class)
            ->set('filter', '2025')
            ->assertDispatched('dashboardFilterUpdated', month: (int) now()->month, year: 2025);
    }
}
