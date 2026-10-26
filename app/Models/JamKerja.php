<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JamKerja extends Model
{
    use HasFactory;

    protected $table = 'jam_kerjas';

    protected $fillable = [
        'nama',
        'jam_masuk',
        'jam_pulang',
        'toleransi_menit',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'toleransi_menit' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
