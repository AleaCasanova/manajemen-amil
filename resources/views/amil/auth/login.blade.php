<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
    <meta name="HandheldFriendly" content="true">
    <meta name="MobileOptimized" content="width">
    <meta name="format-detection" content="telephone=no">
    <title>Masuk Portal Amil - Gerakan Sedekah Cepat</title>
    <link rel="icon" type="image/png" href="{{ asset('logo_gsc.png') }}">
    <link rel="shortcut icon" href="{{ asset('logo_gsc.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- PWA & Mobile Web App Meta -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#065f46">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Amil GSC">
    <link rel="apple-touch-icon" href="{{ asset('logo_gsc.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        html {
            -webkit-text-size-adjust: 100%;
            text-size-adjust: 100%;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
            width: 100%;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-emerald-800 via-emerald-700 to-teal-900 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        <!-- Logo & Header -->
        <div class="text-center mb-6">
            <img src="{{ asset('logo_gsc.png') }}" alt="Logo GSC" class="w-28 h-28 object-contain mx-auto mb-3 bg-white rounded-3xl p-2.5 shadow-2xl inline-block ring-4 ring-white/20">
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Portal Amil GSC</h1>
            <p class="text-sm text-emerald-200 mt-1">Sistem Presensi GPS, Laporan Aktivitas, Donatur & Donasi ZISWAF</p>
        </div>

        <!-- Role Selector Pill -->
        <div class="bg-emerald-900/60 p-1.5 rounded-2xl mb-4 backdrop-blur-md border border-emerald-500/30 flex items-center gap-1">
            <span class="flex-1 py-2 px-3 text-center rounded-xl bg-white text-emerald-800 font-bold text-xs shadow flex items-center justify-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                Portal Amil (Aktif)
            </span>
            <a href="/admin" class="flex-1 py-2 px-3 text-center rounded-xl text-emerald-200 hover:text-white hover:bg-emerald-800/60 font-semibold text-xs transition flex items-center justify-center gap-1.5">
                Panel Admin (Filament)
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
            </a>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-3xl shadow-2xl p-6 sm:p-8 border border-white/20">
            <div class="mb-5">
                <h2 class="text-lg font-bold text-slate-800">Masuk Akun Amil</h2>
                <p class="text-xs text-slate-500 mt-0.5">Masukkan email & password akun petugas amil Anda di bawah ini.</p>
            </div>

            @if(session('info'))
                <div class="mb-4 p-3 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 text-xs font-semibold">
                    {{ session('info') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold">
                    {{ $errors->first() }}
                </div>
            @endif

            <form id="loginForm" action="{{ route('amil.login.post') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Email Petugas Amil</label>
                    <div class="relative">
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                            placeholder="nama@amilgsc.com"
                            class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm font-medium transition placeholder:text-slate-400">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required
                            placeholder="••••••••"
                            class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-sm font-medium transition placeholder:text-slate-400">
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 cursor-pointer text-slate-600 font-medium">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                        <span>Ingat saya</span>
                    </label>
                    <span class="text-slate-400">Lupa password? Hubungi Admin</span>
                </div>

                <button type="submit" class="w-full py-3.5 px-4 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold rounded-xl shadow-lg shadow-emerald-600/30 transition text-sm flex items-center justify-center gap-2 cursor-pointer">
                    <span>Masuk ke Portal Amil</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </form>

            <div class="mt-6 pt-5 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-slate-500">
                <span>Ingin ke Panel Pengurus/Admin?</span>
                <a href="/admin" class="text-emerald-700 font-bold hover:underline flex items-center gap-1">
                    <span>Masuk Panel Admin</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((reg) => console.log('PWA ServiceWorker registered:', reg.scope))
                    .catch((err) => console.error('PWA ServiceWorker registration failed:', err));
            });
        }
    </script>
</body>
</html>
