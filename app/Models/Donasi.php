<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Donasi extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['no_kwitansi', 'jumlah', 'status', 'jenis_dana_id', 'program_id', 'metode_bayar', 'tgl_donasi', 'catatan', 'verified_by', 'verified_at'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('donasi');
    }

    protected $fillable = [
        'no_kwitansi',
        'donatur_id',
        'amil_id',
        'jenis_dana_id',
        'program_id',
        'jumlah',
        'metode_bayar',
        'bank',
        'bukti_transfer',
        'tgl_donasi',
        'niat',
        'atas_nama',
        'peruntukan',
        'pesan',
        'catatan',
        'lampiran_foto',
        'status',
        'verified_by',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'decimal:2',
            'tgl_donasi' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    public function donatur(): BelongsTo
    {
        return $this->belongsTo(Donatur::class);
    }

    public function amil(): BelongsTo
    {
        return $this->belongsTo(Amil::class);
    }

    public function jenisDana(): BelongsTo
    {
        return $this->belongsTo(JenisDana::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function verifiedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function donasiBarangs(): HasMany
    {
        return $this->hasMany(DonasiBarang::class);
    }
}
