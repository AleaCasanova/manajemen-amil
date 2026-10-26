@extends('layouts.amil')

@section('title', 'Presensi & Absensi Amil')

@section('content')
<div class="max-w-4xl mx-auto space-y-4 sm:space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-800 tracking-tight">Presensi Kehadiran</h1>
            <p class="text-xs sm:text-sm text-slate-500">Pencatatan waktu kerja & tugas lapangan amil</p>
        </div>
        <div class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-xl bg-white text-slate-700 self-start sm:self-auto border border-slate-200 shadow-2xs">
            <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
        </div>
    </div>

    <!-- PRESENSI CARD UTAMA -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 sm:gap-6">
        <!-- Kolom Kiri: Form Absensi (Kamera & GPS) -->
        <div class="md:col-span-7 bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-sm border border-slate-200 flex flex-col justify-between">
            <div>
                <!-- Status Hari Ini Header -->
                <div class="flex items-center justify-between pb-3.5 mb-3.5 border-b border-slate-100">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Status Hari Ini</span>
                    @if ($absensiToday)
                        @if ($absensiToday->jam_pulang)
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                                Selesai Bekerja (Sudah Pulang)
                            </span>
                        @else
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                Sudah Masuk (Sedang Bertugas)
                            </span>
                        @endif
                    @else
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 animate-pulse">
                            Belum Absen Masuk
                        </span>
                    @endif
                </div>

                <!-- MODE PRESENSI SELECTOR (KANTOR VS TUGAS LUAR) -->
                @if (!$absensiToday || !$absensiToday->jam_pulang)
                    <div class="mb-3.5">
                        <label class="block text-[11px] font-bold text-slate-600 mb-1.5">Tipe Presensi Saat Ini:</label>
                        <div class="p-1 bg-slate-100 rounded-xl sm:rounded-2xl border border-slate-200 grid grid-cols-2 gap-1">
                            <button type="button" id="btnModeKantor" onclick="switchPresenceMode('kantor')" class="py-2 px-2 rounded-lg sm:rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 bg-white text-emerald-800 shadow-xs">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <span>Presensi Kantor</span>
                            </button>
                            <button type="button" id="btnModeDinasLuar" onclick="switchPresenceMode('dinas_luar')" class="py-2 px-2 rounded-lg sm:rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 text-slate-600 hover:text-slate-800">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span>Tugas Lapangan / Luar</span>
                            </button>
                        </div>
                    </div>

                    <!-- Info banner jika Mode Tugas Luar aktif -->
                    <div id="bannerDinasLuar" class="hidden mb-3.5 p-3 rounded-xl bg-indigo-50 border border-indigo-200 text-xs text-indigo-900 space-y-1">
                        <div class="flex items-center gap-1.5 font-extrabold text-indigo-950 text-xs">
                            <svg class="w-4 h-4 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Mode Tugas Lapangan Aktif</span>
                        </div>
                        <p class="text-[11px] text-indigo-700 leading-snug">Radius kantor dilewati untuk dinas luar. Pastikan GPS HP tetap aktif & isi rincian tugas Anda di bawah.</p>
                    </div>
                @endif

                <!-- GPS & Geofencing Status Box -->
                <div id="gpsStatusBox" class="p-3 sm:p-3.5 rounded-xl sm:rounded-2xl bg-slate-50 border border-slate-200 text-xs mb-3.5 flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span id="gpsIndicator" class="w-2.5 h-2.5 rounded-full bg-slate-400 shrink-0"></span>
                        <div class="min-w-0">
                            <span id="gpsText" class="font-bold text-slate-700 block text-xs truncate">Mendeteksi lokasi GPS...</span>
                            <span id="gpsSubText" class="text-slate-400 text-[10px] sm:text-[11px] block leading-tight">Pastikan izin lokasi pada browser diaktifkan</span>
                        </div>
                    </div>
                    <button type="button" onclick="getLocation()" class="p-1.5 rounded-lg hover:bg-slate-200 text-slate-600 transition shrink-0" title="Refresh Lokasi GPS">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </button>
                </div>

                <!-- Tab Pemilihan Metode Foto Selfie -->
                @if (!$absensiToday || !$absensiToday->jam_pulang)
                    <div class="space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-700">Foto Selfie <span class="text-rose-500">*</span></span>
                            <div class="inline-flex p-0.5 rounded-xl bg-slate-100 border border-slate-200 text-xs font-semibold">
                                <button type="button" id="tabBtnCam" onclick="switchFotoMode('cam')" class="py-1 px-2.5 rounded-lg bg-white text-emerald-800 font-bold shadow-xs transition flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span>Kamera</span>
                                </button>
                                <button type="button" id="tabBtnFile" onclick="switchFotoMode('file')" class="py-1 px-2.5 rounded-lg text-slate-600 hover:text-slate-800 transition flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span>Pilih File</span>
                                </button>
                            </div>
                        </div>

                        <!-- PANEL 1: KAMERA WEBRTC LANGSUNG -->
                        <div id="panelCam" class="space-y-3">
                            <div class="relative rounded-2xl overflow-hidden bg-slate-900 aspect-video flex items-center justify-center border border-slate-200">
                                <!-- Video WebRTC -->
                                <video id="webcam" autoplay playsinline class="w-full h-full object-cover"></video>
                                <canvas id="canvas" class="hidden"></canvas>
                                <!-- Captured Snapshot Image Preview -->
                                <img id="photoPreview" class="hidden w-full h-full object-cover absolute inset-0 z-10" alt="Selfie Amil">

                                <!-- Placeholder Overlay jika belum start kamera -->
                                <div id="camPlaceholder" class="absolute inset-0 flex flex-col items-center justify-center text-white p-4 text-center bg-slate-900/90 z-20">
                                    <svg class="w-10 h-10 text-emerald-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span class="text-xs font-bold">Kamera Belum Aktif</span>
                                    <span class="text-[11px] text-slate-300 mt-0.5">Klik tombol di bawah untuk menyalakan kamera</span>
                                </div>
                            </div>

                            <!-- Camera Action Buttons -->
                            <div class="flex items-center gap-2">
                                <button type="button" id="btnStartCam" onclick="startCamera()" class="flex-1 py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center justify-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    <span>Buka Kamera</span>
                                </button>
                                <button type="button" id="btnCapture" onclick="takeSnapshot()" disabled class="flex-1 py-2 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 disabled:bg-slate-300 text-white font-bold text-xs transition flex items-center justify-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span>Ambil Foto</span>
                                </button>
                                <button type="button" id="btnRetake" onclick="retakeSnapshot()" class="hidden py-2 px-3 rounded-xl border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-50 transition">
                                    Ulangi
                                </button>
                            </div>
                        </div>

                        <!-- PANEL 2: CHOOSE FILE FOTO (UNGGAH DARI PERANGKAT) -->
                        <div id="panelFile" class="hidden space-y-3">
                            <div id="fileDropzone" class="relative rounded-2xl border-2 border-dashed border-emerald-300 bg-emerald-50/40 p-5 text-center hover:bg-emerald-50/70 transition cursor-pointer">
                                <input type="file" id="inputFilePicker" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20" onchange="handleFileSelect(event)">
                                
                                <div id="fileUploadPrompt" class="space-y-2">
                                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center mx-auto shadow-xs">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-800">Klik di sini untuk <span class="text-emerald-700 underline font-extrabold">Choose File / Pilih Foto</span></p>
                                        <p class="text-[11px] text-slate-500 mt-0.5">Mendukung format JPG, PNG, WEBP (Maksimal 5MB)</p>
                                    </div>
                                </div>

                                <!-- Preview Gambar Terpilih -->
                                <div id="filePreviewContainer" class="hidden space-y-2">
                                    <div class="relative max-h-48 rounded-xl overflow-hidden border border-slate-200 mx-auto inline-block">
                                        <img id="filePreviewImg" src="" alt="Preview File Selfie" class="max-h-48 object-contain">
                                    </div>
                                    <div class="flex items-center justify-center gap-2">
                                        <span id="fileNameBadge" class="text-[11px] font-semibold text-emerald-800 bg-emerald-100 px-2.5 py-1 rounded-lg"></span>
                                        <button type="button" onclick="clearFileSelect()" class="text-[11px] font-bold text-rose-600 hover:underline z-30">Ganti Foto</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Tampilan Jika Sudah Absen Masuk & Pulang Lengkap -->
                    <div class="p-6 rounded-2xl bg-emerald-50 border border-emerald-200 text-center text-emerald-800 space-y-2">
                        <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <h4 class="font-bold text-sm">Presensi Hari Ini Selesai</h4>
                        <p class="text-xs text-emerald-700">Terima kasih atas dedikasi dan kerja keras Anda hari ini.</p>
                    </div>
                @endif
            </div>

            <!-- FORM CLOCK IN / CLOCK OUT -->
            <div class="pt-4 border-t border-slate-100 mt-4">
                @if (!$absensiToday || !$absensiToday->jam_masuk)
                    <!-- FORM CLOCK IN -->
                    <form action="{{ route('amil.absensi.masuk') }}" method="POST" enctype="multipart/form-data" id="formClockIn">
                        @csrf
                        <input type="hidden" name="lat" id="inputLat">
                        <input type="hidden" name="lng" id="inputLng">
                        <input type="hidden" name="foto" id="inputFoto">
                        <input type="hidden" name="tipe_presensi" id="inputTipePresensiIn" value="kantor">
                        <input type="file" name="foto_file" id="formClockInFile" class="hidden" accept="image/*">

                        <!-- Field Keterangan Tugas Lapangan (Wajib jika mode dinas luar) -->
                        <div id="boxKeteranganIn" class="hidden mb-3 text-left">
                            <label for="inputKeteranganIn" class="block text-xs font-bold text-slate-700 mb-1">
                                Rincian Kegiatan / Tugas Lapangan <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="keterangan" id="inputKeteranganIn" rows="2" class="w-full text-xs rounded-xl border border-slate-300 p-2.5 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-200" placeholder="Contoh: Jemput mustahik sakit di RSUD / Survei calon penerima manfaat di desa..."></textarea>
                            <span class="text-[10px] text-slate-400 block mt-0.5">Wajib diisi jika presensi di luar kantor (minimal 5 karakter).</span>
                        </div>

                        <button type="submit" id="btnClockIn" disabled class="w-full py-3.5 px-4 rounded-2xl bg-emerald-600 hover:bg-emerald-700 disabled:bg-slate-300 text-white font-extrabold text-sm shadow-md transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            <span id="btnClockInText">Clock In (Masuk Kerja)</span>
                        </button>
                    </form>
                @elseif (!$absensiToday->jam_pulang)
                    <!-- FORM CLOCK OUT -->
                    <form action="{{ route('amil.absensi.pulang') }}" method="POST" enctype="multipart/form-data" id="formClockOut">
                        @csrf
                        <input type="hidden" name="lat" id="inputLat">
                        <input type="hidden" name="lng" id="inputLng">
                        <input type="hidden" name="foto" id="inputFoto">
                        <input type="hidden" name="tipe_presensi" id="inputTipePresensiOut" value="kantor">
                        <input type="file" name="foto_file" id="formClockOutFile" class="hidden" accept="image/*">

                        <!-- Field Keterangan Tugas Pulang (Wajib jika mode dinas luar) -->
                        <div id="boxKeteranganOut" class="hidden mb-3 text-left">
                            <label for="inputKeteranganOut" class="block text-xs font-bold text-slate-700 mb-1">
                                Rincian Kegiatan Lapangan Saat Pulang <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="keterangan_pulang" id="inputKeteranganOut" rows="2" class="w-full text-xs rounded-xl border border-slate-300 p-2.5 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-200" placeholder="Contoh: Selesai pendampingan di lokasi bencana langsung pulang / Selesai rapat eksternal..."></textarea>
                            <span class="text-[10px] text-slate-400 block mt-0.5">Wajib diisi jika presensi pulang di luar kantor (minimal 5 karakter).</span>
                        </div>

                        @if (isset($clockOutEligibility) && ! $clockOutEligibility['can_clock_out'])
                            <!-- Banner Kunci Jam Pulang (Belum 15:30) -->
                            <div id="boxLockClockOut" class="mb-3 p-3 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-left">
                                <div class="flex items-start gap-2.5">
                                    <div class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center shrink-0 mt-0.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    </div>
                                    <div class="text-xs">
                                        <p class="font-bold text-amber-950">Presensi Pulang Kantor Terkunci</p>
                                        <p class="text-[11px] text-amber-800 mt-0.5 leading-relaxed">
                                            Presensi pulang kantor baru dibuka mulai pukul <strong>{{ $clockOutEligibility['min_clock_out_time'] }} WIB</strong> (30 menit sebelum jam {{ $clockOutEligibility['regular_end_time'] }}). Jika sedang penugasan di luar kantor, silakan beralih ke mode <strong>'Tugas Luar'</strong>.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @elseif (isset($clockOutEligibility) && $clockOutEligibility['is_early'])
                            <!-- Pemberitahuan Pulang Lebih Awal (15:30 - 15:59) -->
                            <div id="boxEarlyNotice" class="mb-3 p-2.5 rounded-xl bg-amber-50/80 border border-amber-200 text-amber-900 text-left text-xs">
                                <div class="flex items-center gap-1.5 font-bold text-amber-900">
                                    <svg class="w-3.5 h-3.5 text-amber-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Pemberitahuan Pulang Lebih Awal</span>
                                </div>
                                <p class="text-[11px] text-amber-800 mt-0.5 leading-relaxed">
                                    Anda pulang {{ $clockOutEligibility['minutes_early'] }} menit lebih awal sebelum jam resmi kantor ({{ $clockOutEligibility['regular_end_time'] }} WIB). Presensi akan otomatis tercatat sebagai <strong>Pulang Lebih Awal</strong>.
                                </p>
                            </div>
                        @endif

                        <button type="submit" id="btnClockOut" disabled class="w-full py-3.5 px-4 rounded-2xl bg-rose-600 hover:bg-rose-700 disabled:bg-slate-300 text-white font-extrabold text-sm shadow-md transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            <span id="btnClockOutText">Clock Out (Pulang Kerja)</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Kolom Kanan: Rincian Hari Ini & Jam Kerja Kantor -->
        <div class="md:col-span-5 space-y-3.5 sm:space-y-4">
            <!-- Kartu Jam Kerja -->
            <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-5 shadow-sm border border-slate-200 text-xs space-y-3">
                <h3 class="font-bold text-slate-800 text-xs sm:text-sm border-b border-slate-100 pb-2 flex items-center justify-between">
                    <span>Ketentuan Jam Kerja</span>
                    <span class="text-[10px] text-slate-400 font-normal">WIB</span>
                </h3>
                
                <div class="grid grid-cols-2 gap-2 sm:gap-3">
                    <div class="p-2.5 sm:p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-400 block text-[10px] font-medium">Jam Masuk</span>
                        <span class="font-extrabold text-slate-800 text-xs sm:text-sm">
                            {{ $jamKerja?->jam_masuk ? substr($jamKerja->jam_masuk, 0, 5) . ' WIB' : '08:00 WIB' }}
                        </span>
                    </div>
                    <div class="p-2.5 sm:p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-400 block text-[10px] font-medium">Jam Pulang</span>
                        <span class="font-extrabold text-slate-800 text-xs sm:text-sm">
                            {{ $jamKerja?->jam_pulang ? substr($jamKerja->jam_pulang, 0, 5) . ' WIB' : '16:00 WIB' }}
                        </span>
                    </div>
                </div>

                <div class="space-y-1.5 pt-1 text-[11px] text-slate-500">
                    <div class="flex items-center justify-between">
                        <span>Toleransi Keterlambatan:</span>
                        <span class="font-bold text-slate-700">{{ $jamKerja?->toleransi_menit ?? 15 }} Menit</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Titik Kantor Sah:</span>
                        <span class="font-bold text-slate-800 text-right truncate max-w-[150px]">Kantor Terdekat ({{ $kantorAktif->count() }} Lokasi)</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Radius Geofence:</span>
                        <span class="font-bold text-emerald-700 text-right">Otomatis Tiap Kantor (50 - 150m)</span>
                    </div>
                    <div class="flex items-center justify-between pt-1 border-t border-slate-100 text-[10px]">
                        <span>Kantor Terdekat Anda:</span>
                        <span id="labelNearestOfficeLive" class="font-bold text-slate-500 truncate max-w-[150px] text-right">Menunggu GPS...</span>
                    </div>
                </div>

                <div class="pt-1.5 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('listOfficesBox').classList.toggle('hidden')" class="w-full text-left text-[10px] text-emerald-700 font-bold hover:underline flex items-center justify-between cursor-pointer">
                        <span>Daftar Kantor Cabang yang Sah ({{ $kantorAktif->count() }})</span>
                        <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div id="listOfficesBox" class="hidden mt-2 p-2 rounded-xl bg-slate-50 border border-slate-100 space-y-1.5 text-[10px]">
                        @foreach($kantorAktif as $k)
                            <div class="flex items-start justify-between gap-1 pb-1 border-b border-slate-200/50 last:border-0 last:pb-0">
                                <div class="min-w-0">
                                    <span class="font-bold text-slate-800 block truncate">{{ $k->nama_lokasi }}</span>
                                    <span class="text-slate-400 block text-[9px] truncate">{{ $k->alamat ?? 'Titik Koordinat Resmi' }}</span>
                                </div>
                                <span class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 font-mono text-[9px] shrink-0 font-bold">R: {{ $k->radius_meter }}m</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Kartu Jam Presensi Anda Hari Ini -->
            <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-5 shadow-sm border border-slate-200 text-xs space-y-3">
                <h3 class="font-bold text-slate-800 text-xs sm:text-sm border-b border-slate-100 pb-2">Catatan Waktu Hari Ini</h3>

                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-slate-400 block text-[10px] font-medium">Waktu Masuk</span>
                        <span class="font-extrabold text-slate-800 text-sm sm:text-base">
                            {{ $absensiToday?->jam_masuk ? substr($absensiToday->jam_masuk, 0, 5) . ' WIB' : '--:--' }}
                        </span>
                    </div>
                    @if ($absensiToday?->status)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $absensiToday->status === 'terlambat' ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800' }}">
                            {{ strtoupper($absensiToday->status) }}
                        </span>
                    @endif
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                    <div>
                        <span class="text-slate-400 block text-[10px] font-medium">Waktu Pulang</span>
                        <span class="font-extrabold text-slate-800 text-sm sm:text-base">
                            {{ $absensiToday?->jam_pulang ? substr($absensiToday->jam_pulang, 0, 5) . ' WIB' : '--:--' }}
                        </span>
                    </div>
                    @if ($absensiToday?->jam_pulang)
                        @if ($absensiToday->isPulangAwal())
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                PULANG LEBIH AWAL
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                TEPAT WAKTU
                            </span>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- RIWAYAT ABSENSI AMIL (30 HARI TERAKHIR) -->
    <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-sm border border-slate-200">
        <div class="flex items-center justify-between mb-3.5 pb-2.5 border-b border-slate-100">
            <div>
                <h3 class="font-bold text-slate-800 text-sm sm:text-base">Riwayat Kehadiran</h3>
                <p class="text-[11px] text-slate-400">Catatan 30 hari kalender terakhir</p>
            </div>
            <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-lg">
                {{ $history->count() }} Hari
            </span>
        </div>

        @if ($history->isEmpty())
            <p class="text-xs text-slate-400 py-6 text-center">Belum ada riwayat absensi yang tercatat.</p>
        @else
            <!-- 1. Tampilan Mobile Responsive List (< md) -->
            <div class="md:hidden divide-y divide-slate-100">
                @foreach ($history as $h)
                    <div class="py-3 first:pt-0 last:pb-0 flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1 space-y-1">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="font-bold text-xs text-slate-800">
                                    {{ $h->tanggal ? $h->tanggal->translatedFormat('d M Y') : '-' }}
                                </span>
                                @if ($h->status === 'hadir')
                                    <span class="px-2 py-0.5 rounded-md text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">Tepat Waktu</span>
                                @elseif ($h->status === 'terlambat')
                                    <span class="px-2 py-0.5 rounded-md text-[9px] font-bold bg-rose-50 text-rose-700 border border-rose-200/60">Terlambat</span>
                                @elseif ($h->status === 'izin' || $h->status === 'cuti')
                                    <span class="px-2 py-0.5 rounded-md text-[9px] font-bold bg-blue-50 text-blue-700 border border-blue-200/60">{{ ucfirst($h->status) }}</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md text-[9px] font-bold bg-slate-100 text-slate-700">{{ ucfirst($h->status) }}</span>
                                @endif

                                @if ($h->jam_pulang && $h->isPulangAwal())
                                    <span class="px-2 py-0.5 rounded-md text-[9px] font-bold bg-amber-50 text-amber-800 border border-amber-200/70">
                                        Pulang Awal
                                    </span>
                                @endif

                                @if ($h->tipe_masuk === 'dinas_luar' || $h->tipe_pulang === 'dinas_luar')
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[9px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/60" title="{{ $h->keterangan ?? $h->keterangan_pulang }}">
                                        <svg class="w-2.5 h-2.5 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                        <span>Tugas Luar</span>
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center gap-3 text-[11px] text-slate-500 font-mono">
                                <span>Masuk: <strong class="text-slate-800">{{ $h->jam_masuk ? substr($h->jam_masuk, 0, 5) : '--:--' }}</strong></span>
                                <span>•</span>
                                <span>Pulang: <strong class="text-slate-800">{{ $h->jam_pulang ? substr($h->jam_pulang, 0, 5) : '--:--' }}</strong></span>
                            </div>

                            @if ($h->keterangan || $h->keterangan_pulang)
                                <div class="text-[10px] text-slate-600 bg-slate-50 p-2 rounded-lg border border-slate-100 space-y-0.5">
                                    @if ($h->keterangan)
                                        <div class="line-clamp-1"><strong class="text-slate-700">Masuk:</strong> {{ $h->keterangan }}</div>
                                    @endif
                                    @if ($h->keterangan_pulang)
                                        <div class="line-clamp-2"><strong class="text-slate-700">Pulang:</strong> {{ $h->keterangan_pulang }}</div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Foto Masuk Thumbnail -->
                        @if ($h->foto_masuk)
                            <button type="button" onclick="showAttendancePhoto('{{ asset('storage/' . $h->foto_masuk) }}', '{{ $h->tanggal ? $h->tanggal->translatedFormat('d M Y') : '-' }}', '{{ $h->jam_masuk ? substr($h->jam_masuk, 0, 5) . ' WIB' : '--:--' }}', '{{ ucfirst($h->status) }}', '{{ addslashes($h->keterangan ?? '') }}')" class="w-10 h-10 rounded-xl overflow-hidden border border-slate-200 shrink-0 shadow-2xs block hover:scale-105 active:scale-95 transition cursor-pointer" title="Lihat Foto Presensi">
                                <img src="{{ asset('storage/' . $h->foto_masuk) }}" class="w-full h-full object-cover" alt="Selfie" onerror="this.onerror=null; this.src='/storage/{{ $h->foto_masuk }}';">
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- 2. Tampilan Desktop Table (>= md) -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-100 text-slate-400 uppercase font-semibold">
                        <tr>
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4">Jam Masuk</th>
                            <th class="py-3 px-4">Jam Pulang</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center">Foto Masuk</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($history as $h)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-4 font-semibold text-slate-700">
                                    {{ $h->tanggal ? $h->tanggal->translatedFormat('d M Y') : '-' }}
                                </td>
                                <td class="py-3 px-4 font-mono text-slate-800">
                                    {{ $h->jam_masuk ? substr($h->jam_masuk, 0, 5) . ' WIB' : '-' }}
                                </td>
                                <td class="py-3 px-4 font-mono text-slate-800">
                                    <div>{{ $h->jam_pulang ? substr($h->jam_pulang, 0, 5) . ' WIB' : '-' }}</div>
                                    @if ($h->jam_pulang && $h->isPulangAwal())
                                        <span class="inline-block mt-0.5 px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-50 text-amber-800 border border-amber-200 font-sans">
                                            Pulang Lebih Awal
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <div class="flex flex-col items-center gap-1">
                                        @if ($h->status === 'hadir')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Tepat Waktu</span>
                                        @elseif ($h->status === 'terlambat')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">Terlambat</span>
                                        @elseif ($h->status === 'izin' || $h->status === 'cuti')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">{{ ucfirst($h->status) }}</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">{{ $h->status }}</span>
                                        @endif

                                        @if ($h->jam_pulang && $h->isPulangAwal())
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-100 text-amber-800">
                                                Pulang Awal
                                            </span>
                                        @endif

                                        @if ($h->tipe_masuk === 'dinas_luar' || $h->tipe_pulang === 'dinas_luar')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-indigo-100 text-indigo-800" title="{{ $h->keterangan ?? $h->keterangan_pulang }}">
                                                <svg class="w-2.5 h-2.5 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                                <span>Tugas Luar</span>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if ($h->foto_masuk)
                                        <button type="button" onclick="showAttendancePhoto('{{ asset('storage/' . $h->foto_masuk) }}', '{{ $h->tanggal ? $h->tanggal->translatedFormat('d M Y') : '-' }}', '{{ $h->jam_masuk ? substr($h->jam_masuk, 0, 5) . ' WIB' : '--:--' }}', '{{ ucfirst($h->status) }}', '{{ addslashes($h->keterangan ?? '') }}')" class="inline-block w-8 h-8 rounded-lg overflow-hidden border border-slate-200 hover:scale-110 active:scale-95 transition cursor-pointer" title="Lihat Foto Presensi">
                                            <img src="{{ asset('storage/' . $h->foto_masuk) }}" class="w-full h-full object-cover" alt="Foto" onerror="this.onerror=null; this.src='/storage/{{ $h->foto_masuk }}';">
                                        </button>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Modal Detail Foto Absensi (Mobile & Desktop) -->
    <div id="modalPhotoPreview" class="fixed inset-0 z-50 hidden bg-slate-900/80 backdrop-blur-xs flex items-center justify-center p-4 animate-in fade-in duration-200" onclick="closeAttendancePhoto(event)">
        <div class="bg-white rounded-3xl max-w-sm sm:max-w-md w-full overflow-hidden shadow-2xl border border-slate-100" onclick="event.stopPropagation()">
            <!-- Header Modal -->
            <div class="p-4 bg-emerald-800 text-white flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-emerald-700 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h4 class="font-extrabold text-sm">Foto Presensi Kehadiran</h4>
                </div>
                <button type="button" onclick="closeAttendancePhoto()" class="w-7 h-7 rounded-lg bg-emerald-700/60 hover:bg-emerald-700 text-emerald-200 hover:text-white flex items-center justify-center transition cursor-pointer">
                    ✕
                </button>
            </div>

            <!-- Body Modal: Image -->
            <div class="p-4 bg-slate-900 flex items-center justify-center min-h-[220px] max-h-[360px] overflow-hidden">
                <img id="modalPhotoImage" src="" alt="Foto Selfie Presensi" class="max-h-[340px] w-auto max-w-full rounded-2xl object-contain shadow-md">
            </div>

            <!-- Footer Modal: Info & Aksi -->
            <div class="p-4 bg-slate-50 border-t border-slate-100 space-y-3">
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div class="p-2 rounded-xl bg-white border border-slate-200/80">
                        <span class="text-[10px] text-slate-400 block font-semibold">Tanggal & Jam</span>
                        <span id="modalPhotoTime" class="font-bold text-slate-800 text-[11px] block mt-0.5"></span>
                    </div>
                    <div class="p-2 rounded-xl bg-white border border-slate-200/80">
                        <span class="text-[10px] text-slate-400 block font-semibold">Status Presensi</span>
                        <span id="modalPhotoStatus" class="font-extrabold text-emerald-700 text-[11px] block mt-0.5"></span>
                    </div>
                </div>

                <div id="modalPhotoKetContainer" class="hidden p-2 rounded-xl bg-white border border-slate-200/80 text-[11px] text-slate-600">
                    <span class="text-[10px] text-slate-400 block font-semibold mb-0.5">Keterangan:</span>
                    <span id="modalPhotoKet" class="italic"></span>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <a id="modalPhotoLink" href="#" target="_blank" class="flex-1 py-2 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs text-center transition flex items-center justify-center gap-1.5 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        <span>Buka File Asli</span>
                    </a>
                    <button type="button" onclick="closeAttendancePhoto()" class="py-2 px-4 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs transition cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentLat = null;
    let currentLng = null;
    let hasValidLocation = false;
    let hasSnapshot = false;
    let currentPresenceMode = 'kantor'; // 'kantor' atau 'dinas_luar'
    let officeCheckResult = null;
    const isClockOutLockedKantor = {{ (isset($clockOutEligibility) && ! $clockOutEligibility['can_clock_out']) ? 'true' : 'false' }};

    // Switch Mode Presensi (Kantor vs Tugas Luar)
    function switchPresenceMode(mode) {
        currentPresenceMode = mode;
        const btnKantor = document.getElementById('btnModeKantor');
        const btnDinasLuar = document.getElementById('btnModeDinasLuar');
        const bannerDinasLuar = document.getElementById('bannerDinasLuar');
        const inTipeIn = document.getElementById('inputTipePresensiIn');
        const inTipeOut = document.getElementById('inputTipePresensiOut');
        const boxKetIn = document.getElementById('boxKeteranganIn');
        const boxKetOut = document.getElementById('boxKeteranganOut');
        const txtIn = document.getElementById('btnClockInText');
        const txtOut = document.getElementById('btnClockOutText');
        const boxLockClockOut = document.getElementById('boxLockClockOut');

        if (mode === 'dinas_luar') {
            if (btnDinasLuar) btnDinasLuar.className = 'py-2 px-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 bg-indigo-600 text-white shadow-xs';
            if (btnKantor) btnKantor.className = 'py-2 px-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 text-slate-600 hover:text-slate-800';
            if (bannerDinasLuar) bannerDinasLuar.classList.remove('hidden');
            if (boxLockClockOut) boxLockClockOut.classList.add('hidden');
            if (inTipeIn) inTipeIn.value = 'dinas_luar';
            if (inTipeOut) inTipeOut.value = 'dinas_luar';
            if (boxKetIn) boxKetIn.classList.remove('hidden');
            if (boxKetOut) boxKetOut.classList.remove('hidden');
            if (txtIn) txtIn.innerText = 'Clock In (Tugas Luar)';
            if (txtOut) txtOut.innerText = 'Clock Out (Tugas Luar)';
        } else {
            if (btnKantor) btnKantor.className = 'py-2 px-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 bg-white text-emerald-800 shadow-xs';
            if (btnDinasLuar) btnDinasLuar.className = 'py-2 px-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 text-slate-600 hover:text-slate-800';
            if (bannerDinasLuar) bannerDinasLuar.classList.add('hidden');
            if (boxLockClockOut) boxLockClockOut.classList.remove('hidden');
            if (inTipeIn) inTipeIn.value = 'kantor';
            if (inTipeOut) inTipeOut.value = 'kantor';
            if (boxKetIn) boxKetIn.classList.add('hidden');
            if (boxKetOut) boxKetOut.classList.add('hidden');
            if (txtIn) txtIn.innerText = 'Clock In (Masuk Kerja)';
            if (txtOut) txtOut.innerText = 'Clock Out (Pulang Kerja)';
        }

        updateGpsStatusUI();
        checkSubmitState();
    }

    function updateGpsStatusUI() {
        const gpsIndicator = document.getElementById('gpsIndicator');
        const gpsText = document.getElementById('gpsText');
        const gpsSubText = document.getElementById('gpsSubText');
        if (!gpsIndicator || !gpsText || !gpsSubText) return;

        if (currentLat === null || currentLng === null) {
            return;
        }

        if (currentPresenceMode === 'dinas_luar') {
            hasValidLocation = true;
            gpsIndicator.className = 'w-3 h-3 rounded-full bg-indigo-500';
            gpsText.innerText = 'Lokasi GPS Terkunci (Mode Tugas Luar)';
            if (officeCheckResult) {
                gpsSubText.innerText = `Jarak sekitar ${Math.round(officeCheckResult.distance_meters)} meter dari ${officeCheckResult.office_name} • Lokasi lapangan sah`;
            } else {
                gpsSubText.innerText = `Koordinat: ${currentLat.toFixed(5)}, ${currentLng.toFixed(5)}`;
            }
        } else {
            if (officeCheckResult) {
                const nearestLive = document.getElementById('labelNearestOfficeLive');
                if (nearestLive) {
                    nearestLive.textContent = `${officeCheckResult.office_name} (${Math.round(officeCheckResult.distance_meters)}m)`;
                    if (officeCheckResult.valid) {
                        nearestLive.className = 'font-extrabold text-emerald-700 truncate max-w-[150px] text-right';
                    } else {
                        nearestLive.className = 'font-bold text-rose-600 truncate max-w-[150px] text-right';
                    }
                }

                if (officeCheckResult.valid) {
                    hasValidLocation = true;
                    gpsIndicator.className = 'w-3 h-3 rounded-full bg-emerald-500';
                    gpsText.innerText = `Lokasi Valid: Di dalam radius ${officeCheckResult.office_name}`;
                    gpsSubText.innerText = `Jarak sekitar ${Math.round(officeCheckResult.distance_meters)} meter (Batas: ${officeCheckResult.max_radius}m)`;
                } else {
                    hasValidLocation = false;
                    gpsIndicator.className = 'w-3 h-3 rounded-full bg-rose-500';
                    gpsText.innerText = `Di luar radius kantor!`;
                    gpsSubText.innerText = `Jarak ${Math.round(officeCheckResult.distance_meters)} meter dari ${officeCheckResult.office_name} (Batas: ${officeCheckResult.max_radius}m). Beralih ke "Tugas Luar" jika sedang dinas lapangan.`;
                }
            } else {
                hasValidLocation = true;
            }
        }
    }

    // 1. Geolocation API Browser
    function getLocation() {
        const gpsIndicator = document.getElementById('gpsIndicator');
        const gpsText = document.getElementById('gpsText');
        const gpsSubText = document.getElementById('gpsSubText');

        if (!navigator.geolocation) {
            gpsIndicator.className = 'w-3 h-3 rounded-full bg-rose-500';
            gpsText.innerText = 'Browser tidak mendukung GPS Geolocation';
            return;
        }

        gpsIndicator.className = 'w-3 h-3 rounded-full bg-amber-400 animate-ping';
        gpsText.innerText = 'Mengunci koordinat GPS Anda...';

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                currentLat = pos.coords.latitude;
                currentLng = pos.coords.longitude;

                // Masukkan ke input form
                const inLat = document.getElementById('inputLat');
                const inLng = document.getElementById('inputLng');
                if (inLat) inLat.value = currentLat;
                if (inLng) inLng.value = currentLng;

                // Verifikasi ke backend endpoint
                fetch(`{{ route('amil.absensi.check-location') }}?lat=${currentLat}&lng=${currentLng}`)
                    .then(res => res.json())
                    .then(data => {
                        officeCheckResult = data;
                        updateGpsStatusUI();
                        checkSubmitState();
                    })
                    .catch(() => {
                        officeCheckResult = { valid: true, office_name: 'Kantor', distance_meters: 0, max_radius: 100 };
                        updateGpsStatusUI();
                        checkSubmitState();
                    });
            },
            (err) => {
                gpsIndicator.className = 'w-3 h-3 rounded-full bg-rose-500';
                gpsText.innerText = 'Gagal mengakses GPS: ' + err.message;
                gpsSubText.innerText = 'Mohon izinkan akses lokasi pada browser';
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    }

    // 2. Mode Foto (Kamera vs Choose File)
    let currentFotoMode = 'cam';
    let selectedFile = null;

    function switchFotoMode(mode) {
        currentFotoMode = mode;
        const panelCam = document.getElementById('panelCam');
        const panelFile = document.getElementById('panelFile');
        const tabBtnCam = document.getElementById('tabBtnCam');
        const tabBtnFile = document.getElementById('tabBtnFile');

        if (mode === 'cam') {
            panelCam.classList.remove('hidden');
            panelFile.classList.add('hidden');
            tabBtnCam.className = 'py-1 px-2.5 rounded-lg bg-white text-emerald-800 font-bold shadow-xs transition flex items-center gap-1.5';
            tabBtnFile.className = 'py-1 px-2.5 rounded-lg text-slate-600 hover:text-slate-800 transition flex items-center gap-1.5';

            // Jika sebelumnya pakai file, cek snapshot status kamera
            const inFoto = document.getElementById('inputFoto');
            hasSnapshot = inFoto && inFoto.value !== '';
        } else {
            panelCam.classList.add('hidden');
            panelFile.classList.remove('hidden');
            tabBtnFile.className = 'py-1 px-2.5 rounded-lg bg-white text-emerald-800 font-bold shadow-xs transition flex items-center gap-1.5';
            tabBtnCam.className = 'py-1 px-2.5 rounded-lg text-slate-600 hover:text-slate-800 transition flex items-center gap-1.5';

            // Matikan kamera jika sedang aktif
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }

            // Jika sudah ada file yang dipilih
            hasSnapshot = selectedFile !== null;
        }

        checkSubmitState();
    }

    // Penanganan Choose File
    function handleFileSelect(event) {
        const file = event.target.files[0];
        if (!file) return;

        if (!file.type.startsWith('image/')) {
            alert('File yang dipilih harus berupa gambar (JPG, PNG, WEBP)');
            event.target.value = '';
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            alert('Ukuran file maksimal adalah 5MB');
            event.target.value = '';
            return;
        }

        selectedFile = file;

        // Preview gambar
        const reader = new FileReader();
        reader.onload = (e) => {
            const previewImg = document.getElementById('filePreviewImg');
            const promptBox = document.getElementById('fileUploadPrompt');
            const previewContainer = document.getElementById('filePreviewContainer');
            const nameBadge = document.getElementById('fileNameBadge');

            if (previewImg) previewImg.src = e.target.result;
            if (nameBadge) nameBadge.innerText = `${file.name} (${(file.size / 1024).toFixed(1)} KB)`;
            if (promptBox) promptBox.classList.add('hidden');
            if (previewContainer) previewContainer.classList.remove('hidden');

            // Copy file ke form input file yang aktif
            assignFileToForms(file);

            // Bersihkan input base64 karena sekarang pakai file
            const inFoto = document.getElementById('inputFoto');
            if (inFoto) inFoto.value = '';

            hasSnapshot = true;
            checkSubmitState();
        };
        reader.readAsDataURL(file);
    }

    function clearFileSelect() {
        selectedFile = null;
        const fileInput = document.getElementById('inputFilePicker');
        if (fileInput) fileInput.value = '';

        const promptBox = document.getElementById('fileUploadPrompt');
        const previewContainer = document.getElementById('filePreviewContainer');
        if (promptBox) promptBox.classList.remove('hidden');
        if (previewContainer) previewContainer.classList.add('hidden');

        assignFileToForms(null);

        hasSnapshot = false;
        checkSubmitState();
    }

    function assignFileToForms(file) {
        const fileIn = document.getElementById('formClockInFile');
        const fileOut = document.getElementById('formClockOutFile');

        if (!file) {
            if (fileIn) fileIn.value = '';
            if (fileOut) fileOut.value = '';
            return;
        }

        try {
            const dt = new DataTransfer();
            dt.items.add(file);
            if (fileIn) fileIn.files = dt.files;
            if (fileOut) fileOut.files = dt.files;
        } catch (e) {
            console.error('DataTransfer not supported:', e);
        }
    }

    // 3. Kamera WebRTC & Selfie
    let stream = null;
    const video = document.getElementById('webcam');
    const canvas = document.getElementById('canvas');
    const photoPreview = document.getElementById('photoPreview');
    const btnStartCam = document.getElementById('btnStartCam');
    const btnCapture = document.getElementById('btnCapture');
    const btnRetake = document.getElementById('btnRetake');
    const camPlaceholder = document.getElementById('camPlaceholder');

    async function startCamera() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } },
                audio: false
            });
            video.srcObject = stream;
            camPlaceholder.classList.add('hidden');
            btnStartCam.classList.add('hidden');
            btnCapture.disabled = false;
        } catch (err) {
            alert('Tidak dapat mengakses webcam/kamera selfie: ' + err.message + '\n\nSilakan beralih ke tab "Choose File" untuk memilih foto dari perangkat Anda.');
            switchFotoMode('file');
        }
    }

    function takeSnapshot() {
        if (!stream) return;

        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        const dataUrl = canvas.toDataURL('image/jpeg', 0.85);
        photoPreview.src = dataUrl;
        photoPreview.classList.remove('hidden');

        const inFoto = document.getElementById('inputFoto');
        if (inFoto) inFoto.value = dataUrl;

        // Reset file jika snapshot kamera diambil
        assignFileToForms(null);

        hasSnapshot = true;
        btnCapture.classList.add('hidden');
        btnRetake.classList.remove('hidden');

        // Stop stream
        stream.getTracks().forEach(track => track.stop());

        checkSubmitState();
    }

    function retakeSnapshot() {
        hasSnapshot = false;
        photoPreview.classList.add('hidden');
        btnRetake.classList.add('hidden');
        btnCapture.classList.remove('hidden');
        const inFoto = document.getElementById('inputFoto');
        if (inFoto) inFoto.value = '';

        startCamera();
        checkSubmitState();
    }

    function checkSubmitState() {
        let noteValid = true;
        if (currentPresenceMode === 'dinas_luar') {
            const inKetIn = document.getElementById('inputKeteranganIn');
            const inKetOut = document.getElementById('inputKeteranganOut');
            if (inKetIn && inKetIn.offsetParent !== null) {
                noteValid = inKetIn.value.trim().length >= 5;
            } else if (inKetOut && inKetOut.offsetParent !== null) {
                noteValid = inKetOut.value.trim().length >= 5;
            }
        }

        let canClockOutByPolicy = true;
        if (currentPresenceMode === 'kantor' && isClockOutLockedKantor) {
            canClockOutByPolicy = false;
        }

        const canSubmitIn = hasValidLocation && hasSnapshot && noteValid;
        const canSubmitOut = hasValidLocation && hasSnapshot && noteValid && canClockOutByPolicy;

        const btnIn = document.getElementById('btnClockIn');
        const btnOut = document.getElementById('btnClockOut');

        if (btnIn) btnIn.disabled = !canSubmitIn;
        if (btnOut) {
            btnOut.disabled = !canSubmitOut;
            if (currentPresenceMode === 'kantor' && isClockOutLockedKantor) {
                btnOut.title = 'Presensi pulang kantor baru dibuka pukul {{ $clockOutEligibility['min_clock_out_time'] ?? '15:30' }} WIB';
            } else {
                btnOut.removeAttribute('title');
            }
        }
    }

    // Jalankan deteksi GPS & event listener saat halaman dimuat
    document.addEventListener('DOMContentLoaded', () => {
        getLocation();

        const inKetIn = document.getElementById('inputKeteranganIn');
        const inKetOut = document.getElementById('inputKeteranganOut');
        if (inKetIn) inKetIn.addEventListener('input', checkSubmitState);
        if (inKetOut) inKetOut.addEventListener('input', checkSubmitState);
    });

    // Modal Foto Presensi
    function showAttendancePhoto(url, tanggal, jam, status, keterangan) {
        const modal = document.getElementById('modalPhotoPreview');
        const img = document.getElementById('modalPhotoImage');
        const time = document.getElementById('modalPhotoTime');
        const statusEl = document.getElementById('modalPhotoStatus');
        const ketContainer = document.getElementById('modalPhotoKetContainer');
        const ket = document.getElementById('modalPhotoKet');
        const link = document.getElementById('modalPhotoLink');

        if (modal && img) {
            img.src = url;
            if (time) time.textContent = `${tanggal} - ${jam}`;
            if (statusEl) statusEl.textContent = status;
            if (link) link.href = url;

            if (keterangan && keterangan.trim() !== '') {
                if (ket) ket.textContent = keterangan;
                if (ketContainer) ketContainer.classList.remove('hidden');
            } else {
                if (ketContainer) ketContainer.classList.add('hidden');
            }

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeAttendancePhoto(event) {
        const modal = document.getElementById('modalPhotoPreview');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }
</script>
@endsection
