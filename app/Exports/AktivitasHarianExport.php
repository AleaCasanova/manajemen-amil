<?php

namespace App\Exports;

use App\Models\Amil;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AktivitasHarianExport implements Export, WithMultipleSheets
{
    protected Collection $amils;

    protected int $month;

    protected int $year;

    protected string $statusFilter;

    public function __construct(
        Collection $amils,
        ?int $month = null,
        ?int $year = null,
        string $statusFilter = 'semua'
    ) {
        $this->amils = $amils;
        $this->month = $month ?? (int) now('Asia/Jakarta')->month;
        $this->year = $year ?? (int) now('Asia/Jakarta')->year;
        $this->statusFilter = $statusFilter;
    }

    public function sheets(): array
    {
        return [
            new AktivitasRekapBulananSheet($this->amils, $this->month, $this->year, $this->statusFilter),
            new AktivitasRincianLogSheet($this->amils, $this->month, $this->year, $this->statusFilter),
        ];
    }
}

class AktivitasRekapBulananSheet implements FromArray, ShouldAutoSize, WithStrictNullComparison, WithStyles, WithTitle
{
    public function __construct(
        protected Collection $amils,
        protected int $month,
        protected int $year,
        protected string $statusFilter = 'semua'
    ) {}

    public function title(): string
    {
        return 'Rekapitulasi Aktivitas Amil';
    }

    public function array(): array
    {
        $periodeLabel = Carbon::createFromDate($this->year, $this->month, 1)
            ->locale('id')
            ->translatedFormat('F Y');
        $waktuCetak = Carbon::now('Asia/Jakarta')->format('d/m/Y H:i').' WIB';

        $statusLabel = match ($this->statusFilter) {
            'disetujui' => 'Hanya Disetujui',
            'diajukan' => 'Hanya Menunggu Review',
            'ditolak' => 'Hanya Ditolak',
            default => 'Semua Status',
        };

        $rows = [
            ['REKAPITULASI AKTIVITAS HARIAN AMIL — LAZ GERAK SEDEKAH CILACAP'],
            ["Periode Rekap: {$periodeLabel}  |  Status Review: {$statusLabel}  |  Dicetak pada: {$waktuCetak}  |  Total Personel: {$this->amils->count()} Orang"],
            [
                'No',
                'NIP',
                'Nama Lengkap Amil',
                'Divisi',
                'Jabatan',
                'Total Aktivitas (Bulan Ini)',
                'Disetujui',
                'Menunggu Review',
                'Ditolak',
                'Aktivitas Terakhir',
                'Lokasi Terakhir',
            ],
        ];

        $no = 1;
        foreach ($this->amils as $amil) {
            /** @var Amil $amil */
            $monthAktivitas = $amil->aktivitasHarians
                ->filter(function ($a) {
                    $tgl = Carbon::parse($a->tanggal);
                    $matchMonth = (int) $tgl->month === $this->month && (int) $tgl->year === $this->year;
                    if (! $matchMonth) {
                        return false;
                    }

                    if ($this->statusFilter !== 'semua' && $a->status !== $this->statusFilter) {
                        return false;
                    }

                    return true;
                });

            $total = $monthAktivitas->count();
            $disetujui = $monthAktivitas->where('status', 'disetujui')->count();
            $diajukan = $monthAktivitas->where('status', 'diajukan')->count();
            $ditolak = $monthAktivitas->where('status', 'ditolak')->count();

            $lastAktivitas = $monthAktivitas->sortByDesc('tanggal')->first();
            $aktivitasTerakhirLabel = $lastAktivitas
                ? "{$lastAktivitas->judul} (".Carbon::parse($lastAktivitas->tanggal)->format('d/m/Y').')'
                : 'Belum ada laporan';
            $lokasiTerakhirLabel = $lastAktivitas?->lokasi ?: '-';

            $rows[] = [
                $no++,
                $amil->nip ?? '-',
                $amil->nama,
                $amil->divisi?->nama ?? '-',
                $amil->jabatan_label,
                $total,
                $disetujui,
                $diajukan,
                $ditolak,
                $aktivitasTerakhirLabel,
                $lokasiTerakhirLabel,
            ];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = max(3, 3 + $this->amils->count());

        $sheet->mergeCells('A1:K1');
        $sheet->mergeCells('A2:K2');

        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 13, 'color' => ['argb' => 'FF065F46']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);

        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'size' => 10, 'color' => ['argb' => 'FF475569']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);

