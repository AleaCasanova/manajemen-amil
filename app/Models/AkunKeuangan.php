<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AkunKeuangan extends Model
{
    use HasFactory, SoftDeletes;

    public const FUNGSI_LABELS = [
        'kas_teller' => 'Kas Tunai / Teller',
        'penerimaan' => 'Rekening Penerimaan',
        'penampung' => 'Rekening Penampung',
        'operasional' => 'Rekening Operasional',
        'program' => 'Rekening Program',
        'zakat_wakaf' => 'Rekening Zakat & Wakaf',
    ];

    protected $fillable = [
        'kode_coa',
        'kategori_keuangan_id',
        'nama',
        'nama_bank',
        'nomor_rekening',
        'atas_nama',
        'tipe',
        'fungsi',
        'saldo_awal',
        'saldo_minimal',
        'saldo_riil_terakhir',
        'tgl_rekonsiliasi_terakhir',
        'is_active',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'saldo_awal' => 'decimal:2',
            'saldo_minimal' => 'decimal:2',
            'saldo_riil_terakhir' => 'decimal:2',
            'tgl_rekonsiliasi_terakhir' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function kategoriKeuangan(): BelongsTo
    {
        return $this->belongsTo(KategoriKeuangan::class, 'kategori_keuangan_id');
    }

    public function transaksiKeuangans(): HasMany
    {
        return $this->hasMany(TransaksiKeuangan::class);
    }

    public function cashAdvances(): HasMany
    {
        return $this->hasMany(CashAdvance::class);
    }

    public function cashOpnames(): HasMany
    {
        return $this->hasMany(CashOpname::class);
    }

    /**
     * Eager load agregat total masuk & keluar untuk mencegah N+1 query.
     */
    public function scopeWithMutasiTotals(Builder $query): Builder
    {
        return $query
            ->withSum(['transaksiKeuangans as total_masuk' => fn (Builder $q) => $q->where('jenis', 'masuk')], 'nominal')
            ->withSum(['transaksiKeuangans as total_keluar' => fn (Builder $q) => $q->where('jenis', 'keluar')], 'nominal');
    }

    public function getTotalMasukComputedAttribute(): float
    {
        if (array_key_exists('total_masuk', $this->attributes)) {
            return (float) ($this->attributes['total_masuk'] ?? 0);
        }

        return (float) $this->transaksiKeuangans()->where('jenis', 'masuk')->sum('nominal');
    }

    public function getTotalKeluarComputedAttribute(): float
    {
        if (array_key_exists('total_keluar', $this->attributes)) {
            return (float) ($this->attributes['total_keluar'] ?? 0);
        }

        return (float) $this->transaksiKeuangans()->where('jenis', 'keluar')->sum('nominal');
    }

    /**
     * Hitung saldo aktual (Saldo Awal + Total Masuk - Total Keluar).
     */
    public function getSaldoSekarangAttribute(): float
    {
        return ((float) $this->saldo_awal) + $this->total_masuk_computed - $this->total_keluar_computed;
    }

    public function getSelisihRekonsiliasiAttribute(): ?float
    {
        if ($this->saldo_riil_terakhir === null) {
            return null;
        }

        return $this->saldo_sekarang - (float) $this->saldo_riil_terakhir;
    }

    public function getSaldoTersediaAttribute(): float
    {
        return $this->saldo_sekarang - (float) $this->saldo_minimal;
    }

    public function hitungSaldoSampaiTanggal(string $tanggal): float
    {
        $masuk = (float) $this->transaksiKeuangans()
            ->where('jenis', 'masuk')
            ->whereDate('tgl_transaksi', '<=', $tanggal)
            ->sum('nominal');

        $keluar = (float) $this->transaksiKeuangans()
            ->where('jenis', 'keluar')
            ->whereDate('tgl_transaksi', '<=', $tanggal)
            ->sum('nominal');

        return ((float) $this->saldo_awal) + $masuk - $keluar;
    }
}
