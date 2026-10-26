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
        Schema::create('mustahiks', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nik', 25)->nullable();
            $table->text('alamat')->nullable();
            $table->enum('asnaf', ['fakir', 'miskin', 'amil', 'mualaf', 'riqab', 'gharim', 'fisabilillah', 'ibnu_sabil'])->default('fakir');
            $table->text('kondisi')->nullable();
            $table->enum('status_verifikasi', ['menunggu', 'terverifikasi', 'ditolak'])->default('menunggu');
            $table->timestamps();
        });

        Schema::create('penyalurans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mustahik_id')->constrained('mustahiks')->cascadeOnDelete();
            $table->foreignId('program_id')->nullable()->constrained('programs')->nullOnDelete();
            $table->foreignId('jenis_dana_id')->nullable()->constrained('jenis_danas')->nullOnDelete();
            $table->decimal('jumlah', 15, 2);
            $table->date('tgl_salur');
            $table->string('bukti')->nullable();
            $table->foreignId('amil_id')->nullable()->constrained('amils')->nullOnDelete();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penyalurans');
        Schema::dropIfExists('mustahiks');
    }
};
