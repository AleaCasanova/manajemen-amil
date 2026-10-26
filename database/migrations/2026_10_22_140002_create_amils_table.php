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
        Schema::create('amils', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nip')->unique();
            $table->string('nama');
            $table->string('jabatan')->nullable();
            $table->foreignId('divisi_id')->nullable()->constrained('divisis')->nullOnDelete();
            $table->string('no_hp', 25)->nullable();
            $table->text('alamat')->nullable();
            $table->string('foto')->nullable();
            $table->date('tgl_bergabung')->nullable();
            $table->decimal('target_bulanan', 15, 2)->default(0);
            $table->enum('status', ['aktif', 'nonaktif', 'cuti', 'mutasi'])->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amils');
    }
};
