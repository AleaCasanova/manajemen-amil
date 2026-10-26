<?php

namespace App\Filament\Resources\Amils\Pages;

use App\Filament\Resources\Amils\AmilResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CreateAmil extends CreateRecord
{
    protected static string $resource = AmilResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $roleAmil = Role::firstOrCreate(['name' => 'Amil']);

        // Buat akun User otomatis untuk Amil
        $user = User::create([
            'name' => $data['nama'],
            'email' => $data['email'] ?? strtolower(str_replace(' ', '', $data['nama'])).'@amilgsc.com',
            'password' => Hash::make(! empty($data['password']) ? $data['password'] : 'password123'),
            'role_id' => $roleAmil->id,
            'is_active' => ($data['status'] === 'aktif'),
        ]);

        $user->syncRoles([$roleAmil]);

        $data['user_id'] = $user->id;

        // Bersihkan data yang bukan milik tabel amils
        unset($data['email'], $data['password']);

        return $data;
    }
}
