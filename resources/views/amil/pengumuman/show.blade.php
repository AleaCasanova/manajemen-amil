@extends('layouts.amil')

@section('title', $pengumuman->judul)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header & Back -->
    <div>
        <a href="{{ route('amil.pengumuman.index') }}" class="text-xs font-bold text-slate-500 hover:text-emerald-700 transition inline-flex items-center gap-1 mb-3">
            ← Kembali ke Semua Pengumuman
        </a>
        <div class="flex items-center gap-2 mb-1">
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                Pemberitahuan Resmi
            </span>
            <span class="text-xs text-slate-400">
                {{ $pengumuman->published_at ? $pengumuman->published_at->translatedFormat('l, d F Y - H:i') : '-' }} WIB
            </span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-800 tracking-tight leading-tight">
            {{ $pengumuman->judul }}
        </h1>
        <p class="text-xs text-slate-400 mt-1">Diterbitkan oleh: {{ $pengumuman->creator?->name ?? 'Administrator Yayasan' }}</p>
    </div>

    <!-- Content Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 space-y-6">
        <div class="text-sm text-slate-700 leading-relaxed whitespace-pre-line prose max-w-none">
            {!! nl2br(e($pengumuman->isi)) !!}
        </div>

        @if ($pengumuman->lampiran)
            <div class="p-3.5 sm:p-4 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                    </div>
                    <div>
                        <span class="font-bold text-xs text-slate-800 block">Lampiran Dokumen Resmi</span>
                        <span class="text-[11px] text-slate-400">Klik tombol unduh untuk melihat dokumen</span>
                    </div>
                </div>

                <a href="{{ asset('storage/' . $pengumuman->lampiran) }}" target="_blank" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition inline-flex items-center justify-center gap-1.5 shadow-sm self-start sm:self-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span>Unduh / Buka</span>
                </a>
            </div>
        @endif

        <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-1 text-xs text-slate-400">
            <span>Telah dibaca oleh {{ $pengumuman->pembacaUsers->count() }} orang amil</span>
            <span class="text-emerald-700 font-semibold inline-flex items-center gap-1">
                <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span>Status: Telah Dibaca</span>
            </span>
        </div>
    </div>
</div>
@endsection
