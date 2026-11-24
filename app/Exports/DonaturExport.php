<?php

namespace App\Exports;

use App\Models\Donatur;
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

class DonaturExport implements FromArray, ShouldAutoSize, WithStrictNullComparison, WithStyles, WithTitle
{
    protected int $rowCount = 0;

    public function __construct(
        protected $query = null
    ) {}

    public function title(): string
    {
        return 'Data Donatur';
    }

    public function headings(): array
    {
        return [
            'No.',
            'Kode Donatur',
            'Nama Donatur',
            'Tipe',
            'Kategori',
            'No. WhatsApp / HP',
            'Email',
            'Kecamatan',
            'Kota / Kabupaten',
            'Alamat Lengkap',
            'Amil PIC / Pembina',
            'Frekuensi Donasi',
            'Total Donasi (Rp)',
            'Tanggal Bergabung',
        ];
    }

    public function array(): array
    {
        $waktuCetak = Carbon::now('Asia/Jakarta')->format('d/m/Y H:i').' WIB';

        $baseQuery = $this->query
            ? $this->query->with(['kategori', 'amilPic'])->withCount('donasis')->withSum('donasis', 'jumlah')->orderByDesc('id')
            : Donatur::with(['kategori', 'amilPic'])->withCount('donasis')->withSum('donasis', 'jumlah')->orderByDesc('id');

        $items = $baseQuery->get();

        $this->rowCount = $items->count();
        $individuCount = $items->where('jenis', 'individu')->count();
        $lembagaCount = $items->where('jenis', 'lembaga')->count();
        $totalDonasi = (float) $items->sum('donasis_sum_jumlah');
        $totalDonasiFmt = number_format($totalDonasi, 0, ',', '.');

        $rows = [
            ['DATA DONATUR & CRM — LAZ GERAK SEDEKAH CILACAP'],
            ["Dicetak pada: {$waktuCetak}  |  Total: {$this->rowCount} Donatur ({$individuCount} Individu, {$lembagaCount} Lembaga)  |  Total Donasi Terhimpun: Rp {$totalDonasiFmt}"],
            $this->headings(),
        ];

        $no = 1;
        foreach ($items as $donatur) {
            $rows[] = [
                $no++,
                $donatur->kode_donatur ?? '-',
                $donatur->is_anonim ? 'Hamba Allah (Anonim)' : ($donatur->nama ?? '-'),
                ucfirst($donatur->jenis ?? '-'),
                $donatur->kategori?->nama ?? 'Umum',
                $donatur->no_hp ?? '-',
                $donatur->email ?? '-',
                $donatur->kecamatan ?? '-',
                $donatur->kota ?? '-',
                $donatur->alamat ?? '-',
                $donatur->amilPic?->nama ?? '-',
                $donatur->donasis_count ?? 0,
                (float) ($donatur->donasis_sum_jumlah ?? 0),
                $donatur->created_at ? $donatur->created_at->format('d/m/Y') : '-',
            ];
        }

        // Baris Total Akumulasi
        $rows[] = [
            'TOTAL AKUMULASI DONATUR & DONASI',
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
            '',
            $totalDonasi,
            '',
        ];

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        $lastDataRow = 3 + $this->rowCount;
        $totalRow = $lastDataRow + 1;

        $sheet->mergeCells('A1:N1');
        $sheet->mergeCells('A2:N2');

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
        $sheet->getStyle('A3:N3')->applyFromArray([
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
            $sheet->getStyle("A3:N{$totalRow}")->applyFromArray([
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
            $sheet->getStyle("A4:B{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D4:D{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F4:F{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("L4:L{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("N4:N{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Format Rupiah kolom M (Total Donasi)
            $sheet->getStyle("M4:M{$totalRow}")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("M4:M{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            // Style Baris Total
            $sheet->mergeCells("A{$totalRow}:L{$totalRow}");
            $sheet->getStyle("A{$totalRow}:N{$totalRow}")->applyFromArray([
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
