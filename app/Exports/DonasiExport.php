<?php

namespace App\Exports;

use App\Models\Donasi;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DonasiExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    public function __construct(
        protected $query = null
    ) {}

    public function collection(): Enumerable
    {
        return $this->query
            ? $this->query->with(['donatur', 'amil', 'jenisDana', 'program', 'verifiedByUser'])->get()
            : Donasi::with(['donatur', 'amil', 'jenisDana', 'program', 'verifiedByUser'])->latest()->get();
    }

    public function headings(): array
    {
        return [
            'No. Kwitansi',
            'Tanggal Donasi',
            'Nama Donatur',
            'Kode Donatur',
            'No. HP Donatur',
            'Jenis Dana',
            'Program / Campaign',
            'Jumlah Donasi (Rp)',
            'Metode Pembayaran',
            'Bank',
            'Amil Penerima',
            'Status',
            'Diverifikasi Oleh',
            'Waktu Verifikasi',
        ];
    }

    public function map(mixed $donasi): array
    {
        return [
            $donasi->no_kwitansi,
            $donasi->tgl_donasi ? $donasi->tgl_donasi->format('d/m/Y H:i') : '-',
            $donasi->donatur?->is_anonim ? 'Hamba Allah (Anonim)' : ($donasi->donatur?->nama ?? '-'),
            $donasi->donatur?->kode_donatur ?? '-',
            $donasi->donatur?->no_hp ?? '-',
            $donasi->jenisDana?->nama ?? '-',
            $donasi->program?->nama ?? 'Umum',
            $donasi->jumlah,
            ucfirst($donasi->metode_bayar ?? '-'),
            $donasi->bank ?? '-',
            $donasi->amil?->nama ?? '-',
            strtoupper(str_replace('_', ' ', $donasi->status ?? '-')),
            $donasi->verifiedByUser?->name ?? '-',
            $donasi->verified_at ? $donasi->verified_at->format('d/m/Y H:i') : '-',
        ];
    }

    public function styles(Worksheet $sheet): ?array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF059669'],
                ],
            ],
        ];
    }
}
