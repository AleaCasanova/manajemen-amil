@props(['amil'])

@php
    $tahun = now()->year;
    $saldo = $amil->saldoCutis()->where('tahun', $tahun)->first()
        ?? $amil->saldoCutis()->latest('tahun')->first();

    $jatah = $saldo?->jatah ?? 12;
    $terpakai = $saldo?->terpakai ?? 0;
    $sisa = max(0, $jatah - $terpakai);
    $persenTerpakai = $jatah > 0 ? min(100, round(($terpakai / $jatah) * 100)) : 0;

    $izinCutis = $amil->izinCutis()
        ->orderByDesc('tgl_mulai')
        ->limit(50)
        ->get();

    $pendingCount = $izinCutis->where('status', 'diajukan')->count();
    $disetujuiCount = $izinCutis->where('status', 'disetujui')->count();
    $totalIzinSakit = $izinCutis->whereIn('jenis', ['izin', 'sakit'])->where('status', 'disetujui')->count();
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
            border: 2px solid #f59e0b;
            display: block;
            flex-shrink: 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .gsc-avatar-fallback {
            width: 54px;
            height: 54px;
            min-width: 54px;
            border-radius: 9999px;
            background: rgba(245, 158, 11, 0.15);
            color: #f59e0b;
            font-weight: 700;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid rgba(245, 158, 11, 0.3);
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

        /* Saldo Row */
        .gsc-saldo-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 12px;
            margin-top: 14px;
            padding-top: 14px;
            border-top: 1px solid #e2e8f0;
        }
        .dark .gsc-saldo-grid {
            border-top-color: #1e293b;
        }
        @media (max-width: 640px) {
            .gsc-saldo-grid {
                grid-template-columns: 1fr;
            }
        }
        .gsc-saldo-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 16px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        }
        .dark .gsc-saldo-card {
            background: #1e293b;
            border-color: #334155;
        }

        /* Progress Bar */
        .gsc-progress-track {
            width: 100%;
            height: 8px;
            background: #e2e8f0;
            border-radius: 9999px;
            overflow: hidden;
            margin: 8px 0;
        }
        .dark .gsc-progress-track {
            background: #334155;
        }
        .gsc-progress-bar {
            height: 100%;
            border-radius: 9999px;
            transition: width 0.3s ease;
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
            padding: 11px 14px;
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
        .gsc-table tr.is-pending td {
            background: rgba(245, 158, 11, 0.05);
        }
        .dark .gsc-table tr.is-pending td {
            background: rgba(245, 158, 11, 0.08);
        }
        .gsc-table tr:hover td {
            background: #f8fafc;
        }
        .dark .gsc-table tr:hover td {
            background: rgba(30, 41, 59, 0.5);
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

        /* Buttons */
        .gsc-btn-group {
            display: inline-flex;
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

    {{-- Profile & Saldo Card --}}
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
                            <span>Kontak: {{ $amil->no_hp }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <div>
                @if ($pendingCount > 0)
                    <span class="gsc-badge gsc-badge-warning">
                        ⚠️ {{ $pendingCount }} Pengajuan Menunggu Review
                    </span>
                @else
                    <span class="gsc-badge gsc-badge-success">
                        ✓ Semua Pengajuan Telah Diproses
                    </span>
                @endif
            </div>
        </div>

        {{-- Kuota Cuti & Rekap Bar --}}
        <div class="gsc-saldo-grid">
            <div class="gsc-saldo-card">
                <div style="display: flex; justify-content: space-between; align-items: baseline; font-size: 12px;">
                    <span style="font-weight: 700; color: #334155;">Kuota Cuti Tahunan ({{ $tahun }})</span>
                    <span style="font-weight: 800; color: #10b981; font-size: 13px;">Sisa {{ $sisa }} Hari</span>
                </div>
                <div class="gsc-progress-track">
                    <div class="gsc-progress-bar" style="width: {{ $persenTerpakai }}%; background: {{ $persenTerpakai >= 80 ? '#ef4444' : ($persenTerpakai >= 50 ? '#f59e0b' : '#10b981') }};"></div>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 11px; color: #64748b;">
                    <span>Terpakai: <strong>{{ $terpakai }} Hari</strong> ({{ $persenTerpakai }}%)</span>
                    <span>Jatah Tahunan: <strong>{{ $jatah }} Hari</strong></span>
                </div>
            </div>

            <div class="gsc-saldo-card" style="display: flex; flex-direction: column; justify-content: center;">
                <div style="font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Izin & Sakit Disetujui</div>
                <div style="font-size: 18px; font-weight: 800; color: #0f172a; margin-top: 2px;">
                    {{ $totalIzinSakit }} <span style="font-size: 11px; font-weight: 500; color: #64748b;">Permohonan</span>
                </div>
                <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">Total pengajuan: {{ $izinCutis->count() }}</div>
            </div>
        </div>
    </div>

    {{-- Requests Table --}}
    <div>
        <div class="gsc-section-title-bar">
            <h4 class="gsc-section-title">
                <svg width="18" height="18" fill="none" stroke="#f59e0b" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                Riwayat Permohonan Izin & Cuti
            </h4>
            <span class="gsc-section-count">Total: {{ $izinCutis->count() }} Data</span>
        </div>

        @if ($izinCutis->isEmpty())
            <div class="gsc-empty">
                <svg width="40" height="40" style="margin: 0 auto; display: block; color: #94a3b8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <div class="gsc-empty-title">Belum ada pengajuan izin atau cuti untuk amil ini</div>
                <p class="gsc-empty-sub">Pengajuan dapat diajukan oleh amil atau dicatat oleh admin.</p>
            </div>
        @else
            <div class="gsc-table-box">
                <table class="gsc-table">
                    <thead>
                        <tr>
                            <th>Jenis</th>
                            <th>Periode</th>
                            <th>Durasi</th>
                            <th>Alasan</th>
                            <th style="text-align: center;">Lampiran</th>
                            <th>Status</th>
                            <th style="text-align: right;">Tindakan Admin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($izinCutis as $item)
                            @php
                                $mulai = \Carbon\Carbon::parse($item->tgl_mulai);
                                $selesai = \Carbon\Carbon::parse($item->tgl_selesai);
                                $durasi = $mulai->diffInDays($selesai) + 1;
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
                                $jenisBadge = match ($item->jenis) {
                                    'cuti' => 'gsc-badge-success',
                                    'sakit' => 'gsc-badge-danger',
                                    'izin' => 'gsc-badge-info',
                                    default => 'gsc-badge-warning',
                                };
                            @endphp
                            <tr class="{{ $isPending ? 'is-pending' : '' }}">
                                <td style="white-space: nowrap;">
                                    <span class="gsc-badge {{ $jenisBadge }}" style="text-transform: uppercase;">
                                        {{ $item->jenis }}
                                    </span>
                                </td>
                                <td style="white-space: nowrap;">
                                    <div style="font-weight: 700; color: #0f172a;">{{ $mulai->translatedFormat('d M Y') }}</div>
                                    @if ($item->tgl_mulai !== $item->tgl_selesai)
                                        <div style="font-size: 11px; color: #64748b;">s/d {{ $selesai->translatedFormat('d M Y') }}</div>
                                    @endif
                                </td>
                                <td style="white-space: nowrap;">
                                    <span style="font-weight: 700; color: #334155; background: #f1f5f9; padding: 2px 7px; border-radius: 6px; font-size: 11px;">
                                        {{ $durasi }} Hari
                                    </span>
                                </td>
                                <td style="max-width: 200px;">
                                    <div style="font-weight: 500; color: #1e293b; line-height: 1.4;" title="{{ $item->alasan }}">
                                        {{ $item->alasan }}
                                    </div>
                                    @if ($item->catatan_admin)
                                        <div style="font-size: 11px; color: #ef4444; margin-top: 3px; font-style: italic;">
                                            Catatan: "{{ $item->catatan_admin }}"
                                        </div>
                                    @endif
                                </td>
                                <td style="text-align: center; white-space: nowrap;">
                                    @if ($item->lampiran)
                                        <a href="{{ asset('storage/' . $item->lampiran) }}" target="_blank" style="display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; background: #f1f5f9; border-radius: 6px; color: #10b981; font-size: 11px; font-weight: 600; text-decoration: none;">
                                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                            Berkas
                                        </a>
                                    @else
                                        <span style="color: #94a3b8; font-size: 11px;">-</span>
                                    @endif
                                </td>
                                <td style="white-space: nowrap;">
                                    <span class="gsc-badge {{ $badgeClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td style="text-align: right; white-space: nowrap;">
                                    @if ($isPending)
                                        <div class="gsc-btn-group">
                                            <button 
                                                type="button" 
                                                wire:click="setujuiPengajuan({{ $item->id }})" 
                                                wire:confirm="Setujui pengajuan {{ strtoupper($item->jenis) }} ini? Saldo cuti akan otomatis terpotong jika cuti tahunan."
                                                class="gsc-btn gsc-btn-approve">
                                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                Setujui
                                            </button>
                                            <button 
                                                type="button" 
                                                onclick="let alasan = prompt('Masukkan alasan penolakan:'); if (alasan !== null && alasan.trim() !== '') { @this.call('tolakPengajuan', {{ $item->id }}, alasan); }"
                                                class="gsc-btn gsc-btn-reject">
                                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                Tolak
                                            </button>
                                        </div>
                                    @else
                                        <span style="color: #94a3b8; font-size: 11px; font-style: italic;">
                                            {{ $item->approvedByUser ? 'Oleh: ' . $item->approvedByUser->name : '-' }}
                                        </span>
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
