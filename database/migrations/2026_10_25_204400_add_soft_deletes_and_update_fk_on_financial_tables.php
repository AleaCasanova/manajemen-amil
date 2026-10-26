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
        // 1. Tambah SoftDeletes pada tabel donaturs
        Schema::table('donaturs', function (Blueprint $table) {
            $table->softDeletes()->after('updated_at');
        });

        // 2. Ubah foreign key donatur_id di donasis dari CASCADE menjadi RESTRICT + tambah SoftDeletes
        Schema::table('donasis', function (Blueprint $table) {
            $table->dropForeign(['donatur_id']);
            $table->foreign('donatur_id')
                ->references('id')
                ->on('donaturs')
                ->restrictOnDelete();
            $table->softDeletes()->after('updated_at');
        });

        // 3. Tambah SoftDeletes pada tabel mustahiks
        Schema::table('mustahiks', function (Blueprint $table) {
            $table->softDeletes()->after('updated_at');
        });

        // 4. Ubah foreign key mustahik_id di penyalurans dari CASCADE menjadi RESTRICT + tambah SoftDeletes
        Schema::table('penyalurans', function (Blueprint $table) {
            $table->dropForeign(['mustahik_id']);
            $table->foreign('mustahik_id')
                ->references('id')
                ->on('mustahiks')
                ->restrictOnDelete();
            $table->softDeletes()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penyalurans', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropForeign(['mustahik_id']);
            $table->foreign('mustahik_id')
                ->references('id')
                ->on('mustahiks')
                ->cascadeOnDelete();
        });

        Schema::table('mustahiks', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('donasis', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropForeign(['donatur_id']);
            $table->foreign('donatur_id')
                ->references('id')
                ->on('donaturs')
                ->cascadeOnDelete();
        });

        Schema::table('donaturs', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
