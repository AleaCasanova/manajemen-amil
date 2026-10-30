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

        $this->admin = User::where('email', 'admin@gmail.com')->first()
            ?? User::where('email', 'admin@amilgsc.com')->first()
            ?? User::whereHas('roles', fn ($q) => $q->where('name', 'Admin'))->first()
            ?? User::factory()->create(['email' => 'admin@gmail.com', 'is_active' => true]);

        if (! $this->admin->hasRole('Admin')) {
            $this->admin->assignRole('Admin');
        }
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

    public function test_storage_fallback_route_serves_files(): void
    {
        // Buat file uji sementara di storage/app/public
        $testDir = storage_path('app/public/test');
        if (! is_dir($testDir)) {
            mkdir($testDir, 0755, true);
        }
        $testFile = $testDir.'/sample.txt';
        file_put_contents($testFile, 'hello storage');

        $response = $this->get('/storage/test/sample.txt');
        $response->assertStatus(200);
        $this->assertEquals('hello storage', $response->streamedContent());

        // Hapus file uji
        @unlink($testFile);
        @rmdir($testDir);
    }
}
