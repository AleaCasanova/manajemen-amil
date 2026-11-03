<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $oldAdmin = DB::table('users')->where('email', 'admin@gmail.com')->first();
        if ($oldAdmin) {
            DB::table('users')
                ->where('id', $oldAdmin->id)
                ->update([
                    'name' => 'cilacapadmingsc',
                    'email' => 'cilacapadmingsc@gmail.com',
                    'password' => Hash::make('CilacapAdminGsc11326'),
                    'updated_at' => now(),
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $admin = DB::table('users')->where('email', 'cilacapadmingsc@gmail.com')->first();
        if ($admin) {
            DB::table('users')
                ->where('id', $admin->id)
                ->update([
                    'name' => 'Administrator Amil',
                    'email' => 'admin@gmail.com',
                    'password' => Hash::make('password123'),
                    'updated_at' => now(),
                ]);
        }
    }
};
