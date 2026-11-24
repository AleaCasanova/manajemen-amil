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

    public const TIPE_LABELS = [
        'reguler' => 'Reguler',
        'transfer' => 'Transfer Antar Akun',
        'cash_advance_keluar' => 'Pencairan Cash Advance',
        'cash_advance_kembali' => 'Penyelesaian Cash Advance',
        'cash_advance_realisasi' => 'Realisasi Beban LPJ CA',
        'cash_bon_keluar' => 'Pencairan Cash Bon',
        'cash_bon_kembali' => 'Pengembalian Cash Bon',
    ];

    protected $fillable = [
        'kode_transaksi',
        'no_voucher',
        'reff',
        'jenis',
        'tipe_transaksi',
        'is_transfer',
        'transfer_group_kode',
        'transfer_pasangan_id',
        'kategori_keuangan_id',
        'akun_keuangan_id',
        'program_id',
        'sub_program',
        'donasi_id',
        'penyaluran_id',
        'cash_advance_id',
        'nominal',
        'tgl_transaksi',
        'pihak_terkait',
        'pj_amil_id',
        'divisi_id',
        'keterangan',
        'bukti_transaksi',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'decimal:2',
            'tgl_transaksi' => 'date',
            'is_transfer' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $transaksi): void {
            if (empty($transaksi->kode_transaksi)) {
                $prefix = 'TRX-'.now()->format('Ym').'-';
                $latest = self::withTrashed()
                    ->where('kode_transaksi', 'like', $prefix.'%')
                    ->orderByDesc('id')
                    ->first();

                $sequence = 1;
                if ($latest && preg_match('/'.preg_quote($prefix, '/').'(\d+)/', $latest->kode_transaksi, $matches)) {
                    $sequence = ((int) $matches[1]) + 1;
                }

                $transaksi->kode_transaksi = $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
            }

            if (empty($transaksi->tipe_transaksi)) {
                $transaksi->tipe_transaksi = $transaksi->is_transfer ? 'transfer' : 'reguler';
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

    public function transferPasangan(): BelongsTo
    {
        return $this->belongsTo(self::class, 'transfer_pasangan_id');
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

    public function cashAdvance(): BelongsTo
    {
        return $this->belongsTo(CashAdvance::class, 'cash_advance_id');
    }

    public function pjAmil(): BelongsTo
    {
        return $this->belongsTo(Amil::class, 'pj_amil_id');
    }

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class, 'divisi_id');
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

    public function scopeNonTransfer(Builder $query): Builder
    {
        return $query->where('is_transfer', false);
    }

    public function scopeTransfer(Builder $query): Builder
    {
        return $query->where('is_transfer', true);
    }

    /**
     * Filter transaksi yang merupakan arus kas riil yayasan (bukan pindah buku, bukan pencairan/pengembalian uang muka sementara).
     */
    public function scopeArusKasRiil(Builder $query): Builder
    {
        return $query->where('is_transfer', false)
            ->whereNotIn('tipe_transaksi', [
                'cash_advance_keluar',
                'cash_advance_kembali',
                'cash_bon_keluar',
                'cash_bon_kembali',
            ]);
    }

    public function scopeBulanIni(Builder $query): Builder
    {
        return $query->whereMonth('tgl_transaksi', now()->month)
            ->whereYear('tgl_transaksi', now()->year);
    }
}
