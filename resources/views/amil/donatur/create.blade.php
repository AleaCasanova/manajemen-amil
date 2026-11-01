@extends('layouts.amil')

@section('title', 'Daftarkan Donatur Baru')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('amil.donatur.index') }}" class="text-xs font-bold text-slate-500 hover:text-emerald-700 transition inline-flex items-center gap-1 mb-2">
            ← Kembali ke Donatur Saya
        </a>
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Daftarkan Donatur Baru</h1>
        <p class="text-sm text-slate-500">Tambahkan muzakki / donatur ke dalam binaan Anda</p>
    </div>

    <form action="{{ route('amil.donatur.store') }}" method="POST" class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 space-y-5">
        @csrf

        <!-- Live Duplicate Phone Alert Box -->
        <div id="phoneDuplicateAlert" class="hidden p-3.5 rounded-xl bg-amber-50 border border-amber-300 text-amber-900 text-xs flex items-start gap-2.5">
            <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
                <span class="font-bold block">Peringatan Duplikasi Nomor!</span>
                <span id="phoneDuplicateMessage" class="text-slate-700">Nomor ini sudah terdaftar di sistem. Anda tidak perlu mendaftarkannya kembali.</span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                    Nomor WhatsApp / HP <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <input type="tel" name="no_hp" id="inputNoHp" value="{{ old('no_hp') }}" required placeholder="08xxxxxxxxxx" class="w-full rounded-xl border border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
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
                    Nama Lengkap / Instansi <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="nama" value="{{ old('nama') }}" required placeholder="cth: Hj. Siti Fatimah" class="w-full rounded-xl border border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Jenis Donatur <span class="text-rose-500">*</span></label>
                <div class="flex gap-4 pt-1.5">
                    <label class="flex items-center gap-2 text-xs font-semibold cursor-pointer">
                        <input type="radio" name="jenis" value="individu" {{ old('jenis', 'individu') === 'individu' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                        <span>Individu</span>
                    </label>
                    <label class="flex items-center gap-2 text-xs font-semibold cursor-pointer">
                        <input type="radio" name="jenis" value="lembaga" {{ old('jenis') === 'lembaga' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                        <span>Lembaga / Perusahaan</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Kategori Donatur <span class="text-rose-500">*</span></label>
                <select name="kategori_id" required class="w-full rounded-xl border border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    @foreach ($kategoris as $k)
                        <option value="{{ $k->id }}" {{ old('kategori_id') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="donatur@email.com" class="w-full rounded-xl border border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>

            <!-- Searchable Dropdown Kecamatan / Domisili -->
            <div class="relative" id="containerKecamatan" style="position: relative;">
                <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center justify-between">
                    <span>Kecamatan / Domisili <span class="text-rose-500">*</span></span>
                    <span class="text-[10px] font-normal text-slate-400">24 Kecamatan Cilacap / Luar</span>
                </label>
                <div class="relative w-full" style="position: relative;">
                    <input type="text" 
                           id="inputKecamatanSearch" 
                           autocomplete="off"
                           placeholder="Ketik / pilih kecamatan di Cilacap..." 
                           class="w-full rounded-xl border border-slate-300 py-2.5 pl-3.5 pr-14 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white transition"
                           style="padding-left: 14px; padding-right: 56px;">
                    
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 gap-1"
                         style="position: absolute; top: 0; bottom: 0; right: 0; padding-right: 12px; display: flex; align-items: center; gap: 4px; z-index: 10;">
                        <button type="button" 
                                id="btnClearKecamatan" 
                                onclick="clearKecamatanChoice()" 
                                class="hidden p-1 text-slate-400 hover:text-rose-600 hover:bg-slate-100 rounded-md transition" 
                                style="cursor: pointer;"
                                title="Hapus pilihan">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                        <div class="text-slate-400 flex items-center justify-center pointer-events-none" style="pointer-events: none;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Hidden fields that submit to backend -->
                <input type="hidden" name="kecamatan" id="hiddenKecamatan" value="{{ old('kecamatan') }}">
                <input type="hidden" name="kota" id="hiddenKota" value="{{ old('kota', 'Cilacap') }}">

                <!-- Floating Dropdown list -->
                <div id="dropdownKecamatan" class="hidden absolute z-30 left-0 right-0 mt-1 max-h-60 overflow-y-auto bg-white rounded-xl shadow-xl border border-slate-200 py-1 text-xs divide-y divide-slate-100" style="position: absolute; left: 0; right: 0; top: 100%; margin-top: 4px; z-index: 50;">
                    <!-- Items populated by JS -->
                </div>
            </div>
        </div>

        <!-- Box Input Manual Khusus Jika Donatur Berada di Luar Cilacap -->
        <div id="boxKotaLuar" class="hidden p-3.5 rounded-xl bg-sky-50/80 border border-sky-200 space-y-1.5 transition-all">
            <div class="flex items-center justify-between">
                <label class="block text-xs font-bold text-sky-900">
                    Nama Kota / Asal Domisili Luar Cilacap <span class="text-rose-500">*</span>
                </label>
                <span class="text-[10px] text-sky-700 font-semibold bg-sky-100 px-2 py-0.5 rounded-md">Donatur Luar Daerah</span>
            </div>
            <input type="text" 
                   id="inputKotaManual" 
                   placeholder="cth: Bandung, Jakarta, Banyumas, Yogyakarta..." 
                   value="{{ old('kota') && old('kecamatan') === 'Luar Cilacap' ? old('kota') : '' }}"
                   class="w-full rounded-xl border border-sky-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 bg-white">
            <p class="text-[11px] text-sky-700 leading-tight">Sistem mencatat kecamatan sebagai "Luar Cilacap" dan nama kota di atas sebagai domisili donatur.</p>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Alamat Lengkap</label>
            <textarea name="alamat" rows="2" placeholder="Alamat domisili donatur..." class="w-full rounded-xl border border-slate-300 py-2 px-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">{{ old('alamat') }}</textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">NIK (Opsional)</label>
                <input type="text" name="nik" value="{{ old('nik') }}" placeholder="16 digit NIK KTP" class="w-full rounded-xl border border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">NPWP (Opsional untuk Bukti Zakat)</label>
                <input type="text" name="npwp" value="{{ old('npwp') }}" placeholder="Nomor Pokok Wajib Pajak" class="w-full rounded-xl border border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
        </div>

        <div class="flex items-center gap-2 pt-2 border-t border-slate-100">
            <input type="checkbox" name="is_anonim" id="is_anonim" value="1" {{ old('is_anonim') ? 'checked' : '' }} class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
            <label for="is_anonim" class="text-xs font-semibold text-slate-700 cursor-pointer">
                Donatur ingin namanya disamarkan (Hamba Allah) pada laporan publik
            </label>
        </div>

        <!-- Buttons -->
        <div class="flex items-center justify-end gap-3 pt-3">
            <a href="{{ route('amil.donatur.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-50 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-md shadow-emerald-600/20 transition">
                Daftarkan Donatur
            </button>
        </div>
    </form>
</div>

<script>
    // Live Duplicate Phone Checker
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
                        alertMessage.innerHTML = `Nomor ini sudah terdaftar atas nama <b>${data.donatur.nama}</b> (${data.donatur.kode_donatur}) binaan amil.`;
                        alertBox.classList.remove('hidden');
                    } else {
                        alertBox.classList.add('hidden');
                    }
                })
                .catch(() => {
                    spinner.classList.add('hidden');
                });
        }, 500);
    });

    // ==========================================
    // LOGIKA AUTOCOMPLETE KECAMATAN CILACAP & LUAR CILACAP
    // ==========================================
    const DAFTAR_KECAMATAN_CILACAP = [
        "Adipala", "Bantarsari", "Binangun", "Cilacap Selatan", "Cilacap Tengah", "Cilacap Utara",
        "Cimanggu", "Cipari", "Dayeuhluhur", "Gandrungmangu", "Jeruklegi", "Kampung Laut",
        "Karangpucung", "Kawunganten", "Kedungreja", "Kesugihan", "Kroya", "Majenang",
        "Maos", "Nusawungu", "Patimuan", "Sampang", "Sidareja", "Wanareja"
    ];

    const inputKecamatanSearch = document.getElementById('inputKecamatanSearch');
    const hiddenKecamatan = document.getElementById('hiddenKecamatan');
    const hiddenKota = document.getElementById('hiddenKota');
    const dropdownKecamatan = document.getElementById('dropdownKecamatan');
    const btnClearKecamatan = document.getElementById('btnClearKecamatan');
    const boxKotaLuar = document.getElementById('boxKotaLuar');
    const inputKotaManual = document.getElementById('inputKotaManual');
    const containerKecamatan = document.getElementById('containerKecamatan');

    function renderKecamatanDropdown(query = '') {
        if (!dropdownKecamatan) return;
        dropdownKecamatan.innerHTML = '';
        const q = (query || '').trim().toLowerCase();

        const filtered = DAFTAR_KECAMATAN_CILACAP.filter(k => k.toLowerCase().includes(q));

        if (filtered.length > 0) {
            const groupHeader = document.createElement('div');
            groupHeader.className = 'px-3 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50 flex items-center justify-between';
            groupHeader.innerHTML = `<span>Kecamatan di Kab. Cilacap</span><span>${filtered.length} ditemukan</span>`;
            dropdownKecamatan.appendChild(groupHeader);

            filtered.forEach(k => {
                const item = document.createElement('button');
                item.type = 'button';
                item.className = 'w-full text-left px-3.5 py-2 hover:bg-emerald-50 hover:text-emerald-800 transition flex items-center justify-between group';
                item.innerHTML = `
                    <span class="font-semibold text-slate-800 group-hover:text-emerald-700">${k}</span>
                    <span class="text-[10px] text-slate-400 group-hover:text-emerald-600">Kab. Cilacap</span>
                `;
                item.addEventListener('click', (e) => {
                    e.preventDefault();
                    selectKecamatan(k);
                });
                dropdownKecamatan.appendChild(item);
            });
        }

        // Opsi Spesial: Luar Kabupaten Cilacap
        const luarOption = document.createElement('button');
        luarOption.type = 'button';
        luarOption.className = 'w-full text-left px-3.5 py-2.5 hover:bg-sky-50 transition flex items-center justify-between text-sky-800 group border-t border-slate-100 bg-slate-50/70';
        
        const isTypedNotInCilacap = q && !filtered.some(k => k.toLowerCase() === q);
        const luarLabel = isTypedNotInCilacap
            ? `<div class="flex items-center gap-1.5"><span class="text-sm">📍</span><span>Gunakan <b>"${query}"</b> (Donatur Luar Cilacap)</span></div>`
            : `<div class="flex items-center gap-1.5"><span class="text-sm">📍</span><span><b>Luar Kabupaten Cilacap</b> (Ketik Kota Manual)</span></div>`;

        luarOption.innerHTML = `
            ${luarLabel}
            <span class="text-[10px] font-bold text-sky-600 group-hover:underline">Pilih</span>
        `;
        luarOption.addEventListener('click', (e) => {
            e.preventDefault();
            selectLuarCilacap(isTypedNotInCilacap ? query : '');
        });
        dropdownKecamatan.appendChild(luarOption);

        dropdownKecamatan.classList.remove('hidden');
    }

    function selectKecamatan(namaKecamatan) {
        hiddenKecamatan.value = namaKecamatan;
        hiddenKota.value = 'Cilacap';
        inputKecamatanSearch.value = namaKecamatan;
        btnClearKecamatan.classList.remove('hidden');
        dropdownKecamatan.classList.add('hidden');
        boxKotaLuar.classList.add('hidden');
        inputKotaManual.value = '';
    }

    function selectLuarCilacap(customCity = '') {
        hiddenKecamatan.value = 'Luar Cilacap';
        inputKecamatanSearch.value = 'Luar Kabupaten Cilacap';
        btnClearKecamatan.classList.remove('hidden');
        dropdownKecamatan.classList.add('hidden');
        boxKotaLuar.classList.remove('hidden');
        if (customCity) {
            inputKotaManual.value = customCity;
            hiddenKota.value = customCity;
        } else {
            hiddenKota.value = inputKotaManual.value.trim() || 'Luar Cilacap';
            inputKotaManual.focus();
        }
    }

    function clearKecamatanChoice() {
        hiddenKecamatan.value = '';
        hiddenKota.value = 'Cilacap';
        inputKecamatanSearch.value = '';
        inputKotaManual.value = '';
        btnClearKecamatan.classList.add('hidden');
        boxKotaLuar.classList.add('hidden');
        renderKecamatanDropdown('');
        inputKecamatanSearch.focus();
    }

    if (inputKecamatanSearch) {
        inputKecamatanSearch.addEventListener('focus', function() {
            renderKecamatanDropdown(this.value);
        });

        inputKecamatanSearch.addEventListener('input', function() {
            const val = this.value.trim();
            btnClearKecamatan.classList.toggle('hidden', val.length === 0);
            renderKecamatanDropdown(val);

            // Cek apakah ketikan persis sama dengan salah satu kecamatan
            const match = DAFTAR_KECAMATAN_CILACAP.find(k => k.toLowerCase() === val.toLowerCase());
            if (match) {
                hiddenKecamatan.value = match;
                hiddenKota.value = 'Cilacap';
                boxKotaLuar.classList.add('hidden');
            } else if (val.toLowerCase().includes('luar')) {
                hiddenKecamatan.value = 'Luar Cilacap';
                boxKotaLuar.classList.remove('hidden');
            }
        });

        // Close dropdown saat klik di luar
        document.addEventListener('click', function(e) {
            if (containerKecamatan && !containerKecamatan.contains(e.target)) {
                if (dropdownKecamatan) dropdownKecamatan.classList.add('hidden');
            }
        });
    }

    if (inputKotaManual) {
        inputKotaManual.addEventListener('input', function() {
            hiddenKota.value = this.value.trim() || 'Luar Cilacap';
        });
    }

    function setKecamatanFormValue(kec, kota) {
        if (!kec && !kota) return;
        
        const matched = DAFTAR_KECAMATAN_CILACAP.find(k => k.toLowerCase() === (kec || '').toLowerCase());
        if (matched) {
            selectKecamatan(matched);
        } else if (kec === 'Luar Cilacap' || (kota && kota.toLowerCase() !== 'cilacap')) {
            selectLuarCilacap(kota || '');
        } else if (kec) {
            selectKecamatan(kec);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const initialKec = "{{ old('kecamatan') }}";
        const initialKota = "{{ old('kota') }}";
        if (initialKec || initialKota) {
            setKecamatanFormValue(initialKec, initialKota);
        }
    });
</script>
@endsection
