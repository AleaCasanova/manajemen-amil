<?php

namespace App\Filament\Widgets;

use App\Models\Donasi;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Str;

class DonasiProgramChartWidget extends ChartWidget
{
    public ?string $filter = 'month';

    protected function getFilters(): ?array
    {
        return [
            'month' => 'Bulan Ini',
            'last_month' => 'Bulan Lalu',
            'year' => 'Tahun Ini',
            'all' => 'Semua Waktu',
        ];
    }

    public function getHeading(): ?string
    {
        return match ($this->filter) {
            'last_month' => 'Donasi per program (bulan lalu)',
            'year' => 'Donasi per program (tahun '.now()->year.')',
            'all' => 'Donasi per program (semua waktu)',
            default => 'Donasi per program ('.now()->translatedFormat('F Y').')',
        };
    }

    protected static ?int $sort = 6;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'md' => 1,
        'lg' => 1,
        'xl' => 1,
    ];

    protected ?string $maxHeight = '230px';

    protected function getData(): array
    {
        $query = Donasi::query()
            ->join('programs', 'donasis.program_id', '=', 'programs.id')
            ->where('donasis.status', 'terverifikasi');

        if ($this->filter === 'year') {
            $query->whereYear('donasis.tgl_donasi', now()->year);
        } elseif ($this->filter === 'last_month') {
            $last = now()->subMonth();
            $query->whereYear('donasis.tgl_donasi', $last->year)
                ->whereMonth('donasis.tgl_donasi', $last->month);
        } elseif ($this->filter === 'all') {
            // Tanpa batasan tanggal
        } else {
            $query->whereYear('donasis.tgl_donasi', now()->year)
                ->whereMonth('donasis.tgl_donasi', now()->month);
        }

        $items = $query
            ->groupBy('programs.id', 'programs.nama')
            ->selectRaw('programs.nama, SUM(donasis.jumlah) as total, COUNT(donasis.id) as total_transaksi')
            ->orderByDesc('total')
            ->get();

        $palette = [
            '#10b981', // Emerald
            '#0284c7', // Sky Blue
            '#f59e0b', // Amber
            '#8b5cf6', // Violet
            '#ec4899', // Pink
            '#06b6d4', // Cyan
            '#f43f5e', // Rose
            '#14b8a6', // Teal
            '#6366f1', // Indigo
            '#84cc16', // Lime
        ];

        if ($items->isEmpty()) {
            $periodText = match ($this->filter) {
                'last_month' => 'bulan lalu',
                'year' => 'tahun ini',
                'all' => 'saat ini',
                default => 'bulan ini',
            };

            return [
                'datasets' => [
                    [
                        'data' => [1],
                        'backgroundColor' => ['#e2e8f0'],
                    ],
                ],
                'labels' => ["Belum ada donasi program ({$periodText})"],
            ];
        }

        $labels = [];
        $totals = [];
        $backgroundColors = [];

        foreach ($items as $index => $item) {
            $labels[] = Str::limit($item->nama, 28);
            $totals[] = (float) $item->total;
            $backgroundColors[] = $palette[$index % count($palette)];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Donasi Program (Rp)',
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
                            padding: 12,
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
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) + '%' : '0%';
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