        $sheet->getStyle('A3:K3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF059669'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getRowDimension(3)->setRowHeight(24);

        if ($lastRow >= 4) {
            $sheet->getStyle("A3:K{$lastRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FFCBD5E1'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            $sheet->getStyle("A4:B{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D4:I{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        return [];
    }
}

class AktivitasRincianLogSheet implements FromArray, ShouldAutoSize, WithStrictNullComparison, WithStyles, WithTitle
{
    protected int $rowCount = 0;

    public function __construct(
        protected Collection $amils,
        protected int $month,
        protected int $year,
        protected string $statusFilter = 'semua'
    ) {}

    public function title(): string
    {
        return 'Rincian Log Aktivitas';
    }

    public function array(): array
    {
        $periodeLabel = Carbon::createFromDate($this->year, $this->month, 1)
            ->locale('id')
            ->translatedFormat('F Y');
        $waktuCetak = Carbon::now('Asia/Jakarta')->format('d/m/Y H:i').' WIB';

        $statusLabel = match ($this->statusFilter) {
            'disetujui' => 'Hanya Disetujui',
            'diajukan' => 'Hanya Menunggu Review',
            'ditolak' => 'Hanya Ditolak',
            default => 'Semua Status',
        };

        $rows = [
            ['RINCIAN LOG AKTIVITAS HARIAN AMIL — LAZ GERAK SEDEKAH CILACAP'],
            ["Periode: {$periodeLabel}  |  Status Review: {$statusLabel}  |  Dicetak pada: {$waktuCetak}"],
            [
                'No',
                'Tanggal',
                'Hari',
                'NIP',
                'Nama Lengkap Amil',
                'Divisi',
                'Jabatan',
                'Judul Aktivitas',
                'Deskripsi Kegiatan',
                'Lokasi / Alamat',
                'Status Review',
            ],
        ];

        // Kumpulkan semua aktivitas dari koleksi amil yang relevan
        $allActivities = collect();
        foreach ($this->amils as $amil) {
            /** @var Amil $amil */
            $filtered = $amil->aktivitasHarians
                ->filter(function ($a) {
                    $tgl = Carbon::parse($a->tanggal);
                    $matchMonth = (int) $tgl->month === $this->month && (int) $tgl->year === $this->year;
                    if (! $matchMonth) {
                        return false;
                    }

                    if ($this->statusFilter !== 'semua' && $a->status !== $this->statusFilter) {
                        return false;
                    }

                    return true;
                })
                ->map(function ($a) use ($amil) {
                    return [
                        'item' => $a,
                        'amil' => $amil,
                    ];
                });

            $allActivities = $allActivities->concat($filtered);
        }

        // Urutkan berdasarkan tanggal terbaru
        $sorted = $allActivities->sortByDesc(fn ($entry) => $entry['item']->tanggal.' '.$entry['item']->created_at);

        $no = 1;
        foreach ($sorted as $entry) {
            $item = $entry['item'];
            $amil = $entry['amil'];

            $tglCarbon = Carbon::parse($item->tanggal)->locale('id');

            $statusText = match ($item->status) {
                'disetujui' => 'Disetujui',
                'diajukan' => 'Menunggu Review',
                'ditolak' => 'Ditolak',
                default => ucfirst((string) $item->status),
            };

            $cleanDeskripsi = trim(preg_replace('/\s+/', ' ', strip_tags((string) $item->deskripsi)));

            $rows[] = [
                $no++,
                $tglCarbon->format('d/m/Y'),
                $tglCarbon->translatedFormat('l'),
                $amil->nip ?? '-',
                $amil->nama,
                $amil->divisi?->nama ?? '-',
                $amil->jabatan_label,
                $item->judul,
                $cleanDeskripsi ?: '-',
                $item->lokasi ?: '-',
                $statusText,
            ];
        }

        $this->rowCount = $no - 1;

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = max(3, 3 + $this->rowCount);

        $sheet->mergeCells('A1:K1');
        $sheet->mergeCells('A2:K2');

        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 13, 'color' => ['argb' => 'FF065F46']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);

        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'size' => 10, 'color' => ['argb' => 'FF475569']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);

        $sheet->getStyle('A3:K3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF059669'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getRowDimension(3)->setRowHeight(24);

        if ($lastRow >= 4) {
            $sheet->getStyle("A3:K{$lastRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FFCBD5E1'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            $sheet->getStyle("A4:D{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F4:G{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("K4:K{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        return [];
    }
}
