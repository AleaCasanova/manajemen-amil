@extends('layouts.amil')

@section('title', 'Laporan Aktivitas Harian')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Laporan Aktivitas Harian</h1>
            <p class="text-sm text-slate-500">Dokumentasikan kegiatan jemput donasi, silaturahmi muzakki, dan sosialisasi program</p>
        </div>
        <a href="{{ route('amil.aktivitas.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>+ Buat Laporan Baru</span>
        </a>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
        <form action="{{ route('amil.aktivitas.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
            <div class="sm:col-span-7 relative">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul kegiatan, lokasi, atau deskripsi..." class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <div class="sm:col-span-3">
                <select name="status" class="w-full py-2 px-3 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">Semua Status</option>
                    <option value="diajukan" {{ request('status') === 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                    <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui Admin</option>
                    <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <div class="sm:col-span-2 flex gap-2">
                <button type="submit" class="w-full py-2 px-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition">
                    Filter
                </button>
                @if (request()->hasAny(['q', 'status']))
                    <a href="{{ route('amil.aktivitas.index') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition flex items-center justify-center" title="Reset Filter">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- List Aktivitas -->
    @if ($aktivitas->isEmpty())
        <div class="bg-white rounded-2xl sm:rounded-3xl p-8 sm:p-10 text-center border border-slate-200 shadow-sm">
            <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <h3 class="font-bold text-slate-800 text-base">Belum Ada Catatan Aktivitas</h3>
            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Dokumentasikan seluruh ikhtiar dakwah dan fundraising Anda di lapangan.</p>
            <a href="{{ route('amil.aktivitas.create') }}" class="inline-flex items-center gap-1.5 mt-4 px-4 py-2 rounded-xl bg-emerald-600 text-white font-bold text-xs shadow-sm hover:bg-emerald-700 transition">
                + Buat Laporan Hari Ini
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5 sm:gap-4">
            @foreach ($aktivitas as $a)
                <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-5 shadow-sm border border-slate-200 flex flex-col justify-between hover:border-emerald-300 transition">
                    <div>
                        <!-- Header Card -->
                        <div class="flex items-center justify-between pb-2.5 mb-2.5 border-b border-slate-100 text-xs">
                            <span class="font-semibold text-slate-500 inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span>{{ $a->tanggal ? $a->tanggal->translatedFormat('d M Y') : '-' }}</span>
                            </span>
                            @if ($a->status === 'disetujui')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                    Disetujui
                                </span>
                            @elseif ($a->status === 'ditolak')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                    Ditolak
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                    Menunggu Review
                                </span>
                            @endif
                        </div>

                        <!-- Title & Location -->
                        <h3 class="font-bold text-slate-800 text-sm sm:text-base mb-1">{{ $a->judul }}</h3>
                        @if ($a->lokasi)
                            <div class="text-xs text-emerald-700 font-semibold flex items-center flex-wrap gap-1 mb-2">
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span>{{ $a->lokasi }}</span>
                                </span>
                                @if ($a->lat && $a->lng)
                                    <a href="https://www.google.com/maps?q={{ $a->lat }},{{ $a->lng }}" target="_blank" class="text-[11px] text-blue-600 hover:text-blue-800 underline font-normal inline-flex items-center gap-0.5 ml-1" title="Lihat Peta Google Maps">
                                        (Peta ↗)
                                    </a>
                                @endif
                            </div>
                        @elseif ($a->lat && $a->lng)
                            <div class="text-xs text-blue-700 font-semibold mb-2">
                                <a href="https://www.google.com/maps?q={{ $a->lat }},{{ $a->lng }}" target="_blank" class="hover:underline inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span>Titik Koordinat: {{ $a->lat }}, {{ $a->lng }} (Peta ↗)</span>
                                </a>
                            </div>
                        @endif

                        <!-- Description -->
                        <p class="text-xs text-slate-600 line-clamp-3 mb-4 leading-relaxed">
                            {{ $a->deskripsi }}
                        </p>
                    </div>

                    <!-- Foto Dokumentasi (jika ada) -->
                    @if ($a->foto)
                        <div class="pt-3 border-t border-slate-100">
                            <a href="{{ asset('storage/' . $a->foto) }}" target="_blank" class="block rounded-xl overflow-hidden aspect-video border border-slate-200 group relative">
                                <img src="{{ asset('storage/' . $a->foto) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" alt="Foto Dokumentasi">
                                <span class="absolute bottom-2 right-2 px-2 py-1 bg-black/60 text-white rounded-lg text-[10px] font-bold backdrop-blur-sm">
                                    Lihat Foto Penuh ↗
                                </span>
                            </a>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $aktivitas->links() }}
        </div>
    @endif
</div>
@endsection
