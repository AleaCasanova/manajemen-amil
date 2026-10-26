<?php

namespace App\Models;

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
