<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'tipe',
        'grup',
        'deskripsi',
    ];

    /**
     * Helper untuk mengambil nilai setting berdasarkan key
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        try {
            $setting = static::where('key', $key)->first();

            return $setting ? $setting->value : $default;
        } catch (\Throwable) {
            return $default;
        }
    }

    /**
     * Helper untuk menyimpan nilai setting
     */
    public static function set(string $key, mixed $value, string $grup = 'general', ?string $deskripsi = null): self
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'grup' => $grup,
                'deskripsi' => $deskripsi,
            ]
        );
    }
}
