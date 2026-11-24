<?php

namespace App\Exports;

use App\Models\PengajuanDana;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PengajuanDanaExport implements FromArray, ShouldAutoSize, WithStrictNullComparison, WithStyles, WithTitle
{
    protected int $rowCount = 0;

    public function __construct(
        protected $query = null
    ) {}

    public function title(): string
    {
        return 'Rekap Pengajuan Dana Amil';
    }

    public function array(): array
    {
        $waktuCetak = Carbon::now('Asia/Jakarta')->format('d/m/Y H:i').' WIB';

        $items = $this->query
            ? $this->query->with(['amil', 'divisi', 'program', 'kategoriKeuangan', 'bendahara', 'manager', 'akunKeuangan'])->orderByDesc('id')->get()
            : PengajuanDana::with(['amil', 'divisi', 'program', 'kategoriKeuangan', 'bendahara', 'manager', 'akunKeuangan'])->orderByDesc('id')->get();

        $this->rowCount = $items->count();
        $totalDiajukan = (float) $items->sum('nominal_diajukan');
        $totalDisetujui = (float) $items->sum('nominal_disetujui');
        $totalDiajukanFmt = number_format($totalDiajukan, 0, ',', '.');
        $totalDisetujuiFmt = number_format($totalDisetujui, 0, ',', '.');

        $rows = [
            ['REKAPITULASI PENGAJUAN DANA AMIL — LAZ GERAK SEDEKAH CILACAP'],
            ["Dicetak pada: {$waktuCetak}  |  Total: {$this->rowCount} Permohonan  |  Total Diajukan: Rp {$totalDiajukanFmt}  |  Total Disetujui (ACC): Rp {$totalDisetujuiFmt}"],
            [
                'No.',
                'No. Registrasi',
                'Tanggal Diajukan',
                'Tanggal Kebutuhan',
                'Nama Amil Pemohon',
                'Divisi',
                'Keperluan / Judul',
                'Kategori Anggaran',
                'Program Terkait',
                'Nominal Diajukan (Rp)',
                'Nominal Disetujui (Rp)',
                'Status Otorisasi',
                'Review Bendahara',
                'ACC Manager',
                'Tanggal Pencairan',
                'Metode Pencairan',
            ],
        ];

        $no = 1;
        foreach ($items as $p) {
            $statusLabel = match ($p->status) {
                'diajukan' => 'Menunggu Review Bendahara',
                'diverifikasi_bendahara' => 'Menunggu ACC Manager',
                'disetujui' => 'Disetujui (Siap Cair)',
                'dicairkan' => 'Selesai Dicairkan',
                'ditolak' => 'Ditolak',
                default => strtoupper(str_replace('_', ' ', $p->status ?? '-')),
            };

            $rows[] = [
                $no++,
                $p->nomor_pengajuan,
                $p->created_at ? $p->created_at->format('d/m/Y H:i') : '-',
                $p->tgl_kebutuhan ? $p->tgl_kebutuhan->format('d/m/Y') : '-',
                $p->amil?->nama ?? '-',
                $p->divisi?->nama ?? ($p->amil?->divisi?->nama ?? '-'),
                $p->judul,
                $p->kategoriKeuangan?->nama ?? '-',
                $p->program?->nama ?? '-',
                (float) $p->nominal_diajukan,
                (float) ($p->nominal_disetujui ?? 0),
                $statusLabel,
                $p->bendahara?->name ?? ($p->tgl_verifikasi_bendahara ? 'Diverifikasi' : '-'),
                $p->manager?->name ?? ($p->tgl_acc_manager ? 'Disetujui' : '-'),
                $p->tgl_pencairan ? $p->tgl_pencairan->format('d/m/Y') : '-',
                $p->metode_pencairan ? strtoupper($p->metode_pencairan) : '-',
            ];
        }

        // Baris Total Akumulasi
        $rows[] = [
            'TOTAL AKUMULASI ANGGARAN',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            $totalDiajukan,
            $totalDisetujui,
            '',
            '',
            '',
            '',
            '',
        ];

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        $lastDataRow = 3 + $this->rowCount;
        $totalRow = $lastDataRow + 1;

        $sheet->mergeCells('A1:P1');
        $sheet->mergeCells('A2:P2');

        // Style Judul Banner (Hijau Tua)
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 13, 'color' => ['argb' => 'FF065F46']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);

        // Style Subtitle Info
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'size' => 10, 'color' => ['argb' => 'FF475569']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);

        // Style Table Header (Baris 3) - Hijau Emerald
        $sheet->getStyle('A3:P3')->applyFromArray([
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
        $sheet->getRowDimension(3)->setRowHeight(26);

        // Style Grid Data Rows & Total
        if ($this->rowCount > 0) {
            $sheet->getStyle("A3:P{$totalRow}")->applyFromArray([
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

            // Alignment kolom
            $sheet->getStyle("A4:D{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("L4:P{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Format Rupiah kolom J dan K
            $sheet->getStyle("J4:K{$totalRow}")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("J4:K{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            // Style Baris Total
            $sheet->mergeCells("A{$totalRow}:I{$totalRow}");
            $sheet->getStyle("A{$totalRow}:P{$totalRow}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 10, 'color' => ['argb' => 'FF0F172A']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFF1F5F9'],
                ],
            ]);
            $sheet->getStyle("A{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getRowDimension($totalRow)->setRowHeight(24);
        }

        return [];
    }
}
