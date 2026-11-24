@extends('layouts.amil')

@section('title', 'Pengajuan Dana & Kasbon Kegiatan')

@section('content')
<div class="space-y-6 pb-28 sm:pb-12">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Pengajuan Dana & Anggaran</h1>
            <p class="text-sm text-slate-500">Ajukan permohonan dana kasbon program, operasional lapangan, dan logistik ke Bendahara & Manager</p>
        </div>
        <a href="{{ route('amil.pengajuan-dana.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>+ Buat Pengajuan Baru</span>
        </a>
    </div>

    @if (session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2.5">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Stat Cards Overview -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Diajukan</div>
            <div class="text-base sm:text-lg font-extrabold text-slate-800 mt-1">Rp {{ number_format($totalNominalDiajukan, 0, ',', '.') }}</div>
            <div class="text-[10px] text-slate-500 mt-0.5">Akumulasi permohonan</div>
        </div>

        <div class="bg-amber-50/60 p-4 rounded-2xl border border-amber-200/80 shadow-xs">
            <div class="text-[11px] font-bold uppercase tracking-wider text-amber-700">Menunggu Review</div>
            <div class="text-base sm:text-lg font-extrabold text-amber-900 mt-1">{{ $countMenunggu }} Pengajuan</div>
            <div class="text-[10px] text-amber-700/80 mt-0.5">Bendahara / Manager</div>
        </div>

        <div class="bg-blue-50/60 p-4 rounded-2xl border border-blue-200/80 shadow-xs">
            <div class="text-[11px] font-bold uppercase tracking-wider text-blue-700">Disetujui (ACC)</div>
            <div class="text-base sm:text-lg font-extrabold text-blue-900 mt-1">{{ $countDisetujui }} Pengajuan</div>
            <div class="text-[10px] text-blue-700/80 mt-0.5">Siap dicairkan bendahara</div>
        </div>

        <div class="bg-emerald-50/60 p-4 rounded-2xl border border-emerald-200/80 shadow-xs">
            <div class="text-[11px] font-bold uppercase tracking-wider text-emerald-700">Telah Dicairkan</div>
            <div class="text-base sm:text-lg font-extrabold text-emerald-900 mt-1">{{ $countDicairkan }} Selesai</div>
            <div class="text-[10px] text-emerald-700/80 mt-0.5">Dana masuk ke kasbon</div>
        </div>
    </div>

    <!-- Search Bar with Filter Icon Button -->
    <div class="bg-white rounded-2xl p-3 sm:p-4 shadow-xs border border-slate-200">
        <form action="{{ route('amil.pengajuan-dana.index') }}" method="GET" class="flex items-center gap-2">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            <!-- Search Input -->
            <div class="relative flex-1">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nomor, keperluan, atau memo..." class="w-full pl-9 pr-8 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                @if (request()->filled('q'))
                    <a href="{{ route('amil.pengajuan-dana.index', ['status' => request('status')]) }}" class="absolute right-2.5 top-2.5 p-0.5 text-slate-400 hover:text-slate-600 transition" title="Hapus teks pencarian">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
            </div>

            <!-- Tombol Cari -->
            <button type="submit" class="py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs sm:text-sm transition flex items-center justify-center gap-1.5 shrink-0 shadow-xs cursor-pointer">
                <span>Cari</span>
            </button>

            <!-- Tombol Icon Filter di Samping Tombol Cari -->
            <div class="relative shrink-0">
                <button type="button" id="filterDropdownBtn" onclick="toggleFilterDropdown(event)" class="relative p-2.5 rounded-xl border transition flex items-center justify-center cursor-pointer {{ request('status') ? 'bg-emerald-50 border-emerald-400 text-emerald-700 ring-2 ring-emerald-500/20' : 'bg-slate-100 hover:bg-slate-200 border-slate-200 text-slate-700' }}" title="Filter Status Pengajuan">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    @if(request('status'))
                        <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-emerald-600 rounded-full border-2 border-white"></span>
                    @endif
                </button>

                <!-- Popover Dropdown Menu Filter Status -->
                <div id="filterDropdownMenu" class="hidden absolute right-0 top-full mt-2 w-56 sm:w-60 bg-white rounded-2xl shadow-xl border border-slate-200 p-2 z-50 divide-y divide-slate-100">
                    <div class="px-3 py-2 text-[11px] font-bold uppercase tracking-wider text-slate-400 flex items-center justify-between">
                        <span>Filter Status</span>
                        @if(request('status'))
                            <a href="{{ route('amil.pengajuan-dana.index', ['q' => request('q')]) }}" class="text-rose-600 hover:underline normal-case text-[11px] font-bold">Reset</a>
                        @endif
                    </div>
                    <div class="py-1 space-y-0.5">
                        <a href="{{ route('amil.pengajuan-dana.index', ['q' => request('q')]) }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold {{ !request('status') ? 'bg-emerald-50 text-emerald-800 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                            <span>Semua Status</span>
                            @if(!request('status'))
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            @endif
                        </a>
                        <a href="{{ route('amil.pengajuan-dana.index', ['status' => 'diajukan', 'q' => request('q')]) }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold {{ request('status') === 'diajukan' ? 'bg-amber-50 text-amber-800 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                            <span class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                <span>Review Bendahara</span>
                            </span>
                            @if(request('status') === 'diajukan')
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            @endif
                        </a>
                        <a href="{{ route('amil.pengajuan-dana.index', ['status' => 'diverifikasi_bendahara', 'q' => request('q')]) }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold {{ request('status') === 'diverifikasi_bendahara' ? 'bg-blue-50 text-blue-800 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                            <span class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                <span>ACC Manager</span>
                            </span>
                            @if(request('status') === 'diverifikasi_bendahara')
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            @endif
                        </a>
                        <a href="{{ route('amil.pengajuan-dana.index', ['status' => 'disetujui', 'q' => request('q')]) }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold {{ request('status') === 'disetujui' ? 'bg-indigo-50 text-indigo-800 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                            <span class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                                <span>Siap Cair</span>
                            </span>
                            @if(request('status') === 'disetujui')
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            @endif
                        </a>
                        <a href="{{ route('amil.pengajuan-dana.index', ['status' => 'dicairkan', 'q' => request('q')]) }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold {{ request('status') === 'dicairkan' ? 'bg-emerald-50 text-emerald-800 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                            <span class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span>Dicairkan</span>
                            </span>
                            @if(request('status') === 'dicairkan')
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            @endif
                        </a>
                        <a href="{{ route('amil.pengajuan-dana.index', ['status' => 'ditolak', 'q' => request('q')]) }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold {{ request('status') === 'ditolak' ? 'bg-rose-50 text-rose-800 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                            <span class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                <span>Ditolak</span>
                            </span>
                            @if(request('status') === 'ditolak')
                                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            @endif
                        </a>
                    </div>
                </div>
            </div>
        </form>

        <!-- Active Filter Chip -->
        @if(request('status'))
            <div class="flex items-center gap-2 pt-2.5 mt-2.5 border-t border-slate-100 text-xs">
                <span class="text-slate-400 font-medium text-[11px]">Filter aktif:</span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full font-bold text-[11px] border 
                    {{ match(request('status')) {
                        'diajukan' => 'bg-amber-50 text-amber-800 border-amber-200',
                        'diverifikasi_bendahara' => 'bg-blue-50 text-blue-800 border-blue-200',
                        'disetujui' => 'bg-indigo-50 text-indigo-800 border-indigo-200',
                        'dicairkan' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                        'ditolak' => 'bg-rose-50 text-rose-800 border-rose-200',
                        default => 'bg-slate-100 text-slate-800 border-slate-200'
                    } }}">
                    <span>
                        {{ match(request('status')) {
                            'diajukan' => 'Review Bendahara',
                            'diverifikasi_bendahara' => 'ACC Manager',
                            'disetujui' => 'Siap Cair',
                            'dicairkan' => 'Dicairkan',
                            'ditolak' => 'Ditolak',
                            default => request('status')
                        } }}
                    </span>
                    <a href="{{ route('amil.pengajuan-dana.index', ['q' => request('q')]) }}" class="hover:opacity-75 transition" title="Hapus filter ini">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                </span>
            </div>
        @endif
    </div>

    <!-- List Pengajuan Cards -->
    @if ($pengajuans->isEmpty())
        <div class="bg-white rounded-2xl sm:rounded-3xl p-8 sm:p-10 text-center border border-slate-200 shadow-xs">
            <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h3 class="font-bold text-slate-800 text-base">Belum Ada Pengajuan Dana</h3>
            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Anda belum membuat permohonan dana kasbon program atau operasional.</p>
            <a href="{{ route('amil.pengajuan-dana.create') }}" class="inline-flex items-center gap-1.5 mt-4 px-4 py-2 rounded-xl bg-emerald-600 text-white font-bold text-xs shadow-xs hover:bg-emerald-700 transition">
                + Buat Pengajuan Baru
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($pengajuans as $item)
                @php
                    $badgeStyle = match($item->status) {
                        'diajukan' => 'bg-amber-100 text-amber-800 border-amber-200',
                        'diverifikasi_bendahara' => 'bg-blue-100 text-blue-800 border-blue-200',
                        'disetujui' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                        'dicairkan' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                        'ditolak' => 'bg-rose-100 text-rose-800 border-rose-200',
                        default => 'bg-slate-100 text-slate-800 border-slate-200',
                    };
                @endphp
                <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200 hover:border-emerald-300 transition flex flex-col justify-between">
                    <div>
                        <!-- Top Header -->
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <span class="text-xs font-mono font-bold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-lg border border-emerald-100">
                                {{ $item->nomor_pengajuan }}
                            </span>
                            <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full border {{ $badgeStyle }}">
                                {{ $item->status_label }}
                            </span>
                        </div>

                        <!-- Judul & Kategori -->
                        <h3 class="font-bold text-slate-900 text-base mb-1 line-clamp-1">
                            {{ $item->judul }}
                        </h3>

                        <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500 mb-3">
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                {{ $item->kategoriKeuangan?->nama ?? 'Umum' }}
                            </span>
                            @if($item->program)
                                <span class="text-slate-300">•</span>
                                <span class="text-emerald-600 font-medium">{{ $item->program->nama }}</span>
                            @endif
                        </div>

                        <!-- Referensi Resmi & Info Kebutuhan -->
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs space-y-1 mb-4">
                            @if($item->nomor_dokumen_referensi)
                                <div class="flex items-center justify-between text-slate-600">
                                    <span class="text-slate-400">Ref. Memo / Proposal:</span>
                                    <span class="font-semibold text-slate-800">{{ $item->nomor_dokumen_referensi }}</span>
                                </div>
                            @endif
                            <div class="flex items-center justify-between text-slate-600">
                                <span class="text-slate-400">Tgl. Kebutuhan Dana:</span>
                                <span class="font-semibold text-slate-800">{{ $item->tgl_kebutuhan ? $item->tgl_kebutuhan->format('d M Y') : '-' }}</span>
                            </div>
                            <div class="flex items-center justify-between text-slate-600">
                                <span class="text-slate-400">Nominal Diajukan:</span>
                                <span class="font-extrabold text-slate-800">Rp {{ number_format($item->nominal_diajukan, 0, ',', '.') }}</span>
                            </div>
                            @if($item->nominal_disetujui)
                                <div class="flex items-center justify-between text-emerald-700 font-bold pt-1 border-t border-slate-200/60">
                                    <span>Nominal Disetujui (ACC):</span>
                                    <span>Rp {{ number_format($item->nominal_disetujui, 0, ',', '.') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Footer Action Buttons -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                        <a href="{{ route('amil.pengajuan-dana.cetak', $item->id) }}" target="_blank" class="px-3 py-1.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-semibold flex items-center gap-1.5 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>Cetak Form Resmi</span>
                        </a>

                        <a href="{{ route('amil.pengajuan-dana.show', $item->id) }}" class="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition flex items-center gap-1">
                            <span>Detail Tracking</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $pengajuans->links() }}
        </div>
    @endif
</div>

<script>
    function toggleFilterDropdown(event) {
        event.stopPropagation();
        const menu = document.getElementById('filterDropdownMenu');
        if (menu) {
            menu.classList.toggle('hidden');
        }
    }

    // Tutup dropdown filter saat user mengklik area lain
    document.addEventListener('click', function(event) {
        const menu = document.getElementById('filterDropdownMenu');
        const btn = document.getElementById('filterDropdownBtn');
        if (menu && !menu.classList.contains('hidden')) {
            if (!menu.contains(event.target) && !btn.contains(event.target)) {
                menu.classList.add('hidden');
            }
        }
    });
</script>
@endsection
