<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Mustahik extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nama', 'nik', 'alamat', 'asnaf', 'kondisi', 'status_verifikasi'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('mustahik');
    }

    protected $fillable = [
        'nama',
        'nik',
        'alamat',
        'asnaf',
        'kondisi',
        'status_verifikasi',
    ];

    public function penyalurans(): HasMany
    {
        return $this->hasMany(Penyaluran::class);
    }
}
