<?php

namespace App\Filament\Widgets;

use App\Models\Donasi;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Str;

class TopDonaturChartWidget extends ChartWidget
{
    public ?string $filter = 'month';

    protected function getFilters(): ?array
    {
        $year = now()->year;
        $bulanIndo = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $filters = [
            'month' => 'Bulan Ini',
            'last_month' => 'Bulan Lalu',
            'year' => 'Tahun Ini ('.$year.')',
            'all' => 'Semua Waktu',
        ];

        foreach ($bulanIndo as $num => $nama) {
            $filters['m_'.$num] = $nama.' '.$year;
        }

        return $filters;
    }

    public function getHeading(): ?string
    {
        $bulanIndo = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        if ($this->filter && str_starts_with($this->filter, 'm_')) {
            $m = (int) substr($this->filter, 2);
            $namaBulan = $bulanIndo[$m] ?? '';

            return 'Top 5 donatur tertinggi ('.strtolower($namaBulan).' '.now()->year.')';
        }

        return match ($this->filter) {
            'last_month' => 'Top 5 donatur tertinggi (bulan lalu)',
            'year' => 'Top 5 donatur tertinggi (tahun ini)',
            'all' => 'Top 5 donatur tertinggi (semua waktu)',
            default => 'Top 5 donatur tertinggi (bulan ini)',
        };
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

        if ($this->filter && str_starts_with($this->filter, 'm_')) {
            $m = (int) substr($this->filter, 2);
            $query->whereYear('donasis.tgl_donasi', now()->year)
                ->whereMonth('donasis.tgl_donasi', $m);
        } elseif ($this->filter === 'year') {
            $query->whereYear('donasis.tgl_donasi', now()->year);
        } elseif ($this->filter === 'all') {
            // Tanpa filter tanggal
        } elseif ($this->filter === 'last_month') {
            $last = now()->subMonth();
            $query->whereYear('donasis.tgl_donasi', $last->year)
                ->whereMonth('donasis.tgl_donasi', $last->month);
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
