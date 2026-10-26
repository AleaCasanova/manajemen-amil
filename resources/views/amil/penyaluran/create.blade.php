@extends('layouts.amil')

@section('title', 'Catat Penyaluran Bantuan')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('amil.penyaluran.index') }}" class="text-xs font-bold text-slate-500 hover:text-emerald-700 transition inline-flex items-center gap-1 mb-2">
            ← Kembali ke Riwayat Penyaluran
        </a>
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Formulir Penyaluran Bantuan</h1>
        <p class="text-sm text-slate-500">Catat bukti serah terima dana ZISWAF kepada mustahik penerima manfaat</p>
    </div>

    <form action="{{ route('amil.penyaluran.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 space-y-5">
        @csrf

        <!-- Pilih Mustahik -->
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                Penerima Manfaat (Mustahik) <span class="text-rose-500">*</span>
            </label>
            <select name="mustahik_id" required class="w-full rounded-xl border border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-slate-50">
                <option value="">-- Pilih mustahik yang telah terverifikasi --</option>
                @foreach ($mustahiks as $m)
                    <option value="{{ $m->id }}" {{ old('mustahik_id') == $m->id ? 'selected' : '' }}>
                        {{ $m->nama }} (Asnaf: {{ ucfirst($m->asnaf) }}) - {{ $m->alamat ?? 'Alamat belum diatur' }}
                    </option>
                @endforeach
            </select>
            <p class="text-[11px] text-slate-400 mt-1">Hanya menampilkan mustahik dengan status <b>Terverifikasi Layak</b> oleh Admin.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Program Penyaluran</label>
                <select name="program_id" class="w-full rounded-xl border border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">-- Bantuan Rutin / Bebas --</option>
                    @foreach ($programs as $p)
                        <option value="{{ $p->id }}" {{ old('program_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Sumber Jenis Dana <span class="text-rose-500">*</span></label>
                <select name="jenis_dana_id" required class="w-full rounded-xl border border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    @foreach ($jenisDanas as $jd)
                        <option value="{{ $jd->id }}" {{ old('jenis_dana_id') == $jd->id ? 'selected' : '' }}>
                            {{ $jd->nama }} ({{ strtoupper($jd->kelompok) }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Nominal Bantuan & Terbilang -->
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                Nominal Bantuan yang Diserahkan (Rp) <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-2.5 font-extrabold text-slate-400 text-base">Rp</span>
                <input type="number" name="jumlah" id="inputJumlah" value="{{ old('jumlah') }}" required min="1000" placeholder="500.000" class="w-full pl-12 pr-4 py-2.5 rounded-xl border border-slate-300 text-lg font-extrabold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div class="mt-2 p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600 flex items-center gap-2">
                <span class="text-slate-400">Terbilang:</span>
                <span id="terbilangText" class="text-emerald-700 italic font-semibold">Nol rupiah</span>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Penyaluran <span class="text-rose-500">*</span></label>
            <input type="date" name="tgl_salur" value="{{ old('tgl_salur', date('Y-m-d')) }}" required class="w-full rounded-xl border border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Foto Berita Acara / Dokumentasi Serah Terima</label>
            <p class="text-[11px] text-slate-400 mb-2">Unggah foto saat menyerahkan bantuan langsung kepada penerima (Maks. 5MB)</p>
            <input type="file" name="bukti" id="inputBuktiPenyaluran" accept="image/*" onchange="previewPenyaluranImg(this)" class="w-full text-xs text-slate-500 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700 cursor-pointer">
            <div id="previewPenyaluranBox" class="hidden mt-3 p-2.5 rounded-2xl border border-emerald-200 bg-emerald-50/40 inline-block">
                <img id="previewPenyaluranImg" src="" alt="Preview Bukti Salur" class="max-h-48 rounded-xl object-contain">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Catatan Bantuan</label>
            <textarea name="catatan" rows="3" placeholder="cth: Paket sembako beras 10kg, minyak, dan uang santunan tunai..." class="w-full rounded-xl border border-slate-300 py-2 px-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">{{ old('catatan') }}</textarea>
        </div>

        <!-- Buttons -->
        <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
            <a href="{{ route('amil.penyaluran.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-50 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-md shadow-emerald-600/20 transition">
                Simpan Penyaluran
            </button>
        </div>
    </form>
</div>

<script>
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

    function previewPenyaluranImg(input) {
        const box = document.getElementById('previewPenyaluranBox');
        const img = document.getElementById('previewPenyaluranImg');
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
