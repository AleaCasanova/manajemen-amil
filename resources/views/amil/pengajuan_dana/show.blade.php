@extends('layouts.amil')

@section('title', 'Detail Pengajuan ' . $pengajuanDana->nomor_pengajuan)

@section('content')
<div class="max-w-4xl mx-auto space-y-6 pb-28 sm:pb-12">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('amil.pengajuan-dana.index') }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-mono font-bold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-lg border border-emerald-100">
                        {{ $pengajuanDana->nomor_pengajuan }}
                    </span>
                    <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full border 
                        {{ match($pengajuanDana->status) {
                            'diajukan' => 'bg-amber-100 text-amber-800 border-amber-200',
                            'diverifikasi_bendahara' => 'bg-blue-100 text-blue-800 border-blue-200',
                            'disetujui' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                            'dicairkan' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                            'ditolak' => 'bg-rose-100 text-rose-800 border-rose-200',
                            default => 'bg-slate-100 text-slate-800 border-slate-200'
                        } }}">
                        {{ $pengajuanDana->status_label }}
                    </span>
                </div>
                <h1 class="text-lg sm:text-2xl font-extrabold text-slate-800 tracking-tight mt-1">{{ $pengajuanDana->judul }}</h1>
            </div>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-auto">
            <a href="{{ route('amil.pengajuan-dana.cetak', $pengajuanDana->id) }}" target="_blank" class="px-4 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-xs transition flex items-center gap-1.5">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Form Resmi</span>
            </a>
        </div>
    </div>

    <!-- Stepper Tracker Alur Otorisasi -->
    <div class="bg-white rounded-2xl sm:rounded-3xl p-5 sm:p-6 shadow-xs border border-slate-200">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Status & Alur Persetujuan Bertingkat</h3>
        
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 relative">
            <!-- Step 1: Diajukan -->
            <div class="p-3.5 rounded-2xl border bg-emerald-50/70 border-emerald-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-[10px] font-bold text-emerald-800 uppercase">Tahap 1</span>
                        <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    </div>
                    <div class="text-xs font-bold text-slate-800">Diajukan Amil</div>
                    <div class="text-[11px] text-slate-500 mt-0.5">{{ $pengajuanDana->amil?->nama ?? '-' }}</div>
                </div>
                <div class="text-[10px] text-slate-400 mt-2 font-mono">{{ $pengajuanDana->created_at->format('d/m/Y H:i') }}</div>
            </div>

            <!-- Step 2: Bendahara -->
            @php
                $isBendaharaPassed = in_array($pengajuanDana->status, ['diverifikasi_bendahara', 'disetujui', 'dicairkan']);
                $isBendaharaActive = $pengajuanDana->status === 'diajukan';
                $isRejectedBendahara = $pengajuanDana->status === 'ditolak' && $pengajuanDana->bendahara_id;
            @endphp
            <div class="p-3.5 rounded-2xl border {{ $isBendaharaPassed ? 'bg-emerald-50/70 border-emerald-200' : ($isRejectedBendahara ? 'bg-rose-50 border-rose-200' : ($isBendaharaActive ? 'bg-amber-50/80 border-amber-200 ring-2 ring-amber-400/50' : 'bg-slate-50 border-slate-200 opacity-60')) }} flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-[10px] font-bold {{ $isBendaharaPassed ? 'text-emerald-800' : ($isBendaharaActive ? 'text-amber-800' : 'text-slate-400') }} uppercase">Tahap 2</span>
                        @if($isBendaharaPassed)
                            <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        @elseif($isBendaharaActive)
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                        @endif
                    </div>
                    <div class="text-xs font-bold text-slate-800">Review Bendahara</div>
                    <div class="text-[11px] text-slate-500 mt-0.5">{{ $pengajuanDana->bendahara?->name ?? 'Belum diverifikasi' }}</div>
                </div>
                <div class="text-[10px] text-slate-400 mt-2 font-mono">{{ $pengajuanDana->tgl_verifikasi_bendahara ? $pengajuanDana->tgl_verifikasi_bendahara->format('d/m/Y H:i') : '-' }}</div>
            </div>

            <!-- Step 3: Manager -->
            @php
                $isManagerPassed = in_array($pengajuanDana->status, ['disetujui', 'dicairkan']);
                $isManagerActive = $pengajuanDana->status === 'diverifikasi_bendahara';
                $isRejectedManager = $pengajuanDana->status === 'ditolak' && !$isRejectedBendahara;
            @endphp
            <div class="p-3.5 rounded-2xl border {{ $isManagerPassed ? 'bg-emerald-50/70 border-emerald-200' : ($isRejectedManager ? 'bg-rose-50 border-rose-200' : ($isManagerActive ? 'bg-blue-50/80 border-blue-200 ring-2 ring-blue-400/50' : 'bg-slate-50 border-slate-200 opacity-60')) }} flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-[10px] font-bold {{ $isManagerPassed ? 'text-emerald-800' : ($isManagerActive ? 'text-blue-800' : 'text-slate-400') }} uppercase">Tahap 3</span>
                        @if($isManagerPassed)
                            <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        @elseif($isManagerActive)
                            <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                        @endif
                    </div>
                    <div class="text-xs font-bold text-slate-800">ACC Manager</div>
                    <div class="text-[11px] text-slate-500 mt-0.5">{{ $pengajuanDana->manager?->name ?? 'Belum di-ACC' }}</div>
                </div>
                <div class="text-[10px] text-slate-400 mt-2 font-mono">{{ $pengajuanDana->tgl_acc_manager ? $pengajuanDana->tgl_acc_manager->format('d/m/Y H:i') : '-' }}</div>
            </div>

            <!-- Step 4: Pencairan -->
            @php
                $isCair = $pengajuanDana->status === 'dicairkan';
                $isSiapCair = $pengajuanDana->status === 'disetujui';
            @endphp
            <div class="p-3.5 rounded-2xl border {{ $isCair ? 'bg-emerald-50/70 border-emerald-200' : ($isSiapCair ? 'bg-indigo-50/80 border-indigo-200 ring-2 ring-indigo-400/50' : 'bg-slate-50 border-slate-200 opacity-60')) }} flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-[10px] font-bold {{ $isCair ? 'text-emerald-800' : ($isSiapCair ? 'text-indigo-800' : 'text-slate-400') }} uppercase">Tahap 4</span>
                        @if($isCair)
                            <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        @elseif($isSiapCair)
                            <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                        @endif
                    </div>
                    <div class="text-xs font-bold text-slate-800">Pencairan Kas</div>
                    <div class="text-[11px] text-slate-500 mt-0.5">{{ $pengajuanDana->akunKeuangan?->nama ?? 'Kas / Bank' }}</div>
                </div>
                <div class="text-[10px] text-slate-400 mt-2 font-mono">{{ $pengajuanDana->tgl_pencairan ? $pengajuanDana->tgl_pencairan->format('d/m/Y') : '-' }}</div>
            </div>
        </div>
    </div>

    <!-- Rincian Informasi & Nominal -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Kolom Kiri: Rincian -->
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl sm:rounded-3xl p-5 sm:p-6 shadow-xs border border-slate-200 space-y-4">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2">Rincian Permohonan Anggaran</h3>

                <div class="grid grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 block mb-0.5">Pemohon (Amil)</span>
                        <span class="font-bold text-slate-800">{{ $pengajuanDana->amil?->nama ?? '-' }}</span>
                        <span class="text-slate-500 block text-[11px]">{{ $pengajuanDana->amil?->divisi?->nama ?? 'Divisi Umum' }}</span>
                    </div>

                    <div>
                        <span class="text-slate-400 block mb-0.5">Tanggal Kebutuhan</span>
                        <span class="font-bold text-emerald-700">{{ $pengajuanDana->tgl_kebutuhan ? $pengajuanDana->tgl_kebutuhan->format('d F Y') : '-' }}</span>
                    </div>

                    <div>
                        <span class="text-slate-400 block mb-0.5">Pos Kategori Anggaran</span>
                        <span class="font-semibold text-slate-800">{{ $pengajuanDana->kategoriKeuangan?->nama ?? 'Umum / Penyaluran' }}</span>
                    </div>

                    <div>
                        <span class="text-slate-400 block mb-0.5">Program Terkait</span>
                        <span class="font-semibold text-slate-800">{{ $pengajuanDana->program?->nama ?? 'Tidak Terkait Program' }}</span>
                    </div>

                    @if($pengajuanDana->nomor_dokumen_referensi)
                        <div class="col-span-2 pt-2 border-t border-slate-100">
                            <span class="text-slate-400 block mb-0.5">Nomor Dokumen Referensi (Memo / Proposal)</span>
                            <span class="font-mono font-bold text-slate-800">{{ $pengajuanDana->nomor_dokumen_referensi }}</span>
                        </div>
                    @endif
                </div>

                <div class="pt-2">
                    <span class="text-slate-400 text-xs block mb-1.5 font-bold uppercase tracking-wider">Uraian Kebutuhan & RAB:</span>
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 text-xs text-slate-700 whitespace-pre-line leading-relaxed font-mono">
                        {{ $pengajuanDana->deskripsi }}
                    </div>
                </div>

                @if($pengajuanDana->dokumen_pendukung)
                    <div class="pt-2">
                        <span class="text-slate-400 text-xs block mb-1.5 font-bold uppercase tracking-wider">Dokumen Lampiran Pendukung:</span>
                        <a href="{{ asset('storage/' . $pengajuanDana->dokumen_pendukung) }}" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Unduh / Buka Dokumen Pendukung</span>
                        </a>
                    </div>
                @endif
            </div>

            <!-- Catatan Otorisasi Jika Ada -->
            @if($pengajuanDana->catatan_bendahara || $pengajuanDana->catatan_manager || $pengajuanDana->alasan_penolakan)
                <div class="bg-white rounded-2xl sm:rounded-3xl p-5 sm:p-6 shadow-xs border border-slate-200 space-y-3">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2">Catatan Otorisasi Pimpinan</h3>
                    
                    @if($pengajuanDana->catatan_bendahara)
                        <div class="p-3.5 rounded-xl bg-blue-50 border border-blue-100 text-xs">
                            <span class="font-bold text-blue-900 block mb-0.5">Catatan Verifikasi Bendahara:</span>
                            <p class="text-blue-800">{{ $pengajuanDana->catatan_bendahara }}</p>
                        </div>
                    @endif

                    @if($pengajuanDana->catatan_manager)
                        <div class="p-3.5 rounded-xl bg-indigo-50 border border-indigo-100 text-xs">
                            <span class="font-bold text-indigo-900 block mb-0.5">Catatan Persetujuan Manager:</span>
                            <p class="text-indigo-800">{{ $pengajuanDana->catatan_manager }}</p>
                        </div>
                    @endif

                    @if($pengajuanDana->alasan_penolakan)
                        <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-100 text-xs">
                            <span class="font-bold text-rose-900 block mb-0.5">Alasan Penolakan:</span>
                            <p class="text-rose-800">{{ $pengajuanDana->alasan_penolakan }}</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Kolom Kanan: Card Nominal & Info Kas -->
        <div class="space-y-6">
            <!-- Box Nominal -->
            <div class="bg-gradient-to-br from-emerald-700 to-teal-800 rounded-2xl sm:rounded-3xl p-5 sm:p-6 text-white shadow-md shadow-emerald-900/10">
                <span class="text-xs uppercase font-bold text-emerald-200 block mb-1">
                    {{ $pengajuanDana->nominal_disetujui ? 'Nominal Disetujui (ACC)' : 'Nominal Diajukan' }}
                </span>
                <div class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                    Rp {{ number_format((float) $pengajuanDana->nominal_final, 0, ',', '.') }}
                </div>

                @if($pengajuanDana->nominal_disetujui && $pengajuanDana->nominal_disetujui != $pengajuanDana->nominal_diajukan)
                    <div class="mt-2 pt-2 border-t border-emerald-600/60 text-xs text-emerald-200">
                        Nominal pengajuan awal: <span class="font-bold">Rp {{ number_format($pengajuanDana->nominal_diajukan, 0, ',', '.') }}</span>
                    </div>
                @endif
            </div>

            <!-- Box Pencairan Jika Sudah Cair -->
            @if($pengajuanDana->status === 'dicairkan')
                <div class="bg-white rounded-2xl sm:rounded-3xl p-5 shadow-xs border border-emerald-200 bg-emerald-50/20 space-y-3 text-xs">
                    <div class="flex items-center gap-2 text-emerald-700 font-bold uppercase text-[11px]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Realisasi Pencairan Kas</span>
                    </div>

                    <div class="space-y-1.5 text-slate-600">
                        <div>
                            <span class="text-slate-400 block text-[11px]">Sumber Rekening:</span>
                            <span class="font-bold text-slate-800">{{ $pengajuanDana->akunKeuangan?->nama ?? 'Kas Tunai' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[11px]">Metode:</span>
                            <span class="font-bold uppercase text-slate-800">{{ $pengajuanDana->metode_pencairan }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[11px]">Tanggal Cair:</span>
                            <span class="font-bold text-slate-800">{{ $pengajuanDana->tgl_pencairan ? $pengajuanDana->tgl_pencairan->format('d M Y') : '-' }}</span>
                        </div>
                        @if($pengajuanDana->transaksiKeuangan)
                            <div>
                                <span class="text-slate-400 block text-[11px]">Kode Transaksi Buku Kas:</span>
                                <span class="font-mono font-bold text-emerald-600">{{ $pengajuanDana->transaksiKeuangan->kode_transaksi }}</span>
                            </div>
                        @endif
                    </div>

                    @if($pengajuanDana->bukti_pencairan)
                        <div class="pt-2 border-t border-slate-100">
                            <a href="{{ asset('storage/' . $pengajuanDana->bukti_pencairan) }}" target="_blank" class="text-emerald-600 hover:text-emerald-700 font-bold inline-flex items-center gap-1">
                                <span>Lihat Bukti Struk / Transfer</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
