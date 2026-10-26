<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LokasiKantor extends Model
{
    use HasFactory;

    protected $table = 'lokasi_kantors';

    protected $fillable = [
        'nama_lokasi',
        'alamat',
        'lat',
        'lng',
        'radius_meter',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'lat' => 'decimal:8',
            'lng' => 'decimal:8',
            'radius_meter' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
