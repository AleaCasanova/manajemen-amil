@php
    $latField = $latField ?? 'lat';
    $lngField = $lngField ?? 'lng';
    $radiusField = $radiusField ?? null;
    $addressField = $addressField ?? null;
    $defaultLat = $defaultLat ?? -7.7027157;
    $defaultLng = $defaultLng ?? 109.0270034;
    $uniqueId = 'map_' . md5($latField . '_' . $lngField . '_' . uniqid());
@endphp

<div 
    wire:ignore
    x-data="filamentMapPicker({
        id: '{{ $uniqueId }}',
        latField: '{{ $latField }}',
        lngField: '{{ $lngField }}',
        radiusField: '{{ $radiusField }}',
        addressField: '{{ $addressField }}',
        defaultLat: {{ (float) $defaultLat }},
        defaultLng: {{ (float) $defaultLng }}
    })"
    x-init="init()"
    class="gsc-map-wrapper"
>
    <!-- Scoped Styles for Guaranteed Pixel-Perfect Full-Width Layout -->
    <style>
        .gsc-map-wrapper {
            width: 100%;
            margin: 6px 0;
            box-sizing: border-box;
        }
        .gsc-map-card {
            width: 100%;
            box-sizing: border-box;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            border-radius: 18px;
            padding: 16px 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .dark .gsc-map-card {
            border: 1px solid #27272a;
            background: #18181b;
            color: #f4f4f5;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.3);
        }
        .gsc-map-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding-bottom: 12px;
            margin-bottom: 14px;
            border-bottom: 1px solid #f1f5f9;
        }
        .dark .gsc-map-header {
            border-bottom: 1px solid #27272a;
        }
        .gsc-map-title {
            font-size: 13px;
            font-weight: 800;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
            color: #0f172a;
            letter-spacing: -0.01em;
        }
        .dark .gsc-map-title {
            color: #f8fafc;
        }
        .gsc-map-subtitle {
            font-size: 11px;
            color: #64748b;
            margin: 2px 0 0 0;
        }
        .dark .gsc-map-subtitle {
            color: #a1a1aa;
        }

        /* 2-Column Grid Layout: Map (Left) & Controls/Info (Right) */
        .gsc-map-grid {
            display: grid;
            grid-template-columns: 340px 1fr;
            gap: 20px;
            align-items: start;
        }
        @media (max-width: 860px) {
            .gsc-map-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Square Map Container (TETAP PERSEGI RAPI) */
        .gsc-map-left-col {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            width: 100%;
        }
        .gsc-map-square {
            width: 340px !important;
            height: 340px !important;
            max-width: 100%;
            aspect-ratio: 1 / 1;
            border-radius: 16px;
            overflow: hidden;
            border: 2px solid rgba(16, 185, 129, 0.45);
            position: relative;
            z-index: 1;
            box-shadow: inset 0 2px 6px rgba(0,0,0,0.2), 0 4px 6px -1px rgba(0,0,0,0.1);
            cursor: crosshair;
        }
        @media (max-width: 860px) {
            .gsc-map-square {
                width: 320px !important;
                height: 320px !important;
            }
        }

        /* Right Column (Controls, Info & Tools) */
        .gsc-map-right-col {
            display: flex;
            flex-direction: column;
            gap: 12px;
            width: 100%;
        }

        .gsc-search-bar {
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
        }
        .gsc-search-input {
            flex: 1;
            padding: 8px 12px;
            font-size: 12px;
            border-radius: 12px;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            color: #0f172a;
            box-sizing: border-box;
            outline: none;
            transition: all 0.15s;
        }
        .dark .gsc-search-input {
            border-color: #3f3f46;
            background: #27272a;
            color: #f4f4f5;
        }
        .gsc-search-input:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.25);
        }
        .gsc-btn-search {
            padding: 8px 16px;
            font-size: 12px;
            font-weight: 700;
            border-radius: 12px;
            border: 1px solid #cbd5e1;
            background: #f1f5f9;
            color: #334155;
            cursor: pointer;
            transition: all 0.15s;
            white-space: nowrap;
        }
        .dark .gsc-btn-search {
            border-color: #3f3f46;
            background: #27272a;
            color: #d4d4d8;
        }
        .gsc-btn-search:hover {
            background: #e2e8f0;
            border-color: #94a3b8;
        }
        .dark .gsc-btn-search:hover {
            background: #3f3f46;
            border-color: #52525b;
        }

        /* Search Results Box */
        .gsc-search-results {
            padding: 6px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            max-height: 120px;
            overflow-y: auto;
            font-size: 11px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }
        .dark .gsc-search-results {
            border-color: #3f3f46;
            background: #27272a;
        }
        .gsc-search-item {
            display: block;
            width: 100%;
            text-align: left;
            padding: 6px 10px;
            border: none;
            background: transparent;
            border-radius: 8px;
            color: #334155;
            cursor: pointer;
            font-size: 11px;
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
        }
        .dark .gsc-search-item {
            color: #d4d4d8;
        }
        .gsc-search-item:hover {
            background: rgba(16, 185, 129, 0.15);
            color: #059669;
        }

        /* Action Buttons Row */
        .gsc-actions-row {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }
        .gsc-btn-gps {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #059669;
            color: #ffffff;
            border: none;
            border-radius: 11px;
            padding: 8px 14px;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.15s ease;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        .gsc-btn-gps:hover {
            background: #047857;
            transform: translateY(-1px);
        }
        .gsc-btn-gps:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        .gsc-btn-reverse-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f1f5f9;
            color: #047857;
            border: 1px solid #cbd5e1;
            border-radius: 11px;
            padding: 7px 12px;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .dark .gsc-btn-reverse-action {
            background: #27272a;
            border-color: #3f3f46;
            color: #34d399;
        }
        .gsc-btn-reverse-action:hover {
            background: #e2e8f0;
        }
        .dark .gsc-btn-reverse-action:hover {
            background: #3f3f46;
        }

        /* Stats Cards (Lat, Lng, Radius) */
        .gsc-stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
            gap: 10px;
        }
        .gsc-stat-card {
            padding: 10px 12px;
            border-radius: 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            box-sizing: border-box;
        }
        .dark .gsc-stat-card {
            background: #27272a;
            border-color: #3f3f46;
        }
        .gsc-stat-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #64748b;
            display: block;
            margin-bottom: 2px;
        }
        .dark .gsc-stat-label {
            color: #a1a1aa;
        }
        .gsc-stat-value {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 12px;
            font-weight: 700;
            color: #0f172a;
        }
        .dark .gsc-stat-value {
            color: #f4f4f5;
        }

        /* Info Guide Card */
        .gsc-info-guide {
            padding: 12px 14px;
            border-radius: 12px;
            background: rgba(16, 185, 129, 0.08);
            border: 1px solid rgba(16, 185, 129, 0.25);
            font-size: 11px;
            line-height: 1.5;
            color: #065f46;
        }
        .dark .gsc-info-guide {
            background: rgba(16, 185, 129, 0.12);
            border-color: rgba(16, 185, 129, 0.3);
            color: #6ee7b7;
        }
        .gsc-info-guide ul {
            margin: 4px 0 0 16px;
            padding: 0;
            list-style-type: disc;
        }
        .gsc-info-guide li {
            margin-bottom: 2px;
        }

        .gsc-link-maps {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 600;
            color: #2563eb;
            text-decoration: underline;
            margin-top: 2px;
        }
        .dark .gsc-link-maps {
            color: #60a5fa;
        }
        .gsc-icon-svg {
            width: 14px !important;
            height: 14px !important;
            min-width: 14px !important;
            min-height: 14px !important;
            max-width: 14px !important;
            max-height: 14px !important;
            display: inline-block;
            vertical-align: middle;
        }
    </style>

    <!-- Card Container (Full-Width, Sama Panjang dengan Form) -->
    <div class="gsc-map-card">
        <!-- Header -->
        <div class="gsc-map-header">
            <div>
                <h4 class="gsc-map-title">
                    <svg style="width: 16px; height: 16px; color: #10b981; display: inline-block; vertical-align: text-bottom;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Titik Lokasi & Geofence Kantor</span>
                </h4>
                <p class="gsc-map-subtitle">
                    Tentukan titik presisi koordinat kantor dan periksa area toleransi presensi amil
                </p>
            </div>

            <!-- Pulsing Status Badge -->
            <div style="display: flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 700; color: #10b981; background: rgba(16, 185, 129, 0.12); padding: 5px 10px; border-radius: 9999px;">
                <span style="width: 7px; height: 7px; border-radius: 9999px; background: #10b981; display: inline-block;"></span>
                <span>Peta Interaktif Aktif</span>
            </div>
        </div>

        <!-- 2-Column Grid Layout: Map (Left) & Controls/Info (Right) -->
        <div class="gsc-map-grid">
            <!-- Left Column: Square Map -->
            <div class="gsc-map-left-col">
                <div 
                    :id="'{{ $uniqueId }}'" 
                    class="gsc-map-square"
                >
                    <!-- Leaflet mounts here -->
                </div>
                <a 
                    :href="'https://www.google.com/maps?q=' + currentLat + ',' + currentLng" 
                    target="_blank" 
                    class="gsc-link-maps"
                    title="Buka titik koordinat saat ini di Google Maps"
                >
                    <span>Buka Titik di Google Maps ↗</span>
                </a>
            </div>

            <!-- Right Column: Search, Actions, Stats & Guide -->
            <div class="gsc-map-right-col">
                <!-- Search Place / Alamat -->
                <div>
                    <label style="display: block; font-size: 11px; font-weight: 700; margin-bottom: 4px;" class="dark:text-gray-300">
                        Cari Wilayah / Gedung / Jalan:
                    </label>
                    <div class="gsc-search-bar">
                        <input 
                            type="text" 
                            x-model="searchQuery" 
                            @keydown.enter.prevent="searchPlace()"
                            placeholder="cth: Cilacap, Jl. Sulawesi, Masjid Agung..." 
                            class="gsc-search-input"
                        >
                        <button 
                            type="button" 
                            @click="searchPlace()"
                            :disabled="isSearching"
                            class="gsc-btn-search"
                        >
                            <span x-text="isSearching ? 'Mencari...' : 'Cari'"></span>
                        </button>
                    </div>

                    <!-- Search Results Dropdown -->
                    <div x-show="searchResults.length > 0" x-cloak class="gsc-search-results" style="margin-top: 6px;">
                        <template x-for="(res, idx) in searchResults" :key="idx">
                            <button 
                                type="button" 
                                @click="selectSearchResult(res)"
                                class="gsc-search-item"
                            >
                                <svg style="width: 12px; height: 12px; color: #10b981; display: inline-block; vertical-align: middle; margin-right: 4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span x-text="res.display_name"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Action Buttons: GPS & Reverse Geocode -->
                <div class="gsc-actions-row">
                    <button 
                        type="button" 
                        @click="getCurrentLocation()"
                        :disabled="isLocating"
                        class="gsc-btn-gps"
                        title="Arahkan titik pin langsung ke lokasi GPS perangkat Anda"
                    >
                        <template x-if="!isLocating">
                            <svg class="gsc-icon-svg" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"></circle>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v3m0 14v3M2 12h3m14 0h3" />
                            </svg>
                        </template>
                        <template x-if="isLocating">
                            <svg class="gsc-icon-svg animate-spin" width="14" height="14" fill="none" viewBox="0 0 24 24">
                                <circle opacity="0.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path opacity="0.75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                        </template>
                        <span x-text="isLocating ? 'Mendeteksi GPS...' : 'Gunakan Lokasi GPS Saya'"></span>
                    </button>

                    <template x-if="addressField">
                        <button 
                            type="button" 
                            @click="reverseGeocode()"
                            :disabled="isReverseGeocoding"
                            class="gsc-btn-reverse-action"
                            title="Tarik teks alamat lengkap dari titik peta dan masukkan ke input Alamat"
                        >
                            <svg style="width: 13px; height: 13px; display: inline-block; vertical-align: middle; margin-right: 4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span x-text="isReverseGeocoding ? 'Mengambil alamat...' : 'Salin Alamat ke Kolom Form'"></span>
                        </button>
                    </template>
                </div>

                <!-- Live Coordinate & Geofence Stats -->
                <div class="gsc-stats-container">
                    <div class="gsc-stat-card">
                        <span class="gsc-stat-label">Latitude (Lat)</span>
                        <span class="gsc-stat-value" x-text="displayLat"></span>
                    </div>
                    <div class="gsc-stat-card">
                        <span class="gsc-stat-label">Longitude (Lng)</span>
                        <span class="gsc-stat-value" x-text="displayLng"></span>
                    </div>
                    <template x-if="radiusField">
                        <div class="gsc-stat-card">
                            <span class="gsc-stat-label">Radius Toleransi</span>
                            <span class="gsc-stat-value" style="color: #10b981;" x-text="displayRadius + ' Meter'"></span>
                        </div>
                    </template>
                </div>

                <!-- Guidance & Explanation Box -->
                <div class="gsc-info-guide">
                    <strong>Petunjuk Penentuan Lokasi & Geofence:</strong>
                    <ul>
                        <li><strong>Klik langsung pada peta</strong> atau <strong>geser pin biru</strong> untuk menentukan lokasi kantor yang akurat.</li>
                        <li><strong>Lingkaran hijau</strong> menunjukkan batas geofence sah presensi amil (sesuai nilai radius meter).</li>
                        <li>Nilai koordinat di bawah akan otomatis tersinkronisasi saat Anda memindahkan titik peta.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('filamentMapPicker', (config) => ({
        mapId: config.id,
        latField: config.latField,
        lngField: config.lngField,
        radiusField: config.radiusField,
        addressField: config.addressField,
        defaultLat: config.defaultLat,
        defaultLng: config.defaultLng,

        map: null,
        marker: null,
        circle: null,
        currentLat: config.defaultLat,
        currentLng: config.defaultLng,
        displayLat: '-',
        displayLng: '-',
        displayRadius: 100,
        
        searchQuery: '',
        searchResults: [],
        isSearching: false,
        isLocating: false,
        isReverseGeocoding: false,

        init() {
            this.ensureLeafletLoaded(() => {
                this.setupMap();
            });
        },

        ensureLeafletLoaded(callback) {
            if (window.L) {
                callback();
                return;
            }

            if (!document.getElementById('leaflet-css')) {
                const link = document.createElement('link');
                link.id = 'leaflet-css';
                link.rel = 'stylesheet';
                link.href = '/vendor/leaflet/leaflet.css';
                document.head.appendChild(link);
            }

            if (!document.getElementById('leaflet-js')) {
                const script = document.createElement('script');
                script.id = 'leaflet-js';
                script.src = '/vendor/leaflet/leaflet.js';
                script.onload = () => callback();
                document.body.appendChild(script);
            } else {
                const checkTimer = setInterval(() => {
                    if (window.L) {
                        clearInterval(checkTimer);
                        callback();
                    }
                }, 100);
            }
        },

        setupMap() {
            const mapEl = document.getElementById(this.mapId);
            if (!mapEl) return;
            if (this.map || mapEl._leaflet_id) return; // Prevent duplicate init

            // Determine initial lat, lng, and radius
            let initLat = this.getFieldValue(this.latField);
            let initLng = this.getFieldValue(this.lngField);
            let initRadius = this.radiusField ? this.getFieldValue(this.radiusField) : 100;

            if (initLat !== null && !isNaN(parseFloat(initLat))) {
                this.currentLat = parseFloat(initLat);
            }
            if (initLng !== null && !isNaN(parseFloat(initLng))) {
                this.currentLng = parseFloat(initLng);
            }
            if (initRadius !== null && !isNaN(parseFloat(initRadius))) {
                this.displayRadius = parseFloat(initRadius);
            }

            this.displayLat = this.currentLat.toFixed(7);
            this.displayLng = this.currentLng.toFixed(7);

            // Configure default icons
            const defaultIcon = L.icon({
                iconUrl: '/vendor/leaflet/images/marker-icon.png',
                iconRetinaUrl: '/vendor/leaflet/images/marker-icon-2x.png',
                shadowUrl: '/vendor/leaflet/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

            // Initialize Leaflet Map
            this.map = L.map(mapEl, {
                zoomControl: true,
                attributionControl: false
            }).setView([this.currentLat, this.currentLng], 16);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19
            }).addTo(this.map);

            // Add marker
            this.marker = L.marker([this.currentLat, this.currentLng], {
                draggable: true,
                icon: defaultIcon
            }).addTo(this.map);

            // Add circle if radiusField specified
            if (this.radiusField) {
                this.circle = L.circle([this.currentLat, this.currentLng], {
                    color: '#10b981',
                    fillColor: '#10b981',
                    fillOpacity: 0.20,
                    weight: 2,
                    radius: this.displayRadius,
                    interactive: false // Clicks pass through directly to map!
                }).addTo(this.map);
            }

            // Click anywhere on map -> moves marker and circle instantly!
            this.map.on('click', (e) => {
                this.setCoordinates(e.latlng.lat, e.latlng.lng);
            });

            // While dragging marker -> circle follows in REAL TIME!
            this.marker.on('drag', () => {
                const pos = this.marker.getLatLng();
                this.currentLat = pos.lat;
                this.currentLng = pos.lng;
                this.displayLat = pos.lat.toFixed(7);
                this.displayLng = pos.lng.toFixed(7);
                if (this.circle) {
                    this.circle.setLatLng(pos);
                }
            });

            // On release after drag -> sync coordinate fields
            this.marker.on('dragend', () => {
                const pos = this.marker.getLatLng();
                this.setCoordinates(pos.lat, pos.lng);
            });

            // Handle map redraw when container is revealed
            setTimeout(() => {
                if (this.map) this.map.invalidateSize();
            }, 300);
            setTimeout(() => {
                if (this.map) this.map.invalidateSize();
            }, 800);

            // Listen to radius and coordinate changes from input fields
            window.addEventListener('input', (e) => {
                if (!e.target) return;
                const idStr = (e.target.name || '') + ' ' + (e.target.id || '') + ' ' + (e.target.getAttribute('wire:model') || '');

                // When radius input changes -> resize circle in real time
                if (this.radiusField && idStr.includes(this.radiusField)) {
                    const r = parseFloat(e.target.value) || 100;
                    this.displayRadius = r;
                    if (this.circle) {
                        this.circle.setRadius(r);
                    }
                }

                // When lat or lng input changes manually -> move marker and circle
                if (this.latField && idStr.includes(this.latField)) {
                    const n = parseFloat(e.target.value);
                    if (!isNaN(n) && Math.abs(n - this.currentLat) > 0.000001) {
                        this.currentLat = n;
                        this.displayLat = n.toFixed(7);
                        this.syncMarker(true);
                    }
                }

                if (this.lngField && idStr.includes(this.lngField)) {
                    const n = parseFloat(e.target.value);
                    if (!isNaN(n) && Math.abs(n - this.currentLng) > 0.000001) {
                        this.currentLng = n;
                        this.displayLng = n.toFixed(7);
                        this.syncMarker(true);
                    }
                }
            });

            // Watch Livewire state if $wire exists
            if (this.$wire && this.radiusField) {
                this.$watch('$wire.data.' + this.radiusField, (val) => {
                    const r = parseFloat(val) || 100;
                    this.displayRadius = r;
                    if (this.circle) {
                        this.circle.setRadius(r);
                    }
                });
            }
        },

        setCoordinates(lat, lng, panTo = false) {
            this.currentLat = parseFloat(lat);
            this.currentLng = parseFloat(lng);
            this.displayLat = this.currentLat.toFixed(7);
            this.displayLng = this.currentLng.toFixed(7);

            this.syncMarker(panTo);

            // Update form fields
            this.setFieldValue(this.latField, this.currentLat.toFixed(7));
            this.setFieldValue(this.lngField, this.currentLng.toFixed(7));
        },

        syncMarker(panTo = false) {
            if (this.marker) {
                this.marker.setLatLng([this.currentLat, this.currentLng]);
            }
            if (this.circle) {
                this.circle.setLatLng([this.currentLat, this.currentLng]);
            }
            if (panTo && this.map) {
                this.map.setView([this.currentLat, this.currentLng], 16);
            }
        },

        getFieldValue(field) {
            if (!field) return null;
            const input = document.querySelector(`input[name="${field}"], input[id$="${field}"], [wire\\:model$="${field}"], textarea[name="${field}"]`);
            if (input && input.value !== undefined && input.value !== null && input.value !== '') {
                return input.value;
            }
            if (this.$wire) {
                try {
                    const val = this.$wire.get('data.' + field);
                    if (val !== undefined && val !== null && val !== '') return val;
                } catch(e) {}
            }
            return null;
        },

        setFieldValue(field, val) {
            if (!field) return;
            const input = document.querySelector(`input[name="${field}"], input[id$="${field}"], [wire\\:model$="${field}"], textarea[name="${field}"]`);
            if (input) {
                input.value = val;
                input.dispatchEvent(new Event('input', { bubbles: true }));
                input.dispatchEvent(new Event('change', { bubbles: true }));
            }
            if (this.$wire) {
                try {
                    this.$wire.set('data.' + field, val);
                } catch (e) {}
            }
        },

        getCurrentLocation() {
            if (!navigator.geolocation) {
                alert('Browser Anda tidak mendukung deteksi lokasi GPS.');
                return;
            }
            this.isLocating = true;
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    this.isLocating = false;
                    this.setCoordinates(pos.coords.latitude, pos.coords.longitude, true);
                },
                (err) => {
                    this.isLocating = false;
                    alert('Gagal mengambil lokasi GPS: ' + (err.message || 'Izin akses lokasi ditolak.'));
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        },

        async searchPlace() {
            if (!this.searchQuery.trim()) return;
            this.isSearching = true;
            this.searchResults = [];
            try {
                const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(this.searchQuery)}&limit=5`;
                const res = await fetch(url, { headers: { 'Accept-Language': 'id,en' } });
                const data = await res.json();
                this.searchResults = data || [];
                if (this.searchResults.length === 1) {
                    this.selectSearchResult(this.searchResults[0]);
                } else if (this.searchResults.length === 0) {
                    alert('Lokasi tidak ditemukan. Coba gunakan nama tempat atau kelurahan yang lebih spesifik.');
                }
            } catch (e) {
                console.error(e);
            } finally {
                this.isSearching = false;
            }
        },

        selectSearchResult(item) {
            this.searchResults = [];
            this.searchQuery = item.display_name;
            this.setCoordinates(parseFloat(item.lat), parseFloat(item.lon), true);
            if (this.addressField) {
                this.setFieldValue(this.addressField, item.display_name);
            }
        },

        async reverseGeocode() {
            if (!this.addressField) return;
            this.isReverseGeocoding = true;
            try {
                const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${this.currentLat}&lon=${this.currentLng}`;
                const res = await fetch(url, { headers: { 'Accept-Language': 'id,en' } });
                const data = await res.json();
                if (data && data.display_name) {
                    this.setFieldValue(this.addressField, data.display_name);
                }
            } catch (e) {
                console.error(e);
            } finally {
                this.isReverseGeocoding = false;
            }
        }
    }));
});
</script>
