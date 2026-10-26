@extends('layouts.amil')

@section('title', 'Donatur Saya')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Database Donatur Saya</h1>
            <p class="text-sm text-slate-500">Kelola dan bina muzakki serta donatur setia Anda</p>
        </div>
        <div class="flex gap-2 self-start sm:self-auto">
            <a href="{{ route('amil.donatur.create') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs transition">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                <span>+ Donatur Baru</span>
            </a>
            <a href="{{ route('amil.donasi.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Input Donasi</span>
            </a>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
        <form action="{{ route('amil.donatur.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
            <div class="sm:col-span-7 relative">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, nomor WhatsApp, kota, atau kode donatur..." class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <div class="sm:col-span-3">
                <select name="kategori_id" class="w-full py-2 px-3 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">Semua Kategori</option>
                    @foreach ($kategoris as $k)
                        <option value="{{ $k->id }}" {{ request('kategori_id') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2 flex gap-2">
                <button type="submit" class="w-full py-2 px-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition">
                    Filter
                </button>
                @if (request()->hasAny(['q', 'kategori_id']))
                    <a href="{{ route('amil.donatur.index') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition flex items-center justify-center" title="Reset Filter">
                        ✕
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Data List -->
    @if ($donaturs->isEmpty())
        <div class="bg-white rounded-2xl p-10 text-center border border-slate-200 shadow-sm">
            <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <h3 class="font-bold text-slate-800 text-base">Belum Ada Donatur Terdaftar</h3>
            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Mulai tambahkan donatur baru atau input donasi untuk membangun relasi kebaikan.</p>
            <a href="{{ route('amil.donatur.create') }}" class="inline-flex items-center gap-1.5 mt-4 px-4 py-2 rounded-xl bg-emerald-600 text-white font-bold text-xs shadow-sm hover:bg-emerald-700 transition">
                + Daftarkan Donatur
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($donaturs as $d)
                @php
                    $cleanPhone = preg_replace('/[^0-9]/', '', $d->no_hp ?? '');
                    if (str_starts_with($cleanPhone, '0')) {
                        $cleanPhone = '62' . substr($cleanPhone, 1);
                    }
                @endphp
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200 flex flex-col justify-between hover:border-emerald-300 transition group">
                    <div>
                        <!-- Header Card -->
                        <div class="flex items-start justify-between gap-2 mb-3">
                            <span class="inline-block px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[10px] font-mono font-bold">
                                {{ $d->kode_donatur }}
                            </span>
                            <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700">
                                {{ $d->kategori?->nama ?? 'Umum' }}
                            </span>
                        </div>

                        <!-- Name & Contact -->
                        <div class="mb-4">
                            <a href="{{ route('amil.donatur.show', $d->id) }}" class="font-extrabold text-slate-800 text-base hover:text-emerald-700 transition block leading-tight">
                                {{ $d->is_anonim ? 'Hamba Allah (' . $d->nama . ')' : $d->nama }}
                            </a>
                            <div class="flex items-center gap-2 mt-1.5">
                                <span class="text-xs text-slate-500 font-medium">{{ $d->no_hp }}</span>
                                @if ($cleanPhone)
                                    <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" class="text-emerald-600 hover:text-emerald-800 text-xs font-semibold inline-flex items-center gap-0.5" title="Chat WhatsApp">
                                        <span>💬 Chat</span>
                                    </a>
                                @endif
                            </div>
                            @if ($d->kota)
                                <span class="text-[11px] text-slate-400 block mt-0.5">📍 {{ $d->kota }}</span>
                            @endif
                        </div>

                        <!-- Stats Box -->
                        <div class="grid grid-cols-2 gap-2 p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs mb-4">
                            <div>
                                <span class="text-[10px] text-slate-400 block font-medium">Frekuensi</span>
                                <span class="font-extrabold text-slate-800">{{ $d->donasis_count ?? 0 }}x Donasi</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 block font-medium">Total Nominal</span>
                                <span class="font-extrabold text-emerald-700">Rp {{ number_format($d->donasis_sum_jumlah ?? 0, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2 pt-2 border-t border-slate-100">
                        <a href="{{ route('amil.donatur.show', $d->id) }}" class="flex-1 py-2 px-3 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs text-center transition">
                            Riwayat
                        </a>
                        <a href="{{ route('amil.donasi.create', ['donatur_id' => $d->id]) }}" class="flex-1 py-2 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs text-center shadow-sm transition flex items-center justify-center gap-1">
                            <span>Donasi Lagi</span>
                            <span>→</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $donaturs->links() }}
        </div>
    @endif
</div>
@endsection
