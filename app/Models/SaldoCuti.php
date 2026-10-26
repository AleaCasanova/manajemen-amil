<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaldoCuti extends Model
{
    use HasFactory;

    protected $table = 'saldo_cutis';

    protected $fillable = [
        'amil_id',
        'tahun',
        'jatah',
        'terpakai',
    ];

    protected function casts(): array
    {
        return [
            'tahun' => 'integer',
            'jatah' => 'integer',
            'terpakai' => 'integer',
        ];
    }

    public function amil(): BelongsTo
    {
        return $this->belongsTo(Amil::class);
    }
}
