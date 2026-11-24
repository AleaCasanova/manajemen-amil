<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Amil;
use App\Models\User;
use App\Services\AbsensiService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class HariKerjaDanAlpaTest extends TestCase
{
    use DatabaseTransactions;

    protected Amil $staffAmil;

    protected Amil $magangAmil;

    protected Amil $relawanAmil;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Amil']);

        // 1. Setup Staf (6 Hari Kerja: Senin - Sabtu)
        $staffUser = User::factory()->create([
            'email' => 'staff_test_'.uniqid().'@amilgsc.com',
            'is_active' => true,
        ]);
        $staffUser->assignRole('Amil');
        $this->staffAmil = Amil::create([
            'user_id' => $staffUser->id,
            'nip' => 'STF-'.rand(100, 999),
            'nama' => 'Staf Operasional Test',
            'jabatan' => 'staff',
            'status' => 'aktif',
        ]);

        // 2. Setup Magang (5 Hari Kerja: Senin - Jumat)
        $magangUser = User::factory()->create([
            'email' => 'magang_test_'.uniqid().'@amilgsc.com',
            'is_active' => true,
        ]);
        $magangUser->assignRole('Amil');
        $this->magangAmil = Amil::create([
            'user_id' => $magangUser->id,
            'nip' => 'MGN-'.rand(100, 999),
            'nama' => 'Mahasiswa Magang Test',
            'jabatan' => 'magang',
            'status' => 'aktif',
        ]);

        // 3. Setup Relawan (Bebas Absensi Rutin)
        $relawanUser = User::factory()->create([
            'email' => 'relawan_test_'.uniqid().'@amilgsc.com',
            'is_active' => true,
        ]);
        $relawanUser->assignRole('Amil');
        $this->relawanAmil = Amil::create([
            'user_id' => $relawanUser->id,
            'nip' => 'RLW-'.rand(100, 999),
            'nama' => 'Relawan Test',
            'jabatan' => 'relawan',
            'status' => 'aktif',
        ]);
    }

    /**
     * Uji pada hari Sabtu:
     * - Staf wajib absen; jika tidak absen, tercatat ALPA.
     * - Anak Magang libur; TIDAK boleh ditandai Alpa.
     * - Relawan bebas absen; TIDAK ditandai Alpa.
     */
    public function test_saturday_alpa_marks_staff_but_skips_magang_and_relawan(): void
    {
        // Cari hari Sabtu terdekat
        $saturday = Carbon::parse('next Saturday')->toDateString();

        $this->artisan('amil:mark-alpa', ['--date' => $saturday])
            ->assertSuccessful();

        // 1. Staff harus ditandai ALPA
        $staffAbsen = Absensi::where('amil_id', $this->staffAmil->id)
            ->whereDate('tanggal', $saturday)
            ->first();

        $this->assertNotNull($staffAbsen, 'Staff yang tidak absen di hari Sabtu harus tercatat Alpa');
        $this->assertEquals('alpa', $staffAbsen->status);

        // 2. Magang TIDAK boleh ada catatan Alpa di hari Sabtu
        $magangAbsen = Absensi::where('amil_id', $this->magangAmil->id)
            ->whereDate('tanggal', $saturday)
            ->first();

        $this->assertNull($magangAbsen, 'Anak Magang libur di hari Sabtu sehingga tidak boleh ditandai Alpa');

        // 3. Relawan TIDAK boleh ada catatan Alpa
        $relawanAbsen = Absensi::where('amil_id', $this->relawanAmil->id)
            ->whereDate('tanggal', $saturday)
            ->first();

        $this->assertNull($relawanAbsen, 'Relawan bebas presensi rutin');
    }

    /**
     * Uji pada hari Minggu:
     * - Semua amil libur, proses alpa dilewati sepenuhnya.
     */
    public function test_sunday_skips_all_amils(): void
    {
        $sunday = Carbon::parse('next Sunday')->toDateString();

        $this->artisan('amil:mark-alpa', ['--date' => $sunday])
            ->expectsOutputToContain('adalah hari Minggu (Hari Libur)')
            ->assertSuccessful();

        $count = Absensi::whereDate('tanggal', $sunday)->count();
        $this->assertEquals(0, $count);
    }

    /**
     * Uji pada hari kerja biasa (misal hari Senin):
     * - Staf & Magang yang tidak absen keduanya ditandai ALPA.
     * - Relawan tetap dilewati.
     */
    public function test_weekday_marks_both_staff_and_magang_as_alpa(): void
    {
        $monday = Carbon::parse('next Monday')->toDateString();

        $this->artisan('amil:mark-alpa', ['--date' => $monday])
            ->assertSuccessful();

        // Staf Alpa
        $staffAbsen = Absensi::where('amil_id', $this->staffAmil->id)
            ->whereDate('tanggal', $monday)
            ->first();
        $this->assertNotNull($staffAbsen);
        $this->assertEquals('alpa', $staffAbsen->status);

        // Magang Alpa
        $magangAbsen = Absensi::where('amil_id', $this->magangAmil->id)
            ->whereDate('tanggal', $monday)
            ->first();
        $this->assertNotNull($magangAbsen);
        $this->assertEquals('alpa', $magangAbsen->status);

        // Relawan tidak ada absen
        $relawanAbsen = Absensi::where('amil_id', $this->relawanAmil->id)
            ->whereDate('tanggal', $monday)
            ->first();
        $this->assertNull($relawanAbsen);
    }

    /**
     * Uji amil baru yang tanggal bergabungnya di masa mendatang atau setelah tanggal pemeriksaan:
     * - Tidak boleh ditandai ALPA untuk tanggal sebelum dia bergabung.
     */
    public function test_amil_not_joined_yet_is_not_marked_as_alpa(): void
    {
        $testDate = '2026-11-10';

        // Buat amil baru yang baru bergabung tanggal 23 November 2026
        $newUser = User::factory()->create([
            'email' => 'new_intern_'.uniqid().'@amilgsc.com',
            'is_active' => true,
        ]);
        $newUser->assignRole('Amil');

        $newMagang = Amil::create([
            'user_id' => $newUser->id,
            'nip' => 'MGN-NEW-'.rand(100, 999),
            'nama' => 'Magang Baru Masuk',
            'jabatan' => 'magang',
            'status' => 'aktif',
            'tgl_bergabung' => '2026-11-23',
        ]);

        $this->artisan('amil:mark-alpa', ['--date' => $testDate])
            ->assertSuccessful();

        $absen = Absensi::where('amil_id', $newMagang->id)
            ->whereDate('tanggal', $testDate)
            ->first();

        $this->assertNull($absen, 'Amil yang belum bergabung tidak boleh ditandai Alpa');
    }

    /**
     * Uji autoSyncPastAlpa membersihkan data Alpa yang tidak valid (sebelum tanggal bergabung).
     */
    public function test_autosync_cleans_alpa_prior_to_join_date(): void
    {
        $newUser = User::factory()->create([
            'email' => 'cleanup_test_'.uniqid().'@amilgsc.com',
            'is_active' => true,
        ]);
        $newUser->assignRole('Amil');

        $newAmil = Amil::create([
            'user_id' => $newUser->id,
            'nip' => 'CLN-'.rand(100, 999),
            'nama' => 'Amil Uji Pembersihan',
            'jabatan' => 'staff',
            'status' => 'aktif',
            'tgl_bergabung' => '2026-11-23',
        ]);

        // Simulasikan alpa salah yang masuk sebelum tanggal bergabung
        Absensi::create([
            'amil_id' => $newAmil->id,
            'tanggal' => '2026-11-05',
            'status' => 'alpa',
            'keterangan' => 'Alpa lama yang salah',
        ]);

        $this->assertDatabaseHas('absensis', [
            'amil_id' => $newAmil->id,
            'tanggal' => '2026-11-05',
            'status' => 'alpa',
        ]);

        // Jalankan autoSyncPastAlpa
        app(AbsensiService::class)->autoSyncPastAlpa();

        // Alpa sebelum tgl_bergabung harus sudah terhapus
        $this->assertDatabaseMissing('absensis', [
            'amil_id' => $newAmil->id,
            'tanggal' => '2026-11-05',
            'status' => 'alpa',
        ]);
    }
}
