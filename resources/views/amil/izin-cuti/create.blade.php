@extends('layouts.amil')

@section('title', 'Form Pengajuan Izin & Cuti')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('amil.izin-cuti.index') }}" class="text-xs font-bold text-slate-500 hover:text-emerald-700 transition inline-flex items-center gap-1 mb-2">
            ← Kembali ke Riwayat Izin / Cuti
        </a>
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Formulir Pengajuan Izin & Cuti</h1>
        <p class="text-sm text-slate-500">Ajukan permohonan tidak masuk kerja resmi kepada Administrator</p>
    </div>

    <!-- Saldo Cuti Info Banner -->
    <div class="mb-4 sm:mb-6 p-3.5 sm:p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs flex items-center justify-between text-emerald-900">
        <div class="flex items-center gap-2.5 min-w-0">
            <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div class="min-w-0">
                <span class="font-bold block truncate">Sisa Saldo Cuti Anda: {{ $sisaCuti }} Hari</span>
                <span class="text-emerald-700 text-[11px] block truncate">Tahun {{ $saldoCuti->tahun }} (Total kuota {{ $saldoCuti->jatah }} hari, terpakai {{ $saldoCuti->terpakai }} hari)</span>
            </div>
        </div>
    </div>

    <form action="{{ route('amil.izin-cuti.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-sm border border-slate-200 space-y-4 sm:space-y-5">
        @csrf

        <!-- Jenis Permohonan -->
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-2">Jenis Permohonan <span class="text-rose-500">*</span></label>
            <div class="grid grid-cols-3 gap-2 sm:gap-3">
                <label class="cursor-pointer border border-slate-200 p-2.5 sm:p-3 rounded-xl sm:rounded-2xl flex flex-col items-center justify-center gap-1.5 hover:bg-slate-50 transition has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/60 has-[:checked]:text-emerald-800 text-center">
                    <input type="radio" name="jenis" value="cuti" {{ old('jenis', 'cuti') === 'cuti' ? 'checked' : '' }} onchange="toggleJenis(this.value)" class="sr-only">
                    <div class="w-7 h-7 rounded-lg bg-emerald-100/70 text-emerald-700 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <span class="text-[11px] sm:text-xs font-bold">Cuti</span>
                </label>

                <label class="cursor-pointer border border-slate-200 p-2.5 sm:p-3 rounded-xl sm:rounded-2xl flex flex-col items-center justify-center gap-1.5 hover:bg-slate-50 transition has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/60 has-[:checked]:text-emerald-800 text-center">
                    <input type="radio" name="jenis" value="izin" {{ old('jenis') === 'izin' ? 'checked' : '' }} onchange="toggleJenis(this.value)" class="sr-only">
                    <div class="w-7 h-7 rounded-lg bg-blue-100/70 text-blue-700 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <span class="text-[11px] sm:text-xs font-bold">Izin</span>
                </label>

                <label class="cursor-pointer border border-slate-200 p-2.5 sm:p-3 rounded-xl sm:rounded-2xl flex flex-col items-center justify-center gap-1.5 hover:bg-slate-50 transition has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/60 has-[:checked]:text-emerald-800 text-center">
                    <input type="radio" name="jenis" value="sakit" {{ old('jenis') === 'sakit' ? 'checked' : '' }} onchange="toggleJenis(this.value)" class="sr-only">
                    <div class="w-7 h-7 rounded-lg bg-rose-100/70 text-rose-700 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    </div>
                    <span class="text-[11px] sm:text-xs font-bold">Sakit</span>
                </label>
            </div>
        </div>

        <!-- Rentang Tanggal -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Mulai <span class="text-rose-500">*</span></label>
                <input type="date" name="tgl_mulai" id="tglMulai" value="{{ old('tgl_mulai', date('Y-m-d')) }}" required class="w-full rounded-xl border border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Selesai <span class="text-rose-500">*</span></label>
                <input type="date" name="tgl_selesai" id="tglSelesai" value="{{ old('tgl_selesai', date('Y-m-d')) }}" required class="w-full rounded-xl border border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
        </div>

        <!-- Durasi Preview Alert -->
        <div id="durasiAlert" class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-600 flex items-center justify-between">
            <span>Estimasi Durasi: <b id="durasiText" class="text-slate-800">1 Hari Kerja</b></span>
            <span id="durasiWarning" class="hidden text-rose-600 font-bold">Melebihi saldo cuti!</span>
        </div>

        <!-- Alasan -->
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Alasan Permohonan <span class="text-rose-500">*</span></label>
            <textarea name="alasan" rows="3" required placeholder="Jelaskan keperluan atau alasan izin/cuti Anda secara jelas..." class="w-full rounded-xl border border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">{{ old('alasan') }}</textarea>
        </div>

        <!-- Lampiran Dokumen -->
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">
                Lampiran Berkas / Surat Dokter <span id="lampiranRequired" class="hidden text-rose-500 font-bold">(Wajib jika sakit > 1 hari)</span>
            </label>
            <p class="text-[11px] text-slate-400 mb-2">Format: JPG, PNG, atau PDF (Maksimal 5MB)</p>
            <input type="file" name="lampiran" id="inputLampiranIzin" accept="image/*,.pdf" onchange="previewIzinFile(this)" class="w-full text-xs text-slate-500 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700 cursor-pointer">
            <div id="previewIzinBox" class="hidden mt-3 p-2.5 rounded-2xl border border-emerald-200 bg-emerald-50/40 inline-block">
                <img id="previewIzinImg" src="" alt="Preview Lampiran" class="hidden max-h-48 rounded-xl object-contain">
                <div id="previewIzinPdf" class="hidden flex items-center gap-2 p-2 bg-white rounded-xl border border-slate-200 text-xs font-bold text-slate-700">
                    <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <span id="previewIzinPdfName">Dokumen PDF Terpilih</span>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
            <a href="{{ route('amil.izin-cuti.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-50 transition">
                Batal
            </a>
            <button type="submit" id="btnSubmit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-md shadow-emerald-600/20 transition">
                Kirim Pengajuan
            </button>
        </div>
    </form>
