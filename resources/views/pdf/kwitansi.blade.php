<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi Donasi - {{ $donasi->no_kwitansi }}</title>
    <style>
        @page {
            margin: 8mm 10mm 8mm 10mm;
            size: a5 landscape;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10.5px;
            color: #1e293b;
            line-height: 1.35;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }
        .container {
            border: 2px solid #0f766e;
            border-radius: 12px;
            padding: 12px 16px;
            position: relative;
            background-color: #ffffff;
        }
        .header-table {
            width: 100%;
            border-bottom: 1.5px solid #0f766e;
            padding-bottom: 8px;
            margin-bottom: 10px;
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
            padding: 8px 12px;
            margin: 6px 0 10px 0;
            background-color: #ffffff;
        }
        .amount-box table {
            width: 100%;
        }
        .amount-num {
            font-size: 17px;
            font-weight: bold;
            color: #047857;
            margin-top: 2px;
        }
        .amount-terbilang {
            font-size: 10.5px;
            font-style: italic;
            font-weight: bold;
            color: #0f172a;
            margin-top: 2px;
            line-height: 1.3;
        }
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }
        .footer-table td {
            vertical-align: top;
        }
        .qr-section {
            width: 70px;
            text-align: center;
        }
        .qr-image {
            width: 65px;
            height: 65px;
        }
        .qr-caption {
            font-size: 7px;
            color: #64748b;
            margin-top: 2px;
        }
        .doa-box {
            border-left: 2px solid #0f766e;
            background-color: #f8fafc;
            padding: 5px 8px;
            border-radius: 0 4px 4px 0;
            margin-left: 6px;
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
            height: 38px;
            color: #94a3b8;
            font-size: 8px;
            font-style: italic;
            line-height: 38px;
        }
        .sign-name {
            font-size: 9px;
            font-weight: bold;
            color: #0f172a;
            margin-top: 2px;
        }
        .sign-role {
            font-size: 7.5px;
            color: #64748b;
        }
        .dotted-line {
            border-bottom: 1px dotted #94a3b8;
            width: 130px;
            margin: 0 auto;
        }
        .solid-line {
            border-bottom: 1px solid #1e293b;
            width: 140px;
            margin: 0 auto;
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
                    <span style="font-weight: normal; color: #64748b;">({{ $donasi->donatur->kode_donatur }})</span>
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
                <div class="sign-title">Yang Menyerahkan (Donatur),</div>
                <div class="sign-placeholder">[ Tanda Tangan ]</div>
                <div class="dotted-line"></div>
                <div class="sign-name">( {{ $donasi->donatur->is_anonim ? 'HAMBA ALLAH' : strtoupper($donasi->donatur->nama) }} )</div>
                <div class="sign-role">Donatur</div>
            </td>

            <!-- Kolom 3: Petugas Penerima / Bendahara -->
            <td class="sign-col" style="width: 28%; vertical-align: top;">
                <div class="sign-date">Cilacap, {{ \Carbon\Carbon::parse($donasi->tgl_donasi)->locale('id')->translatedFormat('d F Y') }}</div>
                <div class="sign-title">Petugas Penerima / Bendahara,</div>
                <div style="height: 38px; text-align: center; margin: 0 auto;">
                    <table style="margin: 0 auto; width: auto;">
                        <tr>
                            @if(!empty($capGsc))
                                <td style="vertical-align: middle; padding-right: 4px;">
                                    <img src="{{ $capGsc }}" style="height: 36px; width: auto; object-fit: contain;">
                                </td>
                            @endif
                            @if(!empty($ttdBendahara))
                                <td style="vertical-align: middle;">
                                    <img src="{{ $ttdBendahara }}" style="height: 36px; width: auto; object-fit: contain;">
                                </td>
                            @endif
                            @if(empty($capGsc) && empty($ttdBendahara))
                                <td style="height: 36px;"></td>
                            @endif
                        </tr>
                    </table>
                </div>
                <div class="solid-line"></div>
                <div class="sign-name">( BENDAHARA GSC )</div>
                <div class="sign-role">Pengurus &amp; Bendahara GSC</div>
            </td>
        </tr>
    </table>
</div>

</body>
</html>
