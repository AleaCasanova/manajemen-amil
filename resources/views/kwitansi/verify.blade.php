<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Keaslian Kwitansi Donasi</title>
    <link rel="icon" type="image/png" href="{{ asset('logo_gsc.png') }}">
    <link rel="shortcut icon" href="{{ asset('logo_gsc.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        body {
            background: linear-gradient(135deg, #f0fdf4 0%, #e0f2fe 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 16px;
        }
        .card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 40px -15px rgba(16, 185, 129, 0.15), 0 0 1px 1px rgba(0,0,0,0.05);
            max-width: 480px;
            width: 100%;
            overflow: hidden;
            animation: fadeIn 0.4s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .header {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: #ffffff;
            padding: 28px 24px 22px;
            text-align: center;
            position: relative;
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px);
            padding: 6px 14px;
            border-radius: 9999px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }
        .header h1 {
            font-size: 19px;
            font-weight: 800;
            line-height: 1.3;
        }
        .header p {
            font-size: 12.5px;
            opacity: 0.85;
            margin-top: 4px;
        }
        .content {
            padding: 24px;
        }
        .amount-card {
            background: #f0fdf4;
            border: 1.5px dashed #10b981;
            border-radius: 14px;
            padding: 16px;
            text-align: center;
            margin-bottom: 20px;
        }
        .amount-card span {
            font-size: 11px;
            font-weight: 700;
            color: #047857;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .amount-card h2 {
            font-size: 24px;
            font-weight: 800;
            color: #065f46;
            margin-top: 4px;
        }
        .data-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .data-item {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 10px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13px;
        }
        .data-label {
            color: #64748b;
            font-weight: 500;
            flex: 1;
        }
        .data-val {
            color: #0f172a;
            font-weight: 700;
            text-align: right;
            flex: 1.5;
        }
        .btn-download {
            display: block;
            width: 100%;
            background: #059669;
            color: #ffffff;
            text-decoration: none;
            padding: 12px 18px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 14px;
            text-align: center;
            margin-top: 20px;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.25);
            transition: all 0.2s;
        }
        .btn-download:hover {
            background: #047857;
            transform: translateY(-1px);
        }
        .not-found {
            text-align: center;
            padding: 40px 24px;
        }
        .not-found-icon {
            font-size: 48px;
            margin-bottom: 16px;
        }
    </style>
</head>
<body>

<div class="card">
    @if($donasi)
        <div class="header">
            <img src="{{ asset('logo_gsc.png') }}" alt="Logo GSC" style="width: 64px; height: 64px; object-fit: contain; background: #ffffff; border-radius: 14px; padding: 5px; margin: 0 auto 12px; display: block; box-shadow: 0 4px 12px rgba(0,0,0,0.12);">
            <div class="status-badge">
                ✓ KWITANSI VALID & ASLI
            </div>
            <h1>Gerakan Sedekah Cepat (GSC)</h1>
            <p>Sistem Manajemen Amil & Donasi ZISWAF</p>
        </div>

        <div class="content">
            <div class="amount-card">
                <span>Total Donasi Diterima</span>
                <h2>Rp {{ number_format($donasi->jumlah, 0, ',', '.') }},-</h2>
            </div>

            <div class="data-list">
                <div class="data-item">
                    <span class="data-label">Nomor Kwitansi</span>
                    <span class="data-val">{{ $donasi->no_kwitansi }}</span>
                </div>
                <div class="data-item">
                    <span class="data-label">Nama Donatur</span>
                    <span class="data-val">
                        {{ $donasi->donatur->is_anonim ? 'Hamba Allah (Anonim)' : $donasi->donatur->nama }}
                        @if($donasi->atas_nama)
                            <br><small style="color: #64748b; font-weight: normal;">(a.n {{ $donasi->atas_nama }})</small>
                        @endif
                    </span>
                </div>
                <div class="data-item">
                    <span class="data-label">Jenis Dana</span>
                    <span class="data-val">{{ $donasi->jenisDana->nama }}</span>
                </div>
                @if($donasi->program)
                    <div class="data-item">
                        <span class="data-label">Program Donasi</span>
                        <span class="data-val" style="color: #059669;">{{ $donasi->program->nama }}</span>
                    </div>
                @endif
                <div class="data-item">
                    <span class="data-label">Waktu Transaksi</span>
                    @php
                        $waktuDonasi = \Carbon\Carbon::parse($donasi->tgl_donasi)->timezone('Asia/Jakarta');
                        $jamFormatted = ($waktuDonasi->format('H:i') === '00:00' && $donasi->created_at)
                            ? \Carbon\Carbon::parse($donasi->created_at)->timezone('Asia/Jakarta')->translatedFormat('H:i')
                            : $waktuDonasi->translatedFormat('H:i');
                    @endphp
                    <span class="data-val">{{ $waktuDonasi->translatedFormat('d F Y') }}, {{ $jamFormatted }} WIB</span>
                </div>
                <div class="data-item">
                    <span class="data-label">Metode Pembayaran</span>
                    <span class="data-val" style="text-transform: capitalize;">{{ $donasi->metode_bayar }} {{ $donasi->bank ? '(' . $donasi->bank . ')' : '' }}</span>
                </div>
                <div class="data-item">
                    <span class="data-label">Petugas Amil Penerima</span>
                    <span class="data-val">{{ $donasi->amil ? $donasi->amil->nama : 'Petugas Resmi GSC' }}</span>
                </div>
                <div class="data-item" style="border: none;">
                    <span class="data-label">Status Verifikasi</span>
                    <span class="data-val" style="color: #059669;">{{ strtoupper(str_replace('_', ' ', $donasi->status)) }}</span>
                </div>
            </div>

            <a href="{{ route('kwitansi.download', $donasi->id) }}" class="btn-download">
                📥 Unduh Bukti Kwitansi PDF
            </a>
        </div>
    @else
        <div class="not-found">
            <div class="not-found-icon">⚠️</div>
            <h2 style="font-size: 18px; color: #1e293b; margin-bottom: 8px;">Kwitansi Tidak Ditemukan</h2>
            <p style="font-size: 13px; color: #64748b; line-height: 1.5;">
                Nomor kwitansi <strong>{{ $no_kwitansi }}</strong> tidak terdaftar di basis data resmi Gerakan Sedekah Cepat (GSC). Harap pastikan kembali keaslian dokumen Anda.
            </p>
        </div>
    @endif
</div>

</body>
</html>
