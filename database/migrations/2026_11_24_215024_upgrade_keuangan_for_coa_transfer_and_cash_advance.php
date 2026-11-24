<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Upgrade kategori_keuangans menjadi Master Chart of Accounts (COA)
        Schema::table('kategori_keuangans', function (Blueprint $table) {
            $table->string('jenis', 20)->default('keluar')->change();
            $table->string('kode_coa', 20)->nullable()->unique()->after('id');
            $table->foreignId('parent_id')->nullable()->after('kode_coa')->constrained('kategori_keuangans')->nullOnDelete();
            $table->unsignedTinyInteger('level')->default(3)->after('parent_id');
            $table->string('kelompok', 30)->nullable()->after('nama');
            $table->string('pos_dana', 30)->nullable()->after('kelompok');
            $table->string('saldo_normal', 10)->default('debit')->after('pos_dana');
            $table->boolean('is_postable')->default(true)->after('saldo_normal');

            $table->index(['kelompok', 'is_postable', 'is_active'], 'kat_keuangan_kelompok_post_active_idx');
            $table->index(['jenis', 'is_postable', 'is_active'], 'kat_keuangan_jenis_post_active_idx');
        });

        // 2. Upgrade akun_keuangans untuk Multi-Rekening GSC & Rekonsiliasi
        Schema::table('akun_keuangans', function (Blueprint $table) {
            $table->string('kode_coa', 20)->nullable()->unique()->after('id');
            $table->foreignId('kategori_keuangan_id')->nullable()->after('kode_coa')->constrained('kategori_keuangans')->nullOnDelete();
            $table->string('nama_bank', 50)->nullable()->after('nama');
            $table->string('fungsi', 30)->nullable()->after('tipe');
            $table->decimal('saldo_minimal', 15, 2)->default(0)->after('saldo_awal');
            $table->decimal('saldo_riil_terakhir', 15, 2)->nullable()->after('saldo_minimal');
            $table->date('tgl_rekonsiliasi_terakhir')->nullable()->after('saldo_riil_terakhir');
        });

        // 3. Buat tabel cash_advances (Uang Muka Kerja & Cash Bon)
        Schema::create('cash_advances', function (Blueprint $table) {
            $table->id();
            $table->string('kode_ca', 50)->unique();
            $table->string('tipe', 20)->default('cash_advance');
            $table->foreignId('amil_id')->nullable()->constrained('amils')->nullOnDelete();
            $table->string('nama_pj', 150);
            $table->foreignId('divisi_id')->nullable()->constrained('divisis')->nullOnDelete();
            $table->foreignId('program_id')->nullable()->constrained('programs')->nullOnDelete();
            $table->foreignId('pengajuan_dana_id')->nullable()->constrained('pengajuan_danas')->nullOnDelete();
            $table->foreignId('akun_keuangan_id')->constrained('akun_keuangans')->restrictOnDelete();
            $table->date('tgl_pencairan');
            $table->date('tgl_jatuh_tempo')->nullable();
            $table->decimal('nominal_dicairkan', 15, 2);
            $table->decimal('nominal_realisasi', 15, 2)->default(0);
            $table->decimal('nominal_dikembalikan', 15, 2)->default(0);
            $table->decimal('nominal_reimburse', 15, 2)->default(0);
            $table->text('keperluan');
            $table->string('status', 25)->default('aktif');
            $table->date('tgl_selesai')->nullable();
            $table->foreignId('akun_penyelesaian_id')->nullable()->constrained('akun_keuangans')->nullOnDelete();
            $table->text('catatan_penyelesaian')->nullable();
            $table->string('bukti_pencairan', 255)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tipe', 'status']);
            $table->index('tgl_pencairan');
        });

        // 4. Upgrade transaksi_keuangans untuk Voucher, Transfer Berpasangan, CA & Metadata
        Schema::table('transaksi_keuangans', function (Blueprint $table) {
            $table->string('no_voucher', 50)->nullable()->after('kode_transaksi');
            $table->string('reff', 100)->nullable()->after('no_voucher');
            $table->string('tipe_transaksi', 30)->default('reguler')->after('jenis');
            $table->boolean('is_transfer')->default(false)->after('tipe_transaksi');
            $table->string('transfer_group_kode', 50)->nullable()->after('is_transfer');
            $table->foreignId('transfer_pasangan_id')->nullable()->after('transfer_group_kode')->constrained('transaksi_keuangans')->nullOnDelete();
            $table->foreignId('cash_advance_id')->nullable()->after('penyaluran_id')->constrained('cash_advances')->nullOnDelete();
            $table->foreignId('pj_amil_id')->nullable()->after('pihak_terkait')->constrained('amils')->nullOnDelete();
            $table->foreignId('divisi_id')->nullable()->after('pj_amil_id')->constrained('divisis')->nullOnDelete();
            $table->string('sub_program', 100)->nullable()->after('program_id');

            $table->index(['akun_keuangan_id', 'tgl_transaksi'], 'trx_keuangan_akun_tgl_idx');
            $table->index(['is_transfer', 'tgl_transaksi'], 'trx_keuangan_transfer_tgl_idx');
            $table->index('transfer_group_kode', 'trx_keuangan_transfer_group_idx');
            $table->index('tipe_transaksi', 'trx_keuangan_tipe_idx');
        });

        // 5. Buat tabel cash_advance_realisasis (Item LPJ Cash Advance)
        Schema::create('cash_advance_realisasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_advance_id')->constrained('cash_advances')->cascadeOnDelete();
            $table->foreignId('kategori_keuangan_id')->constrained('kategori_keuangans')->restrictOnDelete();
            $table->foreignId('transaksi_keuangan_id')->nullable()->constrained('transaksi_keuangans')->nullOnDelete();
            $table->date('tgl_realisasi');
            $table->string('uraian', 255);
            $table->decimal('nominal', 15, 2);
            $table->string('bukti_nota', 255)->nullable();
            $table->timestamps();
        });

        // 6. Buat tabel cash_opnames (Hitung Fisik Pecahan Kas Tunai)
        Schema::create('cash_opnames', function (Blueprint $table) {
            $table->id();
            $table->foreignId('akun_keuangan_id')->constrained('akun_keuangans')->restrictOnDelete();
            $table->date('tgl_opname');
            $table->json('pecahan');
            $table->decimal('total_fisik', 15, 2);
            $table->decimal('saldo_buku', 15, 2);
            $table->decimal('selisih', 15, 2);
            $table->text('catatan')->nullable();
            $table->foreignId('petugas_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['akun_keuangan_id', 'tgl_opname']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_opnames');
        Schema::dropIfExists('cash_advance_realisasis');

        Schema::table('transaksi_keuangans', function (Blueprint $table) {
            $table->dropForeign(['transfer_pasangan_id']);
            $table->dropForeign(['cash_advance_id']);
            $table->dropForeign(['pj_amil_id']);
            $table->dropForeign(['divisi_id']);
            $table->dropIndex('trx_keuangan_akun_tgl_idx');
            $table->dropIndex('trx_keuangan_transfer_tgl_idx');
            $table->dropIndex('trx_keuangan_transfer_group_idx');
            $table->dropIndex('trx_keuangan_tipe_idx');
            $table->dropColumn([
                'no_voucher',
                'reff',
                'tipe_transaksi',
                'is_transfer',
                'transfer_group_kode',
                'transfer_pasangan_id',
                'cash_advance_id',
                'pj_amil_id',
                'divisi_id',
                'sub_program',
            ]);
        });

        Schema::dropIfExists('cash_advances');

        Schema::table('akun_keuangans', function (Blueprint $table) {
            $table->dropForeign(['kategori_keuangan_id']);
            $table->dropUnique(['kode_coa']);
            $table->dropColumn([
                'kode_coa',
                'kategori_keuangan_id',
                'nama_bank',
                'fungsi',
                'saldo_minimal',
                'saldo_riil_terakhir',
                'tgl_rekonsiliasi_terakhir',
            ]);
        });

        Schema::table('kategori_keuangans', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropUnique(['kode_coa']);
            $table->dropIndex('kat_keuangan_kelompok_post_active_idx');
            $table->dropIndex('kat_keuangan_jenis_post_active_idx');
            $table->dropColumn([
                'kode_coa',
                'parent_id',
                'level',
                'kelompok',
                'pos_dana',
                'saldo_normal',
                'is_postable',
            ]);
        });
    }
};
