<?php

namespace Tests\Feature;

use App\Filament\Livewire\AdminDatabaseNotifications;
use App\Models\AktivitasHarian;
use App\Models\Amil;
use App\Models\Divisi;
use App\Models\Donasi;
use App\Models\Donatur;
use App\Models\DonaturKategori;
use App\Models\IzinCuti;
use App\Models\JenisDana;
use App\Models\User;
use App\Services\ReviewNotificationService;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ReviewNotificationTest extends TestCase
{
    protected User $adminUser;

    protected User $amilUser;

    protected Amil $amil;

    protected function setUp(): void
    {
        parent::setUp();

        // Bersihkan data tes terdahulu
        DB::table('notifications')->truncate();
        IzinCuti::query()->forceDelete();
        AktivitasHarian::query()->forceDelete();
        Donasi::query()->forceDelete();

        // Pastikan role Admin & Amil ada
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Amil', 'guard_name' => 'web']);

        $this->adminUser = User::firstOrCreate(
            ['email' => 'admin_test@example.com'],
            ['name' => 'Admin Test', 'password' => bcrypt('password'), 'is_active' => true, 'role_id' => 1]
        );
        $this->adminUser->syncRoles(['Admin']);

        $divisi = Divisi::firstOrCreate(['nama' => 'Penghimpunan'], ['deskripsi' => 'Divisi ZIS']);

        $this->amilUser = User::firstOrCreate(
            ['email' => 'amil_test@example.com'],
            ['name' => 'Amil Test', 'password' => bcrypt('password'), 'is_active' => true]
        );
        $this->amilUser->syncRoles(['Amil']);

        $this->amil = Amil::firstOrCreate(
            ['user_id' => $this->amilUser->id],
            [
                'nama' => 'Amil Test',
                'nip' => 'AML-0099',
                'divisi_id' => $divisi->id,
                'no_hp' => '081234567899',
                'status' => 'aktif',
                'tipe' => 'staf',
            ]
        );
    }

    public function test_izin_cuti_diajukan_creates_notification_for_admin_and_disappears_when_approved(): void
    {
        $izin = IzinCuti::create([
            'amil_id' => $this->amil->id,
            'jenis' => 'cuti',
            'tgl_mulai' => now()->toDateString(),
            'tgl_selesai' => now()->addDays(2)->toDateString(),
            'alasan' => 'Acara keluarga mendesak',
            'status' => 'diajukan',
        ]);

        // Verifikasi notifikasi muncul di database
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->adminUser->id,
            'notifiable_type' => User::class,
        ]);

        $notif = DB::table('notifications')
            ->where('notifiable_id', $this->adminUser->id)
            ->where('data->review_type', 'izin_cuti')
            ->where('data->review_id', $izin->id)
            ->first();

        $this->assertNotNull($notif);
        $data = json_decode($notif->data, true);
        $this->assertStringContainsString('Pengajuan Cuti: Amil Test', $data['title']);

        // Setujui pengajuan izin cuti
        $izin->approve($this->adminUser->id);

        // Notifikasi harus hilang setelah disetujui
        $this->assertDatabaseMissing('notifications', [
            'id' => $notif->id,
        ]);
    }

    public function test_aktivitas_harian_diajukan_creates_notification_and_disappears_when_rejected(): void
    {
        $aktivitas = AktivitasHarian::create([
            'amil_id' => $this->amil->id,
            'tanggal' => now()->toDateString(),
            'judul' => 'Sosialisasi Program ZIS',
            'deskripsi' => 'Sosialisasi program ke mitra',
            'status' => 'diajukan',
        ]);

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->adminUser->id,
            'notifiable_type' => User::class,
        ]);

        $notif = DB::table('notifications')
            ->where('notifiable_id', $this->adminUser->id)
            ->where('data->review_type', 'aktivitas_harian')
            ->where('data->review_id', $aktivitas->id)
            ->first();

        $this->assertNotNull($notif);

        // Tolak aktivitas
        $aktivitas->update(['status' => 'ditolak']);

        $this->assertDatabaseMissing('notifications', [
            'id' => $notif->id,
        ]);
    }

    public function test_donasi_menunggu_verifikasi_creates_notification_and_disappears_when_verified(): void
    {
        $kategori = DonaturKategori::firstOrCreate(['nama' => 'Individu']);
        $donatur = Donatur::firstOrCreate(
            ['no_hp' => '087777777777'],
            [
                'kode_donatur' => 'DNT-TEST-01',
                'nama' => 'Bapak Donatur',
                'jenis' => 'individu',
                'kategori_id' => $kategori->id,
                'amil_pic_id' => $this->amil->id,
                'created_by' => $this->amilUser->id,
            ]
        );

        $jenisDana = JenisDana::firstOrCreate(['nama' => 'Zakat Maal'], ['kategori' => 'zakat', 'kode' => 'ZM']);

        $donasi = Donasi::create([
            'no_kwitansi' => 'KW-TEST-'.rand(100, 999),
            'donatur_id' => $donatur->id,
            'amil_id' => $this->amil->id,
            'jenis_dana_id' => $jenisDana->id,
            'jumlah' => 750000,
            'metode_bayar' => 'transfer',
            'tgl_donasi' => now(),
            'status' => 'menunggu_verifikasi',
        ]);

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->adminUser->id,
            'notifiable_type' => User::class,
        ]);

        $notif = DB::table('notifications')
            ->where('notifiable_id', $this->adminUser->id)
            ->where('data->review_type', 'donasi')
            ->where('data->review_id', $donasi->id)
            ->first();

        $this->assertNotNull($notif);
        $data = json_decode($notif->data, true);
        $this->assertStringContainsString('750.000', $data['body']);

        // Verifikasi donasi
        $donasi->update([
            'status' => 'terverifikasi',
            'verified_by' => $this->adminUser->id,
            'verified_at' => now(),
        ]);

        $this->assertDatabaseMissing('notifications', [
            'id' => $notif->id,
        ]);
    }

    public function test_sync_pending_for_user_restores_pending_items_and_cleans_stale(): void
    {
        // Buat data izin cuti pending secara direct (tanpa event)
        $izin = new IzinCuti;
        $izin->amil_id = $this->amil->id;
        $izin->jenis = 'izin';
        $izin->tgl_mulai = now()->toDateString();
        $izin->tgl_selesai = now()->toDateString();
        $izin->alasan = 'Izin test direct';
        $izin->status = 'diajukan';
        $izin->saveQuietly();

        // Pastikan tabel notifications kosong
        DB::table('notifications')->truncate();

        // Panggil syncPendingForUser
        ReviewNotificationService::syncPendingForUser($this->adminUser);

        // Notifikasi harus dibuat secara otomatis oleh sinkronisasi
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->adminUser->id,
            'notifiable_type' => User::class,
        ]);

        // Ubah status izin menjadi disetujui secara quietly
        $izin->status = 'disetujui';
        $izin->saveQuietly();

        // Panggil sync lagi
        ReviewNotificationService::syncPendingForUser($this->adminUser);

        // Notifikasi obsolete harus sudah dibersihkan
        $this->assertEquals(0, DB::table('notifications')->count());
    }

    public function test_admin_database_notifications_component_protects_pending_notifications(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        $izin = IzinCuti::create([
            'amil_id' => $this->amil->id,
            'jenis' => 'cuti',
            'tgl_mulai' => now()->toDateString(),
            'tgl_selesai' => now()->addDay()->toDateString(),
            'alasan' => 'Cuti penting',
            'status' => 'diajukan',
        ]);

        $notif = DB::table('notifications')
            ->where('notifiable_id', $this->adminUser->id)
            ->where('data->review_type', 'izin_cuti')
            ->where('data->review_id', $izin->id)
            ->first();

        $this->assertNotNull($notif);

        // Uji coba clearNotifications pada Livewire component
        Livewire::test(AdminDatabaseNotifications::class)
            ->call('clearNotifications');

        // Notifikasi pengajuan pending tetap ADA (tidak terhapus karena dilindungi)
        $this->assertDatabaseHas('notifications', [
            'id' => $notif->id,
        ]);

        // Uji coba removeNotification pada item pending
        Livewire::test(AdminDatabaseNotifications::class)
            ->call('removeNotification', $notif->id);

        // Masih tetap ada karena belum disetujui / ditolak
        $this->assertDatabaseHas('notifications', [
            'id' => $notif->id,
        ]);

        // Setelah disetujui
        $izin->approve($this->adminUser->id);

        // Notifikasi terhapus
        $this->assertDatabaseMissing('notifications', [
            'id' => $notif->id,
        ]);
    }

    public function test_tabs_and_counts_filtering_in_admin_database_notifications(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        // Buat 1 cuti, 1 aktivitas, 1 donasi
        $cuti = IzinCuti::create([
            'amil_id' => $this->amil->id,
            'jenis' => 'cuti',
            'tgl_mulai' => now()->toDateString(),
            'tgl_selesai' => now()->addDay()->toDateString(),
            'alasan' => 'Uji cuti tabs',
            'status' => 'diajukan',
        ]);

        $aktivitas = AktivitasHarian::create([
            'amil_id' => $this->amil->id,
            'tanggal' => now()->toDateString(),
            'judul' => 'Uji aktivitas tabs',
            'deskripsi' => 'Uji aktivitas tabs deskripsi',
            'status' => 'diajukan',
        ]);

        $kategori = DonaturKategori::firstOrCreate(['nama' => 'Individu']);
        $donatur = Donatur::firstOrCreate(
            ['no_hp' => '086666666666'],
            ['kode_donatur' => 'DNT-TAB-999', 'nama' => 'Donatur Tab', 'jenis' => 'individu', 'kategori_id' => $kategori->id]
        );
        $jenisDana = JenisDana::firstOrCreate(['nama' => 'Infaq'], ['kategori' => 'infaq', 'kode' => 'INF']);

        $donasi = Donasi::create([
            'no_kwitansi' => 'KW-TAB-001',
            'donatur_id' => $donatur->id,
            'amil_id' => $this->amil->id,
            'jenis_dana_id' => $jenisDana->id,
            'jumlah' => 200000,
            'metode_bayar' => 'tunai',
            'tgl_donasi' => now(),
            'status' => 'menunggu_verifikasi',
        ]);

        $component = Livewire::test(AdminDatabaseNotifications::class);

        // Verifikasi tab counts
        $counts = $component->instance()->getTabCounts();
        $this->assertEquals(3, $counts['all']);
        $this->assertEquals(1, $counts['donasi']);
        $this->assertEquals(1, $counts['aktivitas_harian']);
        $this->assertEquals(1, $counts['izin_cuti']);

        // Ganti tab ke donasi
        $component->call('setTab', 'donasi')
            ->assertSet('activeTab', 'donasi');

        $donasiNotifications = $component->instance()->getNotifications();
        $this->assertCount(1, $donasiNotifications);
        $firstDonasi = $donasiNotifications->first();
        $this->assertEquals('donasi', $firstDonasi->data['review_type']);

        // Ganti tab ke aktivitas
        $component->call('setTab', 'aktivitas_harian')
            ->assertSet('activeTab', 'aktivitas_harian');

        $aktivitasNotifications = $component->instance()->getNotifications();
        $this->assertCount(1, $aktivitasNotifications);
        $firstAktivitas = $aktivitasNotifications->first();
        $this->assertEquals('aktivitas_harian', $firstAktivitas->data['review_type']);

        // Ganti tab ke izin cuti
        $component->call('setTab', 'izin_cuti')
            ->assertSet('activeTab', 'izin_cuti');

        $cutiNotifications = $component->instance()->getNotifications();
        $this->assertCount(1, $cutiNotifications);
        $firstCuti = $cutiNotifications->first();
        $this->assertEquals('izin_cuti', $firstCuti->data['review_type']);

        // Kembali ke semua
        $component->call('setTab', 'all')
            ->assertSet('activeTab', 'all');

        $allNotifications = $component->instance()->getNotifications();
        $this->assertCount(3, $allNotifications);
    }
}
