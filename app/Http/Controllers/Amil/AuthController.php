<?php

namespace App\Http\Controllers\Amil;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if (! $user->is_active) {
                Auth::logout();

                return back()->withErrors([
                    'email' => 'Akun amil Anda sedang dinonaktifkan oleh Administrator.',
                ])->onlyInput('email');
            }

            // Pastikan user memiliki profil amil
            if (! $user->amil && ! $user->hasRole('Amil')) {
                Auth::logout();

                return back()->withErrors([
                    'email' => 'Akun ini bukan akun Amil yang terdaftar.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            return redirect()->intended(route('amil.dashboard'))
                ->with('success', 'Selamat datang kembali, '.($user->amil?->nama ?? $user->name).'!');
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan tidak sesuai.',
        ])->onlyInput('email');
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
