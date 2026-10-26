<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisDana extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'kelompok',
        'hak_amil_persen',
        'deskripsi',
    ];

    protected function casts(): array
    {
        return [
            'hak_amil_persen' => 'decimal:2',
        ];
    }

    public function donasis(): HasMany
    {
        return $this->hasMany(Donasi::class);
    }

    public function penyalurans(): HasMany
    {
        return $this->hasMany(Penyaluran::class);
    }
}
