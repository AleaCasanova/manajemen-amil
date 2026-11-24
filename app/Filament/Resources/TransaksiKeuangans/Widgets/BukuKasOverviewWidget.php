<?php

namespace App\Filament\Resources\TransaksiKeuangans\Widgets;

use App\Models\AkunKeuangan;
use App\Models\CashAdvance;
use App\Models\TransaksiKeuangan;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BukuKasOverviewWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        // 1. Total Saldo Seluruh Kas & Rekening Bank (eager load mutasi, 0 N+1)
        $akuns = AkunKeuangan::withMutasiTotals()->where('is_active', true)->get();
        $totalSaldo = $akuns->sum(fn (AkunKeuangan $a) => $a->saldo_sekarang);

        // 2. Total Pemasukan Riil Bulan Ini (non-transfer & non-pengembalian CA/Kasbon)
        $masukBulanIni = (float) TransaksiKeuangan::arusKasRiil()
            ->masuk()
            ->bulanIni()
            ->sum('nominal');

        // 3. Total Pengeluaran Riil Bulan Ini (non-transfer & non-pencairan CA/Kasbon)
        $keluarBulanIni = (float) TransaksiKeuangan::arusKasRiil()
            ->keluar()
            ->bulanIni()
            ->sum('nominal');

        // 4. Outstanding Cash Advance & Cash Bon Aktif
        $caAktifList = CashAdvance::aktif()->get();
        $totalOutstandingCa = $caAktifList->sum(fn (CashAdvance $ca) => $ca->sisa_tanggungan);
        $jumlahCa = $caAktifList->where('tipe', 'cash_advance')->count();
        $jumlahCb = $caAktifList->where('tipe', 'cash_bon')->count();

        return [
            Stat::make('Total Saldo Kas & Bank', 'Rp '.number_format($totalSaldo, 0, ',', '.'))
                ->description('Akumulasi '.$akuns->count().' akun kas & rekening bank')
                ->descriptionIcon('heroicon-m-wallet')
                ->color('primary'),

            Stat::make('Pemasukan Riil Bulan Ini', 'Rp '.number_format($masukBulanIni, 0, ',', '.'))
                ->description('Periode '.now()->translatedFormat('F Y').' (non-transfer)')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Pengeluaran Riil Bulan Ini', 'Rp '.number_format($keluarBulanIni, 0, ',', '.'))
                ->description('Penyaluran, operasional, SDM & adm bank')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),

            Stat::make('Cash Advance & Kasbon Aktif', 'Rp '.number_format($totalOutstandingCa, 0, ',', '.'))
                ->description("{$jumlahCa} CA berjalan • {$jumlahCb} Kasbon belum lunas")
                ->descriptionIcon('heroicon-m-clock')
                ->color($totalOutstandingCa > 0 ? 'warning' : 'success'),
        ];
    }
}
