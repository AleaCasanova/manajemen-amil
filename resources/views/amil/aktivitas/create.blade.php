@extends('layouts.amil')

@section('title', 'Buat Laporan Aktivitas Harian')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('amil.aktivitas.index') }}" class="text-xs font-bold text-slate-500 hover:text-emerald-700 transition inline-flex items-center gap-1 mb-2">
            ← Kembali ke Laporan Aktivitas
        </a>
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Laporan Aktivitas Harian</h1>
        <p class="text-sm text-slate-500">Laporkan kunjungan donatur, jemput donasi, atau tugas lapangan lainnya</p>
    </div>

    <form action="{{ route('amil.aktivitas.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 space-y-5">
        @csrf

        <div class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Kegiatan <span class="text-rose-500">*</span></label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required class="w-full rounded-xl border border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Lokasi / Instansi Terkait</label>
                    <input type="text" id="amilLokasiInput" name="lokasi" value="{{ old('lokasi') }}" placeholder="cth: Kantor PT Berkah / Rumah H. Ahmad" class="w-full rounded-xl border border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
            </div>

            <!-- Hidden Inputs for Coordinates -->
            <input type="hidden" name="lat" id="amilLatInput" value="{{ old('lat') }}">
            <input type="hidden" name="lng" id="amilLngInput" value="{{ old('lng') }}">

            <!-- Square Interactive Map Picker -->
            <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-3.5 sm:p-4 space-y-3">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>Titik Peta Lokasi Kegiatan</span>
                        </span>
                        <p class="text-[11px] text-slate-500">
                            Klik peta atau geser pin untuk menentukan titik lokasi yang dikunjungi
                        </p>
                    </div>

                    <!-- GPS Location Button -->
                    <button 
                        type="button" 
                        id="btnGpsAmil" 
                        onclick="getGpsLocationAmil()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition active:scale-95 cursor-pointer"
                    >
                        <svg id="gpsIconNormal" width="14" height="14" style="width: 14px; height: 14px; display: inline-block;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"></circle>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v3m0 14v3M2 12h3m14 0h3" />
                        </svg>
                        <svg id="gpsIconSpin" width="14" height="14" style="width: 14px; height: 14px; display: inline-block;" class="animate-spin hidden" fill="none" viewBox="0 0 24 24">
                            <circle opacity="0.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path opacity="0.75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span id="gpsLabelAmil">Lokasi GPS Saya</span>
                    </button>
                </div>

                <!-- Search Location -->
                <div class="flex items-center gap-2">
                    <div class="relative flex-1">
                        <input 
                            type="text" 
                            id="searchMapInputAmil" 
                            placeholder="Cari jalan, desa, kecamatan, atau tempat..." 
                            onkeydown="if(event.key === 'Enter'){ event.preventDefault(); searchPlaceAmil(); }"
                            class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl border border-slate-300 bg-white text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                        >
                        <svg width="14" height="14" style="width: 14px; height: 14px; display: inline-block;" class="text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <button 
                        type="button" 
                        onclick="searchPlaceAmil()" 
                        class="px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-200 hover:bg-slate-300 text-slate-700 transition cursor-pointer"
                    >
                        Cari
                    </button>
                </div>

                <!-- Search Results List -->
                <div id="searchResultsAmil" class="hidden p-2 bg-white rounded-xl border border-slate-200 max-h-36 overflow-y-auto space-y-1 text-xs"></div>

                <!-- Square Map Container (Bentuk Persegi Proporsional 1:1) -->
                <div class="w-full flex justify-center">
                    <div 
                        id="mapAmilAktivitas" 
                        class="w-full aspect-square max-w-[320px] rounded-2xl border-2 border-emerald-500/30 overflow-hidden shadow-inner relative z-0"
                    ></div>
                </div>

                <!-- Status Coordinates and Reverse Geocode -->
                <div class="flex flex-wrap items-center justify-between gap-2 pt-1 text-[11px] text-slate-500">
                    <div class="font-mono">
                        Titik: <strong id="lblAmilLat" class="text-slate-700">{{ old('lat', '-') }}</strong>, 
                        <strong id="lblAmilLng" class="text-slate-700">{{ old('lng', '-') }}</strong>
                    </div>

                    <button 
                        type="button" 
                        onclick="reverseGeocodeAmil()" 
                        id="btnReverseAmil"
                        class="text-[11px] text-emerald-700 hover:underline font-semibold inline-flex items-center gap-1 cursor-pointer"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span>Salin Alamat dari Titik Ini</span>
                    </button>
                </div>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Judul Aktivitas / Tugas <span class="text-rose-500">*</span></label>
            <input type="text" name="judul" value="{{ old('judul') }}" required placeholder="cth: Jemput Donasi Zakat Maal & Silaturahmi Muzakki" class="w-full rounded-xl border border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Deskripsi Lengkap Kegiatan <span class="text-rose-500">*</span></label>
            <textarea name="deskripsi" rows="4" required placeholder="Ceritakan rincian aktivitas yang Anda lakukan, hasil pertemuan, tindak lanjut, atau kendala di lapangan..." class="w-full rounded-xl border border-slate-300 py-2.5 px-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">{{ old('deskripsi') }}</textarea>
        </div>

        <!-- FOTO DOKUMENTASI KEGIATAN (KAMERA LANGSUNG & PILIH FILE) -->
        <div class="space-y-3">
            <div class="flex items-center justify-between flex-wrap gap-2">
                <div>
                    <label class="block text-xs font-bold text-slate-700">Foto Dokumentasi Kegiatan</label>
                    <p class="text-[11px] text-slate-400">Ambil foto langsung di lokasi kegiatan atau pilih dari galeri</p>
                </div>

                <!-- Tab Selector: Kamera vs File -->
                <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl text-xs font-bold">
                    <button type="button" id="tabAktivitasCam" onclick="switchAktivitasFotoMode('cam')" class="py-1 px-2.5 rounded-lg bg-white text-emerald-700 shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Kamera</span>
                    </button>
                    <button type="button" id="tabAktivitasFile" onclick="switchAktivitasFotoMode('file')" class="py-1 px-2.5 rounded-lg text-slate-600 hover:text-slate-800 transition flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Pilih File</span>
                    </button>
                </div>
            </div>

            <!-- Hidden input untuk menampung data foto base64 dari kamera -->
            <input type="hidden" name="foto_base64" id="inputAktivitasFotoBase64">

            <!-- MODE 1: KAMERA WEBRTC LANGSUNG -->
            <div id="panelAktivitasCam" class="space-y-3">
                <div class="relative rounded-2xl overflow-hidden bg-slate-900 aspect-video flex items-center justify-center border border-slate-200">
                    <video id="webcamAktivitas" autoplay playsinline class="w-full h-full object-cover transition-transform duration-200"></video>
                    <canvas id="canvasAktivitas" class="hidden"></canvas>
                    <img id="photoPreviewAktivitas" class="hidden w-full h-full object-cover absolute inset-0 z-10" alt="Foto Kegiatan">

                    <!-- Badges Info Kamera -->
                    <div id="camBadgesAktivitas" class="absolute top-2.5 left-2.5 z-20 flex items-center gap-1.5 hidden">
                        <span id="badgeFacingAktivitas" class="text-[10px] font-bold bg-slate-900/80 text-white backdrop-blur-xs px-2.5 py-1 rounded-lg border border-white/20">
                            Kamera Belakang
                        </span>
                    </div>

                    <!-- Floating Buttons (Ganti Kamera Depan/Belakang) -->
                    <div id="camFloatingAktivitas" class="absolute top-2.5 right-2.5 z-20 flex items-center gap-1.5 hidden">
                        <button type="button" onclick="switchAktivitasCamera()" class="p-1.5 px-2.5 rounded-xl bg-slate-900/80 text-white hover:bg-slate-800 backdrop-blur-xs border border-white/20 transition active:scale-95 shadow-md flex items-center gap-1.5 text-[11px] font-bold cursor-pointer" title="Ganti Kamera Depan / Belakang">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span>Ganti Kamera</span>
                        </button>
                    </div>

                    <!-- Placeholder Sebelum Start -->
                    <div id="camPlaceholderAktivitas" class="absolute inset-0 flex flex-col items-center justify-center text-white p-4 text-center bg-slate-900/90 z-20">
                        <svg class="w-10 h-10 text-emerald-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="text-xs font-bold">Kamera Belum Aktif</span>
                        <span class="text-[11px] text-slate-300 mt-0.5">Nyalakan kamera untuk mengambil dokumentasi di lokasi kegiatan</span>
                    </div>
                </div>

                <!-- Action Buttons Kamera -->
                <div class="flex items-center gap-2">
                    <button type="button" id="btnStartCamAktivitas" onclick="startAktivitasCamera()" class="flex-1 py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center justify-center gap-1.5 cursor-pointer">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <span>Buka Kamera</span>
                    </button>
                    <button type="button" id="btnCaptureAktivitas" onclick="takeAktivitasSnapshot()" disabled class="flex-1 py-2 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 disabled:bg-slate-300 text-white font-bold text-xs transition flex items-center justify-center gap-1.5 cursor-pointer">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Ambil Foto</span>
                    </button>
                    <button type="button" id="btnRetakeAktivitas" onclick="retakeAktivitasSnapshot()" class="hidden py-2 px-3 rounded-xl border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-50 transition cursor-pointer">
                        Ulangi Foto
                    </button>
                </div>
            </div>

            <!-- MODE 2: PILIH FILE BIASA DARI PERANGKAT -->
            <div id="panelAktivitasFile" class="hidden space-y-3">
                <input type="file" name="foto" id="inputFotoAktivitas" accept="image/*" onchange="previewImg(this, 'previewAktivitasBox', 'previewAktivitasImg')" class="w-full text-xs text-slate-500 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700 cursor-pointer">
                <div id="previewAktivitasBox" class="hidden mt-3 p-2.5 rounded-2xl border border-emerald-200 bg-emerald-50/40 inline-block">
                    <img id="previewAktivitasImg" src="" alt="Preview Dokumentasi" class="max-h-48 rounded-xl object-contain">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
            <a href="{{ route('amil.aktivitas.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-50 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-md shadow-emerald-600/20 transition">
                Kirim Laporan Aktivitas
            </button>
        </div>
    </form>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/leaflet/leaflet.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('vendor/leaflet/leaflet.js') }}"></script>
