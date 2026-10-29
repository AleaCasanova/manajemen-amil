<?php

namespace App\Filament\Widgets;

use App\Models\Donasi;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class AkumulasiPendapatanBulananChartWidget extends ChartWidget
{
    protected ?string $heading = 'Akumulasi pendapatan perbulan';

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'md' => 1,
        'lg' => 1,
        'xl' => 1,
    ];

    protected ?string $maxHeight = '230px';

    protected function getData(): array
    {
        $items = Donasi::query()
            ->join('jenis_danas', 'donasis.jenis_dana_id', '=', 'jenis_danas.id')
            ->whereYear('donasis.tgl_donasi', now()->year)
            ->whereMonth('donasis.tgl_donasi', now()->month)
            ->where('donasis.status', 'terverifikasi')
            ->groupBy('jenis_danas.id', 'jenis_danas.nama')
            ->selectRaw('jenis_danas.nama, SUM(donasis.jumlah) as total')
            ->orderByDesc('total')
            ->get();

        $palette = [
            '#10b981', // Emerald
            '#f43f5e', // Rose
            '#f59e0b', // Amber
            '#8b5cf6', // Violet
            '#0284c7', // Sky Blue
            '#06b6d4', // Cyan
            '#ec4899', // Pink
            '#14b8a6', // Teal
        ];

        if ($items->isEmpty()) {
            return [
                'datasets' => [
                    [
                        'data' => [1],
                        'backgroundColor' => ['#e2e8f0'],
                    ],
                ],
                'labels' => ['Belum ada transaksi bulan ini'],
            ];
        }

        $labels = [];
        $totals = [];
        $backgroundColors = [];

        foreach ($items as $index => $item) {
            $labels[] = $item->nama;
            $totals[] = (float) $item->total;
            $backgroundColors[] = $palette[$index % count($palette)];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Pendapatan (Rp)',
                    'data' => $totals,
                    'backgroundColor' => $backgroundColors,
                    'borderWidth' => 2,
                    'borderColor' => '#ffffff',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<'JS'
            {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 12,
                            boxHeight: 12,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 14,
                            font: {
                                size: 12,
                            },
                        },
                    },
                    tooltip: {
                        callbacks: {
                            label: (context) => {
                                const label = context.label || '';
                                const value = Number(context.raw) || 0;
                                const dataset = context.dataset;
                                const total = dataset.data.reduce((acc, curr) => acc + Number(curr), 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(2) + '%' : '0%';
                                return ' ' + label + ': Rp ' + value.toLocaleString('id-ID') + ' (' + percentage + ')';
                            },
                        },
                    },
                },
                cutout: '65%',
            }
        JS);
    }
}
