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
        Schema::table('absensis', function (Blueprint $table) {
            $table->string('tipe_masuk', 30)->default('kantor')->after('foto_masuk');
            $table->string('tipe_pulang', 30)->default('kantor')->after('foto_pulang');
            $table->text('keterangan_pulang')->nullable()->after('keterangan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropColumn(['tipe_masuk', 'tipe_pulang', 'keterangan_pulang']);
        });
    }
};
