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
}
