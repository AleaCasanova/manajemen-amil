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
        return 'Buku Kas & Bank';
    }

    public function headings(): array
    {
        return [
            'No.',
            'Kode Transaksi',
            'No. Voucher',
            'Tanggal',
            'Tipe',
            'Jenis',
            'No. COA',
            'Uraian COA',
            'Akun Kas / Bank',
            'Uraian / Pihak Terkait',
            'Reff',
            'Nominal Masuk (Rp)',
            'Nominal Keluar (Rp)',
            'Program Terkait',
            'Sub-Program',
            'PJ',
            'Departemen',
            'Keterangan',
            'Dicatat Oleh',
        ];
    }

    public function array(): array
    {
        $waktuCetak = Carbon::now('Asia/Jakarta')->format('d/m/Y H:i').' WIB';
        $relations = ['akunKeuangan', 'kategoriKeuangan', 'creator', 'program', 'pjAmil', 'divisi'];

        $items = $this->query
            ? $this->query->with($relations)->orderBy('tgl_transaksi', 'desc')->orderBy('id', 'desc')->get()
            : TransaksiKeuangan::with($relations)->orderBy('tgl_transaksi', 'desc')->orderBy('id', 'desc')->get();

        $this->rowCount = $items->count();

        $totalMasuk = (float) $items->where('jenis', 'masuk')->sum('nominal');
        $totalKeluar = (float) $items->where('jenis', 'keluar')->sum('nominal');
        $saldoMutasi = $totalMasuk - $totalKeluar;

        $totalMasukFmt = number_format($totalMasuk, 0, ',', '.');
        $totalKeluarFmt = number_format($totalKeluar, 0, ',', '.');
        $saldoMutasiFmt = number_format($saldoMutasi, 0, ',', '.');

        $rows = [
            ['BUKU KAS & LAPORAN MUTASI REKENING — LAZ GERAK SEDEKAH CILACAP'],
            ["Dicetak pada: {$waktuCetak}  |  Total: {$this->rowCount} Transaksi  |  Total Masuk: Rp {$totalMasukFmt}  |  Total Keluar: Rp {$totalKeluarFmt}  |  Selisih Mutasi: Rp {$saldoMutasiFmt}"],
            $this->headings(),
        ];

        $no = 1;
        foreach ($items as $trx) {
            $rows[] = [
                $no++,
                $trx->kode_transaksi,
                $trx->no_voucher ?? '-',
                $trx->tgl_transaksi ? $trx->tgl_transaksi->format('d/m/Y') : '-',
                TransaksiKeuangan::TIPE_LABELS[$trx->tipe_transaksi] ?? ucfirst((string) $trx->tipe_transaksi),
                $trx->jenis === 'masuk' ? 'KAS MASUK' : 'KAS KELUAR',
                $trx->kategoriKeuangan?->kode_coa ?? '-',
                $trx->kategoriKeuangan?->nama ?? '-',
                $trx->akunKeuangan?->nama ?? '-',
                $trx->pihak_terkait ?? '-',
                $trx->reff ?? '-',
                $trx->jenis === 'masuk' ? (float) $trx->nominal : 0,
                $trx->jenis === 'keluar' ? (float) $trx->nominal : 0,
                $trx->program?->nama ?? '-',
                $trx->sub_program ?? '-',
                $trx->pjAmil?->nama ?? '-',
                $trx->divisi?->nama ?? '-',
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
            '',
            '',
            '',
            '',
            $totalMasuk,
            $totalKeluar,
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

        $sheet->mergeCells('A1:S1');
        $sheet->mergeCells('A2:S2');

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
        $sheet->getStyle('A3:S3')->applyFromArray([
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
            $sheet->getStyle("A3:S{$totalRow}")->applyFromArray([
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
            $sheet->getStyle("B4:G{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("S4:S{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Format Rupiah angka
            $sheet->getStyle("L4:M{$totalRow}")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("L4:M{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            // Style Baris Total
            $sheet->mergeCells("A{$totalRow}:K{$totalRow}");
            $sheet->getStyle("A{$totalRow}:S{$totalRow}")->applyFromArray([
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
