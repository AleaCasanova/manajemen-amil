<?php

use App\Http\Controllers\KwitansiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->hasRole('Amil') || auth()->user()->amil) {
            return redirect()->route('amil.dashboard');
        }

        return redirect('/admin');
    }

    return redirect()->route('login');
});

// Verifikasi & Download Kwitansi Resmi
Route::get('/verifikasi-kwitansi/{no_kwitansi}', [KwitansiController::class, 'verify'])
    ->name('kwitansi.verify')
    ->where('no_kwitansi', '.*');

Route::get('/kwitansi/{donasi}/download', [KwitansiController::class, 'download'])
    ->name('kwitansi.download');

Route::get('/kwitansi/{donasi}/stream', [KwitansiController::class, 'stream'])
    ->name('kwitansi.stream');

// ==========================================
// PORTAL AMIL (Blade + Tailwind)
// ==========================================
use App\Http\Controllers\Amil\AbsensiController as AmilAbsensiController;
use App\Http\Controllers\Amil\AgendaController;
use App\Http\Controllers\Amil\AktivitasController as AmilAktivitasController;
use App\Http\Controllers\Amil\AuthController as AmilAuthController;
use App\Http\Controllers\Amil\DashboardController as AmilDashboardController;
use App\Http\Controllers\Amil\DonasiController as AmilDonasiController;
use App\Http\Controllers\Amil\DonaturController as AmilDonaturController;
use App\Http\Controllers\Amil\IzinCutiController as AmilIzinCutiController;
use App\Http\Controllers\Amil\PengumumanController;
use App\Http\Controllers\Amil\PenyaluranController;
use App\Http\Controllers\Amil\ProfileController as AmilProfileController;
use Illuminate\Support\Facades\Artisan;

