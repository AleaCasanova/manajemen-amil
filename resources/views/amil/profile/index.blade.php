@extends('layouts.amil')

@section('title', 'Profil & Pengaturan Akun')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header Halaman -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </span>
                <span>Profil & Akun Amil</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Kelola informasi biodata diri, nomor kontak, serta kata sandi akun amil Anda.
            </p>
        </div>
        <a href="{{ route('amil.dashboard') }}" class="self-start sm:self-auto inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Kembali ke Dashboard</span>
        </a>
    </div>

    <!-- Ringkasan Profil Card (Avatar, NIP, Divisi) -->
    <div class="bg-gradient-to-br from-emerald-800 to-teal-900 rounded-2xl p-5 sm:p-6 text-white shadow-lg relative overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-6 relative z-10">
            <!-- Foto Avatar -->
            <div class="relative shrink-0">
                @if($amil && $amil->foto)
                    <img src="{{ asset('storage/' . $amil->foto) }}" alt="{{ $amil->nama }}" class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl object-cover border-2 border-emerald-300/40 shadow-md bg-white">
                @else
                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-emerald-950/80 text-emerald-200 font-extrabold text-2xl sm:text-3xl flex items-center justify-center border-2 border-emerald-300/30 uppercase shadow-inner">
                        {{ substr($amil?->nama ?? $user->name, 0, 2) }}
                    </div>
                @endif
                <span class="absolute -bottom-1.5 -right-1.5 px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase bg-emerald-400 text-emerald-950 shadow">
                    {{ $amil?->status ?? 'Aktif' }}
                </span>
            </div>

            <!-- Detail Identitas -->
            <div class="min-w-0 flex-1 space-y-1">
                <div class="flex items-center gap-2 flex-wrap">
                    <h2 class="text-lg sm:text-xl font-extrabold truncate">{{ $amil?->nama ?? $user->name }}</h2>
                    <span class="px-2.5 py-0.5 rounded-lg bg-emerald-700/80 text-emerald-100 font-mono text-[11px] font-bold">
                        NIP: {{ $amil?->nip ?? '-' }}
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-emerald-100/90 font-medium">
                    {{ $amil?->jabatan_label ?? 'Petugas Amil' }} &bull; {{ $amil?->divisi?->nama ?? 'Divisi Umum' }}
                </p>
                <div class="pt-2 flex flex-wrap items-center gap-3 text-[11px] text-emerald-200/90">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span>{{ $user->email }}</span>
                    </span>
                    @if($amil?->no_hp)
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <span>{{ $amil->no_hp }}</span>
                        </span>
                    @endif
                    @if($amil?->tgl_bergabung)
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span>Bergabung sejak {{ $amil->tgl_bergabung->translatedFormat('d M Y') }}</span>
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Grid 2 Kolom: Edit Biodata & Ganti Password -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Formulir 1: Edit Data Diri & Kontak Amil -->
        <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </span>
                        <h3 class="font-extrabold text-slate-800 text-sm sm:text-base">Ubah Data Diri & Kontak</h3>
                    </div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Data Profil</span>
                </div>

                <form id="formProfile" action="{{ route('amil.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <!-- Nama Lengkap -->
                    <div>
                        <label for="nama" class="block text-xs font-bold text-slate-700 mb-1">
                            Nama Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="nama" name="nama" value="{{ old('nama', $amil?->nama ?? $user->name) }}" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition bg-slate-50/50 focus:bg-white"
                            placeholder="Masukkan nama lengkap Anda">
                        @error('nama')
                            <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Akun -->
                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 mb-1">
                            Alamat Email (Login) <span class="text-rose-500">*</span>
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition bg-slate-50/50 focus:bg-white"
                            placeholder="nama@email.com">
                        @error('email')
                            <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nomor WhatsApp / HP -->
                    <div>
                        <label for="no_hp" class="block text-xs font-bold text-slate-700 mb-1">
                            Nomor WhatsApp / HP
                        </label>
                        <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp', $amil?->no_hp) }}"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition bg-slate-50/50 focus:bg-white font-mono"
                            placeholder="Contoh: 081234567890">
                        @error('no_hp')
                            <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Alamat Domisili -->
                    <div>
                        <label for="alamat" class="block text-xs font-bold text-slate-700 mb-1">
                            Alamat Domisili / Tempat Tinggal
                        </label>
                        <textarea id="alamat" name="alamat" rows="3"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition bg-slate-50/50 focus:bg-white resize-none"
                            placeholder="Masukkan alamat lengkap domisili saat ini...">{{ old('alamat', $amil?->alamat) }}</textarea>
                        @error('alamat')
                            <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Upload Foto Profil -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Foto Profil Baru (Opsional)
                        </label>
                        <div class="mt-1 flex items-center gap-3">
                            <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/jpg,image/webp"
                                class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 transition cursor-pointer">
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1">Format: JPG, PNG, atau WEBP. Maksimal ukuran 2MB.</p>
                        @error('foto')
                            <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tombol Simpan Biodata -->
                    <div class="pt-3">
                        <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-extrabold text-xs sm:text-sm shadow-md shadow-emerald-600/30 hover:shadow transition flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Simpan Perubahan Data Diri</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Formulir 2: Rubah Kata Sandi Akun -->
        <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </span>
                        <h3 class="font-extrabold text-slate-800 text-sm sm:text-base">Rubah Kata Sandi</h3>
                    </div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Keamanan Akun</span>
                </div>

                <form id="formPassword" action="{{ route('amil.profile.password') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <!-- Password Saat Ini -->
                    <div>
                        <label for="current_password" class="block text-xs font-bold text-slate-700 mb-1">
                            Kata Sandi Saat Ini <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" id="current_password" name="current_password" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition bg-slate-50/50 focus:bg-white"
                            placeholder="Masukkan password saat ini">
                        @error('current_password')
                            <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Baru -->
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 mb-1">
                            Kata Sandi Baru <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" id="password" name="password" required minlength="8"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition bg-slate-50/50 focus:bg-white"
                            placeholder="Minimal 8 karakter unik">
                        <p class="text-[10px] text-slate-400 mt-1">Gunakan kombinasi huruf, angka, atau simbol agar lebih aman.</p>
                        @error('password')
                            <p class="text-[11px] text-rose-500 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Konfirmasi Password Baru -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-700 mb-1">
                            Ulangi Kata Sandi Baru <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition bg-slate-50/50 focus:bg-white"
                            placeholder="Ketik ulang password baru">
                    </div>

                    <!-- Tombol Simpan Password -->
                    <div class="pt-3">
                        <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-slate-800 hover:bg-slate-900 active:bg-slate-950 text-white font-extrabold text-xs sm:text-sm shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                            <span>Perbarui Kata Sandi</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Petunjuk Keamanan -->
            <div class="mt-5 p-3 rounded-xl bg-slate-50 border border-slate-100 text-[11px] text-slate-500 space-y-1">
                <span class="font-bold text-slate-700 block">Informasi Keamanan:</span>
                <p>Setelah kata sandi diganti, pastikan Anda mengingatnya dengan baik untuk login ke Portal Amil selanjutnya.</p>
            </div>
        </div>

    </div>

    <!-- Informasi Data Kepegawaian (Read-Only dari SK / Admin) -->
    <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-sm">
        <div class="flex items-center gap-2 pb-3 mb-4 border-b border-slate-100">
            <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </span>
            <div>
                <h3 class="font-extrabold text-slate-800 text-sm sm:text-base">Informasi Status Kepegawaian Resmi</h3>
                <p class="text-[11px] text-slate-500">Data berikut ditetapkan secara resmi oleh Administrator Lembaga.</p>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">NIP Pegawai</span>
                <span class="font-mono font-extrabold text-slate-800 text-sm mt-0.5 block">{{ $amil?->nip ?? '-' }}</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Divisi</span>
                <span class="font-extrabold text-slate-800 text-sm mt-0.5 block truncate">{{ $amil?->divisi?->nama ?? 'Divisi Umum' }}</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Jabatan</span>
                <span class="font-extrabold text-slate-800 text-sm mt-0.5 block truncate">{{ $amil?->jabatan_label ?? 'Petugas Amil' }}</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Target Donasi</span>
                <span class="font-extrabold text-emerald-700 text-sm mt-0.5 block">Rp {{ number_format($amil?->target_bulanan ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="mt-4 flex items-start gap-2.5 p-3 rounded-xl bg-blue-50/70 border border-blue-100 text-blue-900 text-[11px]">
            <svg class="w-4 h-4 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p>
                Jika terdapat kesalahan pada NIP, divisi, jabatan, atau target bulanan, silakan melakukan konfirmasi kepada Administrator Lembaga / HRD untuk penyesuaian SK penugasan.
            </p>
        </div>
    </div>

</div>
@endsection
