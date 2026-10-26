@extends('layouts.amil')

@section('title', 'Izin & Cuti Amil')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Manajemen Izin & Cuti</h1>
            <p class="text-sm text-slate-500">Pantau sisa hak cuti dan ajukan permohonan izin kerja secara online</p>
        </div>
        <a href="{{ route('amil.izin-cuti.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>+ Ajukan Izin / Cuti</span>
        </a>
    </div>

    <!-- Saldo Cuti Stats Cards -->
    <div class="grid grid-cols-3 gap-2 sm:gap-4">
        <div class="bg-white rounded-2xl p-3 sm:p-5 shadow-sm border border-slate-200 text-center sm:text-left">
            <span class="text-[10px] sm:text-xs font-semibold text-slate-400 block mb-1 truncate">Jatah ({{ $saldoCuti->tahun }})</span>
            <div class="text-xl sm:text-3xl font-extrabold text-slate-800">{{ $saldoCuti->jatah }} <span class="text-xs sm:text-sm font-semibold text-slate-500">Hari</span></div>
            <span class="text-[10px] sm:text-[11px] text-slate-400 mt-0.5 sm:mt-1 hidden sm:block">Total kuota tahunan</span>
        </div>

        <div class="bg-white rounded-2xl p-3 sm:p-5 shadow-sm border border-slate-200 text-center sm:text-left">
            <span class="text-[10px] sm:text-xs font-semibold text-slate-400 block mb-1 truncate">Terpakai</span>
            <div class="text-xl sm:text-3xl font-extrabold text-amber-600">{{ $saldoCuti->terpakai }} <span class="text-xs sm:text-sm font-semibold text-slate-500">Hari</span></div>
            <span class="text-[10px] sm:text-[11px] text-slate-400 mt-0.5 sm:mt-1 hidden sm:block">Total disetujui</span>
        </div>

        <div class="bg-white rounded-2xl p-3 sm:p-5 shadow-sm border border-slate-200 text-center sm:text-left">
            <span class="text-[10px] sm:text-xs font-semibold text-slate-400 block mb-1 truncate">Sisa Cuti</span>
            <div class="text-xl sm:text-3xl font-extrabold text-emerald-700">{{ $sisaCuti }} <span class="text-xs sm:text-sm font-semibold text-slate-500">Hari</span></div>
            <span class="text-[10px] sm:text-[11px] text-slate-400 mt-0.5 sm:mt-1 hidden sm:block">Dapat digunakan</span>
        </div>
    </div>

    <!-- Grafik Garis Sederhana Persentase Penggunaan Cuti -->
    @php
        $persenTerpakai = $saldoCuti->jatah > 0 ? min(100, round(($saldoCuti->terpakai / $saldoCuti->jatah) * 100)) : 0;
        $barColorClass = match (true) {
            $persenTerpakai >= 80 => 'bg-rose-500',
            $persenTerpakai >= 50 => 'bg-amber-500',
            default => 'bg-emerald-500',
        };
    @endphp
    <div class="bg-white rounded-2xl p-3.5 sm:p-4 shadow-sm border border-slate-200">
        <div class="flex items-center justify-between text-xs mb-2">
            <span class="font-bold text-slate-700 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span>Kuota Cuti Tahunan</span>
            </span>
            <div class="flex items-center gap-2">
                <span class="text-emerald-700 font-bold">Sisa {{ $sisaCuti }} Hari</span>
                <span class="text-slate-400">•</span>
                <span class="text-slate-500 font-semibold">{{ $persenTerpakai }}%</span>
            </div>
        </div>
        <div class="w-full bg-slate-100 rounded-full h-2.5 sm:h-3 overflow-hidden">
            <div class="h-full rounded-full transition-all duration-500 {{ $barColorClass }}" style="width: {{ $persenTerpakai }}%;"></div>
        </div>
        <div class="flex justify-between items-center text-[10px] sm:text-[11px] text-slate-400 mt-1.5">
            <span>0 Hari</span>
            <span class="font-medium text-slate-500">Terpakai {{ $saldoCuti->terpakai }} dari {{ $saldoCuti->jatah }} hari</span>
            <span>Total {{ $saldoCuti->jatah }} Hari</span>
        </div>
    </div>

    <!-- Riwayat Pengajuan Izin / Cuti -->
    <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-sm border border-slate-200">
        <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
            <h3 class="font-bold text-slate-800 text-sm sm:text-base">Riwayat Pengajuan</h3>
            <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-lg">{{ $pengajuans->total() }} Data</span>
        </div>

        @if ($pengajuans->isEmpty())
            <div class="p-8 text-center text-slate-400 text-xs">
                <svg class="w-10 h-10 sm:w-12 sm:h-12 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Belum ada permohonan izin atau cuti yang diajukan.
            </div>
        @else
            <!-- 1. Mobile Feed (< md) -->
            <div class="md:hidden divide-y divide-slate-100">
                @foreach ($pengajuans as $p)
                    @php
                        $mulai = \Carbon\Carbon::parse($p->tgl_mulai);
                        $selesai = \Carbon\Carbon::parse($p->tgl_selesai);
                        $durasi = $mulai->diffInDays($selesai) + 1;
                    @endphp
                    <div class="py-3.5 first:pt-0 last:pb-0 space-y-2">
                        <div class="flex items-center justify-between gap-2">
                            <div class="inline-flex items-center gap-1.5">
                                @if ($p->jenis === 'cuti')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                        <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <span>Cuti</span>
                                    </span>
                                @elseif ($p->jenis === 'sakit')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200/60">
                                        <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                        <span>Sakit</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200/60">
                                        <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span>Izin</span>
                                    </span>
                                @endif
                                <span class="text-xs font-bold text-slate-800">{{ $durasi }} Hari</span>
                            </div>

                            @if ($p->status === 'disetujui')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Disetujui</span>
                            @elseif ($p->status === 'ditolak')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">Ditolak</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Menunggu</span>
                            @endif
                        </div>

                        <div class="text-xs text-slate-600">
                            <span class="text-slate-400 block text-[10px]">Periode:</span>
                            <span class="font-medium text-slate-700">{{ $mulai->translatedFormat('d M') }} - {{ $selesai->translatedFormat('d M Y') }}</span>
                        </div>

                        <p class="text-xs text-slate-600 bg-slate-50 p-2.5 rounded-xl border border-slate-100 leading-snug">
                            {{ $p->alasan }}
                        </p>

                        @if ($p->catatan_admin)
                            <div class="text-[11px] text-rose-600 italic">
                                Catatan Admin: "{{ $p->catatan_admin }}"
                            </div>
                        @endif

                        @if ($p->lampiran)
                            <div class="pt-1">
                                <a href="{{ asset('storage/' . $p->lampiran) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 hover:underline">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                    <span>Buka Lampiran</span>
                                </a>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- 2. Desktop Table (>= md) -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-100 text-slate-400 uppercase font-semibold">
                        <tr>
                            <th class="py-3 px-4">Tipe</th>
                            <th class="py-3 px-4">Periode Tanggal</th>
                            <th class="py-3 px-4">Durasi</th>
                            <th class="py-3 px-4">Alasan</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center">Lampiran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($pengajuans as $p)
                            @php
                                $mulai = \Carbon\Carbon::parse($p->tgl_mulai);
                                $selesai = \Carbon\Carbon::parse($p->tgl_selesai);
                                $durasi = $mulai->diffInDays($selesai) + 1;
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3.5 px-4 font-bold text-slate-800 uppercase">
                                    @if ($p->jenis === 'cuti')
                                        <span class="inline-flex items-center gap-1.5 text-emerald-700">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <span>Cuti</span>
                                        </span>
                                    @elseif ($p->jenis === 'sakit')
                                        <span class="inline-flex items-center gap-1.5 text-rose-600">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                            <span>Sakit</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-blue-600">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            <span>Izin</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 font-medium text-slate-700">
                                    {{ $mulai->translatedFormat('d M Y') }} - {{ $selesai->translatedFormat('d M Y') }}
                                </td>
                                <td class="py-3.5 px-4 font-semibold text-slate-800">
                                    {{ $durasi }} Hari
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 max-w-xs">
                                    <span>{{ $p->alasan }}</span>
                                    @if ($p->catatan_admin)
                                        <span class="block text-[11px] text-rose-600 mt-1 italic">Catatan Admin: "{{ $p->catatan_admin }}"</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if ($p->status === 'disetujui')
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                            Disetujui
                                        </span>
                                    @elseif ($p->status === 'ditolak')
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                            Ditolak
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                            Menunggu Review
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if ($p->lampiran)
                                        <a href="{{ asset('storage/' . $p->lampiran) }}" target="_blank" class="text-emerald-600 hover:text-emerald-800 font-bold underline inline-flex items-center gap-1">
                                            <span>Lihat</span>
                                        </a>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $pengajuans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
