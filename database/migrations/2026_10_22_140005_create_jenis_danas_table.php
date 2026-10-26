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
        Schema::create('jenis_danas', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('kelompok', ['zakat', 'infak', 'dskl', 'sedekah', 'wakaf', 'lainnya'])->default('zakat');
            $table->decimal('hak_amil_persen', 5, 2)->default(12.50);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_danas');
    }
};
