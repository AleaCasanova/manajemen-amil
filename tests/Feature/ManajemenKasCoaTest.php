<?php

namespace Tests\Feature;

use App\Filament\Resources\CashAdvances\Pages\CreateCashAdvance;
use App\Models\AkunKeuangan;
use App\Models\KategoriKeuangan;
use App\Models\TransaksiKeuangan;
use App\Models\User;
use App\Services\CashAdvanceService;
use App\Services\TransaksiKeuanganService;
use Database\Seeders\KeuanganSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use InvalidArgumentException;
use Livewire\Livewire;
use Tests\TestCase;

class ManajemenKasCoaTest extends TestCase
{
    use DatabaseTransactions;

    private function actingAsAdmin(): User
    {
        $admin = User::where('is_active', true)->where('role_id', 1)->first()
            ?? User::factory()->create([
                'role_id' => 1,
                'is_active' => true,
            ]);

        $this->actingAs($admin, 'admin');

        return $admin;
    }

    public function test_keuangan_seeder_mengisi_coa_gsc_dan_9_akun_resmi_secara_idempoten(): void
    {
        $this->seed(KeuanganSeeder::class);
        $this->seed(KeuanganSeeder::class);

        $this->assertGreaterThanOrEqual(180, KategoriKeuangan::whereNotNull('kode_coa')->count());
        $this->assertEquals(9, AkunKeuangan::whereNotNull('kode_coa')->count());

        $this->assertDatabaseHas('kategori_keuangans', ['kode_coa' => '101.01.001', 'is_postable' => true]);
        $this->assertDatabaseHas('kategori_keuangans', ['kode_coa' => '101.09.000', 'jenis' => 'netral', 'is_postable' => true]);
        $this->assertDatabaseHas('kategori_keuangans', ['kode_coa' => '101.06.001', 'jenis' => 'netral', 'is_postable' => true]);
        $this->assertDatabaseHas('kategori_keuangans', ['kode_coa' => '401.00.001', 'pos_dana' => 'zakat']);
        $this->assertDatabaseHas('kategori_keuangans', ['kode_coa' => '503.01.004', 'pos_dana' => 'amil']);
        $this->assertDatabaseHas('akun_keuangans', ['kode_coa' => '101.01.001', 'tipe' => 'kas']);
        $this->assertDatabaseHas('akun_keuangans', ['kode_coa' => '101.02.008', 'tipe' => 'bank']);
    }

    public function test_scope_coa_memfilter_akun_aktif_postable_dan_arah_kas(): void
    {
        $header = KategoriKeuangan::create([
            'kode_coa' => '999.01.000',
            'level' => 2,
            'nama' => 'Header Test',
            'kelompok' => 'penyaluran',
            'pos_dana' => 'amil',
            'jenis' => 'keluar',
            'saldo_normal' => 'debit',
            'is_postable' => false,
            'is_active' => true,
        ]);

        $coaMasuk = KategoriKeuangan::create([
            'kode_coa' => '999.01.001',
            'parent_id' => $header->id,
            'level' => 3,
            'nama' => 'Penerimaan Test',
            'kelompok' => 'penerimaan',
            'pos_dana' => 'infak_umum',
            'jenis' => 'masuk',
            'saldo_normal' => 'kredit',
            'is_postable' => true,
            'is_active' => true,
        ]);

        $coaNetral = KategoriKeuangan::create([
            'kode_coa' => '999.01.002',
            'parent_id' => $header->id,
            'level' => 3,
            'nama' => 'Neraca Test',
            'kelompok' => 'aset',
            'pos_dana' => 'netral',
            'jenis' => 'netral',
            'saldo_normal' => 'debit',
            'is_postable' => true,
            'is_active' => true,
        ]);

        $postableIds = KategoriKeuangan::active()->postable()->pluck('id');
        $this->assertFalse($postableIds->contains($header->id));
        $this->assertTrue($postableIds->contains($coaMasuk->id));
        $this->assertTrue($postableIds->contains($coaNetral->id));

        $keluarIds = KategoriKeuangan::active()->postable()->untukArahKas('keluar')->pluck('id');
        $this->assertFalse($keluarIds->contains($coaMasuk->id));
        $this->assertTrue($keluarIds->contains($coaNetral->id));
    }

