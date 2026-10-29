<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi Donasi - {{ $donasi->no_kwitansi }}</title>
    <style>
        @page {
            margin: 6mm 8mm;
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
            border: 2px solid #0891b2;
            border-radius: 12px;
            padding: 14px 18px;
            position: relative;
            background-color: #ffffff;
        }
        .header-table {
            width: 100%;
            border-bottom: 1.5px solid #0891b2;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }
        .institution-title {
            font-size: 15.5px;
            font-weight: bold;
            color: #0891b2;
            letter-spacing: 0.3px;
            margin-bottom: 2px;
        }
        .institution-subtitle {
            font-size: 8.5px;
            color: #475569;
            line-height: 1.35;
        }
        .receipt-title {
            font-size: 16px;
            font-weight: bold;
            color: #0891b2;
            letter-spacing: 0.5px;
            text-align: right;
        }
        .receipt-no {
            font-size: 11.5px;
            font-weight: bold;
            color: #0f172a;
            text-align: right;
            margin-top: 1px;
        }
        .badge-status {
            display: inline-block;
            font-size: 8.5px;
            font-weight: bold;
            padding: 3px 8px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-verified {
            background-color: #cffafe;
            color: #0e7490;
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
            margin-bottom: 6px;
        }
        .content-table td {
            padding: 3px 0;
            vertical-align: top;
            font-size: 10px;
        }
        .label {
            width: 165px;
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
            border: 1.5px dashed #0891b2;
            border-radius: 8px;
            padding: 7px 14px;
            margin: 6px 0 8px 0;
            background-color: #ffffff;
        }
        .amount-box table {
            width: 100%;
        }
        .amount-num {
            font-size: 18px;
            font-weight: bold;
            color: #0891b2;
            margin-top: 2px;
        }
        .amount-terbilang {
            font-size: 11px;
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
            width: 72px;
            text-align: center;
        }
        .qr-image {
            width: 65px;
            height: 65px;
        }
        .qr-caption {
            font-size: 7.5px;
            color: #64748b;
            margin-top: 3px;
        }
        .doa-box {
            border-left: 2.5px solid #0891b2;
            background-color: #f8fafc;
            padding: 6px 10px;
            border-radius: 0 4px 4px 0;
            margin-left: 8px;
        }
        .doa-title {
            font-size: 8.5px;
            font-weight: bold;
            color: #0891b2;
            margin-bottom: 2px;
        }
        .doa-text {
            font-size: 8px;
            font-style: italic;
            color: #334155;
            line-height: 1.3;
        }
        .sign-col {
            text-align: center;
        }
        .sign-title {
            font-size: 9px;
            font-weight: bold;
            color: #1e293b;
        }
        .sign-date {
            font-size: 9px;
            color: #475569;
            margin-bottom: 2px;
        }
        .sign-placeholder {
            height: 54px;
            line-height: 54px;
            color: #94a3b8;
            font-size: 8.5px;
            font-style: italic;
        }
        .sign-name-solid {
            display: inline-block;
            font-size: 9.5px;
            font-weight: bold;
            color: #0f172a;
            border-bottom: 1.5px solid #1e293b;
            padding-bottom: 2px;
            min-width: 140px;
        }
        .sign-name-dotted {
            display: inline-block;
            font-size: 9.5px;
            font-weight: bold;
            color: #0f172a;
            border-bottom: 1px dotted #94a3b8;
            padding-bottom: 2px;
            min-width: 135px;
        }
        .sign-role {
            font-size: 8px;
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
                        <td style="width: 76px; vertical-align: middle; padding-right: 14px;">
                            @if(!empty($logoKop))
                                <img src="{{ $logoKop }}" style="width: 72px; height: 72px; object-fit: contain;">
                            @else
                                <img src="{{ public_path('image/logo_kop.png') }}" style="width: 72px; height: 72px; object-fit: contain;">
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
            <td class="label">Alamat Donatur</td>
            <td class="colon">:</td>
            <td class="value">
                @if($donasi->donatur->is_anonim)
                    -
                @else
                    {{ $donasi->donatur->alamat ?? ($donasi->donatur->kota ?? '-') }}
                @endif
            </td>
        </tr>
        <tr>
            <td class="label">No. Kontak / WhatsApp</td>
            <td class="colon">:</td>
            <td class="value">
                @if($donasi->donatur->is_anonim)
                    -
                @else
                    {{ $donasi->donatur->no_hp ?? '-' }}
                @endif
            </td>
        </tr>
        <tr>
            <td class="label">Jenis Dana</td>
            <td class="colon">:</td>
            <td class="value">
                <strong>{{ $donasi->jenisDana?->nama }}</strong>
            </td>
        </tr>
        @if($donasi->program)
            <tr>
                <td class="label">Program Penyaluran</td>
                <td class="colon">:</td>
                <td class="value">
                    <strong style="color: #0891b2;">{{ $donasi->program->nama }}</strong>
                </td>
            </tr>
        @endif
        <tr>
            <td class="label">Metode Pembayaran</td>
            <td class="colon">:</td>
            <td class="value">
                @php
                    $metode = strtolower($donasi->metode_bayar ?? 'tunai');
                    if ($metode === 'tunai') {
                        $metodeBayarText = 'Tunai';
                    } elseif ($metode === 'qris') {
                        $metodeBayarText = 'QRIS';
                    } elseif ($metode === 'transfer') {
                        $metodeBayarText = $donasi->bank ? 'Transfer Bank (' . $donasi->bank . ')' : 'Transfer Bank';
                    } elseif ($metode === 'ewallet') {
                        $metodeBayarText = $donasi->bank ? 'E-Wallet (' . $donasi->bank . ')' : 'E-Wallet';
                    } else {
                        $metodeBayarText = ucfirst($metode);
                    }
                @endphp
                {{ $metodeBayarText }}
            </td>
        </tr>
        <tr>
            <td class="label">Waktu Transaksi</td>
            <td class="colon">:</td>
            <td class="value">
                @php
                    $waktuDonasi = \Carbon\Carbon::parse($donasi->tgl_donasi)->timezone('Asia/Jakarta');
                    $jamFormatted = ($waktuDonasi->format('H:i') === '00:00' && $donasi->created_at)
                        ? \Carbon\Carbon::parse($donasi->created_at)->timezone('Asia/Jakarta')->translatedFormat('H:i')
                        : $waktuDonasi->translatedFormat('H:i');
                    $tglLengkap = $waktuDonasi->locale('id')->translatedFormat('d F Y');
                @endphp
                {{ $tglLengkap }}, {{ $jamFormatted }} WIB
            </td>
        </tr>
        @if($donasi->donasiBarangs && $donasi->donasiBarangs->count() > 0)
            @foreach($donasi->donasiBarangs as $itemBarang)
                <tr>
                    <td class="label">Donasi Barang / Natura</td>
                    <td class="colon">:</td>
                    <td class="value">
                        <strong style="color: #0891b2;">{{ $itemBarang->deskripsi }}</strong>
                        @if($itemBarang->estimasi_nilai > 0)
                            <span style="color: #475569; font-size: 9.5px;">(Estimasi Nilai: Rp {{ number_format($itemBarang->estimasi_nilai, 0, ',', '.') }})</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        @endif
    </table>

    <!-- Kotak Jumlah Nominal & Terbilang -->
    <div class="amount-box">
        <table style="width: 100%;">
            <tr>
                <td style="width: 36%; vertical-align: middle;">
                    <div style="font-size: 8.5px; color: #0891b2; font-weight: bold; text-transform: uppercase;">JUMLAH DONASI:</div>
                    <div class="amount-num">Rp {{ number_format($donasi->jumlah, 0, ',', '.') }},-</div>
                </td>
                <td style="width: 64%; vertical-align: middle; border-left: 1px dashed #67e8f9; padding-left: 16px;">
                    <div style="font-size: 8.5px; color: #0891b2; font-weight: bold; text-transform: uppercase;">TERBILANG:</div>
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
                <div style="height: 60px; text-align: center; margin: 0 auto; margin-bottom: -6px; position: relative; z-index: 2;">
                    @if(!empty($ttdCapResmi))
                        <img src="{{ $ttdCapResmi }}" style="height: 60px; width: auto; object-fit: contain;">
                    @elseif(!empty($capGsc) || !empty($ttdBendahara))
                        <table style="margin: 0 auto; width: auto;">
                            <tr>
                                @if(!empty($capGsc))
                                    <td style="vertical-align: middle; padding-right: 4px;">
                                        <img src="{{ $capGsc }}" style="height: 52px; width: auto; object-fit: contain;">
                                    </td>
                                @endif
                                @if(!empty($ttdBendahara))
                                    <td style="vertical-align: middle;">
                                        <img src="{{ $ttdBendahara }}" style="height: 52px; width: auto; object-fit: contain;">
                                    </td>
                                @endif
                            </tr>
                        </table>
                    @else
                        <div style="height: 60px;"></div>
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
