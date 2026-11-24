<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashOpname extends Model
{
    use HasFactory;

    public const DENOMINASI_KERTAS = [100000, 50000, 20000, 10000, 5000, 2000, 1000];

    public const DENOMINASI_KOIN = [1000, 500, 200, 100, 50, 25];

    protected $fillable = [
        'akun_keuangan_id',
        'tgl_opname',
        'pecahan',
        'total_fisik',
        'saldo_buku',
        'selisih',
        'catatan',
        'petugas_id',
    ];

    protected function casts(): array
    {
        return [
            'tgl_opname' => 'date',
            'pecahan' => 'array',
            'total_fisik' => 'decimal:2',
            'saldo_buku' => 'decimal:2',
            'selisih' => 'decimal:2',
        ];
    }

    public static function hitungTotalFisik(array $pecahan): float
    {
        $total = 0.0;

        foreach (self::DENOMINASI_KERTAS as $denom) {
            $qty = (int) ($pecahan["kertas_{$denom}"] ?? 0);
            $total += max(0, $qty) * $denom;
        }

        foreach (self::DENOMINASI_KOIN as $denom) {
            $qty = (int) ($pecahan["koin_{$denom}"] ?? 0);
            $total += max(0, $qty) * $denom;
        }

        $total += max(0.0, (float) ($pecahan['cek_wesel'] ?? 0));

        return $total;
    }

    public function akunKeuangan(): BelongsTo
    {
        return $this->belongsTo(AkunKeuangan::class);
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
}
