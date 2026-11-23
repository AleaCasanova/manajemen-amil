<?php

namespace App\Exports;

use App\Models\TransaksiKeuangan;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransaksiKeuanganExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    protected int $rowNumber = 0;

    public function __construct(
        protected $query = null
    ) {}

    public function collection(): Enumerable
    {
        return $this->query
            ? $this->query->with(['akunKeuangan', 'kategoriKeuangan', 'creator', 'program'])->orderBy('tgl_transaksi', 'desc')->orderBy('id', 'desc')->get()
            : TransaksiKeuangan::with(['akunKeuangan', 'kategoriKeuangan', 'creator', 'program'])->orderBy('tgl_transaksi', 'desc')->orderBy('id', 'desc')->get();
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

    public function map(mixed $trx): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $trx->kode_transaksi,
            $trx->tgl_transaksi ? $trx->tgl_transaksi->format('d/m/Y') : '-',
            $trx->jenis === 'masuk' ? 'KAS MASUK' : 'KAS KELUAR',
            $trx->kategoriKeuangan?->nama ?? '-',
            $trx->akunKeuangan?->nama ?? '-',
            $trx->pihak_terkait ?? '-',
            $trx->jenis === 'masuk' ? (float) $trx->nominal : 0,
            $trx->jenis === 'keluar' ? (float) $trx->nominal : 0,
            $trx->program?->nama ?? '-',
            $trx->keterangan,
            $trx->creator?->name ?? 'Sistem',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1E293B'],
                ],
            ],
        ];
    }
}
