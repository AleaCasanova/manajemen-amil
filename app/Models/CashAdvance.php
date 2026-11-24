<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class CashAdvance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'kode_ca',
        'tipe',
        'amil_id',
        'nama_pj',
        'divisi_id',
        'program_id',
        'pengajuan_dana_id',
        'akun_keuangan_id',
        'tgl_pencairan',
        'tgl_jatuh_tempo',
        'nominal_dicairkan',
        'nominal_realisasi',
        'nominal_dikembalikan',
        'nominal_reimburse',
        'keperluan',
        'status',
        'tgl_selesai',
        'akun_penyelesaian_id',
        'catatan_penyelesaian',
        'bukti_pencairan',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tgl_pencairan' => 'date',
            'tgl_jatuh_tempo' => 'date',
            'tgl_selesai' => 'date',
            'nominal_dicairkan' => 'decimal:2',
            'nominal_realisasi' => 'decimal:2',
            'nominal_dikembalikan' => 'decimal:2',
            'nominal_reimburse' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $ca): void {
            if (empty($ca->kode_ca)) {
                $prefix = ($ca->tipe === 'cash_bon' ? 'CB-' : 'CA-').now()->format('Ym').'-';
                $latest = self::withTrashed()
                    ->where('kode_ca', 'like', $prefix.'%')
                    ->orderByDesc('id')
                    ->first();

                $sequence = 1;
                if ($latest && preg_match('/'.preg_quote($prefix, '/').'(\d+)/', $latest->kode_ca, $matches)) {
                    $sequence = ((int) $matches[1]) + 1;
                }

                $ca->kode_ca = $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
            }

            if (empty($ca->created_by) && Auth::check()) {
                $ca->created_by = Auth::id();
            }
        });
    }

    public function amil(): BelongsTo
    {
        return $this->belongsTo(Amil::class);
    }

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function pengajuanDana(): BelongsTo
    {
        return $this->belongsTo(PengajuanDana::class);
    }

    public function akunKeuangan(): BelongsTo
    {
        return $this->belongsTo(AkunKeuangan::class, 'akun_keuangan_id');
    }

    public function akunPenyelesaian(): BelongsTo
    {
        return $this->belongsTo(AkunKeuangan::class, 'akun_penyelesaian_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function realisasis(): HasMany
    {
        return $this->hasMany(CashAdvanceRealisasi::class);
    }

    public function transaksiKeuangans(): HasMany
    {
        return $this->hasMany(TransaksiKeuangan::class);
    }

    public function getSisaTanggunganAttribute(): float
    {
        if (in_array($this->status, ['selesai', 'dibatalkan'], true)) {
            return 0.0;
        }

        if ($this->tipe === 'cash_bon') {
            return max(0.0, (float) $this->nominal_dicairkan - (float) $this->nominal_dikembalikan);
        }

        return (float) $this->nominal_dicairkan;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'aktif' => $this->tipe === 'cash_bon' ? 'Belum Lunas' : 'Aktif / Belum LPJ',
            'sebagian' => 'Cicilan Sebagian',
            'selesai' => $this->tipe === 'cash_bon' ? 'Lunas' : 'Selesai (LPJ)',
            'dibatalkan' => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'aktif' => 'warning',
            'sebagian' => 'info',
            'selesai' => 'success',
            'dibatalkan' => 'danger',
            default => 'gray',
        };
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->whereIn('status', ['aktif', 'sebagian']);
    }

    public function scopeCashAdvance(Builder $query): Builder
    {
        return $query->where('tipe', 'cash_advance');
    }

    public function scopeCashBon(Builder $query): Builder
    {
        return $query->where('tipe', 'cash_bon');
    }
}
