<?php

namespace App\Filament\Widgets;

use App\Models\Absensi;
use App\Models\Amil;
use App\Models\Donasi;
use App\Models\Donatur;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // 1. Donasi Hari Ini
        $donasiHariIni = Donasi::whereDate('tgl_donasi', today())
            ->where('status', 'terverifikasi')
            ->sum('jumlah');

        // 2. Donasi Bulan Ini
        $donasiBulanIni = Donasi::whereMonth('tgl_donasi', now()->month)
            ->whereYear('tgl_donasi', now()->year)
            ->where('status', 'terverifikasi')
            ->sum('jumlah');

        // 3. Donasi Tahun Ini
        $donasiTahunIni = Donasi::whereYear('tgl_donasi', now()->year)
            ->where('status', 'terverifikasi')
            ->sum('jumlah');

        // 4. Total Amil Aktif
        $totalAmilAktif = Amil::where('status', 'aktif')->count();

        // 5. Amil Belum Absen Hari Ini (6 hari kerja Staf/Driver vs 5 hari kerja Magang)
        $today = today();
        $isSunday = $today->isSunday();
        $isSaturday = $today->isSaturday();

        if ($isSunday) {
            $amilBelumAbsen = 0;
            $absenDesc = 'Hari Minggu (Libur Akhir Pekan Seluruh Amil)';
        } else {
            $amilSudahAbsenIds = Absensi::whereDate('tanggal', $today)->pluck('amil_id');
            $activeAmils = Amil::where('status', 'aktif')
                ->whereNotIn('id', $amilSudahAbsenIds)
                ->get()
                ->filter(function ($a) use ($isSaturday) {
                    if (! $a->needsAttendance()) {
                        return false;
                    }
                    if ($isSaturday && $a->isMagang()) {
                        return false;
                    }

                    return true;
                });

            $amilBelumAbsen = $activeAmils->count();
            $absenDesc = $isSaturday
                ? 'Staf & Driver wajib presensi (Magang libur Sabtu)'
                : 'Perlu diingatkan untuk presensi hari ini';
        }

        // 6. Total Donatur Baru Bulan Ini
        $donaturBaruBulanIni = Donatur::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return [
            Stat::make('Donasi Hari Ini', 'Rp '.number_format($donasiHariIni, 0, ',', '.'))
                ->description('Penerimaan terverifikasi hari ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Donasi Bulan Ini', 'Rp '.number_format($donasiBulanIni, 0, ',', '.'))
                ->description('Bulan '.now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),

            Stat::make('Donasi Tahun Ini', 'Rp '.number_format($donasiTahunIni, 0, ',', '.'))
                ->description('Tahun '.now()->year)
                ->color('info'),

            Stat::make('Amil Aktif', $totalAmilAktif.' Orang')
                ->description('Total petugas lapangan & kantor')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),

            Stat::make('Amil Belum Absen', $amilBelumAbsen.' Orang')
                ->description($absenDesc)
                ->descriptionIcon('heroicon-m-clock')
                ->color($amilBelumAbsen > 0 ? 'warning' : 'success'),

            Stat::make('Donatur Baru Bulan Ini', $donaturBaruBulanIni.' Donatur')
                ->description('Pertumbuhan basis donatur')
                ->descriptionIcon('heroicon-m-heart')
                ->color('danger'),
        ];
    }
}
