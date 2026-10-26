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

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Kota / Kabupaten</label>
                <input type="text" name="kota" value="{{ old('kota') }}" placeholder="cth: Bandung" class="w-full rounded-xl border border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
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
</script>
@endsection
