<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Sesuaikan tanggal bergabung akun-akun amil lama yang sebelumnya default 1 Januari 2026 menjadi 3 November 2026
        DB::table('amils')
            ->whereDate('tgl_bergabung', '2026-01-01')
            ->update(['tgl_bergabung' => '2026-11-03']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('amils')
            ->whereDate('tgl_bergabung', '2026-11-03')
            ->update(['tgl_bergabung' => '2026-01-01']);
    }
};
