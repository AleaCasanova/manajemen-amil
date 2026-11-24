<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class KategoriKeuangan extends Model
{
    use HasFactory;

    public const KELOMPOK_LABELS = [
        'aset' => '100 — Aset',
        'kewajiban' => '200 — Kewajiban',
        'saldo_dana' => '300 — Saldo Dana',
        'penerimaan' => '400 — Penerimaan',
        'penyaluran' => '500 — Penyaluran & Beban',
    ];

    public const POS_DANA_LABELS = [
        'zakat' => 'Dana Zakat',
        'infak_terikat' => 'Infak Terikat',
        'infak_umum' => 'Infak Umum (Tidak Terikat)',
        'amil' => 'Dana Amil',
        'non_halal' => 'Dana Kebajikan (Non-Halal)',
        'wakaf' => 'Dana Wakaf',
        'netral' => 'Netral / Neraca',
    ];

    protected $fillable = [
        'kode_coa',
        'parent_id',
        'level',
        'nama',
        'kelompok',
        'pos_dana',
        'jenis',
        'saldo_normal',
        'is_postable',
        'deskripsi',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_postable' => 'boolean',
            'level' => 'integer',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function transaksiKeuangans(): HasMany
    {
        return $this->hasMany(TransaksiKeuangan::class);
    }

    public function akunKeuangan(): HasOne
    {
        return $this->hasOne(AkunKeuangan::class, 'kategori_keuangan_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopePostable(Builder $query): Builder
    {
        return $query->where('is_postable', true);
    }

    /**
     * Filter akun COA yang valid untuk arah arus kas ('masuk' atau 'keluar'), termasuk akun neraca 'netral'.
     */
    public function scopeUntukArahKas(Builder $query, string $arah): Builder
    {
        return $query->whereIn('jenis', [$arah, 'netral']);
    }

    public function scopeKelompok(Builder $query, string $kelompok): Builder
    {
        return $query->where('kelompok', $kelompok);
    }

    public function getLabelLengkapAttribute(): string
    {
        if (! empty($this->kode_coa)) {
            return "{$this->kode_coa} — {$this->nama}";
        }

        return $this->nama;
    }
}