    public function test_transfer_antar_rekening_dan_biaya_admin_tercatat_berpasangan_dan_arus_kas_riil_akurat(): void
    {
        $admin = $this->actingAsAdmin();

        $akunAsal = AkunKeuangan::create([
            'nama' => 'Bank Asal Test',
            'tipe' => 'bank',
            'fungsi' => 'penampung',
            'saldo_awal' => 1000000,
            'is_active' => true,
        ]);

        $akunTujuan = AkunKeuangan::create([
            'nama' => 'Kas Tujuan Test',
            'tipe' => 'kas',
            'fungsi' => 'kas_teller',
            'saldo_awal' => 500000,
            'is_active' => true,
        ]);

        $result = app(TransaksiKeuanganService::class)->buatTransfer([
            'akun_asal_id' => $akunAsal->id,
            'akun_tujuan_id' => $akunTujuan->id,
            'nominal' => 300000,
            'biaya_admin' => 2500,
            'tgl_transaksi' => '2026-09-10',
            'no_voucher' => 'TRF-001',
            'keterangan' => 'Droping kas operasional',
        ], $admin->id);

        $this->assertTrue($result['keluar']->is_transfer);
        $this->assertTrue($result['masuk']->is_transfer);
        $this->assertEquals($result['masuk']->id, $result['keluar']->transfer_pasangan_id);
        $this->assertEquals($result['keluar']->id, $result['masuk']->transfer_pasangan_id);
        $this->assertNotNull($result['biaya_admin']);
        $this->assertFalse($result['biaya_admin']->is_transfer);

        $this->assertEquals(697500.0, $akunAsal->fresh()->saldo_sekarang);
        $this->assertEquals(800000.0, $akunTujuan->fresh()->saldo_sekarang);

        $arusKeluarRiil = (float) TransaksiKeuangan::arusKasRiil()
            ->where('transfer_group_kode', $result['group_kode'])
            ->where('jenis', 'keluar')
            ->sum('nominal');

        $this->assertEquals(2500.0, $arusKeluarRiil);
    }

