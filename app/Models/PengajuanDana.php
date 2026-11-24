<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class PengajuanDana extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pengajuan_danas';

    protected $fillable = [
        'nomor_pengajuan',
        'amil_id',
        'divisi_id',
        'program_id',
        'kategori_keuangan_id',
        'judul',
        'deskripsi',
        'nominal_diajukan',
        'tgl_kebutuhan',
        'nomor_dokumen_referensi',
        'dokumen_pendukung',
        'status',
        'bendahara_id',
        'tgl_verifikasi_bendahara',
        'catatan_bendahara',
        'manager_id',
        'tgl_acc_manager',
        'catatan_manager',
        'nominal_disetujui',
        'akun_keuangan_id',
        'tgl_pencairan',
        'metode_pencairan',
        'bukti_pencairan',
        'transaksi_keuangan_id',
        'alasan_penolakan',
    ];

    protected function casts(): array
    {
        return [
            'nominal_diajukan' => 'decimal:2',
            'nominal_disetujui' => 'decimal:2',
            'tgl_kebutuhan' => 'date',
            'tgl_pencairan' => 'date',
            'tgl_verifikasi_bendahara' => 'datetime',
            'tgl_acc_manager' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $pengajuan) {
            if (empty($pengajuan->nomor_pengajuan)) {
                $prefix = 'PR-'.now()->format('Ym').'-';
                $latest = self::where('nomor_pengajuan', 'like', $prefix.'%')
                    ->orderByDesc('id')
                    ->first();

                $sequence = 1;
                if ($latest && preg_match('/'.preg_quote($prefix, '/').'(\d+)/', $latest->nomor_pengajuan, $matches)) {
                    $sequence = ((int) $matches[1]) + 1;
                }

                $pengajuan->nomor_pengajuan = $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
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

    public function kategoriKeuangan(): BelongsTo
    {
        return $this->belongsTo(KategoriKeuangan::class);
    }

    public function bendahara(): BelongsTo
    {
        return $this->belongsTo(User::class, 'bendahara_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function akunKeuangan(): BelongsTo
    {
        return $this->belongsTo(AkunKeuangan::class);
    }

    public function transaksiKeuangan(): BelongsTo
    {
        return $this->belongsTo(TransaksiKeuangan::class);
    }

    // Scopes
    public function scopeDiajukan(Builder $query): Builder
    {
        return $query->where('status', 'diajukan');
    }

    public function scopeMenungguManager(Builder $query): Builder
    {
        return $query->where('status', 'diverifikasi_bendahara');
    }

    public function scopeDisetujui(Builder $query): Builder
    {
        return $query->where('status', 'disetujui');
    }

    public function scopeDicairkan(Builder $query): Builder
    {
        return $query->where('status', 'dicairkan');
    }

    public function getNominalFinalAttribute(): float
    {
        return (float) ($this->nominal_disetujui ?: $this->nominal_diajukan);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'diajukan' => 'Menunggu Review Bendahara',
            'diverifikasi_bendahara' => 'Menunggu ACC Manager',
            'disetujui' => 'Disetujui (Siap Dicairkan)',
            'dicairkan' => 'Dana Telah Dicairkan',
            'ditolak' => 'Pengajuan Ditolak',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'diajukan' => 'warning',
            'diverifikasi_bendahara' => 'info',
            'disetujui' => 'primary',
            'dicairkan' => 'success',
            'ditolak' => 'danger',
            default => 'gray',
        };
    }

    // Workflow Actions
    public function verifikasiBendahara(int $userId, ?string $catatan = null): void
    {
        $this->update([
            'status' => 'diverifikasi_bendahara',
            'bendahara_id' => $userId,
            'tgl_verifikasi_bendahara' => now(),
            'catatan_bendahara' => $catatan,
        ]);
    }

    public function accManager(int $userId, ?float $nominalDisetujui = null, ?string $catatan = null): void
    {
        $this->update([
            'status' => 'disetujui',
            'manager_id' => $userId,
            'tgl_acc_manager' => now(),
            'catatan_manager' => $catatan,
            'nominal_disetujui' => $nominalDisetujui ?: $this->nominal_diajukan,
        ]);
    }

    public function tolak(int $userId, string $alasan, string $role = 'manager'): void
    {
        $data = [
            'status' => 'ditolak',
            'alasan_penolakan' => $alasan,
        ];

        if ($role === 'bendahara') {
            $data['bendahara_id'] = $userId;
            $data['tgl_verifikasi_bendahara'] = now();
            $data['catatan_bendahara'] = $alasan;
        } else {
            $data['manager_id'] = $userId;
            $data['tgl_acc_manager'] = now();
            $data['catatan_manager'] = $alasan;
        }

        $this->update($data);
    }

    /**
     * Pencairan dana oleh bendahara dan otomatis sinkronisasi ke Buku Kas Yayasan
     */
    public function cairkan(int $userId, int $akunKeuanganId, string $metode = 'transfer', ?string $buktiPath = null): TransaksiKeuangan
    {
        return DB::transaction(function () use ($userId, $akunKeuanganId, $metode, $buktiPath) {
            $nominal = $this->nominal_disetujui ?: $this->nominal_diajukan;

            // Kategori keuangan fallback jika belum ditentukan
            $kategoriId = $this->kategori_keuangan_id;
            if (! $kategoriId) {
                $kat = KategoriKeuangan::where('jenis', 'keluar')
                    ->where('is_postable', true)
                    ->where('is_active', true)
                    ->where('nama', 'like', '%Penyaluran%')
                    ->first()
                    ?? KategoriKeuangan::where('jenis', 'keluar')
                        ->where('is_postable', true)
                        ->where('is_active', true)
                        ->first();
                $kategoriId = $kat?->id;
            }

            // 1. Buat transaksi kas keluar di Buku Kas & Keuangan
            $transaksi = TransaksiKeuangan::create([
                'jenis' => 'keluar',
                'kategori_keuangan_id' => $kategoriId,
                'akun_keuangan_id' => $akunKeuanganId,
                'program_id' => $this->program_id,
                'nominal' => $nominal,
                'tgl_transaksi' => now()->toDateString(),
                'pihak_terkait' => $this->amil?->nama ?? 'Amil Pemohon',
                'keterangan' => "Pencairan Pengajuan [{$this->nomor_pengajuan}]: {$this->judul}",
                'bukti_transaksi' => $buktiPath,
                'created_by' => $userId,
            ]);

            // 2. Update status pengajuan dana
            $this->update([
                'status' => 'dicairkan',
                'akun_keuangan_id' => $akunKeuanganId,
                'tgl_pencairan' => now()->toDateString(),
                'metode_pencairan' => $metode,
                'bukti_pencairan' => $buktiPath,
                'transaksi_keuangan_id' => $transaksi->id,
            ]);

            return $transaksi;
        });
    }
}
