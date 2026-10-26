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
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200">
            <span class="text-xs font-semibold text-slate-400 block mb-1">Jatah Cuti (Tahun {{ $saldoCuti->tahun }})</span>
            <div class="text-3xl font-extrabold text-slate-800">{{ $saldoCuti->jatah }} <span class="text-sm font-semibold text-slate-500">Hari</span></div>
            <span class="text-[11px] text-slate-400 mt-1 block">Total kuota cuti tahunan</span>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200">
            <span class="text-xs font-semibold text-slate-400 block mb-1">Cuti Terpakai</span>
            <div class="text-3xl font-extrabold text-amber-600">{{ $saldoCuti->terpakai }} <span class="text-sm font-semibold text-slate-500">Hari</span></div>
            <span class="text-[11px] text-slate-400 mt-1 block">Total hari cuti disetujui</span>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200">
            <span class="text-xs font-semibold text-slate-400 block mb-1">Sisa Saldo Cuti</span>
            <div class="text-3xl font-extrabold text-emerald-700">{{ $sisaCuti }} <span class="text-sm font-semibold text-slate-500">Hari</span></div>
            <span class="text-[11px] text-slate-400 mt-1 block">Dapat digunakan hingga akhir tahun</span>
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
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
        <div class="flex items-center justify-between text-xs mb-2">
            <span class="font-bold text-slate-700 flex items-center gap-1.5">
                <span>📊</span> Penggunaan Kuota Cuti Tahunan
            </span>
            <div class="flex items-center gap-2">
                <span class="text-emerald-700 font-bold">Sisa {{ $sisaCuti }} Hari</span>
                <span class="text-slate-400">•</span>
                <span class="text-slate-500 font-semibold">{{ $persenTerpakai }}% Diambil</span>
            </div>
        </div>
        <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
            <div class="h-full rounded-full transition-all duration-500 {{ $barColorClass }}" style="width: {{ $persenTerpakai }}%;"></div>
        </div>
        <div class="flex justify-between items-center text-[11px] text-slate-400 mt-1.5">
            <span>0 Hari</span>
            <span class="font-medium text-slate-500">Terpakai {{ $saldoCuti->terpakai }} dari {{ $saldoCuti->jatah }} hari</span>
            <span>Total {{ $saldoCuti->jatah }} Hari</span>
        </div>
    </div>

    <!-- Riwayat Pengajuan Izin / Cuti -->
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
        <h3 class="font-bold text-slate-800 text-base mb-4">Riwayat Pengajuan Izin & Cuti</h3>

        @if ($pengajuans->isEmpty())
            <div class="p-8 text-center text-slate-400 text-xs">
                <svg class="w-12 h-12 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Belum ada permohonan izin atau cuti yang diajukan.
            </div>
        @else
            <!-- Table View -->
            <div class="overflow-x-auto">
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
                                        <span class="text-emerald-700">🏖️ Cuti</span>
                                    @elseif ($p->jenis === 'sakit')
                                        <span class="text-rose-600">🏥 Sakit</span>
                                    @else
                                        <span class="text-blue-600">📋 Izin</span>
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
