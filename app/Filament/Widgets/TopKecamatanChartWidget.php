<?php

namespace App\Filament\Widgets;

use App\Models\Donasi;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TopKecamatanChartWidget extends ChartWidget
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
            'last_month' => 'Top 5 kecamatan tertinggi (bulan lalu)',
            'year' => 'Top 5 kecamatan tertinggi (tahun ini)',
            'all' => 'Top 5 kecamatan tertinggi (semua waktu)',
            default => 'Top 5 kecamatan tertinggi (bulan ini)',
        };
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
        $kecamatanExpr = "COALESCE(NULLIF(TRIM(donaturs.kecamatan), ''), 'Belum Diisi')";

        $query = Donasi::query()
            ->join('donaturs', 'donasis.donatur_id', '=', 'donaturs.id')
            ->where('donasis.status', 'terverifikasi');

        if ($this->filter === 'year') {
            $query->whereYear('donasis.tgl_donasi', now()->year);
        } elseif ($this->filter === 'all') {
            // Tanpa batasan periode
        } elseif ($this->filter === 'last_month') {
            $last = now()->subMonth();
            $query->whereYear('donasis.tgl_donasi', $last->year)
                ->whereMonth('donasis.tgl_donasi', $last->month);
        } else {
            $query->whereYear('donasis.tgl_donasi', now()->year)
                ->whereMonth('donasis.tgl_donasi', now()->month);
        }

        $topKecamatan = $query
            ->groupBy(DB::raw($kecamatanExpr))
            ->selectRaw("{$kecamatanExpr} as kecamatan, SUM(donasis.jumlah) as total")
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        if ($topKecamatan->isEmpty()) {
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
