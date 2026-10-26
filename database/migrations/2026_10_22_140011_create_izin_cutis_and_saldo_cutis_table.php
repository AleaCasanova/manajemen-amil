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
        Schema::create('izin_cutis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('amil_id')->constrained('amils')->cascadeOnDelete();
            $table->enum('jenis', ['cuti', 'izin', 'sakit'])->default('izin');
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->text('alasan');
            $table->string('lampiran')->nullable();
            $table->enum('status', ['diajukan', 'disetujui', 'ditolak'])->default('diajukan');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });

        Schema::create('saldo_cutis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('amil_id')->constrained('amils')->cascadeOnDelete();
            $table->year('tahun');
            $table->smallInteger('jatah')->default(12);
            $table->smallInteger('terpakai')->default(0);
            $table->timestamps();

            $table->unique(['amil_id', 'tahun']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saldo_cutis');
        Schema::dropIfExists('izin_cutis');
    }
};
