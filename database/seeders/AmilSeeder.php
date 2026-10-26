<?php

namespace Database\Seeders;

use App\Models\Amil;
use App\Models\Divisi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AmilSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $amilRole = Role::firstOrCreate(['name' => 'Amil']);
        $divisi = Divisi::first();

        // 1. Akun Amil 1: Ahmad Fauzi
        $user1 = User::firstOrCreate(
            ['email' => 'amil@amilgsc.com'],
            [
                'name' => 'Ahmad Fauzi',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        $user1->syncRoles([$amilRole]);

        Amil::firstOrCreate(
            ['user_id' => $user1->id],
            [
                'nip' => 'AML-202601',
                'nama' => 'Ahmad Fauzi, S.Sos',
                'jabatan' => 'Petugas Fundraising Senior',
                'divisi_id' => $divisi?->id,
                'no_hp' => '081234567891',
                'alamat' => 'Jl. Sedekah No. 12, Bandung',
                'tgl_bergabung' => now()->subMonths(6),
                'target_bulanan' => 25000000,
                'status' => 'aktif',
            ]
        );

        // 2. Akun Amil 2: Siti Nurhaliza
        $user2 = User::firstOrCreate(
            ['email' => 'siti@amilgsc.com'],
            [
                'name' => 'Siti Nurhaliza',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        $user2->syncRoles([$amilRole]);

        Amil::firstOrCreate(
            ['user_id' => $user2->id],
            [
                'nip' => 'AML-202602',
                'nama' => 'Siti Nurhaliza, S.E.',
                'jabatan' => 'Petugas Kemitraan Donatur',
                'divisi_id' => $divisi?->id,
                'no_hp' => '081234567892',
                'alamat' => 'Jl. Kebaikan No. 45, Bandung',
                'tgl_bergabung' => now()->subMonths(3),
                'target_bulanan' => 20000000,
                'status' => 'aktif',
            ]
        );
    }
}
