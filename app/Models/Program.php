<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'deskripsi',
        'target_dana',
        'tgl_mulai',
        'tgl_selesai',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'target_dana' => 'decimal:2',
            'tgl_mulai' => 'date',
            'tgl_selesai' => 'date',
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
