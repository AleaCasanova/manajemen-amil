<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashAdvanceRealisasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'cash_advance_id',
        'kategori_keuangan_id',
        'transaksi_keuangan_id',
        'tgl_realisasi',
        'uraian',
        'nominal',
        'bukti_nota',
    ];

    protected function casts(): array
    {
        return [
            'tgl_realisasi' => 'date',
            'nominal' => 'decimal:2',
        ];
    }

    public function cashAdvance(): BelongsTo
    {
        return $this->belongsTo(CashAdvance::class);
    }

    public function kategoriKeuangan(): BelongsTo
    {
        return $this->belongsTo(KategoriKeuangan::class);
    }

    public function transaksiKeuangan(): BelongsTo
    {
        return $this->belongsTo(TransaksiKeuangan::class);
    }
}
