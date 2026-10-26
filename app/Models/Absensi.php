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
        'status',
        'keterangan',
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
}
