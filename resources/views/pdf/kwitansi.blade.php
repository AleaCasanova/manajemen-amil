<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi Donasi - {{ $donasi->no_kwitansi }}</title>
    <style>
        @page {
            margin: 6mm 8mm 5mm 8mm;
            size: a5 landscape;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #1e293b;
            line-height: 1.3;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }
        .container {
            border: 2px solid #0f766e;
            border-radius: 10px;
            padding: 8px 12px;
            position: relative;
            background-color: #ffffff;
        }
        .header-table {
            width: 100%;
            border-bottom: 1.5px solid #0f766e;
            padding-bottom: 5px;
            margin-bottom: 6px;
        }
        .institution-title {
            font-size: 14.5px;
            font-weight: bold;
            color: #0f766e;
            letter-spacing: 0.3px;
            margin-bottom: 2px;
        }
        .institution-subtitle {
            font-size: 8.5px;
            color: #475569;
            line-height: 1.3;
        }
        .receipt-title {
            font-size: 15px;
            font-weight: bold;
            color: #0f766e;
            letter-spacing: 0.5px;
            text-align: right;
        }
        .receipt-no {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
            text-align: right;
            margin-top: 1px;
        }
        .badge-status {
            display: inline-block;
            font-size: 8px;
            font-weight: bold;
            padding: 2.5px 7px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-verified {
            background-color: #d1fae5;
            color: #047857;
        }
        .badge-pending {
            background-color: #fef3c7;
            color: #b45309;
        }
        .badge-rejected {
            background-color: #ffe4e6;
            color: #e11d48;
        }
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .content-table td {
            padding: 3px 0;
            vertical-align: top;
            font-size: 10px;
        }
        .label {
            width: 150px;
            color: #334155;
            font-weight: 600;
        }
        .colon {
            width: 12px;
            text-align: center;
            color: #334155;
            font-weight: 600;
        }
        .value {
            color: #0f172a;
        }
        .amount-box {
            border: 1.5px dashed #0f766e;
            border-radius: 8px;
            padding: 5px 10px;
            margin: 4px 0 6px 0;
            background-color: #ffffff;
        }
        .amount-box table {
            width: 100%;
        }
        .amount-num {
            font-size: 16px;
            font-weight: bold;
            color: #047857;
            margin-top: 1px;
        }
        .amount-terbilang {
            font-size: 10px;
            font-style: italic;
            font-weight: bold;
            color: #0f172a;
            margin-top: 1px;
            line-height: 1.25;
        }
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }
        .footer-table td {
            vertical-align: top;
        }
        .qr-section {
            width: 65px;
            text-align: center;
        }
        .qr-image {
            width: 58px;
            height: 58px;
        }
        .qr-caption {
            font-size: 7px;
            color: #64748b;
            margin-top: 2px;
        }
        .doa-box {
            border-left: 2px solid #0f766e;
            background-color: #f8fafc;
            padding: 4px 7px;
            border-radius: 0 4px 4px 0;
            margin-left: 5px;
        }
        .doa-title {
            font-size: 8px;
            font-weight: bold;
            color: #0f766e;
            margin-bottom: 2px;
        }
        .doa-text {
            font-size: 7.5px;
            font-style: italic;
            color: #334155;
            line-height: 1.25;
        }
        .sign-col {
            text-align: center;
        }
        .sign-title {
            font-size: 8.5px;
            font-weight: bold;
            color: #1e293b;
        }
        .sign-date {
            font-size: 8.5px;
            color: #475569;
            margin-bottom: 2px;
        }
        .sign-placeholder {
            height: 48px;
            line-height: 48px;
            color: #94a3b8;
            font-size: 8px;
            font-style: italic;
        }
        .sign-name-solid {
            display: inline-block;
            font-size: 9px;
            font-weight: bold;
            color: #0f172a;
            border-bottom: 1.5px solid #1e293b;
            padding-bottom: 1.5px;
            min-width: 135px;
        }
        .sign-name-dotted {
            display: inline-block;
            font-size: 9px;
            font-weight: bold;
            color: #0f172a;
            border-bottom: 1px dotted #94a3b8;
            padding-bottom: 1.5px;
            min-width: 130px;
        }
        .sign-role {
            font-size: 7.5px;
            color: #64748b;
            margin-top: 3px;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Header -->
    <table class="header-table">
        <tr>
            <td style="width: 62%; vertical-align: middle;">
                <table style="width: auto;">
                    <tr>
                        <td style="width: 54px; vertical-align: middle; padding-right: 10px;">
                            @if(!empty($logoKop))
                                <img src="{{ $logoKop }}" style="width: 48px; height: 48px; object-fit: contain;">
                            @else
                                <img src="{{ public_path('logo_gsc.png') }}" style="width: 48px; height: 48px; object-fit: contain;">
                            @endif
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
            <td style="width: 38%; vertical-align: middle; text-align: right;">
                <div class="receipt-title">BUKTI SETOR DONASI</div>
                <div class="receipt-no">{{ $donasi->no_kwitansi }}</div>
                <div style="margin-top: 3px;">
                    @if($donasi->status === 'terverifikasi')
                        <span class="badge-status badge-verified">TERVERIFIKASI</span>
                    @elseif($donasi->status === 'ditolak')
                        <span class="badge-status badge-rejected">DITOLAK</span>
                    @else
                        <span class="badge-status badge-pending">MENUNGGU VERIFIKASI</span>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <!-- Data Transaksi -->
    <table class="content-table">
        <tr>
            <td class="label">Telah Diterima Dari</td>
            <td class="colon">:</td>
            <td class="value">
                @if($donasi->donatur->is_anonim)
                    <strong>Hamba Allah (Anonim)</strong>
                @else
                    <strong>{{ strtoupper($donasi->donatur->nama) }}</strong>
                @endif
                @if($donasi->atas_nama)
                    <span style="font-weight: normal; color: #475569; font-size: 9.5px;">(a.n: {{ $donasi->atas_nama }})</span>
                @endif
            </td>
        </tr>
        <tr>
            <td class="label">Alamat / Kontak</td>
            <td class="colon">:</td>
            <td class="value">
                @if($donasi->donatur->is_anonim)
                    -
                @else
                    {{ $donasi->donatur->alamat ?? ($donasi->donatur->kota ?? '-') }}
                    @if($donasi->donatur->no_hp)
                        | {{ $donasi->donatur->no_hp }}
                    @endif
                @endif
            </td>
        </tr>
        <tr>
            <td class="label">Jenis Dana / Program</td>
            <td class="colon">:</td>
            <td class="value">
                <strong>{{ $donasi->jenisDana?->nama }}</strong>
                @if($donasi->program)
                    - <span style="color: #047857; font-weight: 600;">{{ $donasi->program->nama }}</span>
                @endif
            </td>
        </tr>
        <tr>
            <td class="label">Metode Pembayaran</td>
            <td class="colon">:</td>
            <td class="value">
                @php
                    $waktuDonasi = \Carbon\Carbon::parse($donasi->tgl_donasi)->timezone('Asia/Jakarta');
                    $jamFormatted = ($waktuDonasi->format('H:i') === '00:00' && $donasi->created_at)
                        ? \Carbon\Carbon::parse($donasi->created_at)->timezone('Asia/Jakarta')->translatedFormat('H:i')
                        : $waktuDonasi->translatedFormat('H:i');
                    $tglLengkap = $waktuDonasi->locale('id')->translatedFormat('d F Y');

                    $metodeBayarText = ucfirst($donasi->metode_bayar);
                    if ($donasi->bank) {
                        $metodeBayarText = $donasi->bank . ' (' . ucfirst($donasi->metode_bayar) . ')';
                    }
                @endphp
                {{ $metodeBayarText }} | Tgl: {{ $tglLengkap }} {{ $jamFormatted }} WIB
            </td>
        </tr>
    </table>

    <!-- Kotak Jumlah Nominal & Terbilang -->
    <div class="amount-box">
        <table style="width: 100%;">
            <tr>
                <td style="width: 36%; vertical-align: middle;">
                    <div style="font-size: 8.5px; color: #0f766e; font-weight: bold; text-transform: uppercase;">JUMLAH DONASI:</div>
                    <div class="amount-num">Rp {{ number_format($donasi->jumlah, 0, ',', '.') }},-</div>
                </td>
                <td style="width: 64%; vertical-align: middle; border-left: 1px dashed #5eead4; padding-left: 16px;">
                    <div style="font-size: 8.5px; color: #0f766e; font-weight: bold; text-transform: uppercase;">TERBILANG:</div>
                    <div class="amount-terbilang">{{ $terbilang }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Footer: QR Code, Doa, Tanda Tangan Donatur, dan Cap & Tanda Tangan Bendahara -->
    <table class="footer-table">
        <tr>
            <!-- Kolom 1: QR Code & Doa Donatur -->
            <td style="width: 44%; vertical-align: top;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td class="qr-section" style="vertical-align: middle;">
                            <img src="{{ $qrCode }}" class="qr-image" alt="QR Code Verifikasi">
                            <div class="qr-caption">Scan Validasi Keaslian</div>
                        </td>
                        <td style="vertical-align: middle; padding-left: 6px;">
                            <div class="doa-box">
                                <div class="doa-title">Doa untuk Donatur:</div>
                                <div class="doa-text">
                                    "Semoga Allah memberikan pahala atas apa yang engkau berikan, dan menjadikannya pembersih bagimu, serta memberkahi harta yang masih ada padamu."
                                </div>
                                @if($donasi->pesan)
                                    <div style="margin-top: 2px; font-size: 7px; font-weight: 600; color: #0f172a;">
                                        Pesan: "{{ $donasi->pesan }}"
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                </table>
            </td>

            <!-- Kolom 2: Yang Menyerahkan (Donatur) -->
            <td class="sign-col" style="width: 28%; vertical-align: top;">
                <div style="font-size: 8.5px; color: transparent; margin-bottom: 2px;">&nbsp;</div>
                <div class="sign-title">Yang Menyerahkan (Donatur),</div>
                <div class="sign-placeholder">[ Tanda Tangan ]</div>
                <div>
                    <span class="sign-name-dotted">{{ $donasi->donatur->is_anonim ? 'HAMBA ALLAH' : strtoupper($donasi->donatur->nama) }}</span>
                </div>
                <div class="sign-role">Donatur</div>
            </td>

            <!-- Kolom 3: Petugas Penerima / Bendahara -->
            <td class="sign-col" style="width: 28%; vertical-align: top;">
                <div class="sign-date">Cilacap, {{ \Carbon\Carbon::parse($donasi->tgl_donasi)->locale('id')->translatedFormat('d F Y') }}</div>
                <div class="sign-title">Petugas Penerima / Bendahara,</div>
                <div style="height: 50px; text-align: center; margin: 0 auto; margin-bottom: -15px; position: relative; z-index: 2;">
                    @if(!empty($ttdCapResmi))
                        <img src="{{ $ttdCapResmi }}" style="height: 50px; width: auto; object-fit: contain;">
                    @elseif(!empty($capGsc) || !empty($ttdBendahara))
                        <table style="margin: 0 auto; width: auto;">
                            <tr>
                                @if(!empty($capGsc))
                                    <td style="vertical-align: middle; padding-right: 4px;">
                                        <img src="{{ $capGsc }}" style="height: 44px; width: auto; object-fit: contain;">
                                    </td>
                                @endif
                                @if(!empty($ttdBendahara))
                                    <td style="vertical-align: middle;">
                                        <img src="{{ $ttdBendahara }}" style="height: 44px; width: auto; object-fit: contain;">
                                    </td>
                                @endif
                            </tr>
                        </table>
                    @else
                        <div style="height: 50px;"></div>
                    @endif
                </div>
                <div style="position: relative; z-index: 1;">
                    <span class="sign-name-solid">BENDAHARA GSC</span>
                    <div class="sign-role">Pengurus &amp; Bendahara GSC</div>
                </div>
            </td>
        </tr>
    </table>
</div>

</body>
</html>