Route::get('/login', [AmilAuthController::class, 'showLoginForm'])->name('login');
Route::get('/amil/login', [AmilAuthController::class, 'showLoginForm'])->name('amil.login');
Route::get('/amil', function () {
    return redirect()->route('amil.dashboard');
});
Route::post('/amil/login', [AmilAuthController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('amil.login.post');

Route::middleware(['auth'])->prefix('amil')->name('amil.')->group(function () {
    Route::post('/logout', [AmilAuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [AmilDashboardController::class, 'index'])->name('dashboard');

    // Modul Absensi (GPS Geofence + Kamera Selfie)
    Route::get('/absensi', [AmilAbsensiController::class, 'index'])->name('absensi.index');
    Route::post('/absensi/masuk', [AmilAbsensiController::class, 'clockIn'])->name('absensi.masuk');
    Route::post('/absensi/pulang', [AmilAbsensiController::class, 'clockOut'])->name('absensi.pulang');
    Route::get('/absensi/check-location', [AmilAbsensiController::class, 'checkLocation'])->name('absensi.check-location');

    // Modul Izin & Cuti Amil
    Route::get('/izin-cuti', [AmilIzinCutiController::class, 'index'])->name('izin-cuti.index');
    Route::get('/izin-cuti/create', [AmilIzinCutiController::class, 'create'])->name('izin-cuti.create');
    Route::post('/izin-cuti', [AmilIzinCutiController::class, 'store'])->name('izin-cuti.store');

    // Modul Aktivitas Harian Lapangan
    Route::get('/aktivitas', [AmilAktivitasController::class, 'index'])->name('aktivitas.index');
    Route::get('/aktivitas/create', [AmilAktivitasController::class, 'create'])->name('aktivitas.create');
    Route::post('/aktivitas', [AmilAktivitasController::class, 'store'])->name('aktivitas.store');

    // Modul Agenda Penugasan
    Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda.index');
    Route::get('/agenda/{agenda}', [AgendaController::class, 'show'])->name('agenda.show');

    // Modul Pengumuman Lembaga
    Route::get('/pengumuman', [PengumumanController::class, 'index'])->name('pengumuman.index');
    Route::get('/pengumuman/{pengumuman}', [PengumumanController::class, 'show'])->name('pengumuman.show');

    // Modul Donasi
    Route::get('/donasi/check-phone', [AmilDonasiController::class, 'checkPhone'])->name('donasi.check-phone');
    Route::get('/donasi', [AmilDonasiController::class, 'index'])->name('donasi.index');
    Route::get('/donasi/create', [AmilDonasiController::class, 'create'])->name('donasi.create');
    Route::post('/donasi', [AmilDonasiController::class, 'store'])->name('donasi.store');
    Route::get('/donasi/{donasi}/success', [AmilDonasiController::class, 'success'])->name('donasi.success');

    // Modul Donatur
    Route::get('/donatur', [AmilDonaturController::class, 'index'])->name('donatur.index');
    Route::get('/donatur/create', [AmilDonaturController::class, 'create'])->name('donatur.create');
    Route::post('/donatur', [AmilDonaturController::class, 'store'])->name('donatur.store');
    Route::get('/donatur/{donatur}', [AmilDonaturController::class, 'show'])->name('donatur.show');

    // Modul Penyaluran & Mustahik
    Route::get('/penyaluran', [PenyaluranController::class, 'index'])->name('penyaluran.index');
    Route::get('/penyaluran/create', [PenyaluranController::class, 'create'])->name('penyaluran.create');
    Route::post('/penyaluran', [PenyaluranController::class, 'store'])->name('penyaluran.store');

    // Profil Amil & Pengaturan Akun (Self-Service Profile & Password)
    Route::get('/profile', [AmilProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [AmilProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [AmilProfileController::class, 'updatePassword'])->name('profile.password');
});

// Helper untuk membuat symlink storage di hosting
Route::get('/storage-link', function () {
    try {
        Artisan::call('storage:link');

        return 'Storage link berhasil dibuat: '.Artisan::output();
    } catch (Throwable $e) {
        return 'Gagal membuat symlink: '.$e->getMessage();
    }
});

// Helper untuk menjalankan migrasi database otomatis di hosting
Route::get('/run-migrate', function () {
    try {
        Artisan::call('migrate', ['--force' => true]);

        return 'Migrasi Database Berhasil:<br>'.nl2br(Artisan::output());
    } catch (Throwable $e) {
        return 'Gagal migrasi: '.$e->getMessage();
    }
});

// Helper untuk menjalankan proses Alpa otomatis di shared hosting / cron web
Route::get('/run-alpa', function () {
    try {
        Artisan::call('amil:mark-alpa', ['--include-weekend' => true]);

        return 'Proses Alpa Berhasil:<br>'.nl2br(Artisan::output());
    } catch (Throwable $e) {
        return 'Gagal memproses alpa: '.$e->getMessage();
    }
});

// Helper untuk membersihkan cache aplikasi via browser di shared hosting
Route::get('/clear-cache', function () {
    try {
        Artisan::call('optimize:clear');

        return 'Cache Berhasil Dibersihkan:<br>'.nl2br(Artisan::output());
    } catch (Throwable $e) {
        return 'Gagal membersihkan cache: '.$e->getMessage();
    }
});

// Helper untuk men-generate dummy media (foto profil amil, selfie presensi, bukti donasi) di hosting
Route::get('/run-seed-media', function () {
    try {
        Artisan::call('db:seed', ['--class' => 'DummyMediaSeeder', '--force' => true]);

        return 'Dummy media berhasil dibuat:<br>'.nl2br(Artisan::output());
    } catch (Throwable $e) {
        return 'Gagal membuat media: '.$e->getMessage();
    }
});

// Fallback Route untuk memastikan file gambar di storage/app/public/ selalu dapat diakses di shared hosting
Route::get('/storage/{path}', function (string $path) {
    $filePath = storage_path('app/public/'.$path);
    $realBase = realpath(storage_path('app/public'));
    $realPath = realpath($filePath);

    if ($realPath && $realBase && str_starts_with($realPath, $realBase) && file_exists($realPath)) {
        return response()->file($realPath);
    }

    // Jika file fisik belum ada atau dummy belum ter-generate di hosting,
    // sajikan gambar SVG placeholder dinamis resmi berlogo GSC agar tidak menghasilkan error 404
    $title = str_contains($path, 'absensi')
        ? 'Foto Presensi Kehadiran'
        : (str_contains($path, 'donasi') ? 'Bukti Transaksi Donasi' : 'Dokumen Digital GSC');

    $svg = <<<SVG
    <svg xmlns="http://www.w3.org/2000/svg" width="600" height="400" viewBox="0 0 600 400">
        <rect width="600" height="400" fill="#065f46" rx="20"/>
        <rect x="20" y="20" width="560" height="360" rx="16" fill="#047857" stroke="#10b981" stroke-width="2"/>
        <circle cx="300" cy="150" r="48" fill="#064e3b" stroke="#34d399" stroke-width="3"/>
        <path d="M285 150 L295 160 L315 140" fill="none" stroke="#34d399" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
        <text x="300" y="235" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="20" font-weight="bold" fill="#ffffff" text-anchor="middle">{$title}</text>
        <text x="300" y="265" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="13" fill="#a7f3d0" text-anchor="middle">Dokumen Digital Terverifikasi Sistem</text>
        <text x="300" y="325" font-family="monospace" font-size="12" fill="#6ee7b7" text-anchor="middle">Gerak Sedekah Cilacap (GSC)</text>
    </svg>
    SVG;

    return response($svg, 200, [
        'Content-Type' => 'image/svg+xml',
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->where('path', '.*');
