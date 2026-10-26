@extends('layouts.amil')

@section('title', 'Detail Agenda - ' . $agenda->judul)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div>
        <a href="{{ route('amil.agenda.index') }}" class="text-xs font-bold text-slate-500 hover:text-emerald-700 transition inline-flex items-center gap-1 mb-2">
            ← Kembali ke Daftar Agenda
        </a>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">{{ $agenda->judul }}</h1>
            @if ($agenda->status === 'berlangsung')
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 self-start sm:self-auto">
                    Sedang Berlangsung
                </span>
            @elseif ($agenda->status === 'selesai')
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 self-start sm:self-auto">
                    Selesai
                </span>
            @elseif ($agenda->status === 'dibatalkan')
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 self-start sm:self-auto">
                    Dibatalkan
                </span>
            @else
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 self-start sm:self-auto">
                    Terjadwal
                </span>
            @endif
        </div>
    </div>

    <!-- Info Card -->
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 space-y-6">
        <!-- Waktu & Tempat Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs">
            <div>
                <span class="text-slate-400 block mb-1">Waktu Pelaksanaan</span>
                <span class="font-extrabold text-slate-800 text-sm block">
                    {{ $agenda->mulai ? $agenda->mulai->translatedFormat('l, d F Y') : '-' }}
                </span>
                <span class="text-slate-500 font-mono mt-0.5 block">
                    {{ $agenda->mulai ? $agenda->mulai->format('H:i') : '' }} - {{ $agenda->selesai ? $agenda->selesai->format('H:i') : '' }} WIB
                </span>
            </div>

            <div>
                <span class="text-slate-400 block mb-1">Lokasi Kegiatan</span>
                <span class="font-extrabold text-slate-800 text-sm block">
                    📍 {{ $agenda->lokasi ?? 'Kantor / Sesuai Arahan' }}
                </span>
            </div>
        </div>

        <!-- Deskripsi Tugas -->
        <div>
            <h3 class="font-bold text-slate-800 text-sm mb-2">Deskripsi & Arahan Tugas</h3>
            <div class="p-4 rounded-2xl bg-white border border-slate-200 text-xs text-slate-700 leading-relaxed whitespace-pre-line">
                {{ $agenda->deskripsi }}
            </div>
        </div>

        <!-- Anggota Tim Amil yang Ditugaskan -->
        <div>
            <h3 class="font-bold text-slate-800 text-sm mb-3">Rekan Tim Amil yang Ditugaskan</h3>

            @if ($agenda->amils->isEmpty())
                <div class="p-4 rounded-xl bg-slate-50 text-xs text-slate-500">
                    Agenda ini bersifat umum / melibatkan seluruh personil amil yayasan.
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach ($agenda->amils as $teamMember)
                        <div class="p-3.5 rounded-2xl border border-slate-200 flex items-center justify-between text-xs hover:border-emerald-300 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-xs">
                                    {{ substr($teamMember->nama, 0, 2) }}
                                </div>
                                <div>
                                    <span class="font-bold text-slate-800 block">{{ $teamMember->nama }}</span>
                                    <span class="text-[11px] text-slate-400">{{ $teamMember->jabatan ?? 'Amil' }}</span>
                                </div>
                            </div>
                            @if ($teamMember->no_hp)
                                @php
                                    $clean = preg_replace('/[^0-9]/', '', $teamMember->no_hp);
                                    if (str_starts_with($clean, '0')) $clean = '62' . substr($clean, 1);
                                @endphp
                                <a href="https://wa.me/{{ $clean }}" target="_blank" class="text-emerald-600 hover:text-emerald-800 font-bold text-[11px]">
                                    💬 WA
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
