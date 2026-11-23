<div class="gsc-akun-wrap">
    <style>
        .gsc-akun-wrap {
            font-family: inherit;
            color: #1e293b;
            box-sizing: border-box;
            padding: 4px;
        }
        .dark .gsc-akun-wrap {
            color: #f1f5f9;
        }
        .gsc-akun-wrap * {
            box-sizing: border-box;
        }

        /* Top Summary Banner */
        .gsc-akun-summary {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            border-radius: 12px;
            padding: 16px 20px;
            color: #ffffff;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.2);
        }
        .gsc-akun-summary-title {
            font-size: 13px;
            font-weight: 500;
            opacity: 0.9;
            margin-bottom: 4px;
        }
        .gsc-akun-summary-value {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.02em;
        }
        .gsc-akun-summary-count {
            background: rgba(255, 255, 255, 0.2);
            padding: 6px 14px;
            border-radius: 9999px;
            font-size: 13px;
            font-weight: 600;
        }

        /* Grid Cards */
        .gsc-akun-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 16px;
        }
        .gsc-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 18px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }
        .dark .gsc-card {
            background: #111827;
            border-color: #1f2937;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.4);
        }
        .gsc-card:hover {
            border-color: #10b981;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
        }

        /* Card Header */
        .gsc-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        .gsc-badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 10px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }
        .gsc-badge-kas {
            background: #fef3c7;
            color: #92400e;
        }
        .dark .gsc-badge-kas {
            background: rgba(245, 158, 11, 0.2);
            color: #fcd34d;
        }
        .gsc-badge-bank {
            background: #dbeafe;
            color: #1e40af;
        }
        .dark .gsc-badge-bank {
            background: rgba(59, 130, 246, 0.2);
            color: #93c5fd;
        }
        .gsc-badge-ewallet {
            background: #d1fae5;
            color: #065f46;
        }
        .dark .gsc-badge-ewallet {
            background: rgba(16, 185, 129, 0.2);
            color: #6ee7b7;
        }

        .gsc-card-id {
            font-size: 12px;
            color: #64748b;
            font-weight: 600;
        }
        .dark .gsc-card-id {
            color: #94a3b8;
        }

        /* Card Title & Meta */
        .gsc-card-title {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 8px 0;
            line-height: 1.3;
        }
        .dark .gsc-card-title {
            color: #f8fafc;
        }
        .gsc-meta-row {
            font-size: 12px;
            color: #64748b;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .dark .gsc-meta-row {
            color: #94a3b8;
        }
        .gsc-meta-val {
            font-weight: 600;
            color: #1e293b;
        }
        .dark .gsc-meta-val {
            color: #e2e8f0;
        }
        .gsc-meta-mono {
            font-family: ui-monospace, monospace;
            background: #f1f5f9;
            padding: 1px 6px;
            border-radius: 4px;
        }
        .dark .gsc-meta-mono {
            background: #1e293b;
            color: #cbd5e1;
        }

        /* Card Saldo */
        .gsc-saldo-box {
            margin-top: 16px;
            padding-top: 14px;
            border-top: 1px solid #f1f5f9;
        }
        .dark .gsc-saldo-box {
            border-top-color: #1e293b;
        }
        .gsc-saldo-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #64748b;
            margin-bottom: 2px;
        }
        .dark .gsc-saldo-label {
            color: #94a3b8;
        }
        .gsc-saldo-num {
            font-size: 20px;
            font-weight: 800;
            color: #059669;
            letter-spacing: -0.01em;
        }
        .dark .gsc-saldo-num {
            color: #34d399;
        }

        /* Mutasi Sub-stats */
        .gsc-mutasi-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-top: 12px;
            padding: 10px;
            background: #f8fafc;
            border-radius: 8px;
            font-size: 11px;
        }
        .dark .gsc-mutasi-row {
            background: #1e293b;
        }
        .gsc-mutasi-label {
            color: #64748b;
            margin-bottom: 2px;
        }
        .dark .gsc-mutasi-label {
            color: #94a3b8;
        }
        .gsc-mutasi-in {
            font-weight: 700;
            color: #059669;
        }
        .dark .gsc-mutasi-in {
            color: #34d399;
        }
        .gsc-mutasi-out {
            font-weight: 700;
            color: #dc2626;
        }
        .dark .gsc-mutasi-out {
            color: #f87171;
        }

        .gsc-empty {
            text-align: center;
            padding: 32px 16px;
            color: #64748b;
            font-size: 14px;
        }
    </style>

    @php
        $totalSemuaSaldo = 0;
        foreach($akuns as $a) {
            $totalSemuaSaldo += $a->saldo_sekarang;
        }
    @endphp

    <!-- Top Total Saldo Banner -->
    <div class="gsc-akun-summary">
        <div>
            <div class="gsc-akun-summary-title">Total Saldo Terkonsolidasi Seluruh Rekening & Kas</div>
            <div class="gsc-akun-summary-value">Rp {{ number_format($totalSemuaSaldo, 0, ',', '.') }}</div>
        </div>
        <div class="gsc-akun-summary-count">
            {{ count($akuns) }} Akun Aktif
        </div>
    </div>

    <!-- Cards Grid -->
    <div class="gsc-akun-grid">
        @forelse($akuns as $akun)
            @php
                $masuk = (float) $akun->transaksiKeuangans()->where('jenis', 'masuk')->sum('nominal');
                $keluar = (float) $akun->transaksiKeuangans()->where('jenis', 'keluar')->sum('nominal');
                $saldo = ((float) $akun->saldo_awal) + $masuk - $keluar;
                $badgeClass = match($akun->tipe) {
                    'kas' => 'gsc-badge-kas',
                    'bank' => 'gsc-badge-bank',
                    'e_wallet' => 'gsc-badge-ewallet',
                    default => 'gsc-badge-bank',
                };
                $badgeText = match($akun->tipe) {
                    'kas' => 'Kas Tunai Fisik',
                    'bank' => 'Rekening Bank',
                    'e_wallet' => 'E-Wallet / QRIS',
                    default => strtoupper($akun->tipe),
                };
            @endphp
            <div class="gsc-card">
                <div>
                    <div class="gsc-card-header">
                        <span class="gsc-badge {{ $badgeClass }}">
                            {{ $badgeText }}
                        </span>
                        <span class="gsc-card-id">#{{ $akun->id }}</span>
                    </div>

                    <h4 class="gsc-card-title">{{ $akun->nama }}</h4>

                    @if($akun->nomor_rekening)
                        <div class="gsc-meta-row">
                            <span>No. Rek:</span>
                            <span class="gsc-meta-val gsc-meta-mono">{{ $akun->nomor_rekening }}</span>
                        </div>
                    @endif

                    @if($akun->atas_nama)
                        <div class="gsc-meta-row">
                            <span>a.n:</span>
                            <span class="gsc-meta-val">{{ $akun->atas_nama }}</span>
                        </div>
                    @endif

                    @if($akun->keterangan)
                        <div class="gsc-meta-row" style="font-style: italic; opacity: 0.85; margin-top: 4px;">
                            {{ $akun->keterangan }}
                        </div>
                    @endif
                </div>

                <div class="gsc-saldo-box">
                    <div class="gsc-saldo-label">Saldo Riil Terkini</div>
                    <div class="gsc-saldo-num">Rp {{ number_format($saldo, 0, ',', '.') }}</div>

                    <div class="gsc-mutasi-row">
                        <div>
                            <div class="gsc-mutasi-label">Total Masuk</div>
                            <div class="gsc-mutasi-in">+ Rp {{ number_format($masuk, 0, ',', '.') }}</div>
                        </div>
                        <div>
                            <div class="gsc-mutasi-label">Total Keluar</div>
                            <div class="gsc-mutasi-out">- Rp {{ number_format($keluar, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="gsc-empty">
                Belum ada akun kas atau rekening bank yang terdaftar.
            </div>
        @endforelse
    </div>
</div>
