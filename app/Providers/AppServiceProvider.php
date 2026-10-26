<?php

namespace App\Providers;

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
    }
}
