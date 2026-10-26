<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Donatur extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['kode_donatur', 'nama', 'jenis', 'kategori_id', 'no_hp', 'email', 'alamat', 'is_anonim', 'amil_pic_id'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('donatur');
    }

    protected $fillable = [
        'kode_donatur',
        'nama',
        'jenis',
        'kategori_id',
        'no_hp',
        'email',
        'alamat',
        'kelurahan',
        'kecamatan',
        'kota',
        'provinsi',
        'kode_pos',
        'nik',
        'npwp',
        'tgl_lahir',
        'is_anonim',
        'consent',
        'amil_pic_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tgl_lahir' => 'date',
            'is_anonim' => 'boolean',
            'consent' => 'boolean',
            'nik' => 'encrypted',
            'npwp' => 'encrypted',
        ];
    }

    /**
     * Accessor NIK yang disamarkan demi privasi donatur (UU PDP)
     */
    public function getMaskedNikAttribute(): ?string
    {
        if (! $this->nik || strlen($this->nik) < 8) {
            return $this->nik;
        }

        $prefix = substr($this->nik, 0, 4);
        $suffix = substr($this->nik, -4);

        return $prefix.str_repeat('*', strlen($this->nik) - 8).$suffix;
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(DonaturKategori::class, 'kategori_id');
    }

    public function amilPic(): BelongsTo
    {
        return $this->belongsTo(Amil::class, 'amil_pic_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function donasis(): HasMany
    {
        return $this->hasMany(Donasi::class);
    }
}
