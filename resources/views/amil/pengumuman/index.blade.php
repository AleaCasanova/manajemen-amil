@extends('layouts.amil')

@section('title', 'Pengumuman Resmi')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Pengumuman Resmi Yayasan</h1>
        <p class="text-sm text-slate-500">Pemberitahuan, surat edaran, dan informasi penting dari manajemen lembaga</p>
    </div>

    <!-- List Pengumuman -->
    @if ($pengumumans->isEmpty())
        <div class="bg-white rounded-3xl p-10 text-center border border-slate-200 shadow-sm">
            <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            <h3 class="font-bold text-slate-800 text-base">Belum Ada Pengumuman</h3>
            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Saat ini belum ada pengumuman resmi yang diterbitkan manajemen.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($pengumumans as $p)
                @php
                    $isRead = $p->pembacaUsers->contains($user->id);
                @endphp
                <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-sm border border-slate-200 hover:border-emerald-300 transition group flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4 {{ !$isRead ? 'ring-2 ring-emerald-500/20' : '' }}">
                    <div class="space-y-1.5 sm:space-y-2 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            @if (!$isRead)
                                <span class="px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-extrabold bg-emerald-600 text-white animate-pulse">
                                    BARU
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-bold bg-slate-100 text-slate-500">
                                    Sudah Dibaca
                                </span>
                            @endif
                            <span class="text-xs text-slate-400 inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span>{{ $p->published_at ? $p->published_at->translatedFormat('d F Y, H:i') : '-' }} WIB</span>
                            </span>
                        </div>

                        <h3 class="font-extrabold text-slate-800 text-sm sm:text-base group-hover:text-emerald-700 transition">
                            <a href="{{ route('amil.pengumuman.show', $p->id) }}">{{ $p->judul }}</a>
                        </h3>

                        <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed">
                            {{ Str::limit(strip_tags($p->isi), 180) }}
                        </p>

                        @if ($p->lampiran)
                            <div class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-lg">
                                <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                <span>Lampiran Berkas Tersedia</span>
                            </div>
                        @endif
                    </div>

                    <div class="sm:self-center">
                        <a href="{{ route('amil.pengumuman.show', $p->id) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-xl bg-slate-100 hover:bg-emerald-600 hover:text-white text-slate-700 font-bold text-xs transition">
                            <span>Baca Selengkapnya</span>
                            <span>→</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $pengumumans->links() }}
        </div>
    @endif
</div>
@endsection
