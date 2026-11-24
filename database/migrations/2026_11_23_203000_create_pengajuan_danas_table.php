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
        Schema::create('pengajuan_danas', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pengajuan', 50)->unique();
            $table->foreignId('amil_id')->constrained('amils')->cascadeOnDelete();
            $table->foreignId('divisi_id')->nullable()->constrained('divisis')->nullOnDelete();
            $table->foreignId('program_id')->nullable()->constrained('programs')->nullOnDelete();
            $table->foreignId('kategori_keuangan_id')->nullable()->constrained('kategori_keuangans')->nullOnDelete();

            $table->string('judul', 200);
            $table->text('deskripsi');
            $table->decimal('nominal_diajukan', 15, 2);
            $table->date('tgl_kebutuhan');

            // Referensi Resmi & Dokumen Pendukung
            $table->string('nomor_dokumen_referensi', 100)->nullable();
            $table->string('dokumen_pendukung', 255)->nullable();

            // Status Workflow Otorisasi
            $table->enum('status', [
                'diajukan',
                'diverifikasi_bendahara',
                'disetujui',
                'dicairkan',
                'ditolak',
            ])->default('diajukan');

            // Tahap 1: Verifikasi Bendahara
            $table->foreignId('bendahara_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('tgl_verifikasi_bendahara')->nullable();
            $table->text('catatan_bendahara')->nullable();

            // Tahap 2: ACC / Persetujuan Manager
            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('tgl_acc_manager')->nullable();
            $table->text('catatan_manager')->nullable();
            $table->decimal('nominal_disetujui', 15, 2)->nullable();

            // Tahap 3: Pencairan Dana & Integrasi Buku Kas
            $table->foreignId('akun_keuangan_id')->nullable()->constrained('akun_keuangans')->nullOnDelete();
            $table->date('tgl_pencairan')->nullable();
            $table->enum('metode_pencairan', ['transfer', 'tunai'])->default('transfer');
            $table->string('bukti_pencairan', 255)->nullable();
            $table->foreignId('transaksi_keuangan_id')->nullable()->constrained('transaksi_keuangans')->nullOnDelete();

            $table->text('alasan_penolakan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_danas');
    }
};
