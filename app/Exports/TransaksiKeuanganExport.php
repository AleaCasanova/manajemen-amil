<?php

namespace App\Exports;

use App\Models\TransaksiKeuangan;
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

class TransaksiKeuanganExport implements FromArray, ShouldAutoSize, WithStrictNullComparison, WithStyles, WithTitle
{
    protected int $rowCount = 0;

    public function __construct(
        protected $query = null
    ) {}

    public function title(): string
    {
        return 'Buku Kas & Keuangan';
    }

    public function headings(): array
    {
        return [
            'No.',
            'Kode Transaksi',
            'Tanggal',
            'Jenis',
            'Kategori Pos',
            'Akun Kas / Bank',
            'Pihak Terkait (Penyetor/Penerima)',
            'Nominal Masuk (Rp)',
            'Nominal Keluar (Rp)',
            'Program Terkait',
            'Keterangan',
            'Dicatat Oleh',
        ];
    }

    public function array(): array
    {
        $waktuCetak = Carbon::now('Asia/Jakarta')->format('d/m/Y H:i').' WIB';

        $items = $this->query
            ? $this->query->with(['akunKeuangan', 'kategoriKeuangan', 'creator', 'program'])->orderBy('tgl_transaksi', 'desc')->orderBy('id', 'desc')->get()
            : TransaksiKeuangan::with(['akunKeuangan', 'kategoriKeuangan', 'creator', 'program'])->orderBy('tgl_transaksi', 'desc')->orderBy('id', 'desc')->get();

        $this->rowCount = $items->count();

        $totalMasuk = (float) $items->where('jenis', 'masuk')->sum('nominal');
        $totalKeluar = (float) $items->where('jenis', 'keluar')->sum('nominal');
        $saldoMutasi = $totalMasuk - $totalKeluar;

        $totalMasukFmt = number_format($totalMasuk, 0, ',', '.');
        $totalKeluarFmt = number_format($totalKeluar, 0, ',', '.');
        $saldoMutasiFmt = number_format($saldoMutasi, 0, ',', '.');

        $rows = [
            ['BUKU KAS & LAPORAN KEUANGAN — LAZ GERAK SEDEKAH CILACAP'],
            ["Dicetak pada: {$waktuCetak}  |  Total: {$this->rowCount} Transaksi  |  Total Masuk: Rp {$totalMasukFmt}  |  Total Keluar: Rp {$totalKeluarFmt}  |  Surplus/Defisit: Rp {$saldoMutasiFmt}"],
            $this->headings(),
        ];

        $no = 1;
        foreach ($items as $trx) {
            $rows[] = [
                $no++,
                $trx->kode_transaksi,
                $trx->tgl_transaksi ? $trx->tgl_transaksi->format('d/m/Y') : '-',
                $trx->jenis === 'masuk' ? 'KAS MASUK' : 'KAS KELUAR',
                $trx->kategoriKeuangan?->nama ?? '-',
                $trx->akunKeuangan?->nama ?? '-',
                $trx->pihak_terkait ?? '-',
                $trx->jenis === 'masuk' ? (float) $trx->nominal : 0,
                $trx->jenis === 'keluar' ? (float) $trx->nominal : 0,
                $trx->program?->nama ?? '-',
                $trx->keterangan ?? '-',
                $trx->creator?->name ?? 'Sistem',
            ];
        }

        // Baris Total Akumulasi
        $rows[] = [
            'TOTAL AKUMULASI',
            '',
            '',
            '',
            '',
            '',
            '',
            $totalMasuk,
            $totalKeluar,
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

        $sheet->mergeCells('A1:L1');
        $sheet->mergeCells('A2:L2');

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
        $sheet->getStyle('A3:L3')->applyFromArray([
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
            $sheet->getStyle("A3:L{$totalRow}")->applyFromArray([
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
            $sheet->getStyle("A4:A{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B4:D{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("L4:L{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Format Rupiah angka
            $sheet->getStyle("H4:I{$totalRow}")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("H4:I{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            // Style Baris Total
            $sheet->mergeCells("A{$totalRow}:G{$totalRow}");
            $sheet->getStyle("A{$totalRow}:L{$totalRow}")->applyFromArray([
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
