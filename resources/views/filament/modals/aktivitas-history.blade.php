@props(['amil'])

@php
    $currentMonth = now()->month;
    $currentYear = now()->year;

    $aktivitasList = $amil->aktivitasHarians()
        ->orderByDesc('tanggal')
        ->orderByDesc('created_at')
        ->limit(50)
        ->get();

    $thisMonthCount = $aktivitasList->filter(function ($a) use ($currentMonth, $currentYear) {
        $date = \Carbon\Carbon::parse($a->tanggal);
        return $date->month === $currentMonth && $date->year === $currentYear;
    })->count();

    $pendingCount = $aktivitasList->where('status', 'diajukan')->count();
    $disetujuiCount = $aktivitasList->where('status', 'disetujui')->count();
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
        .gsc-stats-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin-top: 14px;
            padding-top: 14px;
            border-top: 1px solid #e2e8f0;
        }
        .dark .gsc-stats-grid {
            border-top-color: #1e293b;
        }
        @media (max-width: 600px) {
            .gsc-stats-grid {
                grid-template-columns: 1fr;
            }
        }
        .gsc-stat-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 14px;
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
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            margin-top: 3px;
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

        /* Activity Card */
        .gsc-act-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 16px 18px;
            margin-bottom: 12px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
            transition: all 0.2s ease;
        }
        .dark .gsc-act-card {
            background: #0f172a;
            border-color: #1e293b;
        }
        .gsc-act-card.pending {
            border-color: #f59e0b;
            box-shadow: 0 0 0 1px rgba(245, 158, 11, 0.25);
        }
        .gsc-act-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
        }
        .gsc-act-info {
            flex: 1;
            min-width: 0;
        }
        .gsc-act-head-pills {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 6px;
            flex-wrap: wrap;
        }
        .gsc-date-pill {
            font-size: 11px;
            font-weight: 600;
            background: #f1f5f9;
            color: #334155;
            padding: 2px 8px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .dark .gsc-date-pill {
            background: #1e293b;
            color: #cbd5e1;
        }
        .gsc-badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 9px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 700;
            line-height: 1.3;
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

        .gsc-act-title {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            margin: 4px 0 6px 0;
            line-height: 1.35;
        }
        .dark .gsc-act-title {
            color: #f8fafc;
        }
        .gsc-act-desc {
            font-size: 13px;
            color: #475569;
            line-height: 1.55;
            margin: 0;
            white-space: pre-line;
        }
        .dark .gsc-act-desc {
            color: #cbd5e1;
        }

        /* Location Strip */
        .gsc-loc-strip {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
            flex-wrap: wrap;
            font-size: 12px;
        }
        .gsc-loc-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            color: #475569;
            font-size: 11px;
            font-weight: 500;
        }
        .dark .gsc-loc-badge {
            background: #1e293b;
            border-color: #334155;
            color: #cbd5e1;
        }
        .gsc-map-link {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 6px;
            color: #059669;
            font-size: 11px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }
        .dark .gsc-map-link {
            background: rgba(16, 185, 129, 0.15);
            border-color: rgba(16, 185, 129, 0.3);
            color: #34d399;
        }
        .gsc-map-link:hover {
            background: #10b981;
            color: #ffffff;
        }

        /* Photo & Actions Right */
        .gsc-act-side {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 10px;
            flex-shrink: 0;
        }
        .gsc-thumb-img {
            width: 88px !important;
            height: 66px !important;
            min-width: 88px !important;
            max-width: 88px !important;
            max-height: 66px !important;
            border-radius: 8px !important;
            object-fit: cover !important;
            border: 1px solid #cbd5e1;
            display: block;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            transition: transform 0.2s;
            cursor: pointer;
        }
        .gsc-thumb-img:hover {
            transform: scale(1.05);
        }

        /* Buttons */
        .gsc-btn-group {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .gsc-btn {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 5px 12px;
            border-radius: 7px;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            transition: background-color 0.2s;
            text-decoration: none;
            line-height: 1.3;
        }
        .gsc-btn-approve {
            background: #10b981;
            color: #ffffff;
        }
        .gsc-btn-approve:hover {
            background: #059669;
        }
        .gsc-btn-reject {
            background: #ef4444;
            color: #ffffff;
        }
        .gsc-btn-reject:hover {
            background: #dc2626;
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
                        @if($amil->no_hp)
                            <span>•</span>
                            <span>WA: {{ $amil->no_hp }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <div>
                @if ($pendingCount > 0)
                    <span class="gsc-badge gsc-badge-warning">
                        ⚠️ {{ $pendingCount }} Laporan Perlu Verifikasi
                    </span>
                @else
                    <span class="gsc-badge gsc-badge-success">
                        ✓ Semua Aktivitas Terverifikasi
                    </span>
                @endif
            </div>
        </div>

        {{-- Mini Stat Row --}}
        <div class="gsc-stats-grid">
            <div class="gsc-stat-card">
                <div class="gsc-stat-lbl">Laporan Bulan Ini</div>
                <div class="gsc-stat-num" style="color: #10b981;">{{ $thisMonthCount }} <span style="font-size: 12px; font-weight: 500; color: #64748b;">Aktivitas</span></div>
            </div>
            <div class="gsc-stat-card">
                <div class="gsc-stat-lbl">Total Disetujui</div>
                <div class="gsc-stat-num">{{ $disetujuiCount }} <span style="font-size: 12px; font-weight: 500; color: #64748b;">Laporan</span></div>
            </div>
            <div class="gsc-stat-card">
                <div class="gsc-stat-lbl">Menunggu Review</div>
                <div class="gsc-stat-num" style="{{ $pendingCount > 0 ? 'color: #f59e0b;' : '' }}">{{ $pendingCount }} <span style="font-size: 12px; font-weight: 500; color: #64748b;">Item</span></div>
            </div>
        </div>
    </div>

    {{-- Activities Section --}}
    <div>
        <div class="gsc-section-title-bar">
            <h4 class="gsc-section-title">
                <svg width="18" height="18" fill="none" stroke="#10b981" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Daftar Riwayat Laporan Aktivitas
            </h4>
            <span class="gsc-section-count">Total: {{ $aktivitasList->count() }} Laporan</span>
        </div>

        @if ($aktivitasList->isEmpty())
            <div class="gsc-empty">
                <svg width="40" height="40" style="margin: 0 auto; display: block; color: #94a3b8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <div class="gsc-empty-title">Belum ada laporan aktivitas untuk amil ini</div>
                <p class="gsc-empty-sub">Laporan kegiatan yang dikirimkan amil akan muncul di sini secara otomatis.</p>
            </div>
        @else
            <div>
                @foreach ($aktivitasList as $item)
                    @php
                        $tgl = \Carbon\Carbon::parse($item->tanggal);
                        $isPending = $item->status === 'diajukan';
                        $badgeClass = match ($item->status) {
                            'disetujui' => 'gsc-badge-success',
                            'ditolak' => 'gsc-badge-danger',
                            default => 'gsc-badge-warning',
                        };
                        $statusLabel = match ($item->status) {
                            'diajukan' => 'Menunggu Review',
                            'disetujui' => 'Disetujui',
                            'ditolak' => 'Ditolak',
                            default => ucfirst($item->status),
                        };
                    @endphp

                    <div class="gsc-act-card {{ $isPending ? 'pending' : '' }}">
                        <div class="gsc-act-top">
                            <div class="gsc-act-info">
                                <div class="gsc-act-head-pills">
                                    <span class="gsc-date-pill">
                                        📅 {{ $tgl->translatedFormat('d F Y') }}
                                    </span>
                                    <span class="gsc-badge {{ $badgeClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </div>

                                <div class="gsc-act-title">{{ $item->judul }}</div>
                                <div class="gsc-act-desc">{{ $item->deskripsi }}</div>

                                {{-- Location & GPS --}}
                                @if ($item->lokasi || ($item->lat && $item->lng))
                                    <div class="gsc-loc-strip">
                                        @if ($item->lokasi)
                                            <div class="gsc-loc-badge">
                                                <svg width="14" height="14" fill="none" stroke="#ef4444" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                <span>{{ $item->lokasi }}</span>
                                            </div>
                                        @endif

                                        @if ($item->lat && $item->lng)
                                            <a href="https://www.google.com/maps?q={{ $item->lat }},{{ $item->lng }}" target="_blank" class="gsc-map-link">
                                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                Peta GPS ({{ round($item->lat, 4) }}, {{ round($item->lng, 4) }})
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div class="gsc-act-side">
                                @if ($item->foto)
                                    <a href="{{ asset('storage/' . $item->foto) }}" target="_blank" title="Klik untuk membuka ukuran penuh">
                                        <img src="{{ asset('storage/' . $item->foto) }}" alt="{{ $item->judul }}" class="gsc-thumb-img" />
                                    </a>
                                @endif

                                @if ($isPending)
                                    <div class="gsc-btn-group">
                                        <button 
                                            type="button" 
                                            wire:click="setujuiAktivitas({{ $item->id }})" 
                                            wire:confirm="Setujui laporan kegiatan ini?"
                                            class="gsc-btn gsc-btn-approve">
                                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            Setujui
                                        </button>
                                        <button 
                                            type="button" 
                                            wire:click="tolakAktivitas({{ $item->id }})" 
                                            wire:confirm="Tolak laporan kegiatan ini?"
                                            class="gsc-btn gsc-btn-reject">
                                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            Tolak
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
