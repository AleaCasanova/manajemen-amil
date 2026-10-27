@extends('layouts.amil')

@section('title', 'Dashboard Amil')

@section('content')
<div class="space-y-6">

    <!-- Greeting & Quick Profile Header -->
    <div class="bg-gradient-to-r from-emerald-700 to-teal-800 rounded-2xl p-4 sm:p-6 text-white shadow-lg relative overflow-hidden">
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
            <div class="min-w-0">
                <div class="flex items-center gap-2 mb-1.5 sm:mb-2 flex-wrap">
                    <span class="inline-block px-2.5 py-0.5 rounded-lg bg-emerald-600/60 backdrop-blur-sm text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-emerald-100">
                        {{ $amil->divisi?->nama ?? 'Divisi Umum' }}
                    </span>
                    <span class="inline-block px-2.5 py-0.5 rounded-lg text-[10px] sm:text-[11px] font-bold uppercase tracking-wider {{ $amil->isRelawan() ? 'bg-amber-400 text-amber-950 font-black' : ($amil->isMagang() ? 'bg-cyan-400 text-cyan-950 font-black' : 'bg-emerald-900/80 text-emerald-100') }}">
                        {{ $amil->jabatan_label }}
                    </span>
                </div>
                <h1 class="text-lg sm:text-2xl font-extrabold tracking-tight truncate">Assalamu'alaikum, {{ $amil->nama }}</h1>
                <p class="text-xs sm:text-sm text-emerald-100/90 mt-0.5 sm:mt-1 truncate">
                    NIP: {{ $amil->nip }} • {{ $amil->jabatan_label }}
                </p>
            </div>

            <!-- Quick Action Button -->
            <div class="flex items-center gap-2 self-start sm:self-auto shrink-0">
                @if($amil->isMagang())
                    <a href="{{ route('amil.aktivitas.create') }}" style="background-color: #ffffff !important; color: #0f766e !important;" class="py-2 px-3.5 sm:py-2.5 sm:px-4 bg-white font-bold text-xs sm:text-sm rounded-xl shadow-md hover:bg-teal-50 active:scale-95 transition flex items-center gap-1.5 shrink-0">
                        <svg class="w-4 h-4 shrink-0" style="color: #0d9488 !important;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span style="color: #0f766e !important;">+ Catat Aktivitas</span>
                    </a>
                @elseif($amil->isDriver())
                    <a href="{{ route('amil.aktivitas.create') }}" style="background-color: #ffffff !important; color: #0f766e !important;" class="py-2 px-3.5 sm:py-2.5 sm:px-4 bg-white font-bold text-xs sm:text-sm rounded-xl shadow-md hover:bg-teal-50 active:scale-95 transition flex items-center gap-1.5 shrink-0">
                        <svg class="w-4 h-4 shrink-0" style="color: #0d9488 !important;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <span style="color: #0f766e !important;">+ Catat Tugas</span>
                    </a>
                @else
                    <a href="{{ route('amil.donasi.create') }}" style="background-color: #ffffff !important; color: #065f46 !important;" class="py-2 px-3.5 sm:py-2.5 sm:px-4 bg-white font-bold text-xs sm:text-sm rounded-xl shadow-md hover:bg-emerald-50 active:scale-95 transition flex items-center gap-1.5 shrink-0">
                        <svg class="w-4 h-4 shrink-0" style="color: #059669 !important;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        <span style="color: #065f46 !important;">+ Input Donasi</span>
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Attendance Status Card (Presensi Hari Ini) -->
    @if($amil->needsAttendance())
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3.5 sm:gap-4">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl {{ $absensiHariIni ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }} flex items-center justify-center shrink-0">
                    @if($absensiHariIni)
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                    @else
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    @endif
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <h2 class="text-xs sm:text-sm font-bold text-slate-800">Presensi ({{ now()->translatedFormat('d M Y') }})</h2>
                        @if($absensiHariIni)
                            <span class="px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-bold uppercase bg-emerald-100 text-emerald-800">
                                {{ $absensiHariIni->status }}
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-bold uppercase bg-amber-100 text-amber-800">
                                Belum Absen
                            </span>
                        @endif
                    </div>
                    <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5 leading-snug">
                        @if($absensiHariIni)
                            Masuk: <strong class="text-slate-700">{{ $absensiHariIni->jam_masuk ? substr($absensiHariIni->jam_masuk, 0, 5) . ' WIB' : '-' }}</strong> | Pulang: <strong class="text-slate-700">{{ $absensiHariIni->jam_pulang ? substr($absensiHariIni->jam_pulang, 0, 5) . ' WIB' : 'Belum Pulang' }}</strong>
                        @else
                            Anda belum melakukan absen masuk hari ini.
                        @endif
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto shrink-0">
                @if(!$absensiHariIni || !$absensiHariIni->jam_pulang)
                    <a href="{{ route('amil.absensi.index') }}" class="w-full sm:w-auto px-4 py-2 sm:py-2.5 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white text-xs font-extrabold rounded-xl shadow-md transition flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ $absensiHariIni ? 'Absen Pulang Sekarang' : 'Presensi Kehadiran' }}</span>
                    </a>
                @endif
            </div>
        </div>
    @else
        <!-- Banner Ramah Relawan (Bebas Absensi & Bebas Laporan) -->
        <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200/80 rounded-2xl p-4 sm:p-5 shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
            </div>
            <div class="min-w-0">
                <h2 class="text-xs sm:text-sm font-bold text-amber-900">Selamat Beraktivitas, Relawan Kebaikan!</h2>
                <p class="text-[11px] sm:text-xs text-amber-800/80 mt-0.5 leading-snug">
                    Sebagai Relawan, Anda dapat langsung mencatat donasi & mengelola donatur binaan tanpa perlu mengisi absensi atau laporan kegiatan harian.
                </p>
            </div>
        </div>
    @endif

    <!-- Monthly Target & Metric Cards -->
    @if($amil->canAccessDonasi())
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5 sm:gap-4">
            <!-- Target Bulanan Card -->
            <div class="md:col-span-2 bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">Capaian Donasi Saya Bulan Ini</span>
                        <span class="text-xs font-extrabold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-lg">
                            {{ $progresPersen }}%
                        </span>
                    </div>
                    <div class="flex items-baseline gap-1.5 sm:gap-2 mt-1 flex-wrap">
                        <h3 class="text-xl sm:text-2xl font-black text-slate-900">Rp {{ number_format($donasiBulanIni, 0, ',', '.') }}</h3>
                        <span class="text-[11px] sm:text-xs text-slate-400 font-medium">/ Target Rp {{ number_format($targetBulanan, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mt-3 sm:mt-4">
                    <div class="w-full bg-slate-100 rounded-full h-2.5 sm:h-3 overflow-hidden">
                        <div class="bg-gradient-to-r from-emerald-500 to-teal-500 h-2.5 sm:h-3 rounded-full transition-all duration-500" style="width: {{ $progresPersen }}%"></div>
                    </div>
                    <div class="flex justify-between items-center text-[10px] sm:text-[11px] text-slate-500 mt-1.5 sm:mt-2 font-medium">
                        <span>Periode {{ now()->translatedFormat('F Y') }}</span>
                        <span>Sisa Target: Rp {{ number_format(max(0, $targetBulanan - $donasiBulanIni), 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Donatur & Pending Cards -->
            <div class="grid grid-cols-2 md:grid-cols-1 gap-2.5 sm:gap-3">
                <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/80 shadow-sm flex items-center justify-between gap-2">
                    <div class="min-w-0">
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-500 uppercase block truncate">Donatur Binaan</span>
                        <h4 class="text-base sm:text-xl font-black text-slate-900 mt-0.5 truncate">{{ $totalDonaturSaya }} Donatur</h4>
                        <a href="{{ route('amil.donatur.index') }}" class="text-[10px] sm:text-[11px] font-bold text-emerald-700 hover:underline inline-block mt-0.5">Lihat &rarr;</a>
                    </div>
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/80 shadow-sm flex items-center justify-between gap-2">
                    <div class="min-w-0">
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-500 uppercase block truncate">Menunggu Verifikasi</span>
                        <h4 class="text-sm sm:text-lg font-black text-amber-600 mt-0.5 truncate">Rp {{ number_format($donasiMenungguVerifikasi, 0, ',', '.') }}</h4>
                        <span class="text-[9px] sm:text-[10px] text-slate-400 block truncate">Oleh admin kasir</span>
                    </div>
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Metrik Khusus Magang: Fokus Kehadiran & Laporan Aktivitas -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 sm:gap-4">
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-sm flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Presensi Bulan Ini</span>
                    <h3 class="text-xl sm:text-2xl font-black text-slate-900 mt-1">{{ $totalPresensiBulanIni }} Hari</h3>
                    <p class="text-[11px] text-emerald-600 font-semibold mt-0.5">Status: Aktif Magang</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-sm flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Logbook Aktivitas</span>
                    <h3 class="text-xl sm:text-2xl font-black text-slate-900 mt-1">{{ $totalAktivitasBulanIni }} Laporan</h3>
                    <a href="{{ route('amil.aktivitas.create') }}" class="text-[11px] text-teal-600 font-bold hover:underline inline-block mt-0.5">+ Buat Laporan Baru</a>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-sm flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Agenda Terjadwal</span>
                    <h3 class="text-xl sm:text-2xl font-black text-slate-900 mt-1">{{ $agendaHariIni->count() }} Kegiatan</h3>
                    <a href="{{ route('amil.agenda.index') }}" class="text-[11px] text-indigo-600 font-bold hover:underline inline-block mt-0.5">Lihat Agenda &rarr;</a>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>
    @endif

    <!-- Quick Shortcuts Grid Sesuai Role -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3">
        @if($amil->isRelawan())
            {{-- Shortcuts Relawan: 4 Menu Inti Sederhana --}}
            <a href="{{ route('amil.donasi.create') }}" class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/80 shadow-sm hover:border-emerald-500 hover:shadow-md transition group text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center mb-1.5 sm:mb-2 group-hover:scale-105 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <span class="text-xs font-bold text-slate-800">Catat Donasi</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Kwitansi instan</span>
            </a>

            <a href="{{ route('amil.donasi.index') }}" class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/80 shadow-sm hover:border-emerald-500 hover:shadow-md transition group text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center mb-1.5 sm:mb-2 group-hover:scale-105 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <span class="text-xs font-bold text-slate-800">Riwayat Donasi</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Daftar setoran</span>
            </a>

            <a href="{{ route('amil.donatur.index') }}" class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/80 shadow-sm hover:border-emerald-500 hover:shadow-md transition group text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center mb-1.5 sm:mb-2 group-hover:scale-105 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <span class="text-xs font-bold text-slate-800">Donatur Binaan</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Data binaan Anda</span>
            </a>

            <a href="{{ route('amil.pengumuman.index') }}" class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/80 shadow-sm hover:border-emerald-500 hover:shadow-md transition group text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center mb-1.5 sm:mb-2 group-hover:scale-105 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                    </svg>
                </div>
                <span class="text-xs font-bold text-slate-800">Pengumuman</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Informasi lembaga</span>
            </a>
        @elseif($amil->isMagang())
            {{-- Shortcuts Magang: Presensi, Logbook, Agenda, Cuti --}}
            <a href="{{ route('amil.absensi.index') }}" class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/80 shadow-sm hover:border-teal-500 hover:shadow-md transition group text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center mb-1.5 sm:mb-2 group-hover:scale-105 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-xs font-bold text-slate-800">Presensi Kehadiran</span>
                <span class="text-[10px] text-slate-400 mt-0.5">GPS & Foto Selfie</span>
            </a>

            <a href="{{ route('amil.aktivitas.create') }}" class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/80 shadow-sm hover:border-teal-500 hover:shadow-md transition group text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center mb-1.5 sm:mb-2 group-hover:scale-105 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <span class="text-xs font-bold text-slate-800">Catat Logbook</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Laporan harian</span>
            </a>

            <a href="{{ route('amil.aktivitas.index') }}" class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/80 shadow-sm hover:border-teal-500 hover:shadow-md transition group text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center mb-1.5 sm:mb-2 group-hover:scale-105 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <span class="text-xs font-bold text-slate-800">Riwayat Aktivitas</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Review pembimbing</span>
            </a>

            <a href="{{ route('amil.izin-cuti.index') }}" class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/80 shadow-sm hover:border-teal-500 hover:shadow-md transition group text-center flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center mb-1.5 sm:mb-2 group-hover:scale-105 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <span class="text-xs font-bold text-slate-800">Izin & Cuti</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Form izin sakit/cuti</span>
            </a>
        @else
            {{-- Shortcuts Reguler (Staff, Driver, Ketua) --}}
            <a href="{{ route('amil.donasi.create') }}" class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/80 shadow-sm hover:border-emerald-500 hover:shadow-md transition group text-center flex flex-col items-center">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center mb-1.5 sm:mb-2 group-hover:scale-105 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-xs font-bold text-slate-800">Input Donasi</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Kwitansi otomatis</span>
            </a>

            <a href="{{ route('amil.donatur.index') }}" class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/80 shadow-sm hover:border-emerald-500 hover:shadow-md transition group text-center flex flex-col items-center">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center mb-1.5 sm:mb-2 group-hover:scale-105 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <span class="text-xs font-bold text-slate-800">Donatur Saya</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Bina & follow-up</span>
            </a>

            <a href="{{ route('amil.donasi.index') }}" class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/80 shadow-sm hover:border-emerald-500 hover:shadow-md transition group text-center flex flex-col items-center">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center mb-1.5 sm:mb-2 group-hover:scale-105 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <span class="text-xs font-bold text-slate-800">Riwayat Setoran</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Unduh kwitansi</span>
            </a>

            <a href="{{ route('amil.aktivitas.index') }}" class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/80 shadow-sm hover:border-emerald-500 hover:shadow-md transition group text-center flex flex-col items-center">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center mb-1.5 sm:mb-2 group-hover:scale-105 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <span class="text-xs font-bold text-slate-800">Laporan Aktivitas</span>
                <span class="text-[10px] text-slate-400 mt-0.5">Tugas lapangan</span>
            </a>
        @endif
    </div>

    <!-- 2 Column Layout: Recent Donations & Announcements/Agendas -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Main 2-Col Content: Donasi Terakhir ATAU Logbook Terakhir (Khusus Magang) -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
            @if($amil->canAccessDonasi())
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-800">Transaksi Donasi Terakhir</h3>
                    <a href="{{ route('amil.donasi.index') }}" class="text-xs font-bold text-emerald-700 hover:underline">Lihat Semua &rarr;</a>
                </div>

                @if($donasiTerakhir->count() > 0)
                    <div class="divide-y divide-slate-100">
                        @foreach($donasiTerakhir as $item)
                            <div class="py-3 flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-xs text-slate-900 truncate">
                                            {{ $item->donatur->is_anonim ? 'Hamba Allah (Anonim)' : $item->donatur->nama }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase {{ $item->status === 'terverifikasi' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                            {{ str_replace('_', ' ', $item->status) }}
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5 truncate">
                                        {{ $item->no_kwitansi }} • {{ $item->jenisDana->nama }}
                                        @if($item->program)
                                            • <span class="text-emerald-700 font-medium">{{ $item->program->nama }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="text-right flex-shrink-0">
                                    <span class="font-extrabold text-sm text-slate-900 block">
                                        Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                                    </span>
                                    @php
                                        $tglItem = \Carbon\Carbon::parse($item->tgl_donasi);
                                        $jamItem = ($tglItem->format('H:i') === '00:00' && $item->created_at)
                                            ? \Carbon\Carbon::parse($item->created_at)->format('H:i')
                                            : $tglItem->format('H:i');
                                    @endphp
                                    <span class="text-[10px] text-slate-400 block font-mono">
                                        {{ $tglItem->translatedFormat('d M') }}, {{ $jamItem }} WIB
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-xs text-slate-500">Belum ada donasi yang diinput bulan ini.</p>
                        <a href="{{ route('amil.donasi.create') }}" class="inline-block mt-3 px-4 py-2 bg-emerald-600 text-white font-bold text-xs rounded-xl shadow-sm hover:bg-emerald-700 transition">
                            + Input Donasi Pertama
                        </a>
                    </div>
                @endif
            @else
                {{-- Khusus Magang: Logbook & Laporan Aktivitas Terakhir --}}
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-800">Laporan Aktivitas Harian Terakhir</h3>
                    <a href="{{ route('amil.aktivitas.index') }}" class="text-xs font-bold text-teal-700 hover:underline">Lihat Semua &rarr;</a>
                </div>

                @if($aktivitasTerakhir && $aktivitasTerakhir->count() > 0)
                    <div class="divide-y divide-slate-100">
                        @foreach($aktivitasTerakhir as $akt)
                            <div class="py-3 flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-bold text-xs text-slate-900 truncate">
                                            {{ $akt->judul }}
                                        </h4>
                                        <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase {{ $akt->status === 'disetujui' ? 'bg-emerald-100 text-emerald-800' : ($akt->status === 'ditolak' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                                            {{ $akt->status ?? 'Menunggu' }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5 line-clamp-1">
                                        {{ strip_tags($akt->deskripsi) }}
                                    </p>
                                    @if($akt->lokasi)
                                        <span class="text-[10px] text-slate-400 block mt-0.5">
                                            Lokasi: {{ $akt->lokasi }}
                                        </span>
                                    @endif
                                </div>

                                <div class="text-right flex-shrink-0">
                                    <span class="text-[10px] text-slate-400 block font-mono">
                                        {{ \Carbon\Carbon::parse($akt->tanggal)->translatedFormat('d M Y') }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-xs text-slate-500">Belum ada laporan aktivitas harian yang dibuat.</p>
                        <a href="{{ route('amil.aktivitas.create') }}" class="inline-block mt-3 px-4 py-2 bg-teal-600 text-white font-bold text-xs rounded-xl shadow-sm hover:bg-teal-700 transition">
                            + Catat Aktivitas Hari Ini
                        </a>
                    </div>
                @endif
            @endif
        </div>

        <!-- Sidebar: Pengumuman & Agenda -->
        <div class="space-y-4 sm:space-y-6">
            <!-- Pengumuman Card -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-sm">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                        </svg>
                    </div>
                    <h3 class="text-xs sm:text-sm font-bold text-slate-800">Pengumuman Lembaga</h3>
                </div>

                @if($pengumumanTerbaru->count() > 0)
                    <div class="space-y-2.5 sm:space-y-3">
                        @foreach($pengumumanTerbaru as $pengumuman)
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                                <h4 class="text-xs font-bold text-slate-900">{{ $pengumuman->judul }}</h4>
                                <p class="text-[11px] text-slate-600 line-clamp-2 mt-1">{{ strip_tags($pengumuman->isi) }}</p>
                                <span class="text-[9px] text-slate-400 block mt-1">
                                    {{ \Carbon\Carbon::parse($pengumuman->published_at ?? $pengumuman->created_at)->translatedFormat('d M Y') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-slate-400 italic">Tidak ada pengumuman baru.</p>
                @endif
            </div>

            <!-- Agenda Card -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-sm">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xs sm:text-sm font-bold text-slate-800">Agenda Penugasan Saya</h3>
                </div>

                @if($agendaHariIni->count() > 0)
                    <div class="space-y-2.5">
                        @foreach($agendaHariIni as $agenda)
                            <div class="p-2.5 rounded-xl bg-emerald-50/50 border border-emerald-100">
                                <h4 class="text-xs font-bold text-emerald-900">{{ $agenda->judul }}</h4>
                                <div class="text-[10px] text-slate-600 mt-0.5">
                                    Waktu: {{ \Carbon\Carbon::parse($agenda->mulai)->format('d M, H:i') }} WIB
                                </div>
                                @if($agenda->lokasi)
                                    <div class="text-[10px] text-slate-500">
                                        Lokasi: {{ $agenda->lokasi }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-slate-400 italic">Belum ada agenda penugasan terjadwal.</p>
                @endif
            </div>
        </div>

    </div>

</div>
@endsection
