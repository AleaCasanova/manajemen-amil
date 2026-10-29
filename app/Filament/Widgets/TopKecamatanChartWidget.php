<?php

namespace App\Filament\Widgets;

use App\Models\Donasi;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Str;

class TopKecamatanChartWidget extends ChartWidget
{
    protected ?string $heading = 'Top 5 kecamatan tertinggi bulan ini';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'md' => 1,
        'lg' => 1,
        'xl' => 1,
    ];

    protected ?string $maxHeight = '230px';

    protected function getData(): array
    {
        $topKecamatan = Donasi::query()
            ->join('donaturs', 'donasis.donatur_id', '=', 'donaturs.id')
            ->whereYear('donasis.tgl_donasi', now()->year)
            ->whereMonth('donasis.tgl_donasi', now()->month)
            ->where('donasis.status', 'terverifikasi')
            ->whereNotNull('donaturs.kecamatan')
            ->where('donaturs.kecamatan', '!=', '')
            ->groupBy('donaturs.kecamatan')
            ->selectRaw('donaturs.kecamatan, SUM(donasis.jumlah) as total')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $labels = [];
        $totals = [];

        foreach ($topKecamatan as $item) {
            $labels[] = Str::limit($item->kecamatan, 20);
            $totals[] = (float) $item->total;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Donasi (Rp)',
                    'data' => $totals,
                    'backgroundColor' => '#10b981',
                    'borderColor' => '#059669',
                    'borderWidth' => 1,
                    'borderRadius' => 4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<'JS'
            {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        callbacks: {
                            label: (context) => 'Total Donasi: Rp ' + Number(context.raw).toLocaleString('id-ID'),
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        min: 0,
                        suggestedMax: 5000000,
                        ticks: {
                            precision: 0,
                            callback: (value) => {
                                if (value % 1 !== 0) return null;
                                return 'Rp ' + Number(value).toLocaleString('id-ID');
                            },
                        },
                    },
                    x: {
                        grid: {
                            display: false,
                        },
                    },
                },
            }
        JS);
    }
}
