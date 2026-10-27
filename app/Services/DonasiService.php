<?php

namespace App\Services;

use App\Models\Donasi;
use App\Models\Donatur;
use App\Models\Setting;
use Carbon\CarbonInterface;

class DonasiService
{
    /**
     * Generate Nomor Kwitansi Otomatis (Thread-safe & Anti-collision)
     * Format: KW/YYYY/MM/0001
     */
    public function generateNoKwitansi(?CarbonInterface $tanggal = null, bool $lock = false): string
    {
        $date = $tanggal ?? now();
        $year = $date->format('Y');
        $month = $date->format('m');

        $prefix = Setting::get('receipt_prefix', 'KW');

        // Hitung urutan donasi pada bulan dan tahun tersebut (termasuk yang di-soft-delete)
        $query = Donasi::withTrashed()
            ->whereYear('tgl_donasi', $year)
            ->whereMonth('tgl_donasi', $month)
            ->orderBy('id', 'desc');

        if ($lock) {
            $query->lockForUpdate();
        }

        $latestRecord = $query->first();
        $sequence = 1;

        if ($latestRecord && preg_match('/\/(\d+)$/', $latestRecord->no_kwitansi, $matches)) {
            $sequence = (int) $matches[1] + 1;
        } else {
            $sequence = Donasi::withTrashed()
                ->whereYear('tgl_donasi', $year)
                ->whereMonth('tgl_donasi', $month)
                ->count() + 1;
        }

        // Pastikan nomor kwitansi benar-benar unik dan tidak ada tabrakan nomor
        do {
            $formattedSequence = str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
            $candidate = "{$prefix}/{$year}/{$month}/{$formattedSequence}";
            $exists = Donasi::withTrashed()->where('no_kwitansi', $candidate)->exists();
            if ($exists) {
                $sequence++;
            }
        } while ($exists);

        return $candidate;
    }

    /**
     * Cek apakah nomor HP sudah terdaftar sebagai donatur
     */
    public function checkDuplicatePhone(string $noHp, ?int $excludeId = null): ?Donatur
    {
        // Normalisasi nomor HP: hilangkan spasi, strip, +62 -> 08
        $cleanPhone = preg_replace('/[^0-9]/', '', $noHp);

        if (str_starts_with($cleanPhone, '62')) {
            $cleanPhone = '0'.substr($cleanPhone, 2);
        }

        $query = Donatur::where(function ($q) use ($noHp, $cleanPhone) {
            $q->where('no_hp', $noHp)
                ->orWhere('no_hp', $cleanPhone)
                ->orWhere('no_hp', 'like', '%'.substr($cleanPhone, -8));
        });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->first();
    }

    /**
     * Konversi angka nominal ke kalimat terbilang Bahasa Indonesia
     */
    public static function terbilang(float|int|string $nominal): string
    {
        $angka = abs((int) round((float) $nominal));
        if ($angka === 0) {
            return 'Nol Rupiah';
        }

        return trim(preg_replace('/\s+/', ' ', self::ejaAngka($angka))).' Rupiah';
    }

    private static function ejaAngka(int $angka): string
    {
        $huruf = [
            '',
            'Satu',
            'Dua',
            'Tiga',
            'Empat',
            'Lima',
            'Enam',
            'Tujuh',
            'Delapan',
            'Sembilan',
            'Sepuluh',
            'Sebelas',
        ];

        if ($angka < 12) {
            return ' '.$huruf[$angka];
        }

        if ($angka < 20) {
            return self::ejaAngka($angka - 10).' Belas';
        }

        if ($angka < 100) {
            return self::ejaAngka((int) ($angka / 10)).' Puluh'.self::ejaAngka($angka % 10);
        }

        if ($angka < 200) {
            return ' Seratus'.self::ejaAngka($angka - 100);
        }

        if ($angka < 1000) {
            return self::ejaAngka((int) ($angka / 100)).' Ratus'.self::ejaAngka($angka % 100);
        }

        if ($angka < 2000) {
            return ' Seribu'.self::ejaAngka($angka - 1000);
        }

        if ($angka < 1000000) {
            return self::ejaAngka((int) ($angka / 1000)).' Ribu'.self::ejaAngka($angka % 1000);
        }

        if ($angka < 1000000000) {
            return self::ejaAngka((int) ($angka / 1000000)).' Juta'.self::ejaAngka($angka % 1000000);
        }

        if ($angka < 1000000000000) {
            return self::ejaAngka((int) ($angka / 1000000000)).' Miliar'.self::ejaAngka((int) fmod($angka, 1000000000));
        }

        return self::ejaAngka((int) ($angka / 1000000000000)).' Triliun'.self::ejaAngka((int) fmod($angka, 1000000000000));
    }
}
