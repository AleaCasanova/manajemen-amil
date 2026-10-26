<?php

namespace App\Filament\Widgets;

use App\Models\Donasi;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class DonasiChartWidget extends ChartWidget
{
    protected ?string $heading = 'Tren Penerimaan Donasi Bulanan';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $months = [];
        $totals = [];

        for ($i = 1; $i <= 12; $i++) {
            $monthName = Carbon::create(now()->year, $i, 1)->translatedFormat('M');
            $months[] = $monthName;

            $total = Donasi::whereYear('tgl_donasi', now()->year)
                ->whereMonth('tgl_donasi', $i)
                ->where('status', 'terverifikasi')
                ->sum('jumlah');

            $totals[] = (float) $total;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Donasi (Rp)',
                    'data' => $totals,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
