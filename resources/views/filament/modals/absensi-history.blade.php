@props(['amil'])

@php
    $today = now()->toDateString();
    $currentMonth = now()->month;
    $currentYear = now()->year;

    $absensis = $amil->absensis()
        ->orderByDesc('tanggal')
        ->limit(60)
        ->get();

    $thisMonthRecords = $absensis->filter(function ($a) use ($currentMonth, $currentYear) {
        $date = \Carbon\Carbon::parse($a->tanggal);
        return $date->month === $currentMonth && $date->year === $currentYear;
    });

    $hadirCount = $thisMonthRecords->where('status', 'hadir')->count();
    $terlambatCount = $thisMonthRecords->where('status', 'terlambat')->count();
    $izinCount = $thisMonthRecords->where('status', 'izin')->count();
    $cutiCount = $thisMonthRecords->where('status', 'cuti')->count();
    $totalHadir = $hadirCount + $terlambatCount;
@endphp

<div class="gsc-modal-wrap">
    <style>
        .gsc-modal-wrap {
            font-family: inherit;
            color: #1e293b;
            box-sizing: border-box;
            padding: 4px 2px;
        }
        .dark .gsc-modal-wrap {
            color: #f1f5f9;
        }
        .gsc-modal-wrap * {
            box-sizing: border-box;
        }

        /* Profile & Summary Card */
        .gsc-profile-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 16px 20px;
            margin-bottom: 22px;
        }
        .dark .gsc-profile-box {
            background: #0f172a;
            border-color: #1e293b;
        }
        .gsc-profile-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }
        .gsc-profile-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .gsc-avatar {
            width: 54px !important;
            height: 54px !important;
            min-width: 54px !important;
            max-width: 54px !important;
            max-height: 54px !important;
            border-radius: 9999px !important;
            object-fit: cover !important;
            border: 2px solid #10b981;
            display: block;
            flex-shrink: 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .gsc-avatar-fallback {
            width: 54px;
            height: 54px;
            min-width: 54px;
            border-radius: 9999px;
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
            font-weight: 700;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid rgba(16, 185, 129, 0.3);
            flex-shrink: 0;
        }
        .gsc-profile-name {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.25;
            margin: 0;
        }
        .dark .gsc-profile-name {
            color: #f8fafc;
        }
        .gsc-profile-sub {
            font-size: 12px;
            color: #64748b;
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }
        .dark .gsc-profile-sub {
            color: #94a3b8;
        }

        /* Stats Grid */
        .gsc-stats-grid-4 {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
            margin-top: 14px;
            padding-top: 14px;
            border-top: 1px solid #e2e8f0;
        }
        .dark .gsc-stats-grid-4 {
            border-top-color: #1e293b;
        }
        @media (max-width: 640px) {
            .gsc-stats-grid-4 {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        .gsc-stat-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 12px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        }
        .dark .gsc-stat-card {
            background: #1e293b;
            border-color: #334155;
        }
        .gsc-stat-lbl {
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .dark .gsc-stat-lbl {
            color: #94a3b8;
        }
        .gsc-stat-num {
            font-size: 17px;
            font-weight: 800;
            color: #0f172a;
            margin-top: 2px;
            line-height: 1.2;
        }
        .dark .gsc-stat-num {
            color: #f8fafc;
        }

        /* Section Title */
        .gsc-section-title-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }
        .gsc-section-title {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
        }
        .dark .gsc-section-title {
            color: #f8fafc;
        }
        .gsc-section-count {
            font-size: 12px;
            color: #64748b;
            font-weight: 500;
        }
        .dark .gsc-section-count {
            color: #94a3b8;
        }

        /* Table */
        .gsc-table-box {
            width: 100%;
            overflow-x: auto;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .dark .gsc-table-box {
            border-color: #1e293b;
            background: #0f172a;
        }
        .gsc-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            text-align: left;
        }
        .gsc-table th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            padding: 11px 14px;
            border-bottom: 1px solid #e2e8f0;
            white-space: nowrap;
        }
        .dark .gsc-table th {
            background: #1e293b;
            color: #94a3b8;
            border-bottom-color: #334155;
        }
        .gsc-table td {
            padding: 10px 14px;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
            vertical-align: middle;
        }
        .dark .gsc-table td {
            border-bottom-color: #1e293b;
            color: #e2e8f0;
        }
        .gsc-table tr:last-child td {
            border-bottom: none;
        }
        .gsc-table tr.is-today td {
            background: rgba(16, 185, 129, 0.05);
        }
        .dark .gsc-table tr.is-today td {
            background: rgba(16, 185, 129, 0.08);
        }
        .gsc-table tr:hover td {
            background: #f8fafc;
        }
        .dark .gsc-table tr:hover td {
            background: rgba(30, 41, 59, 0.5);
        }

        /* Selfie Thumbnails */
        .gsc-selfie-thumb {
            width: 36px !important;
            height: 36px !important;
            min-width: 36px !important;
            max-width: 36px !important;
            max-height: 36px !important;
            border-radius: 9999px !important;
            object-fit: cover !important;
            display: inline-block;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
            transition: transform 0.2s;
            cursor: pointer;
        }
        .gsc-selfie-thumb.masuk {
            border: 2px solid #10b981;
        }
        .gsc-selfie-thumb.pulang {
            border: 2px solid #3b82f6;
        }
        .gsc-selfie-thumb:hover {
            transform: scale(1.18);
        }

        /* Badges */
        .gsc-badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 700;
            line-height: 1.25;
            white-space: nowrap;
        }
        .gsc-badge-success {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        .dark .gsc-badge-success {
            background: rgba(6, 95, 70, 0.4);
            color: #34d399;
            border-color: rgba(52, 211, 153, 0.3);
        }
        .gsc-badge-danger {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        .dark .gsc-badge-danger {
            background: rgba(153, 27, 27, 0.4);
            color: #f87171;
            border-color: rgba(248, 113, 113, 0.3);
        }
        .gsc-badge-info {
            background: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }
        .dark .gsc-badge-info {
            background: rgba(30, 64, 175, 0.4);
            color: #60a5fa;
            border-color: rgba(96, 165, 250, 0.3);
        }
        .gsc-badge-warning {
            background: #fffbeb;
            color: #92400e;
            border: 1px solid #fde68a;
        }
        .dark .gsc-badge-warning {
            background: rgba(146, 64, 14, 0.4);
            color: #fbbf24;
            border-color: rgba(251, 191, 36, 0.3);
        }
        .gsc-badge-purple {
            background: #f5f3ff;
            color: #5b21b6;
            border: 1px solid #ddd6fe;
        }
        .dark .gsc-badge-purple {
            background: rgba(91, 33, 182, 0.35);
            color: #c4b5fd;
            border-color: rgba(196, 181, 253, 0.3);
        }

        /* Empty State */
        .gsc-empty {
            text-align: center;
            padding: 40px 20px;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 14px;
        }
        .dark .gsc-empty {
            background: #0f172a;
            border-color: #1e293b;
        }
        .gsc-empty-title {
            font-size: 14px;
            font-weight: 600;
            color: #475569;
            margin: 8px 0 4px 0;
        }
        .dark .gsc-empty-title {
            color: #94a3b8;
        }
        .gsc-empty-sub {
            font-size: 12px;
            color: #94a3b8;
            margin: 0;
        }
    </style>

    {{-- Profile & Summary Header --}}
    <div class="gsc-profile-box">
        <div class="gsc-profile-header-row">
            <div class="gsc-profile-left">
                @if ($amil->foto)
                    <img src="{{ asset('storage/' . $amil->foto) }}" alt="{{ $amil->nama }}" class="gsc-avatar" />
                @else
                    <div class="gsc-avatar-fallback">
                        {{ strtoupper(substr($amil->nama, 0, 2)) }}
                    </div>
                @endif
                <div>
                    <h3 class="gsc-profile-name">{{ $amil->nama }}</h3>
                    <div class="gsc-profile-sub">
                        <span>NIP: <strong>{{ $amil->nip ?? '-' }}</strong></span>
                        <span>•</span>
                        <span>Divisi: <strong>{{ $amil->divisi?->nama ?? '-' }}</strong></span>
                        <span>•</span>
                        <span>Jabatan: {{ $amil->jabatan ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <div>
                <span class="gsc-badge gsc-badge-success">
                    Status: {{ ucfirst($amil->status ?? 'Aktif') }}
                </span>
            </div>
        </div>

        {{-- Mini Stat Pills --}}
        <div class="gsc-stats-grid-4">
            <div class="gsc-stat-card">
                <div class="gsc-stat-lbl">Hadir Bulan Ini</div>
                <div class="gsc-stat-num" style="color: #10b981;">{{ $totalHadir }} <span style="font-size: 11px; font-weight: 500; color: #64748b;">Hari</span></div>
            </div>
            <div class="gsc-stat-card">
                <div class="gsc-stat-lbl">Tepat Waktu</div>
                <div class="gsc-stat-num">{{ $hadirCount }} <span style="font-size: 11px; font-weight: 500; color: #64748b;">Hari</span></div>
            </div>
            <div class="gsc-stat-card">
                <div class="gsc-stat-lbl">Terlambat</div>
                <div class="gsc-stat-num" style="{{ $terlambatCount > 0 ? 'color: #ef4444;' : 'color: #64748b;' }}">{{ $terlambatCount }} <span style="font-size: 11px; font-weight: 500; color: #64748b;">Kali</span></div>
            </div>
            <div class="gsc-stat-card">
                <div class="gsc-stat-lbl">Izin & Cuti</div>
                <div class="gsc-stat-num" style="color: #3b82f6;">{{ $izinCount + $cutiCount }} <span style="font-size: 11px; font-weight: 500; color: #64748b;">Hari</span></div>
            </div>
        </div>
    </div>

    {{-- Attendance History List --}}
    <div>
        <div class="gsc-section-title-bar">
            <h4 class="gsc-section-title">
                <svg width="18" height="18" fill="none" stroke="#10b981" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Riwayat Presensi Harian
            </h4>
            <span class="gsc-section-count">Total: {{ $absensis->count() }} Data</span>
        </div>

        @if ($absensis->isEmpty())
            <div class="gsc-empty">
                <svg width="40" height="40" style="margin: 0 auto; display: block; color: #94a3b8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="gsc-empty-title">Belum ada catatan presensi untuk amil ini</div>
                <p class="gsc-empty-sub">Presensi harian amil akan muncul di sini secara otomatis.</p>
            </div>
        @else
            <div class="gsc-table-box">
                <table class="gsc-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Masuk</th>
                            <th>Pulang</th>
                            <th style="text-align: center;">Selfie Masuk</th>
                            <th style="text-align: center;">Selfie Pulang</th>
                            <th>Lokasi GPS</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($absensis as $item)
                            @php
                                $tgl = \Carbon\Carbon::parse($item->tanggal);
                                $isToday = $item->tanggal === $today;
                                $badgeClass = match ($item->status) {
                                    'hadir' => 'gsc-badge-success',
                                    'terlambat' => 'gsc-badge-danger',
                                    'izin' => 'gsc-badge-info',
                                    'cuti' => 'gsc-badge-warning',
                                    'alpa' => 'gsc-badge-danger',
                                    default => 'gsc-badge-warning',
                                };
                                $statusLabel = match ($item->status) {
                                    'hadir' => 'Tepat Waktu',
                                    'terlambat' => 'Terlambat',
                                    'izin' => 'Izin',
                                    'cuti' => 'Cuti',
                                    'alpa' => 'Alpa',
                                    default => ucfirst($item->status),
                                };
                            @endphp
                            <tr class="{{ $isToday ? 'is-today' : '' }}">
                                <td style="white-space: nowrap;">
                                    <div style="font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 5px;">
                                        {{ $tgl->translatedFormat('d M Y') }}
                                        @if($isToday)
                                            <span style="background: #10b981; color: #ffffff; font-size: 9px; padding: 1px 5px; border-radius: 4px; font-weight: 700;">HARI INI</span>
                                        @endif
                                    </div>
                                    <div style="font-size: 11px; color: #64748b;">{{ $tgl->translatedFormat('l') }}</div>
                                </td>
                                <td style="white-space: nowrap;">
                                    <div style="font-family: monospace; font-weight: 700; color: #0f172a;">
                                        {{ $item->jam_masuk ? \Carbon\Carbon::parse($item->jam_masuk)->format('H:i') : '-' }}
                                    </div>
                                    @if ($item->tipe_masuk === 'dinas_luar')
                                        <div style="margin-top: 2px;">
                                            <span class="gsc-badge gsc-badge-purple" style="font-size: 9px; padding: 1px 6px;">🚗 Luar</span>
                                        </div>
                                    @endif
                                </td>
                                <td style="white-space: nowrap;">
                                    <div style="font-family: monospace; font-weight: 700; color: #0f172a;">
                                        {{ $item->jam_pulang ? \Carbon\Carbon::parse($item->jam_pulang)->format('H:i') : '-' }}
                                    </div>
                                    @if ($item->jam_pulang && $item->isPulangAwal())
                                        <div style="margin-top: 2px;">
                                            <span class="gsc-badge gsc-badge-warning" style="font-size: 9px; padding: 1px 6px;">⚠️ Pulang Awal</span>
                                        </div>
                                    @endif
                                    @if ($item->tipe_pulang === 'dinas_luar')
                                        <div style="margin-top: 2px;">
                                            <span class="gsc-badge gsc-badge-purple" style="font-size: 9px; padding: 1px 6px;">🚗 Luar</span>
                                        </div>
                                    @endif
                                </td>
                                <td style="text-align: center; white-space: nowrap;">
                                    @if ($item->foto_masuk)
                                        <a href="{{ asset('storage/' . $item->foto_masuk) }}" target="_blank" title="Buka Foto Selfie Masuk">
                                            <img src="{{ asset('storage/' . $item->foto_masuk) }}" alt="Foto Masuk" class="gsc-selfie-thumb masuk" />
                                        </a>
                                    @else
                                        <span style="color: #94a3b8; font-size: 11px;">-</span>
                                    @endif
                                </td>
                                <td style="text-align: center; white-space: nowrap;">
                                    @if ($item->foto_pulang)
                                        <a href="{{ asset('storage/' . $item->foto_pulang) }}" target="_blank" title="Buka Foto Selfie Pulang">
                                            <img src="{{ asset('storage/' . $item->foto_pulang) }}" alt="Foto Pulang" class="gsc-selfie-thumb pulang" />
                                        </a>
                                    @else
                                        <span style="color: #94a3b8; font-size: 11px;">-</span>
                                    @endif
                                </td>
                                <td style="white-space: nowrap;">
                                    <div style="display: flex; flex-direction: column; gap: 3px;">
                                        @if ($item->lat_masuk && $item->lng_masuk)
                                            <a href="https://www.google.com/maps?q={{ $item->lat_masuk }},{{ $item->lng_masuk }}" target="_blank" style="display: inline-flex; align-items: center; gap: 4px; color: #10b981; font-weight: 600; text-decoration: none; font-size: 10px;">
                                                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                                Peta Masuk
                                            </a>
                                        @endif
                                        @if ($item->lat_pulang && $item->lng_pulang)
                                            <a href="https://www.google.com/maps?q={{ $item->lat_pulang }},{{ $item->lng_pulang }}" target="_blank" style="display: inline-flex; align-items: center; gap: 4px; color: #3b82f6; font-weight: 600; text-decoration: none; font-size: 10px;">
                                                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                                Peta Pulang
                                            </a>
                                        @endif
                                        @if (!$item->lat_masuk && !$item->lat_pulang)
                                            <span style="color: #94a3b8; font-size: 11px;">-</span>
                                        @endif
                                    </div>
                                </td>
                                <td style="white-space: nowrap;">
                                    <div style="display: flex; flex-direction: column; gap: 3px; align-items: flex-start;">
                                        <span class="gsc-badge {{ $badgeClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                        @if ($item->tipe_masuk === 'dinas_luar' || $item->tipe_pulang === 'dinas_luar')
                                            <span class="gsc-badge gsc-badge-purple">
                                                🚗 Tugas Lapangan
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td style="max-width: 220px; font-size: 11px; color: #475569;">
                                    @if ($item->keterangan && $item->keterangan_pulang)
                                        <div style="white-space: normal; line-height: 1.3;">
                                            <div><strong>Masuk:</strong> {{ $item->keterangan }}</div>
                                            <div style="margin-top: 2px;"><strong>Pulang:</strong> {{ $item->keterangan_pulang }}</div>
                                        </div>
                                    @elseif ($item->keterangan)
                                        <div style="white-space: normal; line-height: 1.3;" title="{{ $item->keterangan }}">
                                            {{ $item->keterangan }}
                                        </div>
                                    @elseif ($item->keterangan_pulang)
                                        <div style="white-space: normal; line-height: 1.3;" title="{{ $item->keterangan_pulang }}">
                                            <strong>Pulang:</strong> {{ $item->keterangan_pulang }}
                                        </div>
                                    @else
                                        <span style="color: #94a3b8;">-</span>
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
