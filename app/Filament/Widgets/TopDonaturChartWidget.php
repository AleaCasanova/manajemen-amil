<?php

namespace App\Filament\Widgets;

use App\Models\Donasi;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class TopDonaturChartWidget extends ChartWidget
{
    public ?string $filter = 'month';

    protected function getFilters(): ?array
    {
        $now = now();
        $currentYear = (int) $now->year;
        $lastMonth = now()->subMonth();

        $filters = [
            'month' => 'Bulan Ini ('.$now->translatedFormat('F Y').')',
            'last_month' => 'Bulan Lalu ('.$lastMonth->translatedFormat('F Y').')',
            'year' => 'Tahun Ini ('.$currentYear.')',
            'all' => 'Semua Waktu',
        ];

        // Opsi pilihan bulan di tahun berjalan (Januari - Desember)
        for ($m = 1; $m <= 12; $m++) {
            $date = Carbon::create($currentYear, $m, 1);
            $key = "m_{$currentYear}_{$m}";
            $filters[$key] = $date->translatedFormat('F Y');
        }

        return $filters;
    }

    public function getHeading(): ?string
    {
        if ($this->filter === 'year') {
            return 'Top 5 donatur tertinggi tahun ini ('.now()->year.')';
        }
        if ($this->filter === 'all') {
            return 'Top 5 donatur tertinggi (semua waktu)';
        }
        if ($this->filter === 'last_month') {
            return 'Top 5 donatur tertinggi ('.now()->subMonth()->translatedFormat('F Y').')';
        }
        if ($this->filter && str_starts_with($this->filter, 'm_')) {
            $parts = explode('_', $this->filter);
            if (count($parts) === 3) {
                $monthName = Carbon::create((int) $parts[1], (int) $parts[2], 1)->translatedFormat('F Y');

                return "Top 5 donatur tertinggi ({$monthName})";
            }
        }

        return 'Top 5 donatur tertinggi bulan ini ('.now()->translatedFormat('F Y').')';
    }

    protected static ?int $sort = 3;

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
            ->join('donaturs', 'donasis.donatur_id', '=', 'donaturs.id')
            ->where('donasis.status', 'terverifikasi');

        if ($this->filter === 'year') {
            $query->whereYear('donasis.tgl_donasi', now()->year);
        } elseif ($this->filter === 'all') {
            // Tanpa filter tanggal
        } elseif ($this->filter === 'last_month') {
            $last = now()->subMonth();
            $query->whereYear('donasis.tgl_donasi', $last->year)
                ->whereMonth('donasis.tgl_donasi', $last->month);
        } elseif ($this->filter && str_starts_with($this->filter, 'm_')) {
            $parts = explode('_', $this->filter);
            if (count($parts) === 3) {
                $query->whereYear('donasis.tgl_donasi', (int) $parts[1])
                    ->whereMonth('donasis.tgl_donasi', (int) $parts[2]);
            }
        } else {
            $query->whereYear('donasis.tgl_donasi', now()->year)
                ->whereMonth('donasis.tgl_donasi', now()->month);
        }

        $topDonaturs = $query
            ->groupBy('donaturs.id', 'donaturs.nama')
            ->selectRaw('donaturs.nama, SUM(donasis.jumlah) as total')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        if ($topDonaturs->isEmpty()) {
            return [
                'datasets' => [
                    [
                        'label' => 'Total Donasi (Rp)',
                        'data' => [0],
                        'backgroundColor' => '#cbd5e1',
                        'borderColor' => '#94a3b8',
                        'borderWidth' => 1,
                        'borderRadius' => 4,
                    ],
                ],
                'labels' => ['Belum ada transaksi donasi pada periode ini'],
            ];
        }

        $labels = [];
        $totals = [];

        foreach ($topDonaturs as $item) {
            $labels[] = Str::limit($item->nama, 20);
            $totals[] = (float) $item->total;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Donasi (Rp)',
                    'data' => $totals,
                    'backgroundColor' => '#f59e0b',
                    'borderColor' => '#d97706',
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
