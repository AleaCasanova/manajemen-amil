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

// Fallback Route untuk memastikan file gambar di storage/app/public/ selalu dapat diakses di shared hosting
Route::get('/storage/{path}', function (string $path) {
    $filePath = storage_path('app/public/'.$path);
    $realBase = realpath(storage_path('app/public'));
    $realPath = realpath($filePath);

    if (! $realPath || ! str_starts_with($realPath, $realBase) || ! file_exists($realPath)) {
        abort(404);
    }

    return response()->file($realPath);
})->where('path', '.*');
