@extends('layouts.amil')

@section('title', 'Riwayat Donasi Saya')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Riwayat Donasi Saya</h1>
            <p class="text-sm text-slate-500">Semua donasi yang telah Anda himpun untuk Gerakan Sedekah Cepat</p>
        </div>
        <a href="{{ route('amil.donasi.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>+ Input Donasi Baru</span>
        </a>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
        <form action="{{ route('amil.donasi.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
            <div class="sm:col-span-7 relative">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nomor kwitansi, nama donatur, atau no HP..." class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <div class="sm:col-span-3">
                <select name="status" class="w-full py-2 px-3 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">Semua Status</option>
                    <option value="menunggu_verifikasi" {{ request('status') === 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="terverifikasi" {{ request('status') === 'terverifikasi' ? 'selected' : '' }}>Terverifikasi</option>
                    <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <div class="sm:col-span-2 flex gap-2">
                <button type="submit" class="w-full py-2 px-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition">
                    Filter
                </button>
                @if (request()->hasAny(['q', 'status']))
                    <a href="{{ route('amil.donasi.index') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition flex items-center justify-center" title="Reset Filter">
                        ✕
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- List Donasi -->
    @if ($donasis->isEmpty())
        <div class="bg-white rounded-2xl p-10 text-center border border-slate-200 shadow-sm">
            <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h3 class="font-bold text-slate-800 text-base">Belum Ada Catatan Donasi</h3>
            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Anda belum menghimpun donasi yang sesuai dengan filter pencarian ini.</p>
            <a href="{{ route('amil.donasi.create') }}" class="inline-flex items-center gap-1.5 mt-4 px-4 py-2 rounded-xl bg-emerald-600 text-white font-bold text-xs shadow-sm hover:bg-emerald-700 transition">
                + Input Donasi Sekarang
            </a>
        </div>
    @else
        <!-- Desktop Table View -->
        <div class="hidden md:block bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-semibold tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">No. Kwitansi</th>
                        <th class="py-3.5 px-4">Tanggal</th>
                        <th class="py-3.5 px-4">Donatur</th>
                        <th class="py-3.5 px-4">Jenis & Program</th>
                        <th class="py-3.5 px-4 text-right">Nominal</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($donasis as $d)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3.5 px-4">
                                <a href="{{ route('amil.donasi.success', $d->id) }}" class="font-mono font-bold text-emerald-700 hover:underline">
                                    {{ $d->no_kwitansi }}
                                </a>
                                <span class="block text-[10px] text-slate-400 capitalize">{{ $d->metode_bayar }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 font-medium">
                                <span class="block text-slate-800 font-bold">{{ $d->tgl_donasi ? $d->tgl_donasi->translatedFormat('d M Y') : '-' }}</span>
                                <span class="text-[11px] text-slate-400 font-mono">{{ $d->tgl_donasi && $d->tgl_donasi->format('H:i') !== '00:00' ? $d->tgl_donasi->format('H:i') . ' WIB' : ($d->created_at ? $d->created_at->format('H:i') . ' WIB' : '') }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-800 block">
                                    {{ $d->donatur?->is_anonim ? 'Hamba Allah' : ($d->donatur?->nama ?? 'Donatur') }}
                                </span>
                                <span class="text-[11px] text-slate-400">{{ $d->donatur?->no_hp ?? '-' }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-semibold text-slate-700 block">{{ $d->jenisDana?->nama ?? '-' }}</span>
                                <span class="text-[11px] text-slate-400">{{ $d->program?->nama ?? 'Umum' }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-right font-extrabold text-slate-800 text-sm">
                                Rp {{ number_format($d->jumlah, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if ($d->status === 'terverifikasi')
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        Terverifikasi
                                    </span>
                                @elseif ($d->status === 'ditolak')
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                        Ditolak
                                    </span>
                                @else
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                        Menunggu
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('amil.donasi.success', $d->id) }}" class="p-1.5 text-slate-600 hover:text-emerald-700 rounded-lg hover:bg-slate-100 transition" title="Lihat Detail & Kwitansi">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('kwitansi.download', $d->id) }}" class="p-1.5 text-emerald-600 hover:text-emerald-800 rounded-lg hover:bg-emerald-50 transition" title="Download PDF Kwitansi">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden space-y-3">
            @foreach ($donasis as $d)
                <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200 space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                        <a href="{{ route('amil.donasi.success', $d->id) }}" class="font-mono font-bold text-xs text-emerald-700">
                            {{ $d->no_kwitansi }}
                        </a>
                        @if ($d->status === 'terverifikasi')
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Terverifikasi</span>
                        @elseif ($d->status === 'ditolak')
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">Ditolak</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Menunggu</span>
                        @endif
                    </div>

                    <div class="flex justify-between items-start">
                        <div>
                            <span class="font-bold text-slate-800 text-sm block">
                                {{ $d->donatur?->is_anonim ? 'Hamba Allah' : ($d->donatur?->nama ?? 'Donatur') }}
                            </span>
                            <span class="text-xs text-slate-500">{{ $d->jenisDana?->nama ?? '-' }} • {{ $d->program?->nama ?? 'Umum' }}</span>
                        </div>
                        <div class="text-right">
                            <span class="font-extrabold text-slate-900 text-sm block">Rp {{ number_format($d->jumlah, 0, ',', '.') }}</span>
                            @php
                                $jamMobile = ($d->tgl_donasi && $d->tgl_donasi->format('H:i') !== '00:00')
                                    ? $d->tgl_donasi->format('H:i')
                                    : ($d->created_at ? $d->created_at->format('H:i') : null);
                            @endphp
                            <span class="text-[10px] text-slate-400 capitalize">
                                {{ $d->tgl_donasi ? $d->tgl_donasi->format('d M Y') : '-' }}{{ $jamMobile ? ', ' . $jamMobile . ' WIB' : '' }}
                            </span>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-[11px] text-slate-400 uppercase font-medium">{{ $d->metode_bayar }}</span>
                        <div class="flex gap-2">
                            <a href="{{ route('amil.donasi.success', $d->id) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-bold text-[11px] hover:bg-slate-200">
                                Detail
                            </a>
                            <a href="{{ route('kwitansi.download', $d->id) }}" class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 font-bold text-[11px] hover:bg-emerald-100">
                                PDF
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $donasis->links() }}
        </div>
    @endif
</div>
@endsection
