<?php

namespace App\Http\Controllers\Amil;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Tampilkan form login amil
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            if (Auth::user()->hasRole('Amil') || Auth::user()->amil) {
                return redirect()->route('amil.dashboard');
            }

            if (Auth::user()->hasRole('Admin')) {
                return redirect('/admin');
            }
        }

        return view('amil.auth.login');
    }

    /**
     * Proses login amil
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => ['required_without:email', 'string'],
            'email' => ['required_without:login', 'string'],
            'password' => ['required', 'string'],
        ]);

        $login = trim($request->input('login', $request->input('email', '')));
        $password = $request->input('password');

        // Cari akun User berdasarkan Email, Username/Nama User, atau Data Amil (Nama, No HP, NIP)
        $user = User::where(function ($query) use ($login) {
            $query->where('email', $login)
                ->orWhere('name', $login)
                ->orWhereHas('amil', function ($q) use ($login) {
                    $q->where('nama', $login)
                        ->orWhere('no_hp', $login)
                        ->orWhere('nip', $login);
                });
        })->first();

        // Jika tidak langsung cocok persis dan bukan format email, cari dengan LIKE case-insensitive
        if (! $user && ! str_contains($login, '@')) {
            $user = User::where(function ($query) use ($login) {
                $query->where('name', 'like', "%{$login}%")
                    ->orWhereHas('amil', function ($q) use ($login) {
                        $q->where('nama', 'like', "%{$login}%")
                            ->orWhere('no_hp', 'like', "%{$login}%");
                    });
            })->first();
        }

        if ($user && Hash::check($password, $user->password)) {
            if (! $user->is_active) {
                return back()->withErrors([
                    'login' => 'Akun amil Anda sedang dinonaktifkan oleh Administrator.',
                ])->onlyInput('login');
            }

            // Pastikan user memiliki profil amil atau role Amil
            if (! $user->amil && ! $user->hasRole('Amil')) {
                return back()->withErrors([
                    'login' => 'Akun ini bukan akun Petugas Amil yang terdaftar.',
                ])->onlyInput('login');
            }

            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            return redirect()->intended(route('amil.dashboard'))
                ->with('success', 'Selamat datang kembali, '.($user->amil?->nama ?? $user->name).'!');
        }

        return back()->withErrors([
            'login' => 'Nama / No. HP / Email atau password yang Anda masukkan tidak sesuai.',
        ])->onlyInput('login');
    }

    /**
     * Proses logout amil
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('amil.login')
            ->with('info', 'Anda telah berhasil keluar.');
    }
}
