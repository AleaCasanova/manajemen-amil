<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Amil extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nip',
        'nama',
        'jabatan',
        'divisi_id',
        'no_hp',
        'alamat',
        'foto',
        'tgl_bergabung',
        'target_bulanan',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tgl_bergabung' => 'date',
            'target_bulanan' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class);
    }

    public function donaturs(): HasMany
    {
        return $this->hasMany(Donatur::class, 'amil_pic_id');
    }

    public function donasis(): HasMany
    {
        return $this->hasMany(Donasi::class);
    }

    public function absensis(): HasMany
    {
        return $this->hasMany(Absensi::class);
    }

    public function izinCutis(): HasMany
    {
        return $this->hasMany(IzinCuti::class);
    }

    public function saldoCutis(): HasMany
    {
        return $this->hasMany(SaldoCuti::class);
    }

    public function aktivitasHarians(): HasMany
    {
        return $this->hasMany(AktivitasHarian::class);
    }

    public function agendas(): BelongsToMany
    {
        return $this->belongsToMany(Agenda::class, 'agenda_amil')
            ->withTimestamps();
    }

    public function penyalurans(): HasMany
    {
        return $this->hasMany(Penyaluran::class);
    }

    public const JABATAN_KETUA = 'ketua';

    public const JABATAN_STAFF = 'staff';

    public const JABATAN_DRIVER = 'driver';

    public const JABATAN_RELAWAN = 'relawan';

    public const JABATAN_MAGANG = 'magang';

    public static function getJabatanOptions(): array
    {
        return [
            self::JABATAN_KETUA => 'Ketua',
            self::JABATAN_STAFF => 'Staff',
            self::JABATAN_DRIVER => 'Driver',
            self::JABATAN_RELAWAN => 'Relawan',
            self::JABATAN_MAGANG => 'Magang',
        ];
    }

    public function getCleanJabatan(): string
    {
        $val = strtolower(trim($this->jabatan ?? 'staff'));

        if (str_contains($val, 'magang') || str_contains($val, 'intern')) {
            return self::JABATAN_MAGANG;
        }

        if (str_contains($val, 'relawan') || str_contains($val, 'volunteer')) {
            return self::JABATAN_RELAWAN;
        }

        if (str_contains($val, 'driver') || str_contains($val, 'supir')) {
            return self::JABATAN_DRIVER;
        }

        if (str_contains($val, 'ketua') || str_contains($val, 'pimpinan') || str_contains($val, 'kepala')) {
            return self::JABATAN_KETUA;
        }

        return self::JABATAN_STAFF;
    }

    public function isRelawan(): bool
    {
        return $this->getCleanJabatan() === self::JABATAN_RELAWAN;
    }

    public function isMagang(): bool
    {
        return $this->getCleanJabatan() === self::JABATAN_MAGANG;
    }

    public function isKetua(): bool
    {
        return $this->getCleanJabatan() === self::JABATAN_KETUA;
    }

    public function isDriver(): bool
    {
        return $this->getCleanJabatan() === self::JABATAN_DRIVER;
    }

    public function isStaff(): bool
    {
        return $this->getCleanJabatan() === self::JABATAN_STAFF;
    }

    /**
     * Fitur Akses: Input Donasi & Database Donatur
     * (Semua peran bisa KECUALI Anak Magang)
     */
    public function canAccessDonasi(): bool
    {
        return ! $this->isMagang();
    }

    /**
     * Fitur Akses: Presensi Kehadiran & Laporan Aktivitas
     * (Semua peran wajib/bisa KECUALI Relawan yang bebas beban absen)
     */
    public function needsAttendance(): bool
    {
        return ! $this->isRelawan();
    }

    /**
     * Fitur Akses: Pengajuan Izin & Cuti
     */
    public function canAccessIzinCuti(): bool
    {
        return ! $this->isRelawan();
    }

    /**
     * Fitur Akses: Penyaluran Bantuan 8 Asnaf
     * (Hanya staff, ketua, driver operasional inti)
     */
    public function canAccessPenyaluran(): bool
    {
        return ! $this->isRelawan() && ! $this->isMagang();
    }

    public function getJabatanAttribute($value): ?string
    {
        if (! $value) {
            return $value;
        }

        // Bersihkan teks penjelasan dalam tanda kurung seperti (Hanya Presensi & Logbook Aktivitas)
        $clean = preg_replace('/\s*\([^)]*\)/', '', (string) $value);

        return trim($clean) ?: $value;
    }

    public function getJabatanLabelAttribute(): string
    {
        $options = self::getJabatanOptions();
        $key = $this->getCleanJabatan();

        if (isset($options[$key])) {
            return $options[$key];
        }

        $clean = preg_replace('/\s*\([^)]*\)/', '', (string) ($this->attributes['jabatan'] ?? 'Staff'));

        return trim(ucfirst($clean)) ?: 'Staff';
    }
}
