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
     * Periksa kelayakan jam presensi pulang dan status pulang lebih awal
     *
     * @return array{
     *     can_clock_out: bool,
     *     is_early: bool,
     *     minutes_early: int,
     *     min_clock_out_time: string,
     *     regular_end_time: string,
     *     message: ?string
     * }
     */
    public function checkClockOutEligibility(Carbon $now, bool $isDinasLuar = false): array
    {
        $jamKerja = JamKerja::where('is_active', true)->first();
        $regularEndStr = ($jamKerja && $jamKerja->jam_pulang) ? $jamKerja->jam_pulang : '16:00:00';

        $regularEnd = Carbon::createFromTimeString($regularEndStr, 'Asia/Jakarta');
        // Jam buka presensi pulang kantor: 30 menit sebelum jam kerja berakhir (default 15:30)
        $minClockOut = $regularEnd->copy()->subMinutes(30);

        $currentTime = Carbon::createFromTimeString($now->format('H:i:s'), 'Asia/Jakarta');

        $isEarly = $currentTime->lessThan($regularEnd);
        $minutesEarly = $isEarly ? (int) $currentTime->diffInMinutes($regularEnd) : 0;

        $minClockOutFormatted = $minClockOut->format('H:i');
        $regularEndFormatted = $regularEnd->format('H:i');

        // Mode Tugas Luar: amil bebas presensi pulang kapan saja setelah tugas selesai
        // Mode Kantor: terkunci jika belum mencapai jam buka presensi pulang (15:30)
        if (! $isDinasLuar && $currentTime->lessThan($minClockOut)) {
            return [
                'can_clock_out' => false,
                'is_early' => true,
                'minutes_early' => $minutesEarly,
                'min_clock_out_time' => $minClockOutFormatted,
                'regular_end_time' => $regularEndFormatted,
                'message' => "Presensi pulang di kantor belum dibuka. Presensi pulang baru dapat dilakukan mulai pukul {$minClockOutFormatted} WIB (atau gunakan mode 'Tugas Luar' jika selesai penugasan lapangan).",
            ];
        }

        return [
            'can_clock_out' => true,
            'is_early' => $isEarly,
            'minutes_early' => $minutesEarly,
            'min_clock_out_time' => $minClockOutFormatted,
            'regular_end_time' => $regularEndFormatted,
            'message' => null,
        ];
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
