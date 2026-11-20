<?php

namespace App\Filament\Widgets;

use App\Models\Donasi;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Attributes\On;

class DonasiProgramChartWidget extends ChartWidget
{
    public ?int $selectedMonth = null;

    public ?int $selectedYear = null;

    public function mount(): void
    {
        $this->selectedMonth = now()->month;
        $this->selectedYear = now()->year;
        parent::mount();
    }

    #[On('dashboardFilterUpdated')]
    public function updateFilter(int $month, int $year): void
    {
        $this->selectedMonth = $month;
        $this->selectedYear = $year;
        $this->cachedData = null;
    }

    public function getHeading(): ?string
    {
        $month = $this->selectedMonth ?? now()->month;
        $year = $this->selectedYear ?? now()->year;
        $monthName = Carbon::create($year, $month, 1)->translatedFormat('F Y');

        return "Donasi per program ({$monthName})";
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
        $month = $this->selectedMonth ?? now()->month;
        $year = $this->selectedYear ?? now()->year;

        $items = Donasi::query()
            ->join('programs', 'donasis.program_id', '=', 'programs.id')
            ->where('donasis.status', 'terverifikasi')
            ->whereYear('donasis.tgl_donasi', $year)
            ->whereMonth('donasis.tgl_donasi', $month)
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
            $monthName = Carbon::create($year, $month, 1)->translatedFormat('F Y');

            return [
                'datasets' => [
                    [
                        'data' => [1],
                        'backgroundColor' => ['#e2e8f0'],
                    ],
                ],
                'labels' => ["Belum ada donasi program ({$monthName})"],
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
