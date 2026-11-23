<?php

namespace App\Filament\Resources\TransaksiKeuangans\Widgets;

use App\Models\AkunKeuangan;
use App\Models\TransaksiKeuangan;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BukuKasOverviewWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        // 1. Total Saldo Seluruh Kas & Rekening Bank
        $akuns = AkunKeuangan::where('is_active', true)->get();
        $totalSaldo = $akuns->sum(fn (AkunKeuangan $a) => $a->saldo_sekarang);

        // 2. Total Kas Masuk Bulan Ini
        $masukBulanIni = (float) TransaksiKeuangan::where('jenis', 'masuk')
            ->whereMonth('tgl_transaksi', now()->month)
            ->whereYear('tgl_transaksi', now()->year)
            ->sum('nominal');

        // 3. Total Kas Keluar Bulan Ini
        $keluarBulanIni = (float) TransaksiKeuangan::where('jenis', 'keluar')
            ->whereMonth('tgl_transaksi', now()->month)
            ->whereYear('tgl_transaksi', now()->year)
            ->sum('nominal');

        // 4. Surplus / Defisit Bulan Ini
        $selisih = $masukBulanIni - $keluarBulanIni;

        return [
            Stat::make('Total Saldo Kas Yayasan', 'Rp '.number_format($totalSaldo, 0, ',', '.'))
                ->description('Akumulasi '.$akuns->count().' akun kas & rekening bank')
                ->descriptionIcon('heroicon-m-wallet')
                ->color('primary'),

            Stat::make('Pemasukan Bulan Ini', 'Rp '.number_format($masukBulanIni, 0, ',', '.'))
                ->description('Periode '.now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Pengeluaran Bulan Ini', 'Rp '.number_format($keluarBulanIni, 0, ',', '.'))
                ->description('Operasional, armada, program & gaji')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),

            Stat::make(
                $selisih >= 0 ? 'Surplus Kas Bulan Ini' : 'Defisit Kas Bulan Ini',
                'Rp '.number_format(abs($selisih), 0, ',', '.')
            )
                ->description($selisih >= 0 ? 'Arus kas positif (Surplus)' : 'Pengeluaran melebihi pemasukan')
                ->descriptionIcon($selisih >= 0 ? 'heroicon-m-check-circle' : 'heroicon-m-exclamation-triangle')
                ->color($selisih >= 0 ? 'success' : 'danger'),
        ];
    }
}
