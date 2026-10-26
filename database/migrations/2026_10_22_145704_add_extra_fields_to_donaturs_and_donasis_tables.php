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
        Schema::table('donaturs', function (Blueprint $table) {
            $table->string('kode_pos', 10)->nullable()->after('provinsi');
        });

        Schema::table('donasis', function (Blueprint $table) {
            $table->string('atas_nama')->nullable()->after('niat');
            $table->string('peruntukan')->default('bebas')->after('atas_nama'); // bebas / program / asnaf
            $table->string('lampiran_foto')->nullable()->after('catatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donaturs', function (Blueprint $table) {
            $table->dropColumn('kode_pos');
        });

        Schema::table('donasis', function (Blueprint $table) {
            $table->dropColumn(['atas_nama', 'peruntukan', 'lampiran_foto']);
        });
    }
};
