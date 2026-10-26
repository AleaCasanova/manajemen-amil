<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'amil_id',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'lat_masuk',
        'lng_masuk',
        'lat_pulang',
        'lng_pulang',
        'foto_masuk',
        'foto_pulang',
        'tipe_masuk',
        'tipe_pulang',
        'status',
        'keterangan',
        'keterangan_pulang',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'lat_masuk' => 'decimal:8',
            'lng_masuk' => 'decimal:8',
            'lat_pulang' => 'decimal:8',
            'lng_pulang' => 'decimal:8',
        ];
    }

    public function amil(): BelongsTo
    {
        return $this->belongsTo(Amil::class);
    }

    /**
     * Cek apakah presensi pulang dicatat lebih awal dari jam kerja resmi (sebelum 16:00)
     */
    public function isPulangAwal(): bool
    {
        if (empty($this->jam_pulang)) {
            return false;
        }

        if ($this->keterangan_pulang && str_contains($this->keterangan_pulang, 'Pulang Lebih Awal')) {
            return true;
        }

        $jamKerja = JamKerja::where('is_active', true)->first();
        $regularEnd = $jamKerja && $jamKerja->jam_pulang ? $jamKerja->jam_pulang : '16:00:00';

        return $this->jam_pulang < $regularEnd;
    }

    /**
     * Dapatkan rincian selisih menit pulang lebih awal jika ada
     */
    public function pulangAwalMenit(): ?int
    {
        if (! $this->isPulangAwal()) {
            return null;
        }

        if ($this->keterangan_pulang && preg_match('/Pulang Lebih Awal \((\d+) menit/', $this->keterangan_pulang, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }
}
