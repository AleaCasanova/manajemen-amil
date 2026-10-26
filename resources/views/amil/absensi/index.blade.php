@extends('layouts.amil')

@section('title', 'Presensi & Absensi Amil')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Presensi Kehadiran Amil</h1>
            <p class="text-sm text-slate-500">Clock-in dan Clock-out berbasis Geolocation GPS & Foto Selfie</p>
        </div>
        <div class="text-xs font-semibold px-3 py-1.5 rounded-xl bg-slate-100 text-slate-600 self-start sm:self-auto border border-slate-200">
            📅 {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
        </div>
    </div>

    <!-- PRESENSI CARD UTAMA -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
        <!-- Kolom Kiri: Form Absensi (Kamera & GPS) -->
        <div class="md:col-span-7 bg-white rounded-3xl p-6 shadow-sm border border-slate-200 flex flex-col justify-between">
            <div>
                <!-- Status Hari Ini Header -->
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Status Hari Ini</span>
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
                </div>

                <!-- MODE PRESENSI SELECTOR (KANTOR VS TUGAS LUAR) -->
                @if (!$absensiToday || !$absensiToday->jam_pulang)
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Tipe Presensi Saat Ini:</label>
                        <div class="p-1 bg-slate-100 rounded-2xl border border-slate-200 grid grid-cols-2 gap-1">
                            <button type="button" id="btnModeKantor" onclick="switchPresenceMode('kantor')" class="py-2 px-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 bg-white text-emerald-800 shadow-xs">
                                <span>🏢 Hadir di Kantor</span>
                            </button>
                            <button type="button" id="btnModeDinasLuar" onclick="switchPresenceMode('dinas_luar')" class="py-2 px-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 text-slate-600 hover:text-slate-800">
                                <span>🚗 Tugas Lapangan / Luar</span>
                            </button>
                        </div>
                    </div>

                    <!-- Info banner jika Mode Tugas Luar aktif -->
                    <div id="bannerDinasLuar" class="hidden mb-4 p-3.5 rounded-2xl bg-indigo-50 border border-indigo-200 text-xs text-indigo-900 space-y-1">
                        <div class="flex items-center gap-2 font-extrabold text-indigo-950">
                            <svg class="w-4 h-4 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Mode Tugas Lapangan / Luar Kantor Aktif</span>
                        </div>
                        <p class="text-[11px] text-indigo-700">Radius kantor dilewati untuk dinas luar. Pastikan GPS HP tetap aktif & wajib mengisi rincian tugas/kegiatan Anda.</p>
                    </div>
                @endif

                <!-- GPS & Geofencing Status Box -->
                <div id="gpsStatusBox" class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs mb-4 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span id="gpsIndicator" class="w-3 h-3 rounded-full bg-slate-400"></span>
                        <div>
                            <span id="gpsText" class="font-bold text-slate-700 block">Mendeteksi lokasi GPS Anda...</span>
                            <span id="gpsSubText" class="text-slate-400 text-[11px]">Pastikan izin lokasi pada browser diaktifkan</span>
                        </div>
                    </div>
                    <button type="button" onclick="getLocation()" class="p-1.5 rounded-lg hover:bg-slate-200 text-slate-600 transition" title="Refresh Lokasi GPS">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </button>
                </div>

                <!-- Tab Pemilihan Metode Foto Selfie -->
                @if (!$absensiToday || !$absensiToday->jam_pulang)
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-700">Foto Selfie Kehadiran <span class="text-rose-500">*</span></span>
                            <div class="inline-flex p-1 rounded-xl bg-slate-100 border border-slate-200 text-xs font-semibold">
                                <button type="button" id="tabBtnCam" onclick="switchFotoMode('cam')" class="py-1 px-3 rounded-lg bg-white text-emerald-700 font-bold shadow-xs transition">
                                    📷 Kamera
                                </button>
                                <button type="button" id="tabBtnFile" onclick="switchFotoMode('file')" class="py-1 px-3 rounded-lg text-slate-600 hover:text-slate-800 transition">
                                    📁 Choose File
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
                                <button type="button" id="btnStartCam" onclick="startCamera()" class="flex-1 py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                                    📷 Buka Kamera
                                </button>
                                <button type="button" id="btnCapture" onclick="takeSnapshot()" disabled class="flex-1 py-2 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 disabled:bg-slate-300 text-white font-bold text-xs transition">
                                    📸 Ambil Foto Selfie
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
        <div class="md:col-span-5 space-y-4">
            <!-- Kartu Jam Kerja -->
            <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-200 text-xs space-y-3">
                <h3 class="font-bold text-slate-800 text-sm border-b border-slate-100 pb-2.5">Ketentuan Jam Kerja</h3>
                
                <div class="grid grid-cols-2 gap-3">
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-400 block text-[10px]">Jam Masuk</span>
                        <span class="font-extrabold text-slate-800 text-sm">{{ $jamKerja?->jam_masuk ?? '08:00' }} WIB</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-400 block text-[10px]">Jam Pulang</span>
                        <span class="font-extrabold text-slate-800 text-sm">{{ $jamKerja?->jam_pulang ?? '17:00' }} WIB</span>
                    </div>
                </div>

                <div class="flex items-center justify-between text-[11px] text-slate-500 pt-1">
                    <span>Toleransi Keterlambatan:</span>
                    <span class="font-bold text-slate-700">{{ $jamKerja?->toleransi_menit ?? 15 }} Menit</span>
                </div>

                <div class="flex items-center justify-between text-[11px] text-slate-500">
                    <span>Lokasi Kantor:</span>
                    <span class="font-bold text-slate-700">{{ $kantor?->nama_lokasi ?? 'Kantor Pusat' }}</span>
                </div>

                <div class="flex items-center justify-between text-[11px] text-slate-500">
                    <span>Radius Geofencing:</span>
                    <span class="font-bold text-emerald-700">{{ $kantor?->radius_meter ?? 300 }} Meter</span>
                </div>
            </div>

            <!-- Kartu Jam Presensi Anda Hari Ini -->
            <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-200 text-xs space-y-3">
                <h3 class="font-bold text-slate-800 text-sm border-b border-slate-100 pb-2.5">Catatan Waktu Hari Ini</h3>

                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-slate-400 block text-[11px]">Waktu Masuk</span>
                        <span class="font-extrabold text-slate-800 text-base">
                            {{ $absensiToday?->jam_masuk ? substr($absensiToday->jam_masuk, 0, 5) . ' WIB' : '--:--' }}
                        </span>
                    </div>
                    @if ($absensiToday?->status)
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $absensiToday->status === 'terlambat' ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800' }}">
                            {{ strtoupper($absensiToday->status) }}
                        </span>
                    @endif
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                    <div>
                        <span class="text-slate-400 block text-[11px]">Waktu Pulang</span>
                        <span class="font-extrabold text-slate-800 text-base">
                            {{ $absensiToday?->jam_pulang ? substr($absensiToday->jam_pulang, 0, 5) . ' WIB' : '--:--' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TABEL RIWAYAT ABSENSI AMIL (30 HARI TERAKHIR) -->
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
        <h3 class="font-bold text-slate-800 text-base mb-4">Riwayat Kehadiran (30 Hari Terakhir)</h3>

        @if ($history->isEmpty())
            <p class="text-xs text-slate-400 py-6 text-center">Belum ada riwayat absensi yang tercatat.</p>
        @else
            <div class="overflow-x-auto">
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
                                    {{ $h->jam_pulang ? substr($h->jam_pulang, 0, 5) . ' WIB' : '-' }}
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

                                        @if ($h->tipe_masuk === 'dinas_luar' || $h->tipe_pulang === 'dinas_luar')
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-indigo-100 text-indigo-800" title="{{ $h->keterangan ?? $h->keterangan_pulang }}">
                                                🚗 Tugas Luar
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if ($h->foto_masuk)
                                        <a href="{{ asset('storage/' . $h->foto_masuk) }}" target="_blank" class="inline-block w-8 h-8 rounded-lg overflow-hidden border border-slate-200">
                                            <img src="{{ asset('storage/' . $h->foto_masuk) }}" class="w-full h-full object-cover" alt="Foto">
                                        </a>
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
</div>

<script>
    let currentLat = null;
    let currentLng = null;
    let hasValidLocation = false;
    let hasSnapshot = false;
    let currentPresenceMode = 'kantor'; // 'kantor' atau 'dinas_luar'
    let officeCheckResult = null;

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

        if (mode === 'dinas_luar') {
            if (btnDinasLuar) btnDinasLuar.className = 'py-2 px-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 bg-indigo-600 text-white shadow-xs';
            if (btnKantor) btnKantor.className = 'py-2 px-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 text-slate-600 hover:text-slate-800';
            if (bannerDinasLuar) bannerDinasLuar.classList.remove('hidden');
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
                if (officeCheckResult.valid) {
                    hasValidLocation = true;
                    gpsIndicator.className = 'w-3 h-3 rounded-full bg-emerald-500';
                    gpsText.innerText = `Lokasi Valid: Di dalam radius ${officeCheckResult.office_name}`;
                    gpsSubText.innerText = `Jarak sekitar ${Math.round(officeCheckResult.distance_meters)} meter (Batas: ${officeCheckResult.max_radius}m)`;
                } else {
                    hasValidLocation = false;
                    gpsIndicator.className = 'w-3 h-3 rounded-full bg-rose-500';
                    gpsText.innerText = 'Di luar radius kantor!';
                    gpsSubText.innerText = `Jarak ${Math.round(officeCheckResult.distance_meters)} meter (Batas: ${officeCheckResult.max_radius}m). Beralih ke "Tugas Luar" jika sedang dinas lapangan.`;
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
            tabBtnCam.className = 'py-1 px-3 rounded-lg bg-white text-emerald-700 font-bold shadow-xs transition';
            tabBtnFile.className = 'py-1 px-3 rounded-lg text-slate-600 hover:text-slate-800 transition';

            // Jika sebelumnya pakai file, cek snapshot status kamera
            const inFoto = document.getElementById('inputFoto');
            hasSnapshot = inFoto && inFoto.value !== '';
        } else {
            panelCam.classList.add('hidden');
            panelFile.classList.remove('hidden');
            tabBtnFile.className = 'py-1 px-3 rounded-lg bg-white text-emerald-700 font-bold shadow-xs transition';
            tabBtnCam.className = 'py-1 px-3 rounded-lg text-slate-600 hover:text-slate-800 transition';

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

        const canSubmit = hasValidLocation && hasSnapshot && noteValid;
        const btnIn = document.getElementById('btnClockIn');
        const btnOut = document.getElementById('btnClockOut');

        if (btnIn) btnIn.disabled = !canSubmit;
        if (btnOut) btnOut.disabled = !canSubmit;
    }

    // Jalankan deteksi GPS & event listener saat halaman dimuat
    document.addEventListener('DOMContentLoaded', () => {
        getLocation();

        const inKetIn = document.getElementById('inputKeteranganIn');
        const inKetOut = document.getElementById('inputKeteranganOut');
        if (inKetIn) inKetIn.addEventListener('input', checkSubmitState);
        if (inKetOut) inKetOut.addEventListener('input', checkSubmitState);
    });
</script>
@endsection
