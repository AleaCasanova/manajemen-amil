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
        Schema::create('donasis', function (Blueprint $table) {
            $table->id();
            $table->string('no_kwitansi')->unique();
            $table->foreignId('donatur_id')->constrained('donaturs')->cascadeOnDelete();
            $table->foreignId('amil_id')->nullable()->constrained('amils')->nullOnDelete();
            $table->foreignId('jenis_dana_id')->constrained('jenis_danas');
            $table->foreignId('program_id')->nullable()->constrained('programs')->nullOnDelete();
            $table->decimal('jumlah', 15, 2);
            $table->string('metode_bayar');
            $table->string('bank')->nullable();
            $table->string('bukti_transfer')->nullable();
            $table->dateTime('tgl_donasi');
            $table->text('niat')->nullable();
            $table->text('pesan')->nullable();
            $table->text('catatan')->nullable();
            $table->enum('status', ['menunggu_verifikasi', 'terverifikasi', 'ditolak'])->default('menunggu_verifikasi');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donasis');
    }
};
