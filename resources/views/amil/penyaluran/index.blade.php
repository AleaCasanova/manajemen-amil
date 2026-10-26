@extends('layouts.amil')

@section('title', 'Penyaluran Bantuan Mustahik')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Penyaluran Bantuan Mustahik</h1>
            <p class="text-sm text-slate-500">Dokumentasikan pendistribusian dana ZISWAF langsung kepada penerima manfaat 8 Asnaf</p>
        </div>
        <a href="{{ route('amil.penyaluran.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>+ Catat Penyaluran Baru</span>
        </a>
    </div>

    <!-- Search Bar -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
        <form action="{{ route('amil.penyaluran.index') }}" method="GET" class="flex gap-2">
            <div class="relative flex-1">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama penerima mustahik atau NIK..." class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <button type="submit" class="py-2 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition">
                Cari
            </button>
            @if (request()->filled('q'))
                <a href="{{ route('amil.penyaluran.index') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition flex items-center justify-center">
                    ✕
                </a>
            @endif
        </form>
    </div>

    <!-- List Penyaluran -->
    @if ($penyalurans->isEmpty())
        <div class="bg-white rounded-3xl p-10 text-center border border-slate-200 shadow-sm">
            <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <h3 class="font-bold text-slate-800 text-base">Belum Ada Penyaluran Tercatat</h3>
            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Anda belum mendokumentasikan distribusi bantuan kepada mustahik binaan.</p>
            <a href="{{ route('amil.penyaluran.create') }}" class="inline-flex items-center gap-1.5 mt-4 px-4 py-2 rounded-xl bg-emerald-600 text-white font-bold text-xs shadow-sm hover:bg-emerald-700 transition">
                + Input Penyaluran Sekarang
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($penyalurans as $p)
                <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-200 flex flex-col justify-between hover:border-emerald-300 transition">
                    <div>
                        <!-- Header -->
                        <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-100 text-xs">
                            <span class="font-semibold text-slate-500">
                                📅 {{ $p->tgl_salur ? $p->tgl_salur->translatedFormat('d F Y') : '-' }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 uppercase">
                                {{ $p->mustahik?->asnaf ?? 'Mustahik' }}
                            </span>
                        </div>

                        <!-- Penerima & Nominal -->
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div>
                                <h3 class="font-bold text-slate-800 text-base">{{ $p->mustahik?->nama ?? '-' }}</h3>
                                @if ($p->mustahik?->nik)
                                    <span class="text-xs text-slate-400 font-mono">NIK: {{ $p->mustahik->nik }}</span>
                                @endif
                                <span class="text-xs text-slate-500 block mt-1">
                                    {{ $p->program?->nama ?? 'Bantuan Rutin' }} • {{ $p->jenisDana?->nama ?? 'ZISWAF' }}
                                </span>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] uppercase font-semibold text-slate-400 block">Nominal Bantuan</span>
                                <span class="font-extrabold text-emerald-700 text-lg">
                                    Rp {{ number_format($p->jumlah, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        @if ($p->catatan)
                            <p class="text-xs text-slate-600 bg-slate-50 p-2.5 rounded-xl border border-slate-100 mb-3">
                                "{{ $p->catatan }}"
                            </p>
                        @endif
                    </div>

                    <!-- Bukti Dokumentasi Serah Terima -->
                    @if ($p->bukti)
                        <div class="pt-3 border-t border-slate-100">
                            <a href="{{ asset('storage/' . $p->bukti) }}" target="_blank" class="block rounded-xl overflow-hidden aspect-video border border-slate-200 group relative">
                                <img src="{{ asset('storage/' . $p->bukti) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" alt="Bukti Serah Terima">
                                <span class="absolute bottom-2 right-2 px-2 py-1 bg-black/60 text-white rounded-lg text-[10px] font-bold backdrop-blur-sm">
                                    Dokumentasi Penyerahan ↗
                                </span>
                            </a>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $penyalurans->links() }}
        </div>
    @endif
</div>
@endsection
