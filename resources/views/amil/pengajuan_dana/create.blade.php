@extends('layouts.amil')

@section('title', 'Buat Pengajuan Dana Baru')

@section('content')
<div class="max-w-3xl mx-auto space-y-6 pb-28 sm:pb-12">
    <!-- Header Bar -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-slate-200 shadow-xs flex items-center gap-3.5">
        <a href="{{ route('amil.pengajuan-dana.index') }}" class="p-2 sm:p-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition shrink-0" title="Kembali ke Daftar Pengajuan">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-lg sm:text-2xl font-extrabold text-slate-800 tracking-tight leading-tight">Formulir Pengajuan Dana Resmi</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Permohonan realisasi anggaran kegiatan & operasional ke Bendahara & Manager</p>
        </div>
    </div>

    <!-- Form Container -->
    <div class="bg-white rounded-2xl sm:rounded-3xl p-5 sm:p-8 shadow-xs border border-slate-200">
        <form action="{{ route('amil.pengajuan-dana.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Section 1: Informasi Kebutuhan -->
            <div class="p-4 sm:p-5 rounded-2xl bg-slate-50/70 border border-slate-200/80 space-y-4">
                <div class="flex items-center gap-2.5 pb-3 border-b border-slate-200/60">
                    <span class="w-6 h-6 rounded-full bg-emerald-600 text-white font-extrabold text-xs flex items-center justify-center shrink-0">1</span>
                    <h2 class="text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wider">
                        Informasi Kebutuhan & Keperluan
                    </h2>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Keperluan / Judul Pengajuan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="judul" value="{{ old('judul') }}" placeholder="Contoh: Pengadaan 50 Paket Sembako Yatim / BBM Servis Ambulans ke Semarang" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('judul') border-rose-400 bg-rose-50/30 @enderror" required>
                    @error('judul')
                        <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Input Nominal with Solid Group Addon (No Overlapping Text) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nominal Dana Dibutuhkan <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex rounded-xl border border-slate-300 focus-within:ring-2 focus-within:ring-emerald-500 focus-within:border-emerald-500 overflow-hidden bg-white @error('nominal_diajukan') border-rose-400 @enderror">
                            <span class="inline-flex items-center px-4 bg-slate-100 text-slate-700 font-extrabold text-sm border-r border-slate-300 select-none">
                                Rp
                            </span>
                            <input type="number" id="nominal_diajukan" name="nominal_diajukan" value="{{ old('nominal_diajukan') }}" placeholder="Contoh: 1500000" min="1000" class="w-full px-3.5 py-3 text-sm font-bold text-slate-800 border-0 focus:ring-0 focus:outline-none" required oninput="previewNominal(this.value)">
                        </div>
                        <div id="nominal_preview_box" class="text-xs font-bold text-emerald-700 mt-1.5 hidden flex items-center gap-1">
                            <span>Format:</span>
                            <span id="nominal_preview_text"></span>
                        </div>
                        @error('nominal_diajukan')
                            <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Tanggal Dana Dibutuhkan <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="tgl_kebutuhan" value="{{ old('tgl_kebutuhan', date('Y-m-d')) }}" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('tgl_kebutuhan') border-rose-400 @enderror" required>
                        @error('tgl_kebutuhan')
                            <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section 2: Pos Anggaran & Program -->
            <div class="p-4 sm:p-5 rounded-2xl bg-slate-50/70 border border-slate-200/80 space-y-4">
                <div class="flex items-center gap-2.5 pb-3 border-b border-slate-200/60">
                    <span class="w-6 h-6 rounded-full bg-emerald-600 text-white font-extrabold text-xs flex items-center justify-center shrink-0">2</span>
                    <h2 class="text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wider">
                        Alokasi Pos Anggaran Yayasan
                    </h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Pos Kategori Anggaran (Opsional)
                        </label>
                        <select name="kategori_keuangan_id" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                            <option value="">-- Pilih Pos Kategori --</option>
                            @foreach ($kategoriKeuangans as $kat)
                                <option value="{{ $kat->id }}" {{ old('kategori_keuangan_id') == $kat->id ? 'selected' : '' }}>
                                    {{ $kat->nama }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-500 mt-1">Misal: Penyaluran Mustahik, Armada & Transportasi, dll.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Program Terkait (Opsional)
                        </label>
                        <select name="program_id" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                            <option value="">-- Tidak Terkait Program Tertentu --</option>
                            @foreach ($programs as $prog)
                                <option value="{{ $prog->id }}" {{ old('program_id') == $prog->id ? 'selected' : '' }}>
                                    {{ $prog->nama }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-500 mt-1">Pilih jika pengajuan ini berkaitan dengan campaign donasi khusus.</p>
                    </div>
                </div>
            </div>

            <!-- Section 3: Referensi Resmi & Dokumen Pendukung -->
            <div class="p-4 sm:p-5 rounded-2xl bg-slate-50/70 border border-slate-200/80 space-y-4">
                <div class="flex items-center gap-2.5 pb-3 border-b border-slate-200/60">
                    <span class="w-6 h-6 rounded-full bg-emerald-600 text-white font-extrabold text-xs flex items-center justify-center shrink-0">3</span>
                    <h2 class="text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wider">
                        Referensi Resmi & Dokumen Pendukung
                    </h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nomor Dokumen Referensi
                        </label>
                        <input type="text" name="nomor_dokumen_referensi" value="{{ old('nomor_dokumen_referensi') }}" placeholder="Contoh: MEMO/PROG/2026/011 / PROP-YTM-02" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                        <p class="text-[11px] text-slate-500 mt-1">Nomor surat internal / memo dinas / proposal jika ada.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Lampiran Proposal / RAB / Foto
                        </label>
                        <input type="file" name="dokumen_pendukung" accept=".pdf,.jpg,.jpeg,.png,.docx,.xlsx" class="w-full text-xs text-slate-600 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 border border-slate-300 rounded-xl bg-white cursor-pointer">
                        <p class="text-[11px] text-slate-500 mt-1">Format PDF, Foto, Word, Excel (Maksimal 5 MB).</p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Rincian Penggunaan Anggaran (RAB / Uraian) <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="deskripsi" rows="4" placeholder="Jelaskan rincian alokasi biaya pengajuan secara transparan:&#10;1. 50 Paket Sembako @ Rp 100.000 = Rp 5.000.000&#10;2. Transportasi & Bongkar Muat = Rp 200.000" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white font-mono leading-relaxed @error('deskripsi') border-rose-400 @enderror" required>{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-end gap-3">
                <a href="{{ route('amil.pengajuan-dana.index') }}" class="w-full sm:w-auto px-5 py-3 rounded-xl border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-50 transition text-center">
                    Batal
                </a>
                <button type="submit" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition flex items-center justify-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    <span>Kirim Pengajuan ke Bendahara</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function previewNominal(val) {
        const box = document.getElementById('nominal_preview_box');
        const text = document.getElementById('nominal_preview_text');
        const num = parseFloat(val);

        if (!isNaN(num) && num > 0) {
            box.classList.remove('hidden');
            text.innerText = 'Rp ' + num.toLocaleString('id-ID');
        } else {
            box.classList.add('hidden');
        }
    }

    // Trigger on page load if old value exists
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('nominal_diajukan');
        if (input && input.value) {
            previewNominal(input.value);
        }
    });
</script>
@endsection
