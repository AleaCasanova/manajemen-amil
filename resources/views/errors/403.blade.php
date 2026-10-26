<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Terbatas - Manajemen Amil GSC</title>
    <link rel="icon" type="image/png" href="{{ asset('logo_gsc.png') }}">
    <link rel="shortcut icon" href="{{ asset('logo_gsc.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-emerald-50 via-slate-50 to-teal-50 min-h-screen flex items-center justify-center p-4 font-['Plus_Jakarta_Sans',sans-serif]">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl shadow-emerald-500/10 border border-slate-100 overflow-hidden text-center p-8 sm:p-10">
        <!-- Logo -->
        <img src="{{ asset('logo_gsc.png') }}" alt="Logo GSC" class="w-24 h-24 object-contain mx-auto mb-4 bg-white rounded-3xl p-2 shadow-lg">
        <!-- Icon -->
        <div class="w-16 h-16 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-inner ring-4 ring-amber-50/50">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>

        <!-- Title & Subtitle -->
        <span class="inline-block px-3 py-1 bg-amber-100 text-amber-800 text-xs font-bold rounded-full uppercase tracking-wider mb-3">
            Akses Khusus Petugas Amil
        </span>
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Akun Ini Bukan Petugas Amil</h1>
        <p class="text-slate-500 text-sm mt-3 leading-relaxed">
            @if(auth()->check())
                Anda saat ini sedang login dengan akun <strong class="text-slate-700">{{ auth()->user()->email }}</strong>. Akun ini tidak terdaftar sebagai petugas amil lapangan.
            @else
                Halaman ini hanya dapat diakses oleh petugas amil yang telah terdaftar di sistem.
            @endif
        </p>

        <!-- Akun Demo Box -->
        <div class="mt-6 p-4 bg-emerald-50/80 rounded-2xl border border-emerald-100/80 text-left text-xs text-emerald-900">
            <div class="font-bold flex items-center gap-1.5 mb-1.5 text-emerald-800">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Gunakan Akun Amil Demo Berikut:
            </div>
            <div class="space-y-1 pl-5 text-slate-600 font-mono text-[11px]">
                <div>Email : <span class="font-semibold text-emerald-700 select-all">amil@amilgsc.com</span></div>
                <div>Password : <span class="font-semibold text-emerald-700 select-all">password</span></div>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-8 space-y-3">
            @if(auth()->check() && auth()->user()->hasRole('admin'))
                <a href="/admin" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-lg shadow-emerald-600/25 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Buka Panel Admin (/admin)
                </a>
            @endif

            @if(auth()->check())
                <form action="{{ route('amil.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Logout & Masuk sebagai Amil
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-lg shadow-emerald-600/25 transition-all">
                    Masuk ke Portal Amil
                </a>
            @endif
        </div>
    </div>
</body>
</html>
