@extends('layouts.amil')

@section('title', 'Input Donasi Baru')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Formulir Donasi Baru</h1>
            <p class="text-sm text-slate-500">Catat penerimaan zakat, infak, atau sedekah langsung ke sistem</p>
        </div>
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-emerald-100 text-emerald-800 text-xs font-bold self-start sm:self-auto shadow-sm">
            <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
            <span>No. Ref: {{ $noKwitansi }}</span>
        </div>
    </div>

    <form action="{{ route('amil.donasi.store') }}" method="POST" enctype="multipart/form-data" id="formDonasi" class="space-y-6">
        @csrf

        <!-- CARD 1: DATA DONATUR -->
        <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-600 text-white font-extrabold text-xs flex items-center justify-center">1</span>
                    <h2 class="font-bold text-slate-800 text-base">Identitas Donatur</h2>
                </div>

                <!-- Mode Selector Tabs -->
                <div class="bg-slate-100 p-1 rounded-xl flex text-xs font-bold" id="donaturModeTabs">
                    <button type="button" onclick="switchDonaturMode('existing')" id="tabExisting" class="px-3 py-1.5 rounded-lg transition {{ ($selectedDonatur || old('donatur_mode', 'existing') === 'existing') ? 'bg-white text-emerald-700 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                        Donatur Terdaftar
                    </button>
                    <button type="button" onclick="switchDonaturMode('new')" id="tabNew" class="px-3 py-1.5 rounded-lg transition {{ (!$selectedDonatur && old('donatur_mode') === 'new') ? 'bg-white text-emerald-700 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                        + Donatur Baru
                    </button>
                </div>
            </div>

            <input type="hidden" name="donatur_mode" id="donaturMode" value="{{ $selectedDonatur ? 'existing' : old('donatur_mode', 'existing') }}">

            <!-- TAB 1: DONATUR SUDAH ADA (SEARCHABLE COMBOBOX) -->
            <div id="sectionExisting" class="{{ (!$selectedDonatur && old('donatur_mode') === 'new') ? 'hidden' : 'block' }}">
                <input type="hidden" name="donatur_id" id="selectedDonaturIdInput" value="{{ $selectedDonatur?->id ?? old('donatur_id') }}">

                <!-- 1. KARTU DONATUR TERPILIH (Aktif jika donatur sudah dipilih) -->
                <div id="selectedDonaturCard" class="{{ $selectedDonatur ? 'block' : 'hidden' }} p-4 rounded-2xl bg-emerald-50/80 border-2 border-emerald-500 shadow-sm transition">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white font-extrabold flex items-center justify-center text-sm shadow shrink-0">
                                <span id="selectedDonaturInitials">{{ substr($selectedDonatur?->nama ?? 'D', 0, 2) }}</span>
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h4 id="selectedDonaturNama" class="text-sm font-extrabold text-slate-900 truncate">
                                        {{ $selectedDonatur?->nama }}
                                    </h4>
                                    <span id="selectedDonaturKode" class="px-2 py-0.5 rounded-md text-[10px] font-bold font-mono bg-white text-emerald-800 border border-emerald-200">
                                        {{ $selectedDonatur?->kode_donatur }}
                                    </span>
                                </div>
                                <div class="text-xs text-slate-600 mt-1 flex items-center gap-3 flex-wrap">
                                    <span class="flex items-center gap-1 font-mono">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        <span id="selectedDonaturPhone">{{ $selectedDonatur?->no_hp }}</span>
                                    </span>
                                    <span id="selectedDonaturKotaWrapper" class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                        <span id="selectedDonaturKota">{{ $selectedDonatur?->kota ?? 'Cilacap' }}</span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <button type="button" onclick="clearSelectedDonatur()" class="px-3 py-1.5 rounded-xl bg-white hover:bg-rose-50 text-slate-600 hover:text-rose-600 border border-slate-200 hover:border-rose-300 text-xs font-bold transition flex items-center gap-1.5 shrink-0 shadow-sm cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span>Ganti Donatur</span>
                        </button>
                    </div>
                </div>

                <!-- 2. BOX PENCARIAN & DROPDOWN HASIL (Aktif jika belum ada donatur yang dipilih) -->
                <div id="donaturSearchContainer" class="{{ $selectedDonatur ? 'hidden' : 'block' }} relative">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Cari & Pilih Donatur Terdaftar <span class="text-rose-500">*</span>
                    </label>

                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" id="donaturSearchInput" placeholder="Ketik nama donatur, nomor HP / WhatsApp, atau kode DNT..." autocomplete="off" class="w-full rounded-xl border border-slate-300 py-2.5 pl-10 pr-10 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white placeholder:text-slate-400">
                        <div id="donaturSearchSpinner" class="hidden absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                        </div>
                        <button type="button" id="donaturSearchClearBtn" onclick="clearSearchInput()" class="hidden absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Dropdown Hasil Pencarian -->
                    <div id="donaturDropdownResults" class="hidden absolute left-0 right-0 top-full mt-1.5 bg-white rounded-2xl shadow-2xl border border-slate-200 z-50 max-h-72 overflow-y-auto divide-y divide-slate-100">
                        <!-- Populated by JS -->
                    </div>

                    <div class="flex items-center justify-between text-[11px] text-slate-500 mt-1.5 px-0.5">
                        <span>Bisa mencari seluruh donatur lembaga (lintas amil).</span>
                        <button type="button" onclick="switchDonaturMode('new')" class="text-emerald-700 font-bold hover:underline">
                            + Donatur Baru
                        </button>
                    </div>
                </div>
            </div>

            <!-- TAB 2: DONATUR BARU -->
            <div id="sectionNew" class="{{ (!$selectedDonatur && old('donatur_mode') === 'new') ? 'block' : 'hidden' }} space-y-4">
                <!-- Live Duplicate Phone Alert Box -->
                <div id="phoneDuplicateAlert" class="hidden p-3.5 rounded-xl bg-amber-50 border border-amber-300 text-amber-900 text-xs flex items-start gap-2.5">
                    <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <span class="font-bold block">Nomor HP Sudah Terdaftar!</span>
                        <span id="phoneDuplicateMessage" class="text-slate-700">Donatur dengan nomor ini sudah tersimpan di sistem. Sistem akan otomatis menautkan donasi ke donatur tersebut.</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Nomor WhatsApp / HP <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="tel" name="no_hp" id="inputNoHp" value="{{ old('no_hp') }}" placeholder="08xxxxxxxxxx" class="w-full rounded-xl border border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <span id="phoneCheckSpinner" class="hidden absolute right-3 top-3 text-slate-400">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Nama Lengkap / Lembaga <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="nama_donatur" id="inputNamaDonatur" value="{{ old('nama_donatur') }}" placeholder="cth: H. Ahmad Dahlan" class="w-full rounded-xl border border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Jenis Donatur</label>
                        <div class="flex gap-4 pt-1.5">
                            <label class="flex items-center gap-2 text-xs font-semibold cursor-pointer">
                                <input type="radio" name="jenis_donatur" value="individu" {{ old('jenis_donatur', 'individu') === 'individu' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                                <span>Individu / Perorangan</span>
                            </label>
                            <label class="flex items-center gap-2 text-xs font-semibold cursor-pointer">
                                <input type="radio" name="jenis_donatur" value="lembaga" {{ old('jenis_donatur') === 'lembaga' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                                <span>Lembaga / Perusahaan</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Kategori Donatur <span class="text-rose-500">*</span></label>
                        <select name="kategori_id" class="w-full rounded-xl border border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            @foreach ($kategoris as $k)
                                <option value="{{ $k->id }}" {{ old('kategori_id') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Email (Opsional)</label>
                        <input type="email" name="email" id="inputEmail" value="{{ old('email') }}" placeholder="donatur@email.com" class="w-full rounded-xl border border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Kota / Domisili</label>
                        <input type="text" name="kota" id="inputKota" value="{{ old('kota') }}" placeholder="cth: Bandung" class="w-full rounded-xl border border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Alamat Lengkap</label>
                    <textarea name="alamat" id="inputAlamat" rows="2" placeholder="Jl. Terusan Buah Batu No..." class="w-full rounded-xl border border-slate-300 py-2 px-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">{{ old('alamat') }}</textarea>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" name="is_anonim" id="is_anonim" value="1" {{ old('is_anonim') ? 'checked' : '' }} class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    <label for="is_anonim" class="text-xs font-semibold text-slate-700 cursor-pointer">
                        Hamba Allah (Sembunyikan nama pada kwitansi publik)
                    </label>
                </div>
            </div>
        </div>

        <!-- CARD 2: RINCIAN TRANSAKSI DONASI -->
        <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-sm border border-slate-200">
            <div class="flex items-center gap-2 pb-4 mb-5 border-b border-slate-100">
                <span class="w-7 h-7 rounded-lg bg-emerald-600 text-white font-extrabold text-xs flex items-center justify-center">2</span>
                <h2 class="font-bold text-slate-800 text-base">Rincian Penerimaan Dana</h2>
            </div>

            <div class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Jenis Dana <span class="text-rose-500">*</span></label>
                        <select name="jenis_dana_id" required class="w-full rounded-xl border border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            @foreach ($jenisDanas as $jd)
                                <option value="{{ $jd->id }}" {{ old('jenis_dana_id') == $jd->id ? 'selected' : '' }}>
                                    {{ $jd->nama }} ({{ strtoupper($jd->kelompok) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Program / Kampanye</label>
                        <select name="program_id" class="w-full rounded-xl border border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">-- Bebas / Rutin Umum --</option>
                            @foreach ($programs as $p)
                                <option value="{{ $p->id }}" {{ old('program_id') == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- NOMINAL DONASI & TERBILANG LIVE -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Nominal Donasi (Rp) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-2.5 font-extrabold text-slate-400 text-base">Rp</span>
                        <input type="number" name="jumlah" id="inputJumlah" value="{{ old('jumlah') }}" required min="1000" step="500" placeholder="100.000" class="w-full pl-12 pr-4 py-2.5 rounded-xl border border-slate-300 text-lg font-extrabold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <!-- Live Terbilang Preview -->
                    <div id="terbilangBox" class="mt-2 p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-600 flex items-center gap-2">
                        <span class="text-slate-400 font-normal">Terbilang:</span>
                        <span id="terbilangText" class="text-emerald-700 italic">Nol rupiah</span>
                    </div>
                </div>

                <!-- METODE PEMBAYARAN -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Metode Pembayaran <span class="text-rose-500">*</span></label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        <label class="cursor-pointer border border-slate-200 p-3 rounded-xl flex flex-col items-center justify-center gap-1.5 text-center hover:bg-slate-50 transition has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/60 has-[:checked]:text-emerald-800">
                            <input type="radio" name="metode_bayar" value="tunai" {{ old('metode_bayar', 'tunai') === 'tunai' ? 'checked' : '' }} onchange="toggleMetodeBayar(this.value)" class="sr-only">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100/70 text-emerald-700 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <span class="text-xs font-bold">Tunai</span>
                        </label>

                        <label class="cursor-pointer border border-slate-200 p-3 rounded-xl flex flex-col items-center justify-center gap-1.5 text-center hover:bg-slate-50 transition has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/60 has-[:checked]:text-emerald-800">
                            <input type="radio" name="metode_bayar" value="transfer" {{ old('metode_bayar') === 'transfer' ? 'checked' : '' }} onchange="toggleMetodeBayar(this.value)" class="sr-only">
                            <div class="w-8 h-8 rounded-lg bg-blue-100/70 text-blue-700 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                                </svg>
                            </div>
                            <span class="text-xs font-bold">Transfer Bank</span>
                        </label>

                        <label class="cursor-pointer border border-slate-200 p-3 rounded-xl flex flex-col items-center justify-center gap-1.5 text-center hover:bg-slate-50 transition has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/60 has-[:checked]:text-emerald-800">
                            <input type="radio" name="metode_bayar" value="qris" {{ old('metode_bayar') === 'qris' ? 'checked' : '' }} onchange="toggleMetodeBayar(this.value)" class="sr-only">
                            <div class="w-8 h-8 rounded-lg bg-indigo-100/70 text-indigo-700 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                </svg>
                            </div>
                            <span class="text-xs font-bold">QRIS</span>
                        </label>

                        <label class="cursor-pointer border border-slate-200 p-3 rounded-xl flex flex-col items-center justify-center gap-1.5 text-center hover:bg-slate-50 transition has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/60 has-[:checked]:text-emerald-800">
                            <input type="radio" name="metode_bayar" value="ewallet" {{ old('metode_bayar') === 'ewallet' ? 'checked' : '' }} onchange="toggleMetodeBayar(this.value)" class="sr-only">
                            <div class="w-8 h-8 rounded-lg bg-amber-100/70 text-amber-700 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </div>
                            <span class="text-xs font-bold">E-Wallet</span>
                        </label>
                    </div>
                </div>

                <!-- Bagian Khusus Bank Jika Transfer -->
                <div id="sectionTransferBank" class="{{ old('metode_bayar') === 'transfer' ? 'block' : 'hidden' }} p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Bank Rekening Tujuan <span class="text-rose-500">*</span></label>
                    <select name="bank" class="w-full rounded-xl border border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                        <option value="BSI - Bank Syariah Indonesia" {{ old('bank') === 'BSI - Bank Syariah Indonesia' ? 'selected' : '' }}>BSI - Bank Syariah Indonesia</option>
                        <option value="BCA - Bank Central Asia" {{ old('bank') === 'BCA - Bank Central Asia' ? 'selected' : '' }}>BCA - Bank Central Asia</option>
                        <option value="Mandiri" {{ old('bank') === 'Mandiri' ? 'selected' : '' }}>Bank Mandiri</option>
                        <option value="BRI" {{ old('bank') === 'BRI' ? 'selected' : '' }}>Bank BRI</option>
                        <option value="Muamalat" {{ old('bank') === 'Muamalat' ? 'selected' : '' }}>Bank Muamalat</option>
                    </select>
                </div>

                <!-- Bagian Unggah File Bukti Pembayaran & Dokumentasi (Choose File) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-2xl bg-emerald-50/50 border border-emerald-200">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Bukti Transaksi / Struk / QRIS <span class="text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        <p class="text-[11px] text-slate-500 mb-2">Unggah tangkapan layar transfer, struk ATM, atau resi pembayaran</p>
                        <input type="file" name="bukti_transfer" id="inputBuktiTransfer" accept="image/*" onchange="previewDonasiImg(this, 'previewBuktiBox', 'previewBuktiImg')" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700 cursor-pointer">
                        <div id="previewBuktiBox" class="hidden mt-2.5 p-2 rounded-xl border border-emerald-200 bg-white inline-block">
                            <img id="previewBuktiImg" src="" alt="Preview Bukti" class="max-h-36 rounded-lg object-contain">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Dokumentasi Penyerahan / Kuitansi Fisik <span class="text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        <p class="text-[11px] text-slate-500 mb-2">Unggah foto saat serah terima dana tunai atau kuitansi bertandatangan</p>
                        <input type="file" name="lampiran_foto" id="inputLampiranFoto" accept="image/*" onchange="previewDonasiImg(this, 'previewDokumentasiBox', 'previewDokumentasiImg')" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700 cursor-pointer">
                        <div id="previewDokumentasiBox" class="hidden mt-2.5 p-2 rounded-xl border border-emerald-200 bg-white inline-block">
                            <img id="previewDokumentasiImg" src="" alt="Preview Dokumentasi" class="max-h-36 rounded-lg object-contain">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal & Waktu Donasi <span class="text-rose-500">*</span></label>
                        <input type="datetime-local" name="tgl_donasi" value="{{ old('tgl_donasi', now()->format('Y-m-d\TH:i')) }}" required class="w-full rounded-xl border border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Atas Nama (Jika Diniatkan untuk Orang Lain)</label>
                        <input type="text" name="atas_nama" value="{{ old('atas_nama') }}" placeholder="cth: Almarhum Ayahanda..." class="w-full rounded-xl border border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Doa / Niat Khusus / Pesan Donatur</label>
                    <textarea name="niat" rows="2" placeholder="Tuliskan doa atau permohonan donatur untuk didoakan bersama..." class="w-full rounded-xl border border-slate-300 py-2 px-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">{{ old('niat') }}</textarea>
                </div>
            </div>
        </div>

        <!-- CARD 3: DONASI BARANG (OPSIONAL ACCORDION) -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="ada_barang" id="ada_barang" value="1" {{ old('ada_barang') ? 'checked' : '' }} onchange="document.getElementById('sectionBarang').classList.toggle('hidden', !this.checked)" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    <label for="ada_barang" class="font-bold text-slate-800 text-sm cursor-pointer">
                        Disertai Donasi Barang / Natura
                    </label>
                </div>
                <span class="text-xs text-slate-400 font-medium">Opsional</span>
            </div>

            <div id="sectionBarang" class="{{ old('ada_barang') ? 'block' : 'hidden' }} mt-4 pt-4 border-t border-slate-100 space-y-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Deskripsi Barang (Nama, Jumlah, Kondisi)</label>
                    <textarea name="barang_deskripsi" rows="2" placeholder="cth: 5 Kardus Al-Quran Wakaf, kondisi baru bersegel..." class="w-full rounded-xl border border-slate-300 py-2 px-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">{{ old('barang_deskripsi') }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Estimasi Nilai Barang (Rp)</label>
                    <input type="number" name="barang_estimasi" value="{{ old('barang_estimasi') }}" placeholder="cth: 2500000" class="w-full rounded-xl border border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
            </div>
        </div>

        <!-- ACTION BUTTONS -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('amil.dashboard') }}" class="px-5 py-3 rounded-xl border border-slate-300 text-slate-700 font-bold text-sm hover:bg-slate-50 transition">
                Batal
            </a>
            <button type="submit" id="btnSubmit" class="px-7 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-sm shadow-lg shadow-emerald-600/30 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Simpan & Terbitkan Kwitansi</span>
            </button>
        </div>
    </form>
</div>

<script>
    // Tab Switching Mode Donatur
    function switchDonaturMode(mode) {
        document.getElementById('donaturMode').value = mode;
        const sectionExisting = document.getElementById('sectionExisting');
        const sectionNew = document.getElementById('sectionNew');
        const tabExisting = document.getElementById('tabExisting');
        const tabNew = document.getElementById('tabNew');

        if (mode === 'existing') {
            sectionExisting.classList.remove('hidden');
            sectionNew.classList.add('hidden');
            tabExisting.className = 'px-3 py-1.5 rounded-lg transition bg-white text-emerald-700 shadow-sm';
            tabNew.className = 'px-3 py-1.5 rounded-lg transition text-slate-600 hover:text-slate-900';
        } else {
            sectionExisting.classList.add('hidden');
            sectionNew.classList.remove('hidden');
            tabNew.className = 'px-3 py-1.5 rounded-lg transition bg-white text-emerald-700 shadow-sm';
            tabExisting.className = 'px-3 py-1.5 rounded-lg transition text-slate-600 hover:text-slate-900';
        }
    }

    // ==========================================
    // LIVE SEARCHABLE DONATUR COMBOBOX
    // ==========================================
    const initialDonaturs = @json($initialDonaturs ?? []);

    let donaturSearchTimeout = null;
    const donaturSearchInput = document.getElementById('donaturSearchInput');
    const donaturDropdown = document.getElementById('donaturDropdownResults');
    const donaturSpinner = document.getElementById('donaturSearchSpinner');
    const donaturClearBtn = document.getElementById('donaturSearchClearBtn');
    const selectedDonaturCard = document.getElementById('selectedDonaturCard');
    const donaturSearchContainer = document.getElementById('donaturSearchContainer');
    const selectedDonaturIdInput = document.getElementById('selectedDonaturIdInput');

    function renderDonaturList(list, query = '') {
        donaturDropdown.innerHTML = '';
        if (!list || list.length === 0) {
            donaturDropdown.innerHTML = `
                <div class="p-4 text-center">
                    <p class="text-xs text-slate-500 font-medium">Tidak ditemukan donatur dengan kata kunci <b>"${query}"</b>.</p>
                    <button type="button" onclick="switchDonaturMode('new'); if(document.getElementById('inputNamaDonatur')) document.getElementById('inputNamaDonatur').value = '${query}';" class="mt-2 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-xs font-bold transition">
                        <span>+ Daftarkan Sebagai Donatur Baru</span>
                    </button>
                </div>
            `;
            donaturDropdown.classList.remove('hidden');
            return;
        }

        list.forEach(item => {
            const row = document.createElement('div');
            row.className = 'p-3 hover:bg-emerald-50/60 cursor-pointer transition flex items-center justify-between gap-3 text-left group';
            row.onclick = () => selectDonatur(item);

            const badge = item.is_binaan_saya
                ? '<span class="px-2 py-0.5 rounded text-[9px] font-bold bg-emerald-100 text-emerald-800">Binaan Anda</span>'
                : '<span class="px-2 py-0.5 rounded text-[9px] font-bold bg-slate-100 text-slate-600">Donatur Lembaga</span>';

            row.innerHTML = `
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center text-xs shrink-0 group-hover:bg-emerald-600 group-hover:text-white transition">
                        ${(item.nama || 'D').substring(0, 2).toUpperCase()}
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="font-extrabold text-xs text-slate-900 group-hover:text-emerald-800 transition truncate">${item.nama}</span>
                            ${badge}
                        </div>
                        <div class="text-[11px] text-slate-500 flex items-center gap-2 mt-0.5 truncate">
                            <span class="font-mono text-emerald-700 font-medium">${item.no_hp || '-'}</span>
                            <span>•</span>
                            <span class="font-mono text-slate-400">${item.kode_donatur || ''}</span>
                            ${item.kota ? `<span>•</span><span>${item.kota}</span>` : ''}
                        </div>
                    </div>
                </div>
                <div class="shrink-0 text-slate-300 group-hover:text-emerald-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
            `;
            donaturDropdown.appendChild(row);
        });

        donaturDropdown.classList.remove('hidden');
    }

    function selectDonatur(item) {
        selectedDonaturIdInput.value = item.id;
        document.getElementById('selectedDonaturNama').innerText = item.nama;
        document.getElementById('selectedDonaturKode').innerText = item.kode_donatur || '-';
        document.getElementById('selectedDonaturPhone').innerText = item.no_hp || '-';
        document.getElementById('selectedDonaturInitials').innerText = (item.nama || 'D').substring(0, 2).toUpperCase();
        document.getElementById('selectedDonaturKota').innerText = item.kota || item.alamat || '-';

        selectedDonaturCard.classList.remove('hidden');
        donaturSearchContainer.classList.add('hidden');
        donaturDropdown.classList.add('hidden');
    }

    function clearSelectedDonatur() {
        selectedDonaturIdInput.value = '';
        selectedDonaturCard.classList.add('hidden');
        donaturSearchContainer.classList.remove('hidden');
        donaturSearchInput.value = '';
        donaturClearBtn.classList.add('hidden');
        donaturSearchInput.focus();
        renderDonaturList(initialDonaturs);
    }

    function clearSearchInput() {
        donaturSearchInput.value = '';
        donaturClearBtn.classList.add('hidden');
        renderDonaturList(initialDonaturs);
        donaturSearchInput.focus();
    }

    if (donaturSearchInput) {
        donaturSearchInput.addEventListener('focus', function() {
            if (!this.value.trim()) {
                renderDonaturList(initialDonaturs);
            } else {
                donaturDropdown.classList.remove('hidden');
            }
        });

        donaturSearchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
            }
        });

        donaturSearchInput.addEventListener('input', function() {
            const query = this.value.trim();

            if (query.length > 0) {
                donaturClearBtn.classList.remove('hidden');
            } else {
                donaturClearBtn.classList.add('hidden');
            }

            // 1. Filter instan di memori lokal
            const localMatches = initialDonaturs.filter(d => {
                const qLower = query.toLowerCase();
                return (d.nama && d.nama.toLowerCase().includes(qLower)) ||
                       (d.no_hp && d.no_hp.includes(query)) ||
                       (d.kode_donatur && d.kode_donatur.toLowerCase().includes(qLower));
            });

            renderDonaturList(localMatches, query);

            // 2. Debounce fetch ke server jika query >= 2 karakter untuk cari seluruh database lembaga
            clearTimeout(donaturSearchTimeout);
            if (query.length >= 2) {
                donaturSpinner.classList.remove('hidden');
                donaturClearBtn.classList.add('hidden');

                donaturSearchTimeout = setTimeout(() => {
                    fetch(`{{ route('amil.donasi.search-donatur') }}?q=${encodeURIComponent(query)}`)
                        .then(res => res.json())
                        .then(data => {
                            donaturSpinner.classList.add('hidden');
                            if (donaturSearchInput.value.trim().length > 0) {
                                donaturClearBtn.classList.remove('hidden');
                            }
                            if (data.donaturs) {
                                renderDonaturList(data.donaturs, query);
                            }
                        })
                        .catch(() => {
                            donaturSpinner.classList.add('hidden');
                            if (donaturSearchInput.value.trim().length > 0) {
                                donaturClearBtn.classList.remove('hidden');
                            }
                        });
                }, 300);
            }
        });

        // Tutup dropdown jika klik di luar
        document.addEventListener('click', function(e) {
            if (donaturSearchContainer && !donaturSearchContainer.contains(e.target)) {
                if (donaturDropdown) donaturDropdown.classList.add('hidden');
            }
        });
    }

    // Toggle Tampilan Transfer Bank
    function toggleMetodeBayar(val) {
        const secTransfer = document.getElementById('sectionTransferBank');
        if (val === 'transfer') {
            secTransfer.classList.remove('hidden');
        } else {
            secTransfer.classList.add('hidden');
        }
    }

    // Live AJAX Cek Duplikat Nomor HP Donatur
    let phoneTimeout = null;
    const inputNoHp = document.getElementById('inputNoHp');
    const alertBox = document.getElementById('phoneDuplicateAlert');
    const alertMessage = document.getElementById('phoneDuplicateMessage');
    const spinner = document.getElementById('phoneCheckSpinner');

    inputNoHp.addEventListener('input', function() {
        clearTimeout(phoneTimeout);
        const val = this.value.trim();

        if (val.length < 9) {
            alertBox.classList.add('hidden');
            spinner.classList.add('hidden');
            return;
        }

        spinner.classList.remove('hidden');

        phoneTimeout = setTimeout(() => {
            fetch(`{{ route('amil.donasi.check-phone') }}?phone=${encodeURIComponent(val)}`)
                .then(res => res.json())
                .then(data => {
                    spinner.classList.add('hidden');
                    if (data.exists) {
                        alertMessage.innerHTML = `Nomor HP ini terdaftar atas nama <b>${data.donatur.nama}</b> (${data.donatur.kode_donatur}). Data donasi akan otomatis dihubungkan.`;
                        alertBox.classList.remove('hidden');

                        // Prefill data donatur baru jika kosong
                        const inputNama = document.getElementById('inputNamaDonatur');
                        if (!inputNama.value) inputNama.value = data.donatur.nama;
                        const inputEmail = document.getElementById('inputEmail');
                        if (!inputEmail.value && data.donatur.email) inputEmail.value = data.donatur.email;
                        const inputKota = document.getElementById('inputKota');
                        if (!inputKota.value && data.donatur.kota) inputKota.value = data.donatur.kota;
                        const inputAlamat = document.getElementById('inputAlamat');
                        if (!inputAlamat.value && data.donatur.alamat) inputAlamat.value = data.donatur.alamat;
                    } else {
                        alertBox.classList.add('hidden');
                    }
                })
                .catch(() => {
                    spinner.classList.add('hidden');
                });
        }, 500);
    });

    // Live Terbilang Bahasa Indonesia di Frontend
    const inputJumlah = document.getElementById('inputJumlah');
    const terbilangText = document.getElementById('terbilangText');

    function angkaKeTerbilang(nilai) {
        nilai = Math.floor(Math.abs(nilai));
        const huruf = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];
        let temp = "";

        if (nilai < 12) {
            temp = " " + huruf[nilai];
        } else if (nilai < 20) {
            temp = angkaKeTerbilang(nilai - 10) + " belas";
        } else if (nilai < 100) {
            temp = angkaKeTerbilang(Math.floor(nilai / 10)) + " puluh" + angkaKeTerbilang(nilai % 10);
        } else if (nilai < 200) {
            temp = " seratus" + angkaKeTerbilang(nilai - 100);
        } else if (nilai < 1000) {
            temp = angkaKeTerbilang(Math.floor(nilai / 100)) + " ratus" + angkaKeTerbilang(nilai % 100);
        } else if (nilai < 2000) {
            temp = " seribu" + angkaKeTerbilang(nilai - 1000);
        } else if (nilai < 1000000) {
            temp = angkaKeTerbilang(Math.floor(nilai / 1000)) + " ribu" + angkaKeTerbilang(nilai % 1000);
        } else if (nilai < 1000000000) {
            temp = angkaKeTerbilang(Math.floor(nilai / 1000000)) + " juta" + angkaKeTerbilang(nilai % 1000000);
        } else if (nilai < 1000000000000) {
            temp = angkaKeTerbilang(Math.floor(nilai / 1000000000)) + " milyar" + angkaKeTerbilang(nilai % 1000000000);
        }
        return temp.trim();
    }

    inputJumlah.addEventListener('input', function() {
        const val = parseInt(this.value, 10);
        if (isNaN(val) || val <= 0) {
            terbilangText.innerText = "Nol rupiah";
        } else {
            const hasil = angkaKeTerbilang(val);
            terbilangText.innerText = (hasil.charAt(0).toUpperCase() + hasil.slice(1)) + " rupiah";
        }
    });

    if (inputJumlah.value) {
        inputJumlah.dispatchEvent(new Event('input'));
    }

    // Preview File Gambar Donasi
    function previewDonasiImg(input, boxId, imgId) {
        const box = document.getElementById(boxId);
        const img = document.getElementById(imgId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
                box.classList.remove('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            img.src = '';
            box.classList.add('hidden');
        }
    }
</script>
@endsection