<script>
    let mapAmil = null;
    let markerAmil = null;
    let defaultLatAmil = -7.7027157;
    let defaultLngAmil = 109.0270034;
    let currentLatAmil = defaultLatAmil;
    let currentLngAmil = defaultLngAmil;

    // ==========================================
    // LOGIKA KAMERA AKTIVITAS & SWITCH KAMERA
    // ==========================================
    let streamAktivitas = null;
    let currentFacingModeAktivitas = 'environment'; // Default kamera belakang untuk foto kegiatan

    const videoAktivitas = document.getElementById('webcamAktivitas');
    const canvasAktivitas = document.getElementById('canvasAktivitas');
    const photoPreviewAktivitas = document.getElementById('photoPreviewAktivitas');
    const btnStartCamAktivitas = document.getElementById('btnStartCamAktivitas');
    const btnCaptureAktivitas = document.getElementById('btnCaptureAktivitas');
    const btnRetakeAktivitas = document.getElementById('btnRetakeAktivitas');
    const camPlaceholderAktivitas = document.getElementById('camPlaceholderAktivitas');
    const camBadgesAktivitas = document.getElementById('camBadgesAktivitas');
    const camFloatingAktivitas = document.getElementById('camFloatingAktivitas');
    const badgeFacingAktivitas = document.getElementById('badgeFacingAktivitas');

    function switchAktivitasFotoMode(mode) {
        const tabCam = document.getElementById('tabAktivitasCam');
        const tabFile = document.getElementById('tabAktivitasFile');
        const panelCam = document.getElementById('panelAktivitasCam');
        const panelFile = document.getElementById('panelAktivitasFile');

        if (mode === 'cam') {
            tabCam.className = 'py-1 px-2.5 rounded-lg bg-white text-emerald-700 shadow-xs transition flex items-center gap-1.5 cursor-pointer';
            tabFile.className = 'py-1 px-2.5 rounded-lg text-slate-600 hover:text-slate-800 transition flex items-center gap-1.5 cursor-pointer';
            panelCam.classList.remove('hidden');
            panelFile.classList.add('hidden');
        } else {
            tabFile.className = 'py-1 px-2.5 rounded-lg bg-white text-emerald-700 shadow-xs transition flex items-center gap-1.5 cursor-pointer';
            tabCam.className = 'py-1 px-2.5 rounded-lg text-slate-600 hover:text-slate-800 transition flex items-center gap-1.5 cursor-pointer';
            panelFile.classList.remove('hidden');
            panelCam.classList.add('hidden');

            if (streamAktivitas) {
                streamAktivitas.getTracks().forEach(t => t.stop());
                streamAktivitas = null;
            }
        }
    }

    function updateAktivitasCamUI() {
        if (videoAktivitas) {
            videoAktivitas.style.transform = currentFacingModeAktivitas === 'user' ? 'scaleX(-1)' : 'none';
        }
        if (badgeFacingAktivitas) {
            badgeFacingAktivitas.textContent = currentFacingModeAktivitas === 'user' ? 'Kamera Depan' : 'Kamera Belakang';
        }
    }

    async function startAktivitasCamera() {
        try {
            if (streamAktivitas) {
                streamAktivitas.getTracks().forEach(t => t.stop());
                streamAktivitas = null;
            }

            const constraints = {
                video: {
                    facingMode: currentFacingModeAktivitas,
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                },
                audio: false
            };

            try {
                streamAktivitas = await navigator.mediaDevices.getUserMedia(constraints);
            } catch (errConstraint) {
                streamAktivitas = await navigator.mediaDevices.getUserMedia({
                    video: true,
                    audio: false
                });
            }

            videoAktivitas.srcObject = streamAktivitas;
            updateAktivitasCamUI();

            camPlaceholderAktivitas.classList.add('hidden');
            btnStartCamAktivitas.classList.add('hidden');
            btnCaptureAktivitas.disabled = false;
            if (camBadgesAktivitas) camBadgesAktivitas.classList.remove('hidden');
            if (camFloatingAktivitas) camFloatingAktivitas.classList.remove('hidden');
        } catch (err) {
            alert('Tidak dapat mengakses kamera: ' + err.message + '\n\nSilakan pastikan izin kamera aktif, atau gunakan tab "Pilih File" untuk mengunggah dari galeri.');
            switchAktivitasFotoMode('file');
        }
    }

    async function switchAktivitasCamera() {
        currentFacingModeAktivitas = currentFacingModeAktivitas === 'user' ? 'environment' : 'user';
        await startAktivitasCamera();
    }

    function takeAktivitasSnapshot() {
        if (!streamAktivitas) return;

        canvasAktivitas.width = videoAktivitas.videoWidth || 640;
        canvasAktivitas.height = videoAktivitas.videoHeight || 480;
        const ctx = canvasAktivitas.getContext('2d');

        if (currentFacingModeAktivitas === 'user') {
            ctx.translate(canvasAktivitas.width, 0);
            ctx.scale(-1, 1);
        }
        ctx.drawImage(videoAktivitas, 0, 0, canvasAktivitas.width, canvasAktivitas.height);

        const dataUrl = canvasAktivitas.toDataURL('image/jpeg', 0.85);
        photoPreviewAktivitas.src = dataUrl;
        photoPreviewAktivitas.style.transform = 'none';
        photoPreviewAktivitas.classList.remove('hidden');

        const inFotoBase64 = document.getElementById('inputAktivitasFotoBase64');
        if (inFotoBase64) inFotoBase64.value = dataUrl;

        // Kosongkan file input biasa jika menggunakan kamera
        const inFotoFile = document.getElementById('inputFotoAktivitas');
        if (inFotoFile) inFotoFile.value = '';

        btnCaptureAktivitas.classList.add('hidden');
        btnRetakeAktivitas.classList.remove('hidden');
        if (camFloatingAktivitas) camFloatingAktivitas.classList.add('hidden');

        streamAktivitas.getTracks().forEach(t => t.stop());
        streamAktivitas = null;
    }

    function retakeAktivitasSnapshot() {
        photoPreviewAktivitas.classList.add('hidden');
        btnRetakeAktivitas.classList.add('hidden');
        btnCaptureAktivitas.classList.remove('hidden');
        const inFotoBase64 = document.getElementById('inputAktivitasFotoBase64');
        if (inFotoBase64) inFotoBase64.value = '';

        startAktivitasCamera();
    }

    function previewImg(input, boxId, imgId) {
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

    document.addEventListener('DOMContentLoaded', function() {
        initAmilMap();
    });

    function initAmilMap() {
        const latInput = document.getElementById('amilLatInput');
        const lngInput = document.getElementById('amilLngInput');

        if (latInput && latInput.value && !isNaN(parseFloat(latInput.value))) {
            currentLatAmil = parseFloat(latInput.value);
        }
        if (lngInput && lngInput.value && !isNaN(parseFloat(lngInput.value))) {
            currentLngAmil = parseFloat(lngInput.value);
        }

        const mapEl = document.getElementById('mapAmilAktivitas');
        if (!mapEl) return;

        const defaultIcon = L.icon({
            iconUrl: '{{ asset("vendor/leaflet/images/marker-icon.png") }}',
            iconRetinaUrl: '{{ asset("vendor/leaflet/images/marker-icon-2x.png") }}',
            shadowUrl: '{{ asset("vendor/leaflet/images/marker-shadow.png") }}',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        mapAmil = L.map('mapAmilAktivitas', {
            zoomControl: true,
            attributionControl: false
        }).setView([currentLatAmil, currentLngAmil], 16);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19
        }).addTo(mapAmil);

        markerAmil = L.marker([currentLatAmil, currentLngAmil], {
            draggable: true,
            icon: defaultIcon
        }).addTo(mapAmil);

        mapAmil.on('click', function(e) {
            setAmilCoordinates(e.latlng.lat, e.latlng.lng, false);
        });

        markerAmil.on('drag', function() {
            const pos = markerAmil.getLatLng();
            currentLatAmil = pos.lat;
            currentLngAmil = pos.lng;
            updateLabelsAmil();
        });

        markerAmil.on('dragend', function() {
            const pos = markerAmil.getLatLng();
            setAmilCoordinates(pos.lat, pos.lng, false);
        });

        updateLabelsAmil();

        setTimeout(function() {
            if (mapAmil) mapAmil.invalidateSize();
        }, 300);
    }

    function setAmilCoordinates(lat, lng, pan) {
        currentLatAmil = parseFloat(lat);
        currentLngAmil = parseFloat(lng);

        document.getElementById('amilLatInput').value = currentLatAmil.toFixed(7);
        document.getElementById('amilLngInput').value = currentLngAmil.toFixed(7);

        updateLabelsAmil();

        if (markerAmil) {
            markerAmil.setLatLng([currentLatAmil, currentLngAmil]);
        }

        if (pan && mapAmil) {
            mapAmil.setView([currentLatAmil, currentLngAmil], 16);
        }
    }

    function updateLabelsAmil() {
        const lblLat = document.getElementById('lblAmilLat');
        const lblLng = document.getElementById('lblAmilLng');
        if (lblLat) lblLat.innerText = currentLatAmil.toFixed(7);
        if (lblLng) lblLng.innerText = currentLngAmil.toFixed(7);
    }

    function getGpsLocationAmil() {
        if (!navigator.geolocation) {
            alert('Browser Anda tidak mendukung deteksi lokasi GPS.');
            return;
        }

        const btn = document.getElementById('btnGpsAmil');
        const iconNormal = document.getElementById('gpsIconNormal');
        const iconSpin = document.getElementById('gpsIconSpin');
        const label = document.getElementById('gpsLabelAmil');

        btn.disabled = true;
        iconNormal.classList.add('hidden');
        iconSpin.classList.remove('hidden');
        label.innerText = 'Mencari GPS...';

        navigator.geolocation.getCurrentPosition(
            function(pos) {
                btn.disabled = false;
                iconNormal.classList.remove('hidden');
                iconSpin.classList.add('hidden');
                label.innerText = 'Lokasi GPS Saya';

                setAmilCoordinates(pos.coords.latitude, pos.coords.longitude, true);
                // Also trigger reverse geocode automatically to make it even easier!
                reverseGeocodeAmil();
            },
            function(err) {
                btn.disabled = false;
                iconNormal.classList.remove('hidden');
                iconSpin.classList.add('hidden');
                label.innerText = 'Lokasi GPS Saya';
                alert('Gagal mendeteksi lokasi GPS: ' + (err.message || 'Izin akses lokasi ditolak.'));
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    }

    async function searchPlaceAmil() {
        const input = document.getElementById('searchMapInputAmil');
        const q = input ? input.value.trim() : '';
        if (!q) return;

        const resBox = document.getElementById('searchResultsAmil');
        resBox.innerHTML = '<span class="text-slate-400 py-1 block">Mencari lokasi...</span>';
        resBox.classList.remove('hidden');

        try {
            const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(q)}&limit=5`;
            const resp = await fetch(url, { headers: { 'Accept-Language': 'id,en' } });
            const data = await resp.json();

            if (!data || data.length === 0) {
                resBox.innerHTML = '<span class="text-rose-500 py-1 block">Lokasi tidak ditemukan. Coba kata kunci lain.</span>';
                return;
            }

            resBox.innerHTML = '';
            data.forEach(item => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'w-full text-left px-2 py-1.5 rounded-lg hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 transition truncate flex items-center gap-1.5 text-xs';
                btn.innerHTML = `<svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg><span class="truncate">${item.display_name}</span>`;
                btn.onclick = function() {
                    resBox.classList.add('hidden');
                    input.value = item.display_name;
                    setAmilCoordinates(parseFloat(item.lat), parseFloat(item.lon), true);
                    const lokasiInput = document.getElementById('amilLokasiInput');
                    if (lokasiInput && !lokasiInput.value.trim()) {
                        lokasiInput.value = item.display_name;
                    }
                };
                resBox.appendChild(btn);
            });
        } catch (e) {
            console.error(e);
            resBox.innerHTML = '<span class="text-rose-500 py-1 block">Gagal memuat pencarian. Cek koneksi internet.</span>';
        }
    }

    async function reverseGeocodeAmil() {
        const btn = document.getElementById('btnReverseAmil');
        const origText = btn.innerHTML;
        btn.innerHTML = '⏳ Menarik alamat...';
        btn.disabled = true;

        try {
            const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${currentLatAmil}&lon=${currentLngAmil}`;
            const resp = await fetch(url, { headers: { 'Accept-Language': 'id,en' } });
            const data = await resp.json();

            if (data && data.display_name) {
                const lokasiInput = document.getElementById('amilLokasiInput');
                if (lokasiInput) {
                    lokasiInput.value = data.display_name;
                }
            }
        } catch (e) {
            console.error(e);
        } finally {
            btn.innerHTML = origText;
            btn.disabled = false;
        }
    }
</script>
@endpush
