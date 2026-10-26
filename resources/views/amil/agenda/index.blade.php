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
            <a href="{{ route('amil.agenda.index', ['tab' => 'mendatang']) }}" class="px-4 py-2 rounded-xl transition {{ $tab === 'mendatang' ? 'bg-white text-emerald-800 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                ⏰ Mendatang / Aktif
            </a>
            <a href="{{ route('amil.agenda.index', ['tab' => 'selesai']) }}" class="px-4 py-2 rounded-xl transition {{ $tab === 'selesai' ? 'bg-white text-emerald-800 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                ✓ Riwayat Selesai
            </a>
        </div>
    </div>

    <!-- List Agenda -->
    @if ($agendas->isEmpty())
        <div class="bg-white rounded-3xl p-10 text-center border border-slate-200 shadow-sm">
            <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <h3 class="font-bold text-slate-800 text-base">Tidak Ada Agenda</h3>
            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Saat ini belum ada penugasan kerja yang tercatat pada kategori ini.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($agendas as $a)
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 flex flex-col justify-between hover:border-emerald-300 transition group">
                    <div>
                        <!-- Header Card -->
                        <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-100 text-xs">
                            <span class="inline-flex items-center gap-1 font-semibold text-emerald-700">
                                📅 {{ $a->mulai ? $a->mulai->translatedFormat('d M Y') : '-' }}
                            </span>
                            @if ($a->status === 'berlangsung')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 animate-pulse">
                                    Sedang Berlangsung
                                </span>
                            @elseif ($a->status === 'selesai')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                    Selesai
                                </span>
                            @elseif ($a->status === 'dibatalkan')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                    Dibatalkan
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">
                                    Terjadwal
                                </span>
                            @endif
                        </div>

                        <!-- Judul & Lokasi -->
                        <h3 class="font-extrabold text-slate-800 text-base mb-1 group-hover:text-emerald-700 transition">
                            <a href="{{ route('amil.agenda.show', $a->id) }}">{{ $a->judul }}</a>
                        </h3>

                        <div class="space-y-1.5 text-xs text-slate-500 mb-3">
                            <div class="flex items-center gap-1.5">
                                <span>🕒</span>
                                <span>{{ $a->mulai ? $a->mulai->format('H:i') : '' }} - {{ $a->selesai ? $a->selesai->format('H:i') : '' }} WIB</span>
                            </div>
                            @if ($a->lokasi)
                                <div class="flex items-center gap-1.5 text-slate-700 font-medium">
                                    <span>📍</span>
                                    <span>{{ $a->lokasi }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Deskripsi -->
                        <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed mb-4">
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
