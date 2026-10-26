@extends('layouts.amil')

@section('title', 'Profil Donatur - ' . $donatur->nama)

@section('content')
<div class="space-y-6">
    <!-- Back Link & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('amil.donatur.index') }}" class="text-xs font-bold text-slate-500 hover:text-emerald-700 transition inline-flex items-center gap-1 mb-2">
                ← Kembali ke Donatur Saya
            </a>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">{{ $donatur->is_anonim ? 'Hamba Allah (' . $donatur->nama . ')' : $donatur->nama }}</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                    {{ $donatur->kategori?->nama ?? 'Umum' }}
                </span>
            </div>
            <p class="text-xs text-slate-400 font-mono mt-0.5">Kode Donatur: {{ $donatur->kode_donatur }}</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @php
                $cleanPhone = preg_replace('/[^0-9]/', '', $donatur->no_hp ?? '');
                if (str_starts_with($cleanPhone, '0')) {
                    $cleanPhone = '62' . substr($cleanPhone, 1);
                }
            @endphp
            @if ($cleanPhone)
                <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" class="px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs transition inline-flex items-center gap-1.5 shadow-sm">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    <span>Hubungi WhatsApp</span>
                </a>
            @endif

            <a href="{{ route('amil.donasi.create', ['donatur_id' => $donatur->id]) }}" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-md shadow-emerald-600/20 transition inline-flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>+ Input Donasi</span>
            </a>
        </div>
    </div>

    <!-- Stats & Bio Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Kolom Info Donatur -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200 space-y-4">
            <h3 class="font-bold text-slate-800 text-sm border-b border-slate-100 pb-3">Informasi Donatur</h3>

            <div class="space-y-3 text-xs">
                <div>
                    <span class="text-slate-400 block mb-0.5">Nomor WhatsApp / Kontak</span>
                    <span class="font-bold text-slate-800">{{ $donatur->no_hp }}</span>
                </div>

                <div>
                    <span class="text-slate-400 block mb-0.5">Email</span>
                    <span class="font-semibold text-slate-700">{{ $donatur->email ?? '-' }}</span>
                </div>

                <div>
                    <span class="text-slate-400 block mb-0.5">Tipe Donatur</span>
                    <span class="font-semibold text-slate-700 capitalize">{{ $donatur->jenis }}</span>
                </div>

                <div>
                    <span class="text-slate-400 block mb-0.5">Domisili / Kota</span>
                    <span class="font-semibold text-slate-700">{{ $donatur->kota ?? '-' }}, {{ $donatur->provinsi ?? '' }}</span>
                </div>

                <div>
                    <span class="text-slate-400 block mb-0.5">Alamat</span>
                    <span class="text-slate-600">{{ $donatur->alamat ?? '-' }}</span>
                </div>

                @if ($donatur->nik)
                    <div>
                        <span class="text-slate-400 block mb-0.5">NIK</span>
                        <span class="font-mono text-slate-700">{{ $donatur->nik }}</span>
                    </div>
                @endif

                @if ($donatur->npwp)
                    <div>
                        <span class="text-slate-400 block mb-0.5">NPWP</span>
                        <span class="font-mono text-slate-700">{{ $donatur->npwp }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Kolom Riwayat Donasi & Metrik -->
        <div class="md:col-span-2 space-y-6">
            <!-- Metrik Cards -->
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200">
                    <span class="text-xs font-semibold text-slate-400 block mb-1">Total Kontribusi</span>
                    <div class="text-2xl font-extrabold text-emerald-700">
                        Rp {{ number_format($donatur->donasis()->sum('jumlah'), 0, ',', '.') }}
                    </div>
                    <span class="text-[11px] text-slate-400 mt-1 block">Akumulasi seluruh donasi</span>
                </div>

                <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200">
                    <span class="text-xs font-semibold text-slate-400 block mb-1">Frekuensi Transaksi</span>
                    <div class="text-2xl font-extrabold text-slate-800">
                        {{ $donatur->donasis()->count() }} Kali
                    </div>
                    <span class="text-[11px] text-slate-400 mt-1 block">Riwayat donasi tercatat</span>
                </div>
            </div>

            <!-- List Riwayat Donasi -->
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200">
                <h3 class="font-bold text-slate-800 text-sm border-b border-slate-100 pb-3 mb-4">Riwayat Donasi Donatur Ini</h3>

                @if ($donasis->isEmpty())
                    <p class="text-xs text-slate-400 py-6 text-center">Belum ada catatan transaksi donasi dari donatur ini.</p>
                @else
                    <div class="space-y-3">
                        @foreach ($donasis as $d)
                            <div class="p-3.5 rounded-xl border border-slate-100 hover:border-emerald-200 bg-slate-50/50 transition flex items-center justify-between text-xs">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('amil.donasi.success', $d->id) }}" class="font-mono font-bold text-emerald-700 hover:underline">
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
                                    @php
                                        $jamD = ($d->tgl_donasi && $d->tgl_donasi->format('H:i') !== '00:00')
                                            ? $d->tgl_donasi->format('H:i')
                                            : ($d->created_at ? $d->created_at->format('H:i') : null);
                                    @endphp
                                    <span class="text-[11px] text-slate-500 block mt-1">
                                        {{ $d->tgl_donasi ? $d->tgl_donasi->format('d M Y') : '-' }}{{ $jamD ? ', ' . $jamD . ' WIB' : '' }} • {{ $d->jenisDana?->nama ?? '-' }} ({{ $d->program?->nama ?? 'Umum' }})
                                    </span>
                                </div>

                                <div class="text-right">
                                    <span class="font-extrabold text-slate-900 block text-sm">
                                        Rp {{ number_format($d->jumlah, 0, ',', '.') }}
                                    </span>
                                    <a href="{{ route('kwitansi.download', $d->id) }}" class="text-[11px] font-bold text-emerald-600 hover:underline">
                                        Unduh PDF
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        {{ $donasis->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
