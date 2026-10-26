<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Penyaluran extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['mustahik_id', 'program_id', 'jenis_dana_id', 'jumlah', 'tgl_salur', 'amil_id', 'catatan'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('penyaluran');
    }

    protected $table = 'penyalurans';

    protected $fillable = [
        'mustahik_id',
        'program_id',
        'jenis_dana_id',
        'jumlah',
        'tgl_salur',
        'bukti',
        'amil_id',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'decimal:2',
            'tgl_salur' => 'date',
        ];
    }

    public function mustahik(): BelongsTo
    {
        return $this->belongsTo(Mustahik::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function jenisDana(): BelongsTo
    {
        return $this->belongsTo(JenisDana::class);
    }

    public function amil(): BelongsTo
    {
        return $this->belongsTo(Amil::class);
    }
}
