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

        // 5. Amil Belum Absen Hari Ini
        $amilSudahAbsenIds = Absensi::whereDate('tanggal', today())->pluck('amil_id');
        $amilBelumAbsen = Amil::where('status', 'aktif')
            ->whereNotIn('id', $amilSudahAbsenIds)
            ->count();

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
                ->description('Perlu diingatkan untuk presensi hari ini')
                ->descriptionIcon('heroicon-m-clock')
                ->color($amilBelumAbsen > 0 ? 'warning' : 'success'),

            Stat::make('Donatur Baru Bulan Ini', $donaturBaruBulanIni.' Donatur')
                ->description('Pertumbuhan basis donatur')
                ->descriptionIcon('heroicon-m-heart')
                ->color('danger'),
        ];
    }
}
