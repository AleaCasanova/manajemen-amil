<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IzinCuti extends Model
{
    use HasFactory;

    protected $table = 'izin_cutis';

    protected $fillable = [
        'amil_id',
        'jenis',
        'tgl_mulai',
        'tgl_selesai',
        'alasan',
        'lampiran',
        'status',
        'approved_by',
        'catatan_admin',
    ];

    protected function casts(): array
    {
        return [
            'tgl_mulai' => 'date',
            'tgl_selesai' => 'date',
        ];
    }

    public function amil(): BelongsTo
    {
        return $this->belongsTo(Amil::class);
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Menyetujui pengajuan izin/cuti:
     * 1. Potong saldo cuti jika jenis cuti
     * 2. Otomatis catat ke tabel absensi sebagai status cuti/izin (bukan alpa)
     */
    public function approve(int $adminUserId): void
    {
        $mulai = Carbon::parse($this->tgl_mulai);
        $selesai = Carbon::parse($this->tgl_selesai);
        $durasi = $mulai->diffInDays($selesai) + 1;

        // Potong saldo cuti jika jenisnya cuti tahunan
        if ($this->jenis === 'cuti' && $this->amil_id) {
            $tahun = $mulai->year;
            $saldo = SaldoCuti::firstOrCreate(
                ['amil_id' => $this->amil_id, 'tahun' => $tahun],
                ['jatah' => 12, 'terpakai' => 0]
            );
            $saldo->increment('terpakai', $durasi);
        }

        // Sinkronisasi otomatis ke tabel absensis agar tercatat sebagai cuti / izin (bukan alpa)
        $current = $mulai->copy();
        while ($current->lte($selesai)) {
            Absensi::updateOrCreate(
                [
                    'amil_id' => $this->amil_id,
                    'tanggal' => $current->toDateString(),
                ],
                [
                    'status' => in_array($this->jenis, ['cuti', 'izin']) ? $this->jenis : 'izin',
                    'keterangan' => 'Pengajuan '.ucfirst($this->jenis).' Disetujui: '.($this->alasan ?? ''),
                ]
            );
            $current->addDay();
        }

        $this->update([
            'status' => 'disetujui',
            'approved_by' => $adminUserId,
        ]);
    }

    /**
     * Menolak pengajuan izin/cuti
     */
    public function reject(int $adminUserId, ?string $alasan = null): void
    {
        $this->update([
            'status' => 'ditolak',
            'approved_by' => $adminUserId,
            'catatan_admin' => $alasan,
        ]);
    }
}
