<?php

namespace Tests\Feature;

use App\Filament\Pages\Auth\CustomLogin;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminSecurityPathAndRateLimitTest extends TestCase
{
    use DatabaseTransactions;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'admin']);

        $this->adminUser = User::firstOrCreate(
            ['email' => 'admin_secure_test@example.com'],
            ['name' => 'Admin Secure Test', 'password' => bcrypt('password'), 'is_active' => true, 'role_id' => 1]
        );
        $this->adminUser->syncRoles(['Admin']);
    }

    public function test_default_admin_url_is_blocked_with_404(): void
    {
        $adminPath = config('filament.admin_path', 'portal-internal-gsc');

        if ($adminPath !== 'admin') {
            $response = $this->get('/admin');
            $response->assertStatus(404);

            $loginResponse = $this->get('/admin/login');
            $loginResponse->assertStatus(404);
        } else {
            $this->markTestSkipped('Admin path is configured as admin');
        }
    }

    public function test_custom_secret_admin_path_is_accessible(): void
    {
        $adminPath = config('filament.admin_path', 'portal-internal-gsc');

        $response = $this->get('/'.$adminPath.'/login');
        $response->assertStatus(200);
        $response->assertSee('Masuk ke akun Anda');
    }

    public function test_custom_login_rate_limiting_protects_against_brute_force(): void
    {
        $component = Livewire::test(CustomLogin::class);

        // Lakukan 5 kali percobaan gagal
        for ($i = 1; $i <= 5; $i++) {
            $component->fillForm([
                'email' => 'wrong_admin@example.com',
                'password' => 'wrongpassword'.$i,
            ])->call('authenticate');
        }

        // Percobaan ke-6 harus ditolak oleh rate limiter (TooManyRequestsException caught)
        $component->fillForm([
            'email' => 'wrong_admin@example.com',
            'password' => 'wrongpassword6',
        ])->call('authenticate');

        $component->assertNotified('Akses Dibatasi Sementara');
    }

    public function test_authenticated_admin_accessing_admin_is_redirected_to_custom_path(): void
    {
        $adminPath = config('filament.admin_path', 'portal-internal-gsc');

        $response = $this->actingAs($this->adminUser)->get('/admin');
        $response->assertRedirect('/'.$adminPath);
    }
}
