<?php

namespace Tests\Feature;

use App\Models\Amil;
use App\Models\Donasi;
use App\Models\Donatur;
use App\Models\DonaturKategori;
use App\Models\JenisDana;
use App\Models\Mustahik;
use App\Models\Penyaluran;
use App\Models\User;
use App\Services\DonasiService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class FinancialDataSecurityTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;

    protected Amil $amil;

    protected Donatur $donatur;

    protected JenisDana $jenisDana;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::firstOrCreate(
            ['email' => 'admin_finance_test@amilgsc.com'],
            ['name' => 'Admin Finance Test', 'password' => bcrypt('password'), 'is_active' => true]
        );

        $this->amil = Amil::firstOrCreate(
            ['nip' => 'AML-FIN-01'],
            ['user_id' => $this->user->id, 'nama' => 'Amil Finansial', 'target_bulanan' => 5000000, 'status' => 'aktif']
        );

        $kategori = DonaturKategori::firstOrCreate(['nama' => 'Individu Reguler']);

        $this->donatur = Donatur::create([
            'kode_donatur' => 'DNT-FIN-'.rand(1000, 9999),
            'nama' => 'Bapak Uji Keuangan',
            'jenis' => 'individu',
            'kategori_id' => $kategori->id,
            'no_hp' => '081299998888',
            'amil_pic_id' => $this->amil->id,
            'created_by' => $this->user->id,
        ]);

        $this->jenisDana = JenisDana::first() ?? JenisDana::create([
            'nama' => 'Zakat Maal Uji',
            'kelompok' => 'zakat',
            'hak_amil_persen' => 12.50,
        ]);
    }

    /**
     * 1. Uji Database Foreign Key Restrict: Donatur dengan riwayat donasi tidak bisa di-hard-delete
     */
    public function test_donatur_with_donations_cannot_be_hard_deleted_due_to_restrict_constraint(): void
    {
        // Buat donasi riil
        $donasi = Donasi::create([
            'no_kwitansi' => 'KW/TEST/'.rand(1000, 9999),
            'donatur_id' => $this->donatur->id,
            'amil_id' => $this->amil->id,
            'jenis_dana_id' => $this->jenisDana->id,
            'jumlah' => 2500000,
            'metode_bayar' => 'transfer',
            'tgl_donasi' => now(),
            'status' => 'terverifikasi',
        ]);

        $this->assertDatabaseHas('donasis', ['id' => $donasi->id]);

        // Coba force delete donatur (harus ditolak oleh RDBMS Foreign Key Constraint)
        $this->expectException(QueryException::class);
        $this->donatur->forceDelete();
    }

    /**
     * 2. Uji Database Foreign Key Restrict: Mustahik dengan riwayat penyaluran tidak bisa di-hard-delete
     */
    public function test_mustahik_with_disbursements_cannot_be_hard_deleted(): void
    {
        $mustahik = Mustahik::create([
            'nama' => 'Ibu Dhuafa Uji',
            'asnaf' => 'fakir',
            'status_verifikasi' => 'terverifikasi',
        ]);

        $penyaluran = Penyaluran::create([
            'mustahik_id' => $mustahik->id,
            'jenis_dana_id' => $this->jenisDana->id,
            'jumlah' => 750000,
            'tgl_salur' => now()->toDateString(),
            'amil_id' => $this->amil->id,
        ]);

        $this->assertDatabaseHas('penyalurans', ['id' => $penyaluran->id]);

        // Coba force delete mustahik (harus ditolak oleh foreign key restrict)
        $this->expectException(QueryException::class);
        $mustahik->forceDelete();
    }

    /**
     * 3. Uji SoftDeletes: Menghapus donasi tidak melenyapkan data dari database
     */
    public function test_soft_deletes_preserves_financial_history(): void
    {
        $donasi = Donasi::create([
            'no_kwitansi' => 'KW/SOFT/'.rand(1000, 9999),
            'donatur_id' => $this->donatur->id,
            'amil_id' => $this->amil->id,
            'jenis_dana_id' => $this->jenisDana->id,
            'jumlah' => 1000000,
            'metode_bayar' => 'tunai',
            'tgl_donasi' => now(),
            'status' => 'menunggu_verifikasi',
        ]);

        // Hapus donasi secara standar (soft delete)
        $donasi->delete();

        // Data tidak boleh hilang dari database (hanya kolom deleted_at terisi)
        $this->assertSoftDeleted('donasis', ['id' => $donasi->id]);

        // Bisa dipulihkan kembali
        $donasi->restore();
        $this->assertNotSoftDeleted('donasis', ['id' => $donasi->id]);
    }

    /**
     * 4. Uji Anti-Collision & Thread-Safety Nomor Kwitansi
     */
    public function test_generate_no_kwitansi_is_collision_free_and_respects_soft_deletes(): void
    {
        $service = app(DonasiService::class);
        $noKwitansi = $service->generateNoKwitansi(now(), lock: true);

        $this->assertNotEmpty($noKwitansi);

        // Buat donasi dengan nomor tersebut
        $donasi = Donasi::create([
            'no_kwitansi' => $noKwitansi,
            'donatur_id' => $this->donatur->id,
            'amil_id' => $this->amil->id,
            'jenis_dana_id' => $this->jenisDana->id,
            'jumlah' => 500000,
            'metode_bayar' => 'tunai',
            'tgl_donasi' => now(),
        ]);

        // Meskipun donasi di-soft-delete, nomor kwitansi berikutnya TIDAK BOLEH menduplikasi nomor yang sama!
        $donasi->delete();
        $this->assertSoftDeleted('donasis', ['id' => $donasi->id]);

        $nextNoKwitansi = $service->generateNoKwitansi(now(), lock: true);
        $this->assertNotEquals($noKwitansi, $nextNoKwitansi);
    }

    /**
     * 5. Uji ActivityLog: Setiap perubahan data donasi tercatat di tabel log
     */
    public function test_donasi_records_activity_log_automatically(): void
    {
        $this->actingAs($this->user);

        $donasi = Donasi::create([
            'no_kwitansi' => 'KW/LOG/'.rand(1000, 9999),
            'donatur_id' => $this->donatur->id,
            'amil_id' => $this->amil->id,
            'jenis_dana_id' => $this->jenisDana->id,
            'jumlah' => 3000000,
            'metode_bayar' => 'transfer',
            'tgl_donasi' => now(),
            'status' => 'menunggu_verifikasi',
        ]);

        // Ubah status donasi menjadi terverifikasi
        $donasi->update([
            'status' => 'terverifikasi',
            'verified_by' => $this->user->id,
            'verified_at' => now(),
        ]);

        $latestLog = Activity::where('subject_type', Donasi::class)
            ->where('subject_id', $donasi->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($latestLog);
        $this->assertEquals('donasi', $latestLog->log_name);
    }
}
