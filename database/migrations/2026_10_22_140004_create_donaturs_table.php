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
        Schema::create('donaturs', function (Blueprint $table) {
            $table->id();
            $table->string('kode_donatur')->unique();
            $table->string('nama');
            $table->enum('jenis', ['individu', 'lembaga'])->default('individu');
            $table->foreignId('kategori_id')->constrained('donatur_kategoris');
            $table->string('no_hp', 25)->nullable();
            $table->string('email')->nullable();
            $table->text('alamat')->nullable();
            $table->string('kelurahan', 100)->nullable();
            $table->string('kecamatan', 100)->nullable();
            $table->string('kota', 100)->nullable();
            $table->string('provinsi', 100)->nullable();
            $table->string('nik', 25)->nullable();
            $table->string('npwp', 30)->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->boolean('is_anonim')->default(false);
            $table->boolean('consent')->default(true);
            $table->foreignId('amil_pic_id')->nullable()->constrained('amils')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donaturs');
    }
};
