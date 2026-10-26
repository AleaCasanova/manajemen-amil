<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi Donasi - {{ $donasi->no_kwitansi }}</title>
    <style>
        @page {
            margin: 15mm 15mm 15mm 15mm;
            size: a5 landscape;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .container {
            border: 2px solid #059669;
            border-radius: 8px;
            padding: 16px 20px;
            position: relative;
            background-color: #ffffff;
        }
        .header {
            border-bottom: 2px solid #059669;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }
        .header table {
            width: 100%;
        }
        .institution-title {
            font-size: 15px;
            font-weight: bold;
            color: #059669;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .institution-subtitle {
            font-size: 9px;
            color: #64748b;
        }
        .receipt-title-box {
            text-align: right;
        }
        .receipt-title {
            font-size: 16px;
            font-weight: bold;
            color: #059669;
            letter-spacing: 1px;
        }
        .receipt-no {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
        }
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .content-table td {
            padding: 4px 0;
            vertical-align: top;
        }
        .label {
            width: 160px;
            color: #475569;
            font-weight: 500;
        }
        .colon {
            width: 15px;
            text-align: center;
        }
        .value {
            color: #0f172a;
            font-weight: 600;
        }
        .amount-box {
            background-color: #ecfdf5;
            border: 1.5px dashed #059669;
            border-radius: 6px;
            padding: 8px 12px;
            margin: 8px 0;
        }
        .amount-num {
            font-size: 16px;
            font-weight: bold;
            color: #047857;
        }
        .amount-terbilang {
            font-size: 10px;
            font-style: italic;
            color: #065f46;
            margin-top: 2px;
        }
        .footer-table {
            width: 100%;
            margin-top: 10px;
        }
        .qr-section {
            width: 100px;
            text-align: center;
            vertical-align: middle;
        }
        .qr-image {
            width: 80px;
            height: 80px;
        }
        .qr-caption {
            font-size: 7.5px;
            color: #64748b;
            margin-top: 2px;
        }
        .doa-section {
            font-size: 9.5px;
            font-style: italic;
            color: #334155;
            background: #f8fafc;
            border-left: 3px solid #10b981;
            padding: 6px 10px;
            border-radius: 0 4px 4px 0;
        }
        .signature-section {
            text-align: center;
            width: 170px;
            vertical-align: top;
        }
        .sign-date {
            font-size: 9.5px;
            color: #475569;
            margin-bottom: 35px;
        }
        .sign-name {
            font-weight: bold;
            font-size: 10.5px;
            text-decoration: underline;
            color: #0f172a;
        }
        .sign-role {
            font-size: 9px;
            color: #64748b;
        }
        .badge-verified {
            display: inline-block;
            background-color: #d1fae5;
            color: #065f46;
            font-size: 8px;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 3px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Header -->
    <div class="header">
        <table>
            <tr>
                <td style="width: 60%;">
                    <table style="width: auto;">
                        <tr>
                            <td style="width: 48px; vertical-align: middle; padding-right: 10px;">
                                <img src="{{ public_path('logo_gsc.png') }}" style="width: 44px; height: 44px; object-fit: contain;">
                            </td>
                            <td style="vertical-align: middle;">
                                <div class="institution-title">{{ $institution_name }}</div>
                                <div class="institution-subtitle">
                                    {{ $address }}<br>
                                    Telp/WA: {{ $phone }} | Email: {{ $email }}
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td class="receipt-title-box">
                    <div class="receipt-title">BUKTI SETOR DONASI</div>
                    <div class="receipt-no">{{ $donasi->no_kwitansi }}</div>
                    <div style="margin-top: 3px;">
                        <span class="badge-verified">{{ strtoupper(str_replace('_', ' ', $donasi->status)) }}</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Data Transaksi -->
    <table class="content-table">
        <tr>
            <td class="label">Telah Diterima Dari</td>
            <td class="colon">:</td>
            <td class="value">
                @if($donasi->donatur->is_anonim)
                    Hamba Allah (Anonim)
                @else
                    {{ $donasi->donatur->nama }} 
                    <span style="font-size: 9px; font-weight: normal; color: #64748b;">({{ $donasi->donatur->kode_donatur }})</span>
                @endif
                @if($donasi->atas_nama)
                    <span style="font-weight: normal; color: #475569; font-size: 9.5px;">(a.n: {{ $donasi->atas_nama }})</span>
                @endif
            </td>
        </tr>
        <tr>
            <td class="label">Alamat / Kontak</td>
            <td class="colon">:</td>
            <td class="value" style="font-weight: normal; color: #334155;">
                {{ $donasi->donatur->is_anonim ? '-' : ($donasi->donatur->kota ? $donasi->donatur->kota . ', ' . $donasi->donatur->provinsi : ($donasi->donatur->alamat ?? '-')) }}
                @if(!$donasi->donatur->is_anonim && $donasi->donatur->no_hp)
                    | {{ $donasi->donatur->no_hp }}
                @endif
            </td>
        </tr>
        <tr>
            <td class="label">Jenis Dana / Program</td>
            <td class="colon">:</td>
            <td class="value">
                {{ $donasi->jenisDana->nama }}
                @if($donasi->program)
                    - <span style="color: #047857;">{{ $donasi->program->nama }}</span>
                @endif
            </td>
        </tr>
        <tr>
            <td class="label">Metode Pembayaran</td>
            <td class="colon">:</td>
            <td class="value" style="text-transform: capitalize;">
                {{ $donasi->metode_bayar }} {{ $donasi->bank ? '(' . $donasi->bank . ')' : '' }}
                @php
                    $waktuDonasi = \Carbon\Carbon::parse($donasi->tgl_donasi)->timezone('Asia/Jakarta');
                    $jamFormatted = ($waktuDonasi->format('H:i') === '00:00' && $donasi->created_at)
                        ? \Carbon\Carbon::parse($donasi->created_at)->timezone('Asia/Jakarta')->translatedFormat('H:i')
                        : $waktuDonasi->translatedFormat('H:i');
                @endphp
                | Tgl: {{ $waktuDonasi->translatedFormat('d F Y') }}, {{ $jamFormatted }} WIB
            </td>
        </tr>
    </table>

    <!-- Kotak Jumlah Nominal & Terbilang -->
    <div class="amount-box">
        <table style="width: 100%;">
            <tr>
                <td style="width: 140px; vertical-align: middle;">
                    <span style="font-size: 9px; color: #065f46; font-weight: bold; text-transform: uppercase;">Jumlah Donasi:</span><br>
                    <span class="amount-num">Rp {{ number_format($donasi->jumlah, 0, ',', '.') }},-</span>
                </td>
                <td style="vertical-align: middle; padding-left: 15px; border-left: 1px solid #a7f3d0;">
                    <span style="font-size: 8px; color: #065f46; text-transform: uppercase; font-weight: bold;">Terbilang:</span>
                    <div class="amount-terbilang"># {{ $terbilang }} #</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Footer: QR Code, Doa, dan Tanda Tangan -->
    <table class="footer-table">
        <tr>
            <td class="qr-section">
                <img src="{{ $qrCode }}" class="qr-image" alt="QR Code Verifikasi">
                <div class="qr-caption">Scan Verifikasi Keaslian</div>
            </td>
            <td style="padding: 0 15px; vertical-align: middle;">
                <div class="doa-section">
                    <strong>Doa untuk Donatur:</strong><br>
                    "Semoga Allah memberikan pahala atas apa yang engkau berikan, dan menjadikannya pembersih bagimu, serta memberkahi harta yang masih ada padamu."
                    @if($donasi->pesan)
                        <div style="margin-top: 3px; font-weight: 500; color: #0f172a;">Pesan: "{{ $donasi->pesan }}"</div>
                    @endif
                </div>
            </td>
            <td class="signature-section">
                <div class="sign-date">{{ \Carbon\Carbon::parse($donasi->tgl_donasi)->translatedFormat('d F Y') }}</div>
                <div class="sign-name">{{ $donasi->amil ? $donasi->amil->nama : 'Petugas Amil' }}</div>
                <div class="sign-role">
                    NIP: {{ $donasi->amil ? $donasi->amil->nip : '-' }}<br>
                    Petugas Penerima
                </div>
            </td>
        </tr>
    </table>
</div>

</body>
</html>
