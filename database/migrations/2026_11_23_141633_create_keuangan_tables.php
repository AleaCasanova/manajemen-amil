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
        // 1. Akun Kas & Bank (Dompet / Rekening Keuangan)
        Schema::create('akun_keuangans', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('nomor_rekening', 50)->nullable();
            $table->string('atas_nama', 150)->nullable();
            $table->enum('tipe', ['kas', 'bank', 'e_wallet'])->default('kas');
            $table->decimal('saldo_awal', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Kategori Pos Keuangan (Pemasukan / Pengeluaran)
        Schema::create('kategori_keuangans', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->enum('jenis', ['masuk', 'keluar']);
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Buku Kas / Transaksi Keuangan Keseluruhan Yayasan
        Schema::create('transaksi_keuangans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi', 50)->unique();
            $table->enum('jenis', ['masuk', 'keluar']);
            $table->foreignId('kategori_keuangan_id')->constrained('kategori_keuangans')->restrictOnDelete();
            $table->foreignId('akun_keuangan_id')->constrained('akun_keuangans')->restrictOnDelete();
            $table->foreignId('program_id')->nullable()->constrained('programs')->nullOnDelete();
            $table->foreignId('donasi_id')->nullable()->constrained('donasis')->nullOnDelete();
            $table->foreignId('penyaluran_id')->nullable()->constrained('penyalurans')->nullOnDelete();
            $table->decimal('nominal', 15, 2);
            $table->date('tgl_transaksi');
            $table->string('pihak_terkait', 150)->nullable();
            $table->text('keterangan');
            $table->string('bukti_transaksi', 255)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_keuangans');
        Schema::dropIfExists('kategori_keuangans');
        Schema::dropIfExists('akun_keuangans');
    }
};
