<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! $this->app->runningInConsole()) {
            // Jika diakses melalui Ngrok atau proxy HTTPS, paksa skema HTTPS agar aset dan form tidak diblokir browser HP
            if (request()->header('x-forwarded-proto') === 'https' || str_contains(request()->getHost(), 'ngrok') || request()->isSecure()) {
                URL::forceScheme('https');
            }

            // Jika diakses dari HP / Ngrok / domain luar, gunakan aset build produksi
            // agar browser HP tidak mencari localhost:5173 yang hanya ada di laptop
            $host = request()->getHost();
            if ($host !== '127.0.0.1' && $host !== 'localhost') {
                Vite::useHotFile(storage_path('framework/no_hot'));
            }
        }

        // Rate limiter cerdas untuk login amil:
        // 1. Batasi 10 percobaan per akun spesifik (mencegah brute force akun tertentu)
        // 2. Batasi 120 percobaan per IP (agar 20-30 amil dalam satu WiFi/IP kantor bisa login bersamaan)
        RateLimiter::for('amil-login', function (Request $request) {
            $login = (string) $request->input('login', $request->input('email', ''));

            return [
                Limit::perMinute(10)->by(strtolower(trim($login)).'|'.$request->ip()),
                Limit::perMinute(120)->by($request->ip()),
            ];
        });
    }
}
