<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminResourcesTableTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::where('email', 'admin@amilgsc.com')->first()
            ?? User::factory()->create(['email' => 'admin@amilgsc.com', 'is_active' => true]);
    }

    public function test_admin_can_access_absensi_amil_table(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/absensis');
        $response->assertStatus(200);
    }

    public function test_admin_can_access_izin_cuti_table(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/izin-cutis');
        $response->assertStatus(200);
    }

    public function test_admin_can_access_aktivitas_harian_table(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/aktivitas-harians');
        $response->assertStatus(200);
    }
}
