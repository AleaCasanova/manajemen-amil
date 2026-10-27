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
        DB::table('settings')
            ->where('key', 'app_institution')
            ->update(['value' => 'Yayasan Gerak Sedekah Cilacap (GSC)']);

        DB::table('settings')
            ->where('key', 'app_address')
            ->where('value', 'LIKE', '%Gerakan Sedekah Cepat%')
            ->update(['value' => 'Jl. Gerak Sedekah Cilacap No. 01, Cilacap, Jawa Tengah']);

        DB::table('lokasi_kantors')
            ->where('alamat', 'LIKE', '%Gerakan Sedekah Cepat%')
            ->update(['alamat' => 'Jl. Gerak Sedekah Cilacap No. 01, Cilacap, Jawa Tengah']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
