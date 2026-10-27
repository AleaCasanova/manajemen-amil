<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
    <meta name="HandheldFriendly" content="true">
    <meta name="MobileOptimized" content="width">
    <meta name="format-detection" content="telephone=no">
    <title>@yield('title', 'Portal Amil') - Gerak Sedekah Cilacap</title>
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
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col antialiased pb-20 lg:pb-6">
    @php
        $currentAmil = Auth::user()->amil;
        $isRelawan = $currentAmil?->isRelawan() ?? false;
        $isMagang = $currentAmil?->isMagang() ?? false;
        $isDriver = $currentAmil?->isDriver() ?? false;
        $isKetua = $currentAmil?->isKetua() ?? false;
        $isStaff = $currentAmil?->isStaff() ?? false;
        $canAccessDonasi = $currentAmil ? $currentAmil->canAccessDonasi() : true;
        $needsAttendance = $currentAmil ? $currentAmil->needsAttendance() : true;
        $canAccessIzinCuti = $currentAmil ? $currentAmil->canAccessIzinCuti() : true;
        $canAccessPenyaluran = $currentAmil ? $currentAmil->canAccessPenyaluran() : true;
        $jabatanLabel = $currentAmil?->jabatan_label ?? 'Petugas Amil';
    @endphp

    <!-- Top Navigation Bar (Header Modern) -->
    <header class="bg-emerald-800 text-white shadow-md sticky top-0 z-40 border-b border-emerald-700/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 gap-4">

                <!-- 1. Brand / Logo -->
                <div class="flex items-center gap-3 shrink-0">
                    <a href="{{ route('amil.dashboard') }}" class="flex items-center gap-3 group">
                        <img src="{{ asset('logo_gsc.png') }}" alt="Logo GSC" class="w-12 h-12 object-contain rounded-xl bg-white p-1 shadow-md group-hover:scale-105 transition">
                        <div class="leading-tight">
                            <span class="font-extrabold text-sm sm:text-base tracking-tight block text-white">Portal Amil</span>
                            <span class="text-[10px] text-emerald-200 block font-medium">Gerak Sedekah Cilacap</span>
                        </div>
                    </a>
                </div>

                <!-- 2. Desktop Navigation (Terorganisir & Bersih Khusus Per Role) -->
                <nav class="hidden lg:flex items-center gap-1 xl:gap-2">
                    <!-- Dashboard (Semua Role) -->
                    <a href="{{ route('amil.dashboard') }}" class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap {{ request()->routeIs('amil.dashboard') ? 'bg-emerald-900 text-white shadow-inner' : 'text-emerald-100 hover:bg-emerald-700 hover:text-white' }}">
                        <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    @if($isRelawan)
                        {{-- NAV KHUSUS RELAWAN (Mudah & Simpel) --}}
                        <div class="relative group">
                            <button type="button" class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap {{ request()->routeIs('amil.donasi.*', 'amil.donatur.*') ? 'bg-emerald-900 text-white shadow-inner' : 'text-emerald-100 hover:bg-emerald-700 hover:text-white' }}">
                                <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Donasi & Donatur</span>
                                <svg class="w-3.5 h-3.5 opacity-70 group-hover:rotate-180 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div class="absolute left-0 top-full pt-1.5 w-56 hidden group-hover:block z-50 animate-in fade-in slide-in-from-top-1 duration-150">
                                <div class="bg-white rounded-2xl shadow-xl border border-slate-100 py-1.5 text-slate-700 text-xs overflow-hidden">
                                    <a href="{{ route('amil.donasi.create') }}" class="flex items-center gap-2.5 px-3.5 py-2 hover:bg-emerald-50 text-emerald-800 font-extrabold transition">
                                        <span class="w-6 h-6 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-700">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                        </span>
                                        <span>Input Donasi Baru</span>
                                    </a>
                                    <a href="{{ route('amil.donasi.index') }}" class="flex items-center gap-2.5 px-3.5 py-2 hover:bg-slate-50 font-semibold transition">
                                        <span class="w-6 h-6 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </span>
                                        <span>Riwayat Donasi Saya</span>
                                    </a>
                                    <a href="{{ route('amil.donatur.index') }}" class="flex items-center gap-2.5 px-3.5 py-2 hover:bg-slate-50 font-semibold transition">
                                        <span class="w-6 h-6 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                        </span>
                                        <span>Database Donatur</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Pengumuman Langsung -->
                        <a href="{{ route('amil.pengumuman.index') }}" class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap {{ request()->routeIs('amil.pengumuman.*') ? 'bg-emerald-900 text-white shadow-inner' : 'text-emerald-100 hover:bg-emerald-700 hover:text-white' }}">
                            <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                            </svg>
                            <span>Pengumuman</span>
                        </a>

                    @elseif($isMagang)
                        {{-- NAV KHUSUS ANAK MAGANG (Presensi & Logbook Aktivitas) --}}
                        <a href="{{ route('amil.absensi.index') }}" class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap {{ request()->routeIs('amil.absensi.*') ? 'bg-emerald-900 text-white shadow-inner' : 'text-emerald-100 hover:bg-emerald-700 hover:text-white' }}">
                            <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Presensi</span>
                        </a>

                        <a href="{{ route('amil.aktivitas.index') }}" class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap {{ request()->routeIs('amil.aktivitas.*') ? 'bg-emerald-900 text-white shadow-inner' : 'text-emerald-100 hover:bg-emerald-700 hover:text-white' }}">
                            <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <span>Logbook Aktivitas</span>
                        </a>

                        <a href="{{ route('amil.izin-cuti.index') }}" class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap {{ request()->routeIs('amil.izin-cuti.*') ? 'bg-emerald-900 text-white shadow-inner' : 'text-emerald-100 hover:bg-emerald-700 hover:text-white' }}">
                            <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>Izin & Cuti</span>
                        </a>

                        <!-- Dropdown: Agenda & Info -->
                        <div class="relative group">
                            <button type="button" class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap {{ request()->routeIs('amil.agenda.*', 'amil.pengumuman.*') ? 'bg-emerald-900 text-white shadow-inner' : 'text-emerald-100 hover:bg-emerald-700 hover:text-white' }}">
                                <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>Agenda & Info</span>
                                <svg class="w-3.5 h-3.5 opacity-70 group-hover:rotate-180 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div class="absolute left-0 top-full pt-1.5 w-52 hidden group-hover:block z-50 animate-in fade-in slide-in-from-top-1 duration-150">
                                <div class="bg-white rounded-2xl shadow-xl border border-slate-100 py-1.5 text-slate-700 text-xs overflow-hidden">
                                    <a href="{{ route('amil.agenda.index') }}" class="flex items-center gap-2.5 px-3.5 py-2 hover:bg-slate-50 font-semibold transition">
                                        <span class="w-6 h-6 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </span>
                                        <span>Agenda Penugasan</span>
                                    </a>
                                    <a href="{{ route('amil.pengumuman.index') }}" class="flex items-center gap-2.5 px-3.5 py-2 hover:bg-slate-50 font-semibold transition">
                                        <span class="w-6 h-6 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                                        </span>
                                        <span>Pengumuman Lembaga</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                    @elseif($isDriver)
                        {{-- NAV KHUSUS DRIVER OPERASIONAL --}}
                        <a href="{{ route('amil.aktivitas.index') }}" class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap {{ request()->routeIs('amil.aktivitas.*') ? 'bg-emerald-900 text-white shadow-inner' : 'text-emerald-100 hover:bg-emerald-700 hover:text-white' }}">
                            <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <span>Tugas Lapangan</span>
                        </a>

                        <a href="{{ route('amil.penyaluran.index') }}" class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap {{ request()->routeIs('amil.penyaluran.*') ? 'bg-emerald-900 text-white shadow-inner' : 'text-emerald-100 hover:bg-emerald-700 hover:text-white' }}">
                            <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span>Penyaluran (8 Asnaf)</span>
                        </a>

                        <a href="{{ route('amil.absensi.index') }}" class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap {{ request()->routeIs('amil.absensi.*') ? 'bg-emerald-900 text-white shadow-inner' : 'text-emerald-100 hover:bg-emerald-700 hover:text-white' }}">
                            <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Presensi</span>
                        </a>

                        <a href="{{ route('amil.izin-cuti.index') }}" class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap {{ request()->routeIs('amil.izin-cuti.*') ? 'bg-emerald-900 text-white shadow-inner' : 'text-emerald-100 hover:bg-emerald-700 hover:text-white' }}">
                            <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>Izin & Cuti</span>
                        </a>

                        <!-- Dropdown: Agenda & Info -->
                        <div class="relative group">
                            <button type="button" class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap {{ request()->routeIs('amil.agenda.*', 'amil.pengumuman.*') ? 'bg-emerald-900 text-white shadow-inner' : 'text-emerald-100 hover:bg-emerald-700 hover:text-white' }}">
                                <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>Agenda & Info</span>
                                <svg class="w-3.5 h-3.5 opacity-70 group-hover:rotate-180 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div class="absolute left-0 top-full pt-1.5 w-52 hidden group-hover:block z-50 animate-in fade-in slide-in-from-top-1 duration-150">
                                <div class="bg-white rounded-2xl shadow-xl border border-slate-100 py-1.5 text-slate-700 text-xs overflow-hidden">
                                    <a href="{{ route('amil.agenda.index') }}" class="flex items-center gap-2.5 px-3.5 py-2 hover:bg-slate-50 font-semibold transition">
                                        <span class="w-6 h-6 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </span>
                                        <span>Agenda Penugasan</span>
                                    </a>
                                    <a href="{{ route('amil.pengumuman.index') }}" class="flex items-center gap-2.5 px-3.5 py-2 hover:bg-slate-50 font-semibold transition">
                                        <span class="w-6 h-6 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                                        </span>
                                        <span>Pengumuman Lembaga</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                    @else
                        {{-- NAV REGULER (STAFF, KETUA) --}}
                        <!-- Dropdown: Fundraising & Donasi -->
                        <div class="relative group">
                            <button type="button" class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap {{ request()->routeIs('amil.donasi.*', 'amil.donatur.*') ? 'bg-emerald-900 text-white shadow-inner' : 'text-emerald-100 hover:bg-emerald-700 hover:text-white' }}">
                                <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Donasi & Donatur</span>
                                <svg class="w-3.5 h-3.5 opacity-70 group-hover:rotate-180 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div class="absolute left-0 top-full pt-1.5 w-56 hidden group-hover:block z-50 animate-in fade-in slide-in-from-top-1 duration-150">
                                <div class="bg-white rounded-2xl shadow-xl border border-slate-100 py-1.5 text-slate-700 text-xs overflow-hidden">
                                    <a href="{{ route('amil.donasi.create') }}" class="flex items-center gap-2.5 px-3.5 py-2 hover:bg-emerald-50 text-emerald-800 font-extrabold transition">
                                        <span class="w-6 h-6 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-700">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                        </span>
                                        <span>Input Donasi Baru</span>
                                    </a>
                                    <a href="{{ route('amil.donasi.index') }}" class="flex items-center gap-2.5 px-3.5 py-2 hover:bg-slate-50 font-semibold transition">
                                        <span class="w-6 h-6 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </span>
                                        <span>Riwayat Donasi Saya</span>
                                    </a>
                                    <a href="{{ route('amil.donatur.index') }}" class="flex items-center gap-2.5 px-3.5 py-2 hover:bg-slate-50 font-semibold transition">
                                        <span class="w-6 h-6 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                        </span>
                                        <span>Database Donatur</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Dropdown: Operasional & SDM -->
                        <div class="relative group">
                            <button type="button" class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap {{ request()->routeIs('amil.absensi.*', 'amil.aktivitas.*', 'amil.izin-cuti.*') ? 'bg-emerald-900 text-white shadow-inner' : 'text-emerald-100 hover:bg-emerald-700 hover:text-white' }}">
                                <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <span>Operasional</span>
                                <svg class="w-3.5 h-3.5 opacity-70 group-hover:rotate-180 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div class="absolute left-0 top-full pt-1.5 w-60 hidden group-hover:block z-50 animate-in fade-in slide-in-from-top-1 duration-150">
                                <div class="bg-white rounded-2xl shadow-xl border border-slate-100 py-1.5 text-slate-700 text-xs overflow-hidden">
                                    <a href="{{ route('amil.absensi.index') }}" class="flex items-center gap-2.5 px-3.5 py-2 hover:bg-slate-50 font-semibold transition">
                                        <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </span>
                                        <div>
                                            <span class="block text-slate-800 font-bold">Presensi Kehadiran</span>
                                            <span class="text-[10px] text-slate-400">Absen GPS & Foto Selfie</span>
                                        </div>
                                    </a>
                                    <a href="{{ route('amil.aktivitas.index') }}" class="flex items-center gap-2.5 px-3.5 py-2 hover:bg-slate-50 font-semibold transition">
                                        <span class="w-6 h-6 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        </span>
                                        <div>
                                            <span class="block text-slate-800 font-bold">Laporan Aktivitas</span>
                                            <span class="text-[10px] text-slate-400">Logbook & Kegiatan Harian</span>
                                        </div>
                                    </a>
                                    <a href="{{ route('amil.izin-cuti.index') }}" class="flex items-center gap-2.5 px-3.5 py-2 hover:bg-slate-50 font-semibold transition">
                                        <span class="w-6 h-6 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </span>
                                        <div>
                                            <span class="block text-slate-800 font-bold">Izin & Cuti</span>
                                            <span class="text-[10px] text-slate-400">Pengajuan & Sisa Cuti</span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Penyaluran ZISWAF -->
                        <a href="{{ route('amil.penyaluran.index') }}" class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap {{ request()->routeIs('amil.penyaluran.*') ? 'bg-emerald-900 text-white shadow-inner' : 'text-emerald-100 hover:bg-emerald-700 hover:text-white' }}">
                            <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span>Penyaluran (8 Asnaf)</span>
                        </a>

                        <!-- Dropdown: Agenda & Info -->
                        <div class="relative group">
                            <button type="button" class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap {{ request()->routeIs('amil.agenda.*', 'amil.pengumuman.*') ? 'bg-emerald-900 text-white shadow-inner' : 'text-emerald-100 hover:bg-emerald-700 hover:text-white' }}">
                                <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>Agenda & Info</span>
                                <svg class="w-3.5 h-3.5 opacity-70 group-hover:rotate-180 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div class="absolute left-0 top-full pt-1.5 w-52 hidden group-hover:block z-50 animate-in fade-in slide-in-from-top-1 duration-150">
                                <div class="bg-white rounded-2xl shadow-xl border border-slate-100 py-1.5 text-slate-700 text-xs overflow-hidden">
                                    <a href="{{ route('amil.agenda.index') }}" class="flex items-center gap-2.5 px-3.5 py-2 hover:bg-slate-50 font-semibold transition">
                                        <span class="w-6 h-6 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </span>
                                        <span>Agenda Penugasan</span>
                                    </a>
                                    <a href="{{ route('amil.pengumuman.index') }}" class="flex items-center gap-2.5 px-3.5 py-2 hover:bg-slate-50 font-semibold transition">
                                        <span class="w-6 h-6 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                                        </span>
                                        <span>Pengumuman Lembaga</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </nav>

                <!-- 3. Action Buttons & User Profile (Kanan) -->
                <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                    <!-- Quick Button Header (Desktop) -->
                    @if($isMagang)
                        <a href="{{ route('amil.aktivitas.create') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-teal-500 hover:bg-teal-400 text-white font-extrabold text-xs shadow-sm hover:shadow transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            <span>+ Aktivitas</span>
                        </a>
                    @elseif($isDriver)
                        <a href="{{ route('amil.aktivitas.create') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-teal-500 hover:bg-teal-400 text-white font-extrabold text-xs shadow-sm hover:shadow transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <span>+ Catat Tugas</span>
                        </a>
                    @else
                        <a href="{{ route('amil.donasi.create') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-white font-extrabold text-xs shadow-sm hover:shadow transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>+ Donasi</span>
                        </a>
                    @endif

                    <!-- User Profile Card & Interactive Dropdown -->
                    <div class="relative group" id="profileDropdownContainer">
                        <button type="button" id="profileDropdownBtn" onclick="toggleProfileDropdown(event)" class="flex items-center gap-2 p-1.5 sm:px-2.5 sm:py-1.5 rounded-xl bg-emerald-700/80 hover:bg-emerald-700 text-white transition border border-emerald-600/50 cursor-pointer select-none">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-emerald-950 text-emerald-200 font-extrabold text-xs flex items-center justify-center uppercase shadow-inner shrink-0">
                                {{ substr(Auth::user()->amil?->nama ?? Auth::user()->name, 0, 2) }}
                            </div>
                            <div class="hidden sm:block text-left leading-tight">
                                <span class="text-xs font-bold block max-w-[120px] truncate">{{ Auth::user()->amil?->nama ?? Auth::user()->name }}</span>
                                <span class="text-[10px] text-emerald-200 block font-mono">{{ $jabatanLabel }}</span>
                            </div>
                            <svg id="profileDropdownChevron" class="w-3.5 h-3.5 opacity-70 transition-transform duration-200 shrink-0 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <!-- Profile Dropdown Popup -->
                        <div id="profileDropdownMenu" class="absolute right-0 top-full pt-1.5 w-64 hidden z-50 animate-in fade-in slide-in-from-top-1 duration-150">
                            <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 p-4 text-slate-800 text-xs">
                                <div class="pb-3 mb-3 border-b border-slate-100">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Akun Terdaftar</span>
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold {{ $isRelawan ? 'bg-amber-100 text-amber-800' : ($isMagang ? 'bg-blue-100 text-blue-800' : 'bg-emerald-100 text-emerald-800') }}">
                                            {{ $jabatanLabel }}
                                        </span>
                                    </div>
                                    <span class="font-extrabold text-slate-900 text-sm block truncate">{{ Auth::user()->amil?->nama ?? Auth::user()->name }}</span>
                                    <span class="text-slate-500 font-mono text-[11px] block">{{ Auth::user()->amil?->nip ?? '-' }}</span>
                                    <span class="inline-block mt-1 px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-bold text-[10px]">
                                        {{ Auth::user()->amil?->divisi?->nama ?? 'Divisi Umum' }}
                                    </span>
                                </div>

                                <!-- Menu Profil Saya -->
                                <div class="space-y-1 mb-2">
                                    <a href="{{ route('amil.profile') }}" class="w-full py-2.5 px-3 rounded-xl hover:bg-emerald-50 text-slate-700 hover:text-emerald-800 font-bold text-xs transition flex items-center gap-2.5">
                                        <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </span>
                                        <div class="text-left">
                                            <span class="block text-slate-900 font-extrabold">Profil Saya</span>
                                            <span class="text-[10px] text-slate-400 font-normal">Edit Biodata & Password</span>
                                        </div>
                                    </a>
                                </div>

                                <!-- Tombol Keluar (Logout) -->
                                <form action="{{ route('amil.logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem?')" class="w-full py-2 px-3 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs transition flex items-center justify-center gap-2 cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        <span>Keluar (Logout)</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Hamburger Button untuk Layar Mobile/Tablet -->
                    <button type="button" onclick="document.getElementById('mobileDrawer').classList.toggle('hidden')" class="lg:hidden p-2 rounded-xl bg-emerald-700 text-emerald-100 hover:text-white hover:bg-emerald-600 transition" title="Menu Lengkap">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>

            </div>
        </div>
    </header>

    <!-- Mobile Drawer Menu (Slide / Toggle) -->
    <div id="mobileDrawer" class="hidden lg:hidden bg-emerald-900 border-b border-emerald-800 text-white px-4 py-4 space-y-3 z-30 shadow-xl">
        <div class="grid grid-cols-2 gap-2 text-xs font-semibold">
            <a href="{{ route('amil.dashboard') }}" class="p-2.5 rounded-xl bg-emerald-800/80 hover:bg-emerald-700 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>Dashboard</span>
            </a>

            @if($canAccessDonasi)
                <a href="{{ route('amil.donasi.create') }}" class="p-2.5 rounded-xl bg-emerald-600 text-white font-bold flex items-center gap-2">
                    <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>Input Donasi</span>
                </a>
                <a href="{{ route('amil.donasi.index') }}" class="p-2.5 rounded-xl bg-emerald-800/80 hover:bg-emerald-700 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Riwayat Donasi</span>
                </a>
                <a href="{{ route('amil.donatur.index') }}" class="p-2.5 rounded-xl bg-emerald-800/80 hover:bg-emerald-700 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span>Database Donatur</span>
                </a>
            @elseif($isMagang)
                <a href="{{ route('amil.aktivitas.create') }}" class="p-2.5 rounded-xl bg-teal-600 text-white font-bold flex items-center gap-2">
                    <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>+ Catat Aktivitas</span>
                </a>
            @endif

            @if($needsAttendance)
                <a href="{{ route('amil.absensi.index') }}" class="p-2.5 rounded-xl bg-emerald-800/80 hover:bg-emerald-700 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Presensi Kehadiran</span>
                </a>
            @endif

            @if(!$isRelawan)
                <a href="{{ route('amil.aktivitas.index') }}" class="p-2.5 rounded-xl bg-emerald-800/80 hover:bg-emerald-700 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <span>Laporan Aktivitas</span>
                </a>
            @endif

            @if($canAccessPenyaluran)
                <a href="{{ route('amil.penyaluran.index') }}" class="p-2.5 rounded-xl bg-emerald-800/80 hover:bg-emerald-700 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Penyaluran ZISWAF</span>
                </a>
            @endif

            @if($canAccessIzinCuti)
                <a href="{{ route('amil.izin-cuti.index') }}" class="p-2.5 rounded-xl bg-emerald-800/80 hover:bg-emerald-700 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Izin & Cuti</span>
                </a>
            @endif

            @if(!$isRelawan)
                <a href="{{ route('amil.agenda.index') }}" class="p-2.5 rounded-xl bg-emerald-800/80 hover:bg-emerald-700 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Agenda Kerja</span>
                </a>
            @endif

            <a href="{{ route('amil.pengumuman.index') }}" class="p-2.5 rounded-xl bg-emerald-800/80 hover:bg-emerald-700 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                <span>Pengumuman</span>
            </a>
        </div>
    </div>

    <!-- Main Content Container -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Flash Alert Messages -->
        @if (session('success'))
            <div class="mb-5 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start gap-3 shadow-sm">
                <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="text-sm font-medium">{{ session('success') }}</div>
            </div>
        @endif

        @if (session('error') || $errors->any())
            <div class="mb-5 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 flex items-start gap-3 shadow-sm">
                <svg class="w-5 h-5 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="text-sm font-medium">
                    {{ session('error') ?? 'Mohon periksa kembali input formulir Anda di bawah ini.' }}
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Mobile Floating Bottom Navigation Bar (Disesuaikan Per Role Amil) -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 shadow-2xl z-50 px-2 py-2">
        <div class="grid grid-cols-5 items-center w-full max-w-lg mx-auto">
            @if($isRelawan)
                {{-- 1. Nav Khusus Relawan: Bentuk & Struktur Selaras Staff & Magang --}}
                <a href="{{ route('amil.dashboard') }}" class="flex flex-col items-center justify-center gap-1 py-1 w-full text-center rounded-xl transition {{ request()->routeIs('amil.dashboard') ? 'text-emerald-700 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="text-[10px] tracking-tight leading-none">Dashboard</span>
                </a>

                <a href="{{ route('amil.donasi.index') }}" class="flex flex-col items-center justify-center gap-1 py-1 w-full text-center rounded-xl transition {{ request()->routeIs('amil.donasi.index') ? 'text-emerald-700 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="text-[10px] tracking-tight leading-none">Riwayat</span>
                </a>

                <a href="{{ route('amil.donasi.create') }}" class="flex flex-col items-center justify-center gap-1 py-1 w-full text-center transition {{ request()->routeIs('amil.donasi.create') ? 'text-emerald-700 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                    <div style="background: linear-gradient(135deg, #059669 0%, #047857 100%) !important; color: #ffffff !important; box-shadow: 0 10px 18px -2px rgba(5, 150, 105, 0.45) !important;" class="w-12 h-12 rounded-full flex items-center justify-center -mt-6 border-2 border-white active:scale-95 transition-transform duration-150 mx-auto">
                        <svg class="w-6 h-6 text-white" style="color: #ffffff !important; stroke: #ffffff !important;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <span class="text-[10px] font-bold text-emerald-700 tracking-tight leading-none">Donasi</span>
                </a>

                <a href="{{ route('amil.donatur.index') }}" class="flex flex-col items-center justify-center gap-1 py-1 w-full text-center rounded-xl transition {{ request()->routeIs('amil.donatur.*') ? 'text-emerald-700 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span class="text-[10px] tracking-tight leading-none">Donatur</span>
                </a>

                <a href="{{ route('amil.pengumuman.index') }}" class="flex flex-col items-center justify-center gap-1 py-1 w-full text-center rounded-xl transition {{ request()->routeIs('amil.pengumuman.*') ? 'text-emerald-700 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                    </svg>
                    <span class="text-[10px] tracking-tight leading-none">Info</span>
                </a>
            @elseif($isMagang)
                {{-- 2. Nav Khusus Magang: Fokus Disiplin Absensi & Laporan Aktivitas Harian --}}
                <a href="{{ route('amil.dashboard') }}" class="flex flex-col items-center justify-center gap-1 py-1 w-full text-center rounded-xl transition {{ request()->routeIs('amil.dashboard') ? 'text-teal-700 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="text-[10px] tracking-tight leading-none">Dashboard</span>
                </a>

                <a href="{{ route('amil.absensi.index') }}" class="flex flex-col items-center justify-center gap-1 py-1 w-full text-center rounded-xl transition {{ request()->routeIs('amil.absensi.*') ? 'text-teal-700 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-[10px] tracking-tight leading-none">Presensi</span>
                </a>

                <a href="{{ route('amil.aktivitas.create') }}" class="flex flex-col items-center justify-center gap-1 py-1 w-full text-center transition {{ request()->routeIs('amil.aktivitas.create') ? 'text-teal-700 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                    <div style="background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%) !important; color: #ffffff !important; box-shadow: 0 10px 18px -2px rgba(13, 148, 136, 0.45) !important;" class="w-12 h-12 rounded-full flex items-center justify-center -mt-6 border-2 border-white active:scale-95 transition-transform duration-150 mx-auto">
                        <svg class="w-6 h-6 text-white" style="color: #ffffff !important; stroke: #ffffff !important;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <span class="text-[10px] font-bold text-teal-700 tracking-tight leading-none">Aktivitas</span>
                </a>

                <a href="{{ route('amil.aktivitas.index') }}" class="flex flex-col items-center justify-center gap-1 py-1 w-full text-center rounded-xl transition {{ request()->routeIs('amil.aktivitas.*') && !request()->routeIs('amil.aktivitas.create') ? 'text-teal-700 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span class="text-[10px] tracking-tight leading-none">Logbook</span>
                </a>

                <a href="{{ route('amil.izin-cuti.index') }}" class="flex flex-col items-center justify-center gap-1 py-1 w-full text-center rounded-xl transition {{ request()->routeIs('amil.izin-cuti.*') ? 'text-teal-700 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="text-[10px] tracking-tight leading-none">Izin Cuti</span>
                </a>
            @else
                {{-- 3. Nav Reguler (Staff, Driver, Ketua): Lengkap Semua Fitur --}}
                <a href="{{ route('amil.dashboard') }}" class="flex flex-col items-center justify-center gap-1 py-1 w-full text-center rounded-xl transition {{ request()->routeIs('amil.dashboard') ? 'text-emerald-700 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="text-[10px] tracking-tight leading-none">Dashboard</span>
                </a>

                <a href="{{ route('amil.absensi.index') }}" class="flex flex-col items-center justify-center gap-1 py-1 w-full text-center rounded-xl transition {{ request()->routeIs('amil.absensi.*') ? 'text-emerald-700 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-[10px] tracking-tight leading-none">Absen</span>
                </a>

                <a href="{{ route('amil.donasi.create') }}" class="flex flex-col items-center justify-center gap-1 py-1 w-full text-center transition {{ request()->routeIs('amil.donasi.create') ? 'text-emerald-700 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                    <div style="background: linear-gradient(135deg, #059669 0%, #047857 100%) !important; color: #ffffff !important; box-shadow: 0 10px 18px -2px rgba(5, 150, 105, 0.45) !important;" class="w-12 h-12 rounded-full flex items-center justify-center -mt-6 border-2 border-white active:scale-95 transition-transform duration-150 mx-auto">
                        <svg class="w-6 h-6 text-white" style="color: #ffffff !important; stroke: #ffffff !important;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <span class="text-[10px] font-bold text-emerald-700 tracking-tight leading-none">Donasi</span>
                </a>

                <a href="{{ route('amil.aktivitas.index') }}" class="flex flex-col items-center justify-center gap-1 py-1 w-full text-center rounded-xl transition {{ request()->routeIs('amil.aktivitas.*') ? 'text-emerald-700 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span class="text-[10px] tracking-tight leading-none">Aktivitas</span>
                </a>

                <a href="{{ route('amil.donatur.index') }}" class="flex flex-col items-center justify-center gap-1 py-1 w-full text-center rounded-xl transition {{ request()->routeIs('amil.donatur.*') ? 'text-emerald-700 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span class="text-[10px] tracking-tight leading-none">Donatur</span>
                </a>
            @endif
        </div>
    </nav>

    <!-- PWA Installation Banner Popup -->
    <div id="pwaInstallBanner" class="hidden fixed bottom-20 lg:bottom-5 right-4 left-4 sm:left-auto sm:w-96 bg-slate-900/95 text-white p-4 rounded-2xl shadow-2xl backdrop-blur-md border border-slate-700/80 z-50 animate-in fade-in slide-in-from-bottom-5 duration-300">
        <div class="flex items-start gap-3">
            <img src="{{ asset('logo_gsc.png') }}" alt="Logo GSC" class="w-11 h-11 rounded-xl bg-white p-1 shadow-md shrink-0">
            <div class="flex-1 min-w-0">
                <h4 class="text-xs font-extrabold text-white">Pasang Aplikasi Amil GSC</h4>
                <p class="text-[11px] text-slate-300 mt-0.5 leading-snug">Pasang di layar utama HP Anda agar akses lebih cepat tanpa perlu membuka browser.</p>
                <div class="flex items-center gap-2 mt-3">
                    <button type="button" id="btnPwaInstall" class="py-1.5 px-3 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-white font-extrabold text-xs shadow-md transition cursor-pointer flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span>Install Sekarang</span>
                    </button>
                    <button type="button" id="btnPwaDismiss" class="py-1.5 px-3 rounded-xl text-slate-400 hover:text-white font-semibold text-xs transition cursor-pointer">
                        Nanti Saja
                    </button>
                </div>
            </div>
            <button type="button" id="btnPwaClose" class="text-slate-400 hover:text-white p-1 rounded-lg transition" title="Tutup">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <!-- PWA Service Worker & Install Logic -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((reg) => console.log('PWA ServiceWorker registered:', reg.scope))
                    .catch((err) => console.error('PWA ServiceWorker registration failed:', err));
            });
        }

        let deferredPrompt = null;
        const pwaBanner = document.getElementById('pwaInstallBanner');
        const btnInstall = document.getElementById('btnPwaInstall');
        const btnDismiss = document.getElementById('btnPwaDismiss');
        const btnClose = document.getElementById('btnPwaClose');

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;

            if (!sessionStorage.getItem('pwa_dismissed') && pwaBanner) {
                pwaBanner.classList.remove('hidden');
            }
        });

        if (btnInstall) {
            btnInstall.addEventListener('click', async () => {
                if (!deferredPrompt) return;
                pwaBanner.classList.add('hidden');
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                console.log(`PWA install outcome: ${outcome}`);
                deferredPrompt = null;
            });
        }

        const dismissPwa = () => {
            if (pwaBanner) pwaBanner.classList.add('hidden');
            sessionStorage.setItem('pwa_dismissed', 'true');
        };

        if (btnDismiss) btnDismiss.addEventListener('click', dismissPwa);
        if (btnClose) btnClose.addEventListener('click', dismissPwa);

        window.addEventListener('appinstalled', () => {
            console.log('Aplikasi Amil GSC berhasil dipasang di perangkat!');
            if (pwaBanner) pwaBanner.classList.add('hidden');
            deferredPrompt = null;
        });
        // Dropdown Profil Amil Toggle
        function toggleProfileDropdown(event) {
            if (event) {
                event.stopPropagation();
            }
            const menu = document.getElementById('profileDropdownMenu');
            const chevron = document.getElementById('profileDropdownChevron');
            if (menu) {
                const isHidden = menu.classList.contains('hidden');
                if (isHidden) {
                    menu.classList.remove('hidden');
                    if (chevron) chevron.classList.add('rotate-180');
                } else {
                    menu.classList.add('hidden');
                    if (chevron) chevron.classList.remove('rotate-180');
                }
            }
        }

        // Close dropdown when clicked outside
        document.addEventListener('click', function(e) {
            const container = document.getElementById('profileDropdownContainer');
            const menu = document.getElementById('profileDropdownMenu');
            const chevron = document.getElementById('profileDropdownChevron');
            if (container && menu && !container.contains(e.target)) {
                menu.classList.add('hidden');
                if (chevron) chevron.classList.remove('rotate-180');
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
