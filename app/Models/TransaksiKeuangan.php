<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class TransaksiKeuangan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'kode_transaksi',
        'jenis',
        'kategori_keuangan_id',
        'akun_keuangan_id',
        'program_id',
        'donasi_id',
        'penyaluran_id',
        'nominal',
        'tgl_transaksi',
        'pihak_terkait',
        'keterangan',
        'bukti_transaksi',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'decimal:2',
            'tgl_transaksi' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $transaksi) {
            if (empty($transaksi->kode_transaksi)) {
                $prefix = 'TRX-'.now()->format('Ym').'-';
                $latest = self::where('kode_transaksi', 'like', $prefix.'%')
                    ->orderByDesc('id')
                    ->first();

                $sequence = 1;
                if ($latest && preg_match('/'.preg_quote($prefix, '/').'(\d+)/', $latest->kode_transaksi, $matches)) {
                    $sequence = ((int) $matches[1]) + 1;
                }

                $transaksi->kode_transaksi = $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
            }

            if (empty($transaksi->created_by) && Auth::check()) {
                $transaksi->created_by = Auth::id();
            }
        });
    }

    public function kategoriKeuangan(): BelongsTo
    {
        return $this->belongsTo(KategoriKeuangan::class, 'kategori_keuangan_id');
    }

    public function akunKeuangan(): BelongsTo
    {
        return $this->belongsTo(AkunKeuangan::class, 'akun_keuangan_id');
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function donasi(): BelongsTo
    {
        return $this->belongsTo(Donasi::class, 'donasi_id');
    }

    public function penyaluran(): BelongsTo
    {
        return $this->belongsTo(Penyaluran::class, 'penyaluran_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeMasuk(Builder $query): Builder
    {
        return $query->where('jenis', 'masuk');
    }

    public function scopeKeluar(Builder $query): Builder
    {
        return $query->where('jenis', 'keluar');
    }

    public function scopeBulanIni(Builder $query): Builder
    {
        return $query->whereMonth('tgl_transaksi', now()->month)
            ->whereYear('tgl_transaksi', now()->year);
    }
}
