@extends('layouts.amil')

@section('title', 'Agenda Penugasan Amil')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Agenda & Penugasan Kerja</h1>
            <p class="text-sm text-slate-500">Jadwal kegiatan rapat, jemput donasi gabungan, dan event sosialisasi yayasan</p>
        </div>

        <!-- Filter Tab -->
        <div class="bg-slate-200/70 p-1 rounded-2xl flex text-xs font-bold self-start sm:self-auto">
            <a href="{{ route('amil.agenda.index', ['tab' => 'mendatang']) }}" class="px-3.5 py-1.5 sm:px-4 sm:py-2 rounded-xl transition flex items-center gap-1.5 {{ $tab === 'mendatang' ? 'bg-white text-emerald-800 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Mendatang</span>
            </a>
            <a href="{{ route('amil.agenda.index', ['tab' => 'selesai']) }}" class="px-3.5 py-1.5 sm:px-4 sm:py-2 rounded-xl transition flex items-center gap-1.5 {{ $tab === 'selesai' ? 'bg-white text-emerald-800 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span>Selesai</span>
            </a>
        </div>
    </div>

    <!-- List Agenda -->
    @if ($agendas->isEmpty())
        <div class="bg-white rounded-2xl sm:rounded-3xl p-8 sm:p-10 text-center border border-slate-200 shadow-sm">
            <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <h3 class="font-bold text-slate-800 text-base">Tidak Ada Agenda</h3>
            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Saat ini belum ada penugasan kerja yang tercatat pada kategori ini.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5 sm:gap-4">
            @foreach ($agendas as $a)
                <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-sm border border-slate-200 flex flex-col justify-between hover:border-emerald-300 transition group">
                    <div>
                        <!-- Header Card -->
                        <div class="flex items-center justify-between pb-2.5 mb-2.5 border-b border-slate-100 text-xs">
                            <span class="inline-flex items-center gap-1.5 font-semibold text-emerald-700">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span>{{ $a->mulai ? $a->mulai->translatedFormat('d M Y') : '-' }}</span>
                            </span>
                            @if ($a->status === 'berlangsung')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 animate-pulse">
                                    Berlangsung
                                </span>
                            @elseif ($a->status === 'selesai')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                    Selesai
                                </span>
                            @elseif ($a->status === 'dibatalkan')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                    Dibatalkan
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">
                                    Terjadwal
                                </span>
                            @endif
                        </div>

                        <!-- Judul & Lokasi -->
                        <h3 class="font-extrabold text-slate-800 text-sm sm:text-base mb-1 group-hover:text-emerald-700 transition">
                            <a href="{{ route('amil.agenda.show', $a->id) }}">{{ $a->judul }}</a>
                        </h3>

                        <div class="space-y-1 text-xs text-slate-500 mb-2.5">
                            <div class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>{{ $a->mulai ? $a->mulai->format('H:i') : '' }} - {{ $a->selesai ? $a->selesai->format('H:i') : '' }} WIB</span>
                            </div>
                            @if ($a->lokasi)
                                <div class="flex items-center gap-1.5 text-slate-700 font-medium">
                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span class="truncate">{{ $a->lokasi }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Deskripsi -->
                        <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed mb-3">
                            {{ $a->deskripsi }}
                        </p>
                    </div>

                    <!-- Footer: Tim Amil & Action -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-1">
                            <span class="text-[11px] text-slate-400">Tim:</span>
                            <span class="font-bold text-slate-700 text-[11px]">
                                {{ $a->amils->count() > 0 ? $a->amils->count() . ' Amil Ditugaskan' : 'Semua Amil' }}
                            </span>
                        </div>
                        <a href="{{ route('amil.agenda.show', $a->id) }}" class="font-bold text-emerald-700 hover:text-emerald-800 flex items-center gap-1">
                            <span>Detail</span>
                            <span>→</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $agendas->links() }}
        </div>
    @endif
</div>
@endsection
