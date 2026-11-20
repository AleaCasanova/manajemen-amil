<?php

namespace App\Models;

use App\Services\ReviewNotificationService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AktivitasHarian extends Model
{
    use HasFactory;

    protected $table = 'aktivitas_harians';

    protected $fillable = [
        'amil_id',
        'tanggal',
        'judul',
        'deskripsi',
        'lokasi',
        'lat',
        'lng',
        'foto',
        'status',
    ];

    protected static function booted(): void
    {
        static::created(function (AktivitasHarian $aktivitas) {
            if ($aktivitas->status === 'diajukan') {
                ReviewNotificationService::notifyAllAdminsForAktivitas($aktivitas);
            }
        });

        static::updated(function (AktivitasHarian $aktivitas) {
            if ($aktivitas->isDirty('status')) {
                if ($aktivitas->status !== 'diajukan') {
                    ReviewNotificationService::removeNotification('aktivitas_harian', (int) $aktivitas->id);
                } else {
                    ReviewNotificationService::notifyAllAdminsForAktivitas($aktivitas);
                }
            }
        });

        static::deleted(function (AktivitasHarian $aktivitas) {
            ReviewNotificationService::removeNotification('aktivitas_harian', (int) $aktivitas->id);
        });
    }

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'lat' => 'float',
            'lng' => 'float',
        ];
    }

    public function amil(): BelongsTo
    {
        return $this->belongsTo(Amil::class);
    }
}
