<?php

namespace App\Models;

use Carbon\Carbon;
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

    /**
     * Hitung durasi kerja dalam satuan menit (selisih jam masuk dan pulang)
     */
    public function getDurasiMenitAttribute(): int
    {
        if (empty($this->jam_masuk) || empty($this->jam_pulang)) {
            return 0;
        }

        try {
            $masuk = Carbon::parse($this->jam_masuk);
            $pulang = Carbon::parse($this->jam_pulang);

            if ($pulang->lessThan($masuk)) {
                $pulang->addDay();
            }

            $diffMinutes = $masuk->diffInMinutes($pulang);

            return (int) min($diffMinutes, 960); // Cap 16 jam per shift
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * Durasi kerja dalam satuan jam desimal (misal 8.5)
     */
    public function getDurasiJamDecimalAttribute(): float
    {
        return round($this->durasi_menit / 60, 1);
    }

    /**
     * Menit kerja berjalan untuk amil yang sedang bertugas hari ini dan belum clock out
     */
    public function getMenitBerjalanHariIniAttribute(): int
    {
        if (empty($this->jam_masuk) || ! empty($this->jam_pulang)) {
            return 0;
        }

        $tanggalStr = $this->tanggal instanceof Carbon ? $this->tanggal->toDateString() : (string) $this->tanggal;
        if ($tanggalStr !== today()->toDateString()) {
            return 0;
        }

        try {
            $masuk = Carbon::parse($this->jam_masuk);
            $now = now();
            if ($now->greaterThan($masuk)) {
                return (int) min($masuk->diffInMinutes($now), 720);
            }
        } catch (\Throwable $e) {
            return 0;
        }

        return 0;
    }

    /**
     * Format ringkas durasi kerja, misal "8j 47m" atau "Sedang Bekerja"
     */
    public function getDurasiKerjaFormattedAttribute(): string
    {
        if (! empty($this->jam_masuk) && ! empty($this->jam_pulang)) {
            $menit = $this->durasi_menit;
            $jam = intdiv($menit, 60);
            $sisaMenit = $menit % 60;

            if ($jam > 0 && $sisaMenit > 0) {
                return "{$jam}j {$sisaMenit}m";
            } elseif ($jam > 0) {
                return "{$jam} Jam";
            } else {
                return "{$sisaMenit}m";
            }
        }

        $tanggalStr = $this->tanggal instanceof Carbon ? $this->tanggal->toDateString() : (string) $this->tanggal;

        if (! empty($this->jam_masuk) && empty($this->jam_pulang)) {
            if ($tanggalStr === today()->toDateString()) {
                $berjalan = $this->menit_berjalan_hari_ini;
                if ($berjalan > 0) {
                    $jam = intdiv($berjalan, 60);
                    $sisaMenit = $berjalan % 60;

                    return "Sedang Bekerja ({$jam}j {$sisaMenit}m)";
                }

                return 'Sedang Bekerja';
            }

            return 'Belum Clock Out';
        }

        return '-';
    }

    /**
     * Format lengkap durasi kerja, misal "8 Jam 47 Menit"
     */
    public function getDurasiKerjaLengkapAttribute(): string
    {
        if (! empty($this->jam_masuk) && ! empty($this->jam_pulang)) {
            $menit = $this->durasi_menit;
            $jam = intdiv($menit, 60);
            $sisaMenit = $menit % 60;

            if ($jam > 0 && $sisaMenit > 0) {
                return "{$jam} Jam {$sisaMenit} Menit";
            } elseif ($jam > 0) {
                return "{$jam} Jam";
            } else {
                return "{$sisaMenit} Menit";
            }
        }

        return $this->durasi_kerja_formatted;
    }
}
