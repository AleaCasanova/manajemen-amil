<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AkunKeuangan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nama',
        'nomor_rekening',
        'atas_nama',
        'tipe',
        'saldo_awal',
        'is_active',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'saldo_awal' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function transaksiKeuangans(): HasMany
    {
        return $this->hasMany(TransaksiKeuangan::class);
    }

    /**
     * Hitung saldo aktual (Saldo Awal + Total Masuk - Total Keluar)
     */
    public function getSaldoSekarangAttribute(): float
    {
        $masuk = (float) $this->transaksiKeuangans()->where('jenis', 'masuk')->sum('nominal');
        $keluar = (float) $this->transaksiKeuangans()->where('jenis', 'keluar')->sum('nominal');

        return ((float) $this->saldo_awal) + $masuk - $keluar;
    }
}
