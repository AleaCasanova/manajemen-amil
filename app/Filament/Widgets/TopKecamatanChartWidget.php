<?php

namespace App\Filament\Widgets;

use App\Models\Donasi;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Attributes\On;

class TopKecamatanChartWidget extends ChartWidget
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

        return "Top 5 amil pendapatan tertinggi ({$monthName})";
    }

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
        $month = $this->selectedMonth ?? now()->month;
        $year = $this->selectedYear ?? now()->year;

        $topAmils = Donasi::query()
            ->join('amils', 'donasis.amil_id', '=', 'amils.id')
            ->where('donasis.status', 'terverifikasi')
            ->whereYear('donasis.tgl_donasi', $year)
            ->whereMonth('donasis.tgl_donasi', $month)
            ->groupBy('amils.id', 'amils.nama')
            ->selectRaw('amils.nama, SUM(donasis.jumlah) as total')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        if ($topAmils->isEmpty()) {
            $monthName = Carbon::create($year, $month, 1)->translatedFormat('F Y');

            return [
                'datasets' => [
                    [
                        'label' => 'Total Pendapatan (Rp)',
                        'data' => [0],
                        'backgroundColor' => '#cbd5e1',
                        'borderColor' => '#94a3b8',
                        'borderWidth' => 1,
                        'borderRadius' => 4,
                    ],
                ],
                'labels' => ["Belum ada transaksi amil ({$monthName})"],
            ];
        }

        $labels = [];
        $totals = [];

        foreach ($topAmils as $item) {
            $labels[] = Str::limit($item->nama, 20);
            $totals[] = (float) $item->total;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Pendapatan (Rp)',
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
                            label: (context) => 'Total: Rp ' + Number(context.raw).toLocaleString('id-ID'),
                        },
                    },
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                        },
                        ticks: {
                            maxRotation: 20,
                            minRotation: 20,
                            font: {
                                size: 10,
                            },
                        },
                    },
                    y: {
                        beginAtZero: true,
                        min: 0,
                        ticks: {
                            precision: 0,
                            callback: (value) => {
                                if (value % 1 !== 0) return null;
                                return 'Rp ' + Number(value).toLocaleString('id-ID');
                            },
                        },
                    },
                },
            }
        JS);
    }
}
