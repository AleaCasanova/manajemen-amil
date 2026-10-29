<?php

namespace App\Filament\Widgets;

use App\Models\Donasi;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class DonasiChartWidget extends ChartWidget
{
    protected ?string $heading = 'Tren Penerimaan Donasi Bulanan';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

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
                    'pointBackgroundColor' => '#10b981',
                    'pointBorderColor' => '#ffffff',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<'JS'
            {
                scales: {
                    y: {
                        beginAtZero: true,
                        min: 0,
                        ticks: {
                            callback: (value) => 'Rp ' + Number(value).toLocaleString('id-ID'),
                        },
                    },
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: (context) => 'Total Donasi: Rp ' + Number(context.raw).toLocaleString('id-ID'),
                        },
                    },
                },
            }
        JS);
    }
}
