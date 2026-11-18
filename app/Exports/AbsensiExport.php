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

class AbsensiExport implements Export, WithMultipleSheets
{
    protected Collection $amils;

    protected int $month;

    protected int $year;

    public function __construct(Collection $amils, ?int $month = null, ?int $year = null)
    {
        $this->amils = $amils;
        $this->month = $month ?? (int) now('Asia/Jakarta')->month;
        $this->year = $year ?? (int) now('Asia/Jakarta')->year;
    }

    public function sheets(): array
    {
        return [
            new AbsensiRekapBulananSheet($this->amils, $this->month, $this->year),
            new AbsensiRincianHarianSheet($this->amils, $this->month, $this->year),
        ];
    }
}

class AbsensiRekapBulananSheet implements FromArray, ShouldAutoSize, WithStrictNullComparison, WithStyles, WithTitle
{
    public function __construct(
        protected Collection $amils,
        protected int $month,
        protected int $year
    ) {}

    public function title(): string
    {
        return 'Rekap Kehadiran Bulanan';
    }

    public function array(): array
    {
        $periodeLabel = Carbon::createFromDate($this->year, $this->month, 1)
            ->locale('id')
            ->translatedFormat('F Y');
        $waktuCetak = Carbon::now('Asia/Jakarta')->format('d/m/Y H:i').' WIB';

        $rows = [
            ['REKAPITULASI KEHADIRAN AMIL — LAZ GERAK SEDEKAH CILACAP'],
            ["Periode Rekap: {$periodeLabel}  |  Dicetak pada: {$waktuCetak}  |  Total Personel: {$this->amils->count()} Orang"],
            [
                'No',
                'NIP',
                'Nama Lengkap Amil',
                'Divisi',
                'Jabatan',
                'Skema Kerja',
                'Hadir Tepat Waktu (Hari)',
                'Terlambat (Kali)',
                'Total Hadir (Hari)',
                'Izin (Hari)',
                'Cuti (Hari)',
                'Alpa / Tanpa Ket. (Hari)',
                'Total Jam Kerja',
                'Rata-rata Jam / Hari',
                'Tingkat Kehadiran',
            ],
        ];

        $now = Carbon::now('Asia/Jakarta');
        $startOfMonth = Carbon::createFromDate($this->year, $this->month, 1, 'Asia/Jakarta')->startOfDay();
        $endOfPeriod = ($this->year === (int) $now->year && $this->month === (int) $now->month)
            ? $now->copy()->startOfDay()
            : $startOfMonth->copy()->endOfMonth()->startOfDay();

        $no = 1;
        foreach ($this->amils as $amil) {
            /** @var Amil $amil */
            $monthAbsens = $amil->absensis
                ->filter(function ($a) {
                    $tgl = Carbon::parse($a->tanggal);

                    return (int) $tgl->month === $this->month && (int) $tgl->year === $this->year;
                });

            $tepatWaktu = $monthAbsens->where('status', 'hadir')->count();
            $terlambat = $monthAbsens->where('status', 'terlambat')->count();
            $totalHadir = $tepatWaktu + $terlambat;
            $izin = $monthAbsens->where('status', 'izin')->count();
            $cuti = $monthAbsens->where('status', 'cuti')->count();
            $alpa = $monthAbsens->where('status', 'alpa')->count();

            $statsJam = $amil->getTotalJamKerjaBulan($this->month, $this->year);

            $isMagang = $amil->isMagang();
            $isRelawan = $amil->isRelawan();

            if ($isRelawan) {
                $skemaLabel = 'Bebas Absen (Relawan)';
                $tingkatKehadiranLabel = '-';
            } else {
                $skemaLabel = $isMagang ? '5 Hari Kerja (Sen-Jum)' : '6 Hari Kerja (Sen-Sab)';
                $cursor = $startOfMonth->copy();
                $workingDays = 0;

                while ($cursor->lte($endOfPeriod)) {
                    if ($isMagang) {
                        if (! $cursor->isWeekend()) {
                            $workingDays++;
                        }
                    } else {
                        if (! $cursor->isSunday()) {
                            $workingDays++;
                        }
                    }
                    $cursor->addDay();
                }

                $target = max(1, $workingDays);
                $persen = min(100, (int) round(($totalHadir / $target) * 100));
                $tingkatKehadiranLabel = "{$persen}% ({$totalHadir}/{$target} Hari)";
            }

            $rows[] = [
                $no++,
                $amil->nip ?? '-',
                $amil->nama,
                $amil->divisi?->nama ?? '-',
                $amil->jabatan_label,
                $skemaLabel,
                $tepatWaktu,
                $terlambat,
                $totalHadir,
                $izin,
                $cuti,
                $alpa,
                $statsJam['formatted_lengkap'],
                $statsJam['rata_rata_jam_harian'].' Jam/hari',
                $tingkatKehadiranLabel,
            ];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = max(3, 3 + $this->amils->count());

        $sheet->mergeCells('A1:O1');
        $sheet->mergeCells('A2:O2');

        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 13, 'color' => ['argb' => 'FF065F46']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);

        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'size' => 10, 'color' => ['argb' => 'FF475569']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);