</div>

<script>
    const sisaCuti = {{ $sisaCuti }};
    const tglMulai = document.getElementById('tglMulai');
    const tglSelesai = document.getElementById('tglSelesai');
    const durasiText = document.getElementById('durasiText');
    const durasiWarning = document.getElementById('durasiWarning');
    const btnSubmit = document.getElementById('btnSubmit');

    function calculateDuration() {
        const start = new Date(tglMulai.value);
        const end = new Date(tglSelesai.value);

        if (isNaN(start) || isNaN(end) || end < start) {
            durasiText.innerText = "-";
            return;
        }

        const diffTime = Math.abs(end - start);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

        durasiText.innerText = `${diffDays} Hari`;

        // Cek jika cuti melebihi sisa
        const currentJenis = document.querySelector('input[name="jenis"]:checked')?.value;
        if (currentJenis === 'cuti' && diffDays > sisaCuti) {
            durasiWarning.classList.remove('hidden');
            btnSubmit.disabled = true;
        } else {
            durasiWarning.classList.add('hidden');
            btnSubmit.disabled = false;
        }
    }

    function toggleJenis(val) {
        const lampiranReq = document.getElementById('lampiranRequired');
        if (val === 'sakit') {
            lampiranReq.classList.remove('hidden');
        } else {
            lampiranReq.classList.add('hidden');
        }
        calculateDuration();
    }

    tglMulai.addEventListener('change', calculateDuration);
    tglSelesai.addEventListener('change', calculateDuration);

    document.addEventListener('DOMContentLoaded', calculateDuration);

    function previewIzinFile(input) {
        const box = document.getElementById('previewIzinBox');
        const img = document.getElementById('previewIzinImg');
        const pdf = document.getElementById('previewIzinPdf');
        const pdfName = document.getElementById('previewIzinPdfName');

        if (input.files && input.files[0]) {
            const file = input.files[0];
            box.classList.remove('hidden');

            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    img.src = e.target.result;
                    img.classList.remove('hidden');
                    pdf.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                img.classList.add('hidden');
                pdf.classList.remove('hidden');
                pdfName.innerText = `${file.name} (${(file.size / 1024).toFixed(1)} KB)`;
            }
        } else {
            box.classList.add('hidden');
            img.src = '';
            img.classList.add('hidden');
            pdf.classList.add('hidden');
        }
    }
</script>
@endsection
