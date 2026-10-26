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
            <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 space-y-3">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                            <span class="text-emerald-600">📍</span> Titik Peta Lokasi Kegiatan
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

                <!-- Square Map Container (TIDAK TERLALU BESAR) -->
                <div class="flex justify-center w-full">
                    <div 
                        id="mapAmilAktivitas" 
                        class="w-full max-w-[340px] aspect-square rounded-2xl border-2 border-emerald-500/30 overflow-hidden shadow-inner relative z-0"
                        style="height: 320px; max-height: 320px;"
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
                        🔄 Salin Alamat dari Titik Ini
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

        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Foto Dokumentasi Kegiatan</label>
            <p class="text-[11px] text-slate-400 mb-2">Unggah foto saat kunjungan, serah terima, atau rapat (Format: JPG, PNG maksimal 5MB)</p>
            <input type="file" name="foto" id="inputFotoAktivitas" accept="image/*" onchange="previewImg(this, 'previewAktivitasBox', 'previewAktivitasImg')" class="w-full text-xs text-slate-500 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700 cursor-pointer">
            <div id="previewAktivitasBox" class="hidden mt-3 p-2.5 rounded-2xl border border-emerald-200 bg-emerald-50/40 inline-block">
                <img id="previewAktivitasImg" src="" alt="Preview Dokumentasi" class="max-h-48 rounded-xl object-contain">
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
                btn.className = 'w-full text-left px-2 py-1.5 rounded-lg hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 transition truncate block';
                btn.innerHTML = `📍 ${item.display_name}`;
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
