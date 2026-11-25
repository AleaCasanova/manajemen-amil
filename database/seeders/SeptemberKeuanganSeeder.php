<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SeptemberKeuanganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sqlPath = database_path('september_keuangan.sql');

        if (File::exists($sqlPath)) {
            DB::unprepared(File::get($sqlPath));
        }
    }
}
