<?php

namespace App\Exports;

use App\Models\Donasi;
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

class DonasiExport implements FromArray, ShouldAutoSize, WithStrictNullComparison, WithStyles, WithTitle
{
    protected int $rowCount = 0;

    public function __construct(
        protected $query = null
    ) {}

    public function title(): string
    {
        return 'Rekapitulasi Donasi';
    }

    public function headings(): array
    {
        return [
            'No.',
            'No. Kwitansi',
            'Tanggal Donasi',
            'Nama Donatur',
            'Kode Donatur',
            'No. HP Donatur',
            'Jenis Dana',
            'Program / Campaign',
            'Jumlah Donasi (Rp)',
            'Metode Pembayaran',
            'Bank / Kanal',
            'Amil Penerima',
            'Status',
            'Diverifikasi Oleh',
            'Waktu Verifikasi',
        ];
    }

    public function array(): array
    {
        $waktuCetak = Carbon::now('Asia/Jakarta')->format('d/m/Y H:i').' WIB';

        $items = $this->query
            ? $this->query->with(['donatur', 'amil', 'jenisDana', 'program', 'verifiedByUser'])->orderByDesc('id')->get()
            : Donasi::with(['donatur', 'amil', 'jenisDana', 'program', 'verifiedByUser'])->orderByDesc('id')->get();

        $this->rowCount = $items->count();
        $totalNominal = (float) $items->sum('jumlah');
        $totalNominalFmt = number_format($totalNominal, 0, ',', '.');
        $terverifikasiCount = $items->where('status', 'terverifikasi')->count();

        $rows = [
            ['REKAPITULASI PENERIMAAN DONASI — LAZ GERAK SEDEKAH CILACAP'],
            ["Dicetak pada: {$waktuCetak}  |  Total: {$this->rowCount} Transaksi ({$terverifikasiCount} Terverifikasi)  |  Total Terhimpun: Rp {$totalNominalFmt}"],
            $this->headings(),
        ];

        $no = 1;
        foreach ($items as $donasi) {
            $rows[] = [
                $no++,
                $donasi->no_kwitansi,
                $donasi->tgl_donasi ? $donasi->tgl_donasi->format('d/m/Y H:i') : '-',
                $donasi->donatur?->is_anonim ? 'Hamba Allah (Anonim)' : ($donasi->donatur?->nama ?? '-'),
                $donasi->donatur?->kode_donatur ?? '-',
                $donasi->donatur?->no_hp ?? '-',
                $donasi->jenisDana?->nama ?? '-',
                $donasi->program?->nama ?? 'Umum',
                (float) $donasi->jumlah,
                ucfirst($donasi->metode_bayar ?? '-'),
                $donasi->bank ?? '-',
                $donasi->amil?->nama ?? '-',
                strtoupper(str_replace('_', ' ', $donasi->status ?? '-')),
                $donasi->verifiedByUser?->name ?? '-',
                $donasi->verified_at ? $donasi->verified_at->format('d/m/Y H:i') : '-',
            ];
        }

        // Baris Total Akumulasi
        $rows[] = [
            'TOTAL PENGHIMPUNAN DONASI',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            $totalNominal,
            '',
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

        $sheet->mergeCells('A1:O1');
        $sheet->mergeCells('A2:O2');

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
        $sheet->getRowDimension(3)->setRowHeight(26);

        // Style Grid Data Rows & Total
        if ($this->rowCount > 0) {
            $sheet->getStyle("A3:O{$totalRow}")->applyFromArray([
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
            $sheet->getStyle("A4:C{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E4:F{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("J4:K{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("M4:O{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Format Rupiah kolom I (Jumlah)
            $sheet->getStyle("I4:I{$totalRow}")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("I4:I{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            // Style Baris Total
            $sheet->mergeCells("A{$totalRow}:H{$totalRow}");
            $sheet->getStyle("A{$totalRow}:O{$totalRow}")->applyFromArray([
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
