<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Amil;
use Tests\TestCase;

class JamKerjaAbsensiTest extends TestCase
{
    public function test_absensi_calculates_daily_work_duration_correctly(): void
    {
        $absensi = new Absensi([
            'tanggal' => '2026-11-02',
            'jam_masuk' => '07:45:00',
            'jam_pulang' => '16:30:00',
            'status' => 'hadir',
        ]);

        // 07:45 to 16:30 is 8 hours 45 minutes = 525 minutes
        $this->assertEquals(525, $absensi->durasi_menit);
        $this->assertEquals(8.8, $absensi->durasi_jam_decimal);
        $this->assertEquals('8j 45m', $absensi->durasi_kerja_formatted);
        $this->assertEquals('8 Jam 45 Menit', $absensi->durasi_kerja_lengkap);
    }

    public function test_absensi_shows_sedang_bekerja_when_clocked_in_today_without_clock_out(): void
    {
        $absensi = new Absensi([
            'tanggal' => today(),
            'jam_masuk' => '08:00:00',
            'jam_pulang' => null,
            'status' => 'hadir',
        ]);

        $this->assertEquals(0, $absensi->durasi_menit);
        $this->assertStringContainsString('Sedang Bekerja', $absensi->durasi_kerja_formatted);
    }

    public function test_amil_accumulates_monthly_work_hours(): void
    {
        $amil = Amil::first();
        if (! $amil) {
            $this->markTestSkipped('No Amil found in database');
        }

        $result = $amil->getTotalJamKerjaBulan(10, 2026);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('total_menit', $result);
        $this->assertArrayHasKey('total_jam', $result);
        $this->assertArrayHasKey('formatted', $result);
        $this->assertArrayHasKey('rata_rata_jam_harian', $result);
    }
}