        $sheet->getStyle('A3:O3')->applyFromArray([
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
            $sheet->getStyle("A3:O{$lastRow}")->applyFromArray([
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
            $sheet->getStyle("D4:L{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("M4:O{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        return [];
    }
}

class AbsensiRincianHarianSheet implements FromArray, ShouldAutoSize, WithStrictNullComparison, WithStyles, WithTitle
{
    protected int $rowCount = 0;

    public function __construct(
        protected Collection $amils,
        protected int $month,
        protected int $year
    ) {}

    public function title(): string
    {
        return 'Rincian Presensi Harian';
    }

    public function array(): array
    {
        $periodeLabel = Carbon::createFromDate($this->year, $this->month, 1)
            ->locale('id')
            ->translatedFormat('F Y');
        $waktuCetak = Carbon::now('Asia/Jakarta')->format('d/m/Y H:i').' WIB';

        $rows = [
            ['RINCIAN LOG PRESENSI HARIAN AMIL — LAZ GERAK SEDEKAH CILACAP'],
            ["Periode: {$periodeLabel}  |  Dicetak pada: {$waktuCetak}"],
            [
                'No',
                'Tanggal',
                'Hari',
                'NIP',
                'Nama Lengkap Amil',
                'Divisi',
                'Jabatan',
                'Jam Masuk',
                'Jam Pulang',
                'Durasi Kerja',
                'Tipe Presensi',
                'Status Kehadiran',
                'Keterangan',
            ],
        ];

        $no = 1;
        foreach ($this->amils as $amil) {
            /** @var Amil $amil */
            $records = $amil->absensis
                ->filter(function ($a) {
                    $tgl = Carbon::parse($a->tanggal);

                    return (int) $tgl->month === $this->month && (int) $tgl->year === $this->year;
                })
                ->sortByDesc('tanggal');

            foreach ($records as $absen) {
                $tglCarbon = Carbon::parse($absen->tanggal)->locale('id');
                $isHadir = in_array($absen->status, ['hadir', 'terlambat'], true);

                $jamMasuk = ($isHadir && $absen->jam_masuk) ? Carbon::parse($absen->jam_masuk)->format('H:i') : '-';
                $jamPulang = ($isHadir && $absen->jam_pulang) ? Carbon::parse($absen->jam_pulang)->format('H:i') : '-';
                $durasi = $isHadir ? $absen->durasi_kerja_formatted : '-';

                $tipeLabel = '-';
                if ($isHadir) {
                    $tipeLabel = ($absen->tipe_masuk === 'dinas_luar' || $absen->tipe_pulang === 'dinas_luar')
                        ? 'Tugas Luar / Lapangan'
                        : 'Hadir di Kantor';
                }

                $statusLabel = match ($absen->status) {
                    'hadir' => 'Tepat Waktu',
                    'terlambat' => 'Terlambat',
                    'izin' => 'Izin',
                    'cuti' => 'Cuti',
                    'alpa' => 'Alpa (Tanpa Keterangan)',
                    default => ucfirst((string) $absen->status),
                };

                $rows[] = [
                    $no++,
                    $tglCarbon->format('d/m/Y'),
                    $tglCarbon->translatedFormat('l'),
                    $amil->nip ?? '-',
                    $amil->nama,
                    $amil->divisi?->nama ?? '-',
                    $amil->jabatan_label,
                    $jamMasuk,
                    $jamPulang,
                    $durasi,
                    $tipeLabel,
                    $statusLabel,
                    $absen->keterangan ?? '-',
                ];
            }
        }

        $this->rowCount = $no - 1;

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = max(3, 3 + $this->rowCount);

        $sheet->mergeCells('A1:M1');
        $sheet->mergeCells('A2:M2');

        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 13, 'color' => ['argb' => 'FF065F46']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);

        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'size' => 10, 'color' => ['argb' => 'FF475569']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);

        $sheet->getStyle('A3:M3')->applyFromArray([
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
            $sheet->getStyle("A3:M{$lastRow}")->applyFromArray([
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
            $sheet->getStyle("F4:L{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        return [];
    }
}
