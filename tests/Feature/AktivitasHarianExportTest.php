<?php

namespace Tests\Feature;

use App\Exports\AktivitasHarianExport;
use App\Models\AktivitasHarian;
use App\Models\Amil;
use App\Models\Divisi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AktivitasHarianExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_aktivitas_export_generates_two_sheets_with_correct_structure(): void
    {
        $divisi = Divisi::create([
            'nama' => 'Fundraising',
            'deskripsi' => 'Divisi Fundraising',
        ]);

        $user = User::factory()->create();

        $amil = Amil::create([
            'user_id' => $user->id,
            'divisi_id' => $divisi->id,
            'nip' => 'AML-099',
            'nama' => 'Test Amil',
            'email' => 'testamil@example.com',
            'jabatan' => 'staff',
            'status' => 'aktif',
        ]);

        AktivitasHarian::create([
            'amil_id' => $amil->id,
            'tanggal' => now()->toDateString(),
            'judul' => 'Kegiatan Sosialisasi',
            'deskripsi' => 'Melakukan sosialisasi ke masyarakat',
            'lokasi' => 'Cilacap Tengah',
            'status' => 'disetujui',
        ]);

        $amils = Amil::with(['divisi', 'aktivitasHarians'])->get();

        $export = new AktivitasHarianExport($amils, now()->month, now()->year, 'semua');
        $sheets = $export->sheets();

        $this->assertCount(2, $sheets);

        $rekapSheet = $sheets[0];
        $this->assertSame('Rekapitulasi Aktivitas Amil', $rekapSheet->title());
        $rekapData = $rekapSheet->array();
        $this->assertCount(4, $rekapData); // Title, Subtitle, Header, 1 Data row
        $this->assertSame('Test Amil', $rekapData[3][2]);
        $this->assertSame(1, $rekapData[3][5]); // Total Aktivitas
        $this->assertSame(1, $rekapData[3][6]); // Disetujui
        $this->assertSame(0, $rekapData[3][7]); // Menunggu review

        $logSheet = $sheets[1];
        $this->assertSame('Rincian Log Aktivitas', $logSheet->title());
        $logData = $logSheet->array();
        $this->assertCount(4, $logData); // Title, Subtitle, Header, 1 Data row
        $this->assertSame('Kegiatan Sosialisasi', $logData[3][7]);
        $this->assertSame('Disetujui', $logData[3][10]);
    }
}
