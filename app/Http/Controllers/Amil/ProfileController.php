<?php

namespace App\Http\Controllers\Amil;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profil dan ganti password amil
     */
    public function index(): View
    {
        $user = Auth::user();
        $amil = $user->amil;

        return view('amil.profile.index', compact('user', 'amil'));
    }

    /**
     * Perbarui informasi profil amil (data diri & kontak)
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $amil = $user->amil;

        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'no_hp' => ['nullable', 'string', 'max:25'],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        // Perbarui data Akun User
        $user->name = $request->nama;
        $user->email = $request->email;
        $user->save();

        // Perbarui data Profil Amil jika terdaftar
        if ($amil) {
            $amil->nama = $request->nama;
            $amil->no_hp = $request->no_hp;
            $amil->alamat = $request->alamat;

            if ($request->hasFile('foto')) {
                // Hapus foto profil lama jika ada
                if ($amil->foto && Storage::disk('public')->exists($amil->foto)) {
                    Storage::disk('public')->delete($amil->foto);
                }

                $path = $request->file('foto')->store('amil-foto', 'public');
                $amil->foto = $path;
            }

            $amil->save();
        }

        return redirect()->route('amil.profile')
            ->with('success', 'Data profil Anda berhasil diperbarui.');
    }

    /**
     * Perbarui kata sandi akun amil
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.current_password' => 'Password saat ini yang Anda masukkan salah.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'password.min' => 'Password baru minimal harus 8 karakter.',
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('amil.profile')
            ->with('success', 'Kata sandi akun Anda berhasil diubah. Mohon gunakan kata sandi baru untuk login berikutnya.');
    }
}