    public function test_hapus_dan_pulihkan_transfer_bekerja_atomik_untuk_seluruh_grup(): void
    {
        $admin = $this->actingAsAdmin();
        $service = app(TransaksiKeuanganService::class);

        $akunAsal = AkunKeuangan::create([
            'nama' => 'Akun A',
            'tipe' => 'bank',
            'saldo_awal' => 1000000,
            'is_active' => true,
        ]);
        $akunTujuan = AkunKeuangan::create([
            'nama' => 'Akun B',
            'tipe' => 'bank',
            'saldo_awal' => 1000000,
            'is_active' => true,
        ]);

        $res = $service->buatTransfer([
            'akun_asal_id' => $akunAsal->id,
            'akun_tujuan_id' => $akunTujuan->id,
            'nominal' => 200000,
            'biaya_admin' => 2500,
            'tgl_transaksi' => '2026-09-11',
        ], $admin->id);

        $service->hapusTransfer($res['keluar']);

        $this->assertSoftDeleted('transaksi_keuangans', ['id' => $res['keluar']->id]);
        $this->assertSoftDeleted('transaksi_keuangans', ['id' => $res['masuk']->id]);
        $this->assertSoftDeleted('transaksi_keuangans', ['id' => $res['biaya_admin']->id]);

        $service->pulihkanTransfer($res['masuk']->fresh());

        $this->assertDatabaseHas('transaksi_keuangans', ['id' => $res['keluar']->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('transaksi_keuangans', ['id' => $res['masuk']->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('transaksi_keuangans', ['id' => $res['biaya_admin']->id, 'deleted_at' => null]);
    }

    public function test_validasi_transfer_menolak_akun_sama_dan_nominal_tidak_valid(): void
    {
        $akun = AkunKeuangan::create([
            'nama' => 'Akun Sama',
            'tipe' => 'kas',
            'saldo_awal' => 100000,
            'is_active' => true,
        ]);

        $service = app(TransaksiKeuanganService::class);

        $this->expectException(InvalidArgumentException::class);
        $service->buatTransfer([
            'akun_asal_id' => $akun->id,
            'akun_tujuan_id' => $akun->id,
            'nominal' => 50000,
        ]);
    }

    public function test_cash_opname_menghitung_pecahan_fisik_dan_memperbarui_rekonsiliasi_akun(): void
    {
        $admin = $this->actingAsAdmin();

        $kas = AkunKeuangan::create([
            'nama' => 'Kas Brankas Test',
            'tipe' => 'kas',
            'saldo_awal' => 600000,
            'is_active' => true,
        ]);

        $opname = app(TransaksiKeuanganService::class)->simpanCashOpname(
            $kas,
            '2026-09-30',
            [
                'kertas_100000' => 5,
                'kertas_50000' => 2,
                'koin_1000' => 3,
                'cek_wesel' => 10000,
            ],
            'Opname akhir bulan',
            $admin->id
        );

        $this->assertEquals(613000.0, (float) $opname->total_fisik);
        $this->assertEquals(600000.0, (float) $opname->saldo_buku);
        $this->assertEquals(13000.0, (float) $opname->selisih);
        $this->assertEquals(613000.0, (float) $kas->fresh()->saldo_riil_terakhir);
    }

    public function test_siklus_cash_advance_lpj_gross_settlement_dengan_sisa_dikembalikan(): void
    {
        $admin = $this->actingAsAdmin();
        $service = app(CashAdvanceService::class);

        $akun = AkunKeuangan::create([
            'nama' => 'Kas CA Test',
            'tipe' => 'kas',
            'saldo_awal' => 5000000,
            'is_active' => true,
        ]);

        $coaBeban = KategoriKeuangan::firstOrCreate(
            ['kode_coa' => '501.99.001'],
            [
                'level' => 3,
                'nama' => 'Beban Santunan Test',
                'kelompok' => 'penyaluran',
                'pos_dana' => 'infak_umum',
                'jenis' => 'keluar',
                'saldo_normal' => 'debit',
                'is_postable' => true,
                'is_active' => true,
            ]
        );

        $ca = $service->cairkan([
            'tipe' => 'cash_advance',
            'akun_keuangan_id' => $akun->id,
            'nominal_dicairkan' => 1000000,
            'nama_pj' => 'Budi Santoso',
            'tgl_pencairan' => '2026-09-05',
            'keperluan' => 'Santunan Anak Yatim',
        ], $admin->id);

        $this->assertEquals('aktif', $ca->status);
        $this->assertStringStartsWith('CA-', $ca->kode_ca);
        $this->assertEquals(4000000.0, $akun->fresh()->saldo_sekarang);

        $selesai = $service->selesaikanLpj(
            $ca,
            [
                [
                    'kategori_keuangan_id' => $coaBeban->id,
                    'uraian' => 'Paket Sembako',
                    'nominal' => 600000,
                    'tgl_realisasi' => '2026-09-07',
                ],
                [
                    'kategori_keuangan_id' => $coaBeban->id,
                    'uraian' => 'Konsumsi & Transport',
                    'nominal' => 250000,
                    'tgl_realisasi' => '2026-09-07',
                ],
            ],
            $akun->id,
            '2026-09-08',
            'LPJ sudah lengkap',
            $admin->id
        );

        $this->assertEquals('selesai', $selesai->status);
        $this->assertEquals(850000.0, (float) $selesai->nominal_realisasi);
        $this->assertEquals(150000.0, (float) $selesai->nominal_dikembalikan);
        $this->assertEquals(0.0, (float) $selesai->nominal_reimburse);
        $this->assertCount(2, $selesai->realisasis);
        $this->assertEquals(4150000.0, $akun->fresh()->saldo_sekarang);
    }

    public function test_siklus_cash_advance_lpj_reimburse_ketika_realisasi_melebihi_uang_muka(): void
    {
        $admin = $this->actingAsAdmin();
        $service = app(CashAdvanceService::class);

        $akun = AkunKeuangan::create([
            'nama' => 'Kas Reimburse Test',
            'tipe' => 'kas',
            'saldo_awal' => 2000000,
            'is_active' => true,
        ]);

        $coaBeban = KategoriKeuangan::firstOrCreate(
            ['kode_coa' => '503.99.001'],
            [
                'level' => 3,
                'nama' => 'Beban Operasional Test',
                'kelompok' => 'penyaluran',
                'pos_dana' => 'amil',
                'jenis' => 'keluar',
                'saldo_normal' => 'debit',
                'is_postable' => true,
                'is_active' => true,
            ]
        );

        $ca = $service->cairkan([
            'tipe' => 'cash_advance',
            'akun_keuangan_id' => $akun->id,
            'nominal_dicairkan' => 500000,
            'nama_pj' => 'Siti Aminah',
            'tgl_pencairan' => '2026-09-12',
            'keperluan' => 'Operasional Event',
        ], $admin->id);

        $selesai = $service->selesaikanLpj(
            $ca,
            [
                [
                    'kategori_keuangan_id' => $coaBeban->id,
                    'uraian' => 'Cetak Spanduk & Sound',
                    'nominal' => 650000,
                ],
            ],
            $akun->id,
            '2026-09-14',
            null,
            $admin->id
        );

        $this->assertEquals(650000.0, (float) $selesai->nominal_realisasi);
        $this->assertEquals(0.0, (float) $selesai->nominal_dikembalikan);
        $this->assertEquals(150000.0, (float) $selesai->nominal_reimburse);
        $this->assertEquals(1350000.0, $akun->fresh()->saldo_sekarang);
    }

    public function test_siklus_cash_bon_cicilan_dan_pelunasan_serta_tolak_kelebihan_bayar(): void
    {
        $admin = $this->actingAsAdmin();
        $service = app(CashAdvanceService::class);

        $akun = AkunKeuangan::create([
            'nama' => 'Kas Kasbon Test',
            'tipe' => 'kas',
            'saldo_awal' => 3000000,
            'is_active' => true,
        ]);

        $cb = $service->cairkan([
            'tipe' => 'cash_bon',
            'akun_keuangan_id' => $akun->id,
            'nominal_dicairkan' => 1000000,
            'nama_pj' => 'Ahmad Amil',
            'tgl_pencairan' => '2026-09-01',
            'keperluan' => 'Pinjaman karyawan',
        ], $admin->id);

        $this->assertStringStartsWith('CB-', $cb->kode_ca);
        $this->assertEquals(2000000.0, $akun->fresh()->saldo_sekarang);

        $cicilan1 = $service->bayarCashBon($cb, 400000, $akun->id, '2026-09-15', 'Cicilan 1', null, $admin->id);
        $this->assertEquals('sebagian', $cicilan1->status);
        $this->assertEquals(600000.0, $cicilan1->sisa_tanggungan);

        try {
            $service->bayarCashBon($cicilan1, 700000, $akun->id, '2026-09-20', null, null, $admin->id);
            $this->fail('Harusnya melempar InvalidArgumentException saat pembayaran melebihi sisa tanggungan.');
        } catch (InvalidArgumentException $e) {
            $this->assertStringContainsString('melebihi', $e->getMessage());
        }

        $lunas = $service->bayarCashBon($cicilan1, 600000, $akun->id, '2026-09-30', 'Pelunasan', null, $admin->id);
        $this->assertEquals('selesai', $lunas->status);
        $this->assertEquals(0.0, $lunas->sisa_tanggungan);
        $this->assertEquals(3000000.0, $akun->fresh()->saldo_sekarang);
    }

    public function test_halaman_keuangan_admin_dapat_diakses_dan_create_cash_advance_livewire(): void
    {
        $this->actingAsAdmin();

        $this->get('/portal-internal-gsc/transaksi-keuangans')->assertStatus(200);
        $this->get('/portal-internal-gsc/cash-advances')->assertStatus(200);
        $this->get('/portal-internal-gsc/akun-keuangans')->assertStatus(200);
        $this->get('/portal-internal-gsc/kategori-keuangans')->assertStatus(200);

        $akun = AkunKeuangan::where('is_active', true)->firstOrFail();

        Livewire::test(CreateCashAdvance::class)
            ->fillForm([
                'tipe' => 'cash_advance',
                'tgl_pencairan' => '2026-09-20',
                'akun_keuangan_id' => $akun->id,
                'nominal_dicairkan' => 250000,
                'nama_pj' => 'Tester Livewire',
                'keperluan' => 'Uji form pencairan Livewire',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('cash_advances', [
            'nama_pj' => 'Tester Livewire',
            'nominal_dicairkan' => 250000,
            'status' => 'aktif',
        ]);
    }
}
