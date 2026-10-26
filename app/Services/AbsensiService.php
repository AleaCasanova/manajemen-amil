<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\Amil;
use App\Models\JamKerja;
use App\Models\LokasiKantor;
use Carbon\Carbon;

class AbsensiService
{
    /**
     * Hitung jarak dua titik koordinat bumi (dalam meter) dengan rumus Haversine
     */
    public function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000; // dalam meter

        $latDelta = deg2rad($lat2 - $lat1);
        $lngDelta = deg2rad($lng2 - $lng1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lngDelta / 2) * sin($lngDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 1);
    }

    /**
     * Periksa apakah amil berada di dalam radius salah satu kantor aktif
     *
     * @return array{valid: bool, nearest_office: ?LokasiKantor, distance_meters: float}
     */
    public function validateLocation(float $userLat, float $userLng): array
    {
        $offices = LokasiKantor::where('is_active', true)->get();

        if ($offices->isEmpty()) {
            return [
                'valid' => true,
                'nearest_office' => null,
                'distance_meters' => 0.0,
            ];
        }

        $minDistance = PHP_INT_MAX;
        $nearestOffice = null;

        foreach ($offices as $office) {
            $dist = $this->calculateDistance($userLat, $userLng, (float) $office->lat, (float) $office->lng);
            if ($dist < $minDistance) {
                $minDistance = $dist;
                $nearestOffice = $office;
            }
        }

        $isValid = $nearestOffice ? ($minDistance <= $nearestOffice->radius_meter) : false;

        return [
            'valid' => $isValid,
            'nearest_office' => $nearestOffice,
            'distance_meters' => (float) $minDistance,
        ];
    }

    /**
     * Tentukan status kehadiran (hadir / terlambat)
     */
    public function determineStatus(Carbon $clockInTime): string
    {
        $jamKerja = JamKerja::where('is_active', true)->first();

        if (! $jamKerja || ! $jamKerja->jam_masuk) {
            return 'hadir';
        }

        // Parse batas waktu toleransi
        $targetTime = Carbon::createFromTimeString($jamKerja->jam_masuk, 'Asia/Jakarta');
        $targetTime->addMinutes($jamKerja->toleransi_menit ?? 15);

        $currentTime = Carbon::createFromTimeString($clockInTime->format('H:i:s'), 'Asia/Jakarta');

        return $currentTime->greaterThan($targetTime) ? 'terlambat' : 'hadir';
    }

    /**
     * Dapatkan data absensi amil hari ini
     */
    public function getTodayAbsensi(Amil $amil): ?Absensi
    {
        return Absensi::where('amil_id', $amil->id)
            ->whereDate('tanggal', Carbon::today('Asia/Jakarta'))
            ->first();
    }
}
