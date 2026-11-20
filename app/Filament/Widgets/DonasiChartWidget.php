<?php

namespace App\Filament\Widgets;

use App\Models\Donasi;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Livewire\Attributes\On;

class DonasiChartWidget extends ChartWidget
{
    public ?string $filter = null;

    public ?int $selectedMonth = null;

    public function mount(): void
    {
        $this->selectedMonth = now()->month;
        parent::mount();
    }

    #[On('dashboardMonthSelected')]
    public function selectMonth(int $month): void
    {
        $this->selectedMonth = $month;
        $this->cachedData = null;
        $year = (int) ($this->filter ?: now()->year);
        $this->dispatch('dashboardFilterUpdated', month: $month, year: $year);
    }

    public function updatedFilter($value): void
    {
        $this->cachedData = null;
        $year = (int) ($value ?: now()->year);
        $month = $this->selectedMonth ?? now()->month;
        $this->dispatch('dashboardFilterUpdated', month: $month, year: $year);
    }

    protected function getFilters(): ?array
    {
        $years = Donasi::selectRaw('YEAR(tgl_donasi) as yr')
            ->distinct()
            ->orderByDesc('yr')
            ->pluck('yr', 'yr')
            ->toArray();

        $currentYear = (string) now()->year;
        if (! isset($years[$currentYear])) {
            $years[$currentYear] = $currentYear;
        }

        return $years;
    }

    public function getHeading(): ?string
    {
        $selectedYear = $this->filter ?? now()->year;

        return "Tren Penerimaan Donasi Bulanan ({$selectedYear})";
    }

    public function getDescription(): ?string
    {
        $selectedYear = $this->filter ?? now()->year;
        $monthName = Carbon::create((int) $selectedYear, $this->selectedMonth ?? now()->month, 1)->translatedFormat('F');

        return "💡 Klik titik bulan pada grafik tren untuk menyinkronkan rincian di bawah (Terpilih: {$monthName} {$selectedYear})";
    }

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '250px';

    protected function getData(): array
    {
        $selectedYear = (int) ($this->filter ?? now()->year);
        $activeMonth = $this->selectedMonth ?? now()->month;

        $months = [];
        $totals = [];
        $pointRadii = [];
        $pointHoverRadii = [];
        $pointBgColors = [];
        $pointBorderColors = [];
        $pointBorderWidths = [];

        for ($i = 1; $i <= 12; $i++) {
            $monthName = Carbon::create($selectedYear, $i, 1)->translatedFormat('M');
            $months[] = $monthName;

            $total = Donasi::whereYear('tgl_donasi', $selectedYear)
                ->whereMonth('tgl_donasi', $i)
                ->where('status', 'terverifikasi')
                ->sum('jumlah');

            $totals[] = (float) $total;

            $isSelected = ($i === $activeMonth);
            $pointRadii[] = $isSelected ? 7 : 4;
            $pointHoverRadii[] = $isSelected ? 9 : 6;
            $pointBgColors[] = $isSelected ? '#059669' : '#10b981';
            $pointBorderColors[] = '#ffffff';
            $pointBorderWidths[] = $isSelected ? 3 : 2;
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
                    'pointBackgroundColor' => $pointBgColors,
                    'pointBorderColor' => $pointBorderColors,
                    'pointBorderWidth' => $pointBorderWidths,
                    'pointRadius' => $pointRadii,
                    'pointHoverRadius' => $pointHoverRadii,
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
                maintainAspectRatio: false,
                onClick: (event, elements, chart) => {
                    const evt = event.native || event;
                    let target = elements;
                    if (!target || !target.length) {
                        if (chart && typeof chart.getElementsAtEventForMode === 'function') {
                            target = chart.getElementsAtEventForMode(evt, 'nearest', { intersect: false }, true);
                        }
                    }
                    if (target && target.length > 0) {
                        const index = target[0].index;
                        const month = index + 1;
                        if (typeof Livewire !== 'undefined') {
                            Livewire.dispatch('dashboardMonthSelected', { month: month });
                        } else if (window.Livewire) {
                            window.Livewire.dispatch('dashboardMonthSelected', { month: month });
                        }
                    }
                },
                onHover: (event, elements, chart) => {
                    if (chart && chart.canvas) {
                        const evt = event.native || event;
                        let target = elements;
                        if (!target || !target.length) {
                            if (typeof chart.getElementsAtEventForMode === 'function') {
                                target = chart.getElementsAtEventForMode(evt, 'nearest', { intersect: false }, true);
                            }
                        }
                        chart.canvas.style.cursor = (target && target.length > 0) ? 'pointer' : 'default';
                    }
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
