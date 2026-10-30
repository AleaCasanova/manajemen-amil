<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Amil;
use App\Models\Donasi;
use App\Models\Donatur;
use App\Models\DonaturKategori;
use App\Models\IzinCuti;
use App\Models\JenisDana;
use App\Models\LokasiKantor;
use App\Models\Mustahik;
use App\Models\Program;
use App\Models\SaldoCuti;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AmilWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;

    protected Amil $amil;

    protected function setUp(): void
    {
        parent::setUp();

        // Siapkan akun amil pengujian
        $existingAmilUser = User::where('email', 'amil@amilgsc.com')->first()
            ?? User::whereHas('roles', fn ($q) => $q->where('name', 'Amil'))->first();

        if (! $existingAmilUser) {
            $this->user = User::factory()->create(['email' => 'testamil@amilgsc.com', 'is_active' => true]);
            $this->user->assignRole('Amil');
            $this->amil = Amil::create([
                'user_id' => $this->user->id,
                'nip' => 'AML-TEST01',
                'nama' => 'Amil Penguji',
                'target_bulanan' => 10000000,
                'status' => 'aktif',
            ]);
        } else {
            $this->user = $existingAmilUser;
            $this->amil = $this->user->amil ?? Amil::create([
                'user_id' => $this->user->id,
                'nip' => 'AML-TEST01',
                'nama' => $this->user->name,
                'target_bulanan' => 10000000,
                'status' => 'aktif',
            ]);
        }

        // Siapkan kantor aktif untuk uji absensi geofencing
        LokasiKantor::firstOrCreate(
            ['nama_lokasi' => 'Kantor Pengujian'],
            [
                'lat' => -6.20876340,
                'lng' => 106.84559900,
                'radius_meter' => 500,
                'is_active' => true,
            ]
        );
    }

    /**
     * Uji Halaman Login & Dashboard Amil
     */
    public function test_amil_can_login_and_access_dashboard(): void
    {
        $response = $this->post('/amil/login', [
            'email' => $this->user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('amil.dashboard'));
        $this->assertAuthenticatedAs($this->user);

        $dashResponse = $this->actingAs($this->user)->get(route('amil.dashboard'));
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee($this->amil->nama);
    }

    /**
     * Uji API Real-time Cek Nomor HP Donatur
     */
    public function test_check_phone_api_returns_correct_response(): void
    {
        $kategori = DonaturKategori::first();

        // Buat donatur pengujian
        $donatur = Donatur::create([
            'kode_donatur' => 'DNT-TEST999',
            'nama' => 'Bapak Donatur Pengujian',
            'jenis' => 'individu',
            'kategori_id' => $kategori?->id,
            'no_hp' => '08999998888',
            'amil_pic_id' => $this->amil->id,
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson(route('amil.donasi.check-phone', ['phone' => '08999998888']));

        $response->assertStatus(200);
        $response->assertJson([
            'exists' => true,
            'donatur' => [
                'nama' => 'Bapak Donatur Pengujian',
            ],
        ]);
    }

    /**
     * Uji Input Donasi & Penerbitan Kwitansi Otomatis
     */
    public function test_amil_can_input_donasi_and_issue_kwitansi(): void
    {
        $kategori = DonaturKategori::first();
        $jenisDana = JenisDana::first();
        $program = Program::first();

        $response = $this->actingAs($this->user)->post(route('amil.donasi.store'), [
            'donatur_mode' => 'new',
            'nama_donatur' => 'H. Muhsinin',
            'jenis_donatur' => 'individu',
            'kategori_id' => $kategori->id,
            'no_hp' => '081234455667',
            'jenis_dana_id' => $jenisDana->id,
            'program_id' => $program?->id,
            'jumlah' => 1500000,
            'metode_bayar' => 'tunai',
            'tgl_donasi' => date('Y-m-d'),
            'niat' => 'Zakat Maal Keluarga',
        ]);

        $donasi = Donasi::where('amil_id', $this->amil->id)
            ->where('jumlah', 1500000)
            ->latest()
            ->first();

        $this->assertNotNull($donasi);
        $this->assertStringStartsWith('KWT-GSC/', $donasi->no_kwitansi);
        $response->assertRedirect(route('amil.donasi.success', $donasi->id));

        // Uji Halaman Sukses
        $successPage = $this->actingAs($this->user)->get(route('amil.donasi.success', $donasi->id));
        $successPage->assertStatus(200);
        $successPage->assertSee($donasi->no_kwitansi);

        // Uji Stream PDF Kwitansi
        $pdfStream = $this->get(route('kwitansi.stream', $donasi->id));
        $pdfStream->assertStatus(200);
        $this->assertEquals('application/pdf', $pdfStream->headers->get('content-type'));

        // Uji Halaman Verifikasi Publik QR Code
        $verifyPage = $this->get(route('kwitansi.verify', $donasi->no_kwitansi));
        $verifyPage->assertStatus(200);
        $verifyPage->assertSee($donasi->no_kwitansi);
        $verifyPage->assertSee('Verifikasi Keaslian Kwitansi');
    }

    /**
     * Uji Presensi Masuk (Clock In) dengan Waktu Server & GPS
     */
    public function test_amil_can_clock_in_and_clock_out(): void
    {
        // Bersihkan absensi hari ini jika ada
        Carbon::setTestNow(Carbon::parse('2026-10-26 07:55:00', 'Asia/Jakarta'));
        Absensi::where('amil_id', $this->amil->id)->whereDate('tanggal', Carbon::today())->delete();

        // 1. Clock In (koordinat tepat di kantor)
        $responseIn = $this->actingAs($this->user)->post(route('amil.absensi.masuk'), [
            'lat' => -6.20876340,
            'lng' => 106.84559900,
            'foto' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ]);

        $responseIn->assertRedirect(route('amil.absensi.index'));
        $absensi = Absensi::where('amil_id', $this->amil->id)->whereDate('tanggal', Carbon::today())->first();
        $this->assertNotNull($absensi);
        $this->assertNotNull($absensi->jam_masuk);

        // Uji coba dobel clock in hari yang sama (harus ditolak)
        $responseDouble = $this->actingAs($this->user)->post(route('amil.absensi.masuk'), [
            'lat' => -6.20876340,
            'lng' => 106.84559900,
            'foto' => 'data:image/png;base64,dummy',
        ]);
        $responseDouble->assertSessionHas('error');

        // 2. Clock Out (Simulasi di atas jam 16:00 tepat waktu)
        Carbon::setTestNow(Carbon::parse('2026-10-26 16:05:00', 'Asia/Jakarta'));
        $responseOut = $this->actingAs($this->user)->post(route('amil.absensi.pulang'), [
            'lat' => -6.20876340,
            'lng' => 106.84559900,
            'foto' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ]);

        $responseOut->assertRedirect(route('amil.absensi.index'));
        $absensi->refresh();
        $this->assertNotNull($absensi->jam_pulang);
        $this->assertFalse($absensi->isPulangAwal());

        Carbon::setTestNow(); // reset test now
    }

    /**
     * Uji Kebijakan Jam Presensi Pulang (Terkunci Sebelum 15:30, Notasi Pulang Lebih Awal, & Bypass Tugas Luar)
     */
    public function test_clock_out_policy_and_early_departure_recording(): void
    {
        Absensi::where('amil_id', $this->amil->id)->whereDate('tanggal', Carbon::today())->delete();

        // 1. Clock In pagi hari (08:00)
        Carbon::setTestNow(Carbon::parse('2026-10-26 08:00:00', 'Asia/Jakarta'));
        $this->actingAs($this->user)->post(route('amil.absensi.masuk'), [
            'lat' => -6.20876340,
            'lng' => 106.84559900,
            'foto' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ]);

        $absensi = Absensi::where('amil_id', $this->amil->id)->whereDate('tanggal', Carbon::today())->first();
        $this->assertNotNull($absensi);

        // 2. Coba Clock Out di kantor sebelum jam 15:30 (misal 14:00) -> Harus DITOLAK / TERKUNCI
        Carbon::setTestNow(Carbon::parse('2026-10-26 14:00:00', 'Asia/Jakarta'));
        $resEarlyLocked = $this->actingAs($this->user)->post(route('amil.absensi.pulang'), [
            'lat' => -6.20876340,
            'lng' => 106.84559900,
            'foto' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
            'tipe_presensi' => 'kantor',
        ]);
        $resEarlyLocked->assertSessionHas('error');
        $this->assertStringContainsString('belum dibuka', session('error'));

        // 3. Clock Out mode 'dinas_luar' sebelum jam 15:30 (misal 14:15) -> DIIZINKAN & tercatat Pulang Lebih Awal
        Carbon::setTestNow(Carbon::parse('2026-10-26 14:15:00', 'Asia/Jakarta'));
        $resDinasLuar = $this->actingAs($this->user)->post(route('amil.absensi.pulang'), [
            'lat' => -6.20876340,
            'lng' => 106.84559900,
            'foto' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
            'tipe_presensi' => 'dinas_luar',
            'keterangan_pulang' => 'Selesai pendampingan di lapangan',
        ]);
        $resDinasLuar->assertRedirect(route('amil.absensi.index'));
        $absensi->refresh();
        $this->assertNotNull($absensi->jam_pulang);
        $this->assertTrue($absensi->isPulangAwal());
        $this->assertStringContainsString('Pulang Lebih Awal', $absensi->keterangan_pulang);

        // 4. Reset untuk uji Clock Out kantor di antara 15:30 s/d 15:59 (misal 15:45)
        $absensi->update(['jam_pulang' => null, 'keterangan_pulang' => null]);
        Carbon::setTestNow(Carbon::parse('2026-10-26 15:45:00', 'Asia/Jakarta'));
        $resEarlyAllowed = $this->actingAs($this->user)->post(route('amil.absensi.pulang'), [
            'lat' => -6.20876340,
            'lng' => 106.84559900,
            'foto' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
            'tipe_presensi' => 'kantor',
        ]);
        $resEarlyAllowed->assertRedirect(route('amil.absensi.index'));
        $absensi->refresh();
        $this->assertTrue($absensi->isPulangAwal());
        $this->assertEquals(15, $absensi->pulangAwalMenit());
        $this->assertStringContainsString('Pulang Lebih Awal (15 menit sebelum jam 16:00 WIB)', $absensi->keterangan_pulang);

        Carbon::setTestNow(); // reset test now
    }

    /**
     * Uji Pengajuan Izin Cuti & Kuota Saldo Cuti
     */
    public function test_amil_can_submit_izin_cuti(): void
    {
        $saldo = SaldoCuti::firstOrCreate(
            ['amil_id' => $this->amil->id, 'tahun' => now()->year],
            ['jatah' => 12, 'terpakai' => 0]
        );

        $response = $this->actingAs($this->user)->post(route('amil.izin-cuti.store'), [
            'jenis' => 'cuti',
            'tgl_mulai' => now()->addDays(5)->format('Y-m-d'),
            'tgl_selesai' => now()->addDays(6)->format('Y-m-d'), // 2 hari
            'alasan' => 'Keperluan keluarga di luar kota',
        ]);

        $response->assertRedirect(route('amil.izin-cuti.index'));
        $this->assertDatabaseHas('izin_cutis', [
            'amil_id' => $this->amil->id,
            'jenis' => 'cuti',
            'status' => 'diajukan',
        ]);

        $izin = IzinCuti::where('amil_id', $this->amil->id)->where('status', 'diajukan')->latest()->first();
        $this->assertNotNull($izin);

        // Simulasi Admin menyetujui pengajuan cuti
        $admin = User::role('admin')->first() ?? $this->user;
        $izin->approve($admin->id);

        $this->assertEquals('disetujui', $izin->status);
        $saldo->refresh();
        $this->assertEquals(2, $saldo->terpakai);

        // Verifikasi otomatis tercatat di tabel absensis sebagai 'cuti' (bukan alpa)
        $tglCuti = now()->addDays(5)->format('Y-m-d');
        $this->assertDatabaseHas('absensis', [
            'amil_id' => $this->amil->id,
            'tanggal' => $tglCuti,
            'status' => 'cuti',
        ]);
    }

    /**
     * Uji Penyaluran Bantuan kepada Mustahik
     */
    public function test_amil_can_record_penyaluran(): void
    {
        $mustahik = Mustahik::firstOrCreate(
            ['nik' => '3201019900010001'],
            [
                'nama' => 'Ibu Aminah',
                'asnaf' => 'miskin',
                'status_verifikasi' => 'terverifikasi',
            ]
        );

        $jenisDana = JenisDana::first();

        $response = $this->actingAs($this->user)->post(route('amil.penyaluran.store'), [
            'mustahik_id' => $mustahik->id,
            'jenis_dana_id' => $jenisDana->id,
            'jumlah' => 500000,
            'tgl_salur' => date('Y-m-d'),
            'catatan' => 'Santunan sembako dan biaya hidup dhuafa',
        ]);

        $response->assertRedirect(route('amil.penyaluran.index'));
        $this->assertDatabaseHas('penyalurans', [
            'mustahik_id' => $mustahik->id,
            'amil_id' => $this->amil->id,
            'jumlah' => 500000,
        ]);
    }

    /**
     * Uji Halaman Presensi Amil Berhasil Dimuat (Render Blade OK)
     */
    public function test_amil_can_view_absensi_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('amil.absensi.index'));
        $response->assertStatus(200);
        $response->assertSee('Tipe Presensi Saat Ini');
        $response->assertSee('Tugas Lapangan / Luar');
    }

    /**
     * Uji Presensi Kantor Ditolak Jika di Luar Radius
     */
    public function test_amil_clock_in_office_mode_rejected_when_outside_radius(): void
    {
        // Bersihkan absensi hari ini jika ada
        Absensi::where('amil_id', $this->amil->id)->whereDate('tanggal', Carbon::today('Asia/Jakarta'))->delete();

        // Titik sangat jauh (luar radius)
        $farLat = -7.50000000;
        $farLng = 110.50000000;

        $response = $this->actingAs($this->user)->post(route('amil.absensi.masuk'), [
            'lat' => $farLat,
            'lng' => $farLng,
            'foto' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=',
            'tipe_presensi' => 'kantor',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('absensis', [
            'amil_id' => $this->amil->id,
            'tanggal' => Carbon::today('Asia/Jakarta')->toDateString(),
        ]);
    }

    /**
     * Uji Presensi Mode Tugas Luar Wajib Mengisi Keterangan
     */
    public function test_amil_dinas_luar_requires_task_description(): void
    {
        Absensi::where('amil_id', $this->amil->id)->whereDate('tanggal', Carbon::today('Asia/Jakarta'))->delete();

        $response = $this->actingAs($this->user)->post(route('amil.absensi.masuk'), [
            'lat' => -7.50000000,
            'lng' => 110.50000000,
            'foto' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=',
            'tipe_presensi' => 'dinas_luar',
            'keterangan' => '', // kosong
        ]);

        $response->assertSessionHasErrors(['keterangan']);
    }

    /**
     * Uji Presensi Mode Tugas Luar Berhasil Walau di Luar Radius Kantor
     */
    public function test_amil_can_clock_in_with_dinas_luar_mode_outside_radius(): void
    {
        Absensi::where('amil_id', $this->amil->id)->whereDate('tanggal', Carbon::today('Asia/Jakarta'))->delete();

        $farLat = -7.50000000;
        $farLng = 110.50000000;

        $response = $this->actingAs($this->user)->post(route('amil.absensi.masuk'), [
            'lat' => $farLat,
            'lng' => $farLng,
            'foto' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=',
            'tipe_presensi' => 'dinas_luar',
            'keterangan' => 'Penugasan survei mustahik di Desa Cibogo',
        ]);

        $response->assertRedirect(route('amil.absensi.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('absensis', [
            'amil_id' => $this->amil->id,
            'tanggal' => Carbon::today('Asia/Jakarta')->toDateString(),
            'tipe_masuk' => 'dinas_luar',
            'keterangan' => 'Penugasan survei mustahik di Desa Cibogo',
        ]);
    }

    /**
     * Uji Command Otomatis Tandai Alpa
     */
    public function test_mark_alpa_command_marks_absent_amil_as_alpa(): void
    {
        // Pastikan amil ini tidak punya absensi pada tanggal pengujian
        $testDate = '2026-11-05';
        Absensi::where('amil_id', $this->amil->id)->whereDate('tanggal', $testDate)->delete();

        $this->artisan('amil:mark-alpa', ['--date' => $testDate, '--include-weekend' => true])
            ->assertExitCode(0);

        $this->assertDatabaseHas('absensis', [
            'amil_id' => $this->amil->id,
            'tanggal' => $testDate,
            'status' => 'alpa',
        ]);
    }

    /**
     * Uji Halaman Profil Amil dapat diakses
     */
    public function test_amil_can_view_profile_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('amil.profile'));

        $response->assertOk();
        $response->assertSee('Profil & Akun Amil', false);
        $response->assertSee('Ubah Data Diri & Kontak', false);
        $response->assertSee('Rubah Kata Sandi');
    }

    /**
     * Uji Amil dapat memperbarui data profil dirinya sendiri
     */
    public function test_amil_can_update_profile_info(): void
    {
        $response = $this->actingAs($this->user)->put(route('amil.profile.update'), [
            'nama' => 'Nama Baru Amil',
            'email' => $this->user->email,
            'no_hp' => '081299998888',
            'alamat' => 'Jl. Kebon Jeruk No. 5, Jakarta Barat',
        ]);

        $response->assertRedirect(route('amil.profile'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => 'Nama Baru Amil',
        ]);

        $this->assertDatabaseHas('amils', [
            'id' => $this->amil->id,
            'nama' => 'Nama Baru Amil',
            'no_hp' => '081299998888',
            'alamat' => 'Jl. Kebon Jeruk No. 5, Jakarta Barat',
        ]);
    }

    /**
     * Uji Amil dapat merubah kata sandi akunnya
     */
    public function test_amil_can_change_password(): void
    {
        $this->user->password = bcrypt('rahasia123');
        $this->user->save();

        // 1. Gagal jika current_password salah
        $failResponse = $this->actingAs($this->user)->put(route('amil.profile.password'), [
            'current_password' => 'passwordsalah',
            'password' => 'passwordbaru123',
            'password_confirmation' => 'passwordbaru123',
        ]);
        $failResponse->assertSessionHasErrors('current_password');

        // 2. Berhasil jika current_password valid
        $successResponse = $this->actingAs($this->user)->put(route('amil.profile.password'), [
            'current_password' => 'rahasia123',
            'password' => 'passwordbaru123',
            'password_confirmation' => 'passwordbaru123',
        ]);

        $successResponse->assertRedirect(route('amil.profile'));
        $successResponse->assertSessionHas('success');
    }
}
