<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonasiBarang extends Model
{
    use HasFactory;

    protected $table = 'donasi_barangs';

    protected $fillable = [
        'donasi_id',
        'deskripsi',
        'estimasi_nilai',
    ];

    protected function casts(): array
    {
        return [
            'estimasi_nilai' => 'decimal:2',
        ];
    }

    public function donasi(): BelongsTo
    {
        return $this->belongsTo(Donasi::class);
    }
}
