<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

// Jadwal Backup Database Otomatis Harian Pukul 01:00 Dini Hari
Schedule::command('app:backup-database')->dailyAt('01:00');

// Otomatis tandai amil yang belum presensi dan tanpa izin/cuti sebagai ALPA setiap malam pukul 23:59 WIB
Schedule::command('amil:mark-alpa')
    ->dailyAt('23:59')
    ->timezone('Asia/Jakarta');
