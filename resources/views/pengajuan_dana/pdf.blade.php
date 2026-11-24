<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Formulir Pengajuan Dana - {{ $pengajuan->nomor_pengajuan }}</title>
    <style>
        @page {
            margin: 1.5cm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 11pt;
            line-height: 1.5;
        }
        .header {
            border-bottom: 2px solid #059669;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header table {
            width: 100%;
        }
        .logo {
            max-height: 65px;
            max-width: 180px;
        }
        .institution-name {
            font-size: 15pt;
            font-weight: bold;
            color: #047857;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .institution-sub {
            font-size: 8.5pt;
            color: #64748b;
            margin-top: 2px;
        }
        .doc-title {
            text-align: center;
            font-size: 13pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 4px;
        }
        .doc-no {
            text-align: center;
            font-size: 9.5pt;
            color: #059669;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .info-table td {
            padding: 5px 6px;
            font-size: 10pt;
            vertical-align: top;
        }
        .info-table td.label {
            width: 25%;
            color: #475569;
            font-weight: 500;
        }
        .info-table td.colon {
            width: 3%;
            color: #64748b;
        }
        .info-table td.value {
            width: 72%;
            font-weight: 600;
            color: #0f172a;
        }
        .section-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px 14px;
            margin-bottom: 16px;
        }
        .section-title {
            font-size: 10pt;
            font-weight: bold;
            color: #047857;
            text-transform: uppercase;
            margin-bottom: 8px;
            border-bottom: 1px dashed #cbd5e1;
            padding-bottom: 4px;
        }
        .amount-box {
            background: #ecfdf5;
            border: 1.5px solid #10b981;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 20px;
            text-align: center;
        }
        .amount-label {
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #065f46;
        }
        .amount-value {
            font-size: 18pt;
            font-weight: bold;
            color: #047857;
            margin: 4px 0;
        }
        .amount-terbilang {
            font-size: 9.5pt;
            font-style: italic;
            color: #334155;
        }
        .signatures {
            width: 100%;
            margin-top: 35px;
            page-break-inside: avoid;
        }
        .signatures table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }
        .signatures td {
            width: 33.33%;
            vertical-align: top;
            padding: 0 8px;
        }
        .sig-role {
            font-size: 9.5pt;
            font-weight: bold;
            color: #334155;
            margin-bottom: 4px;
        }
        .sig-status {
            font-size: 8pt;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 45px;
        }
        .status-acc {
            background: #d1fae5;
            color: #065f46;
        }
        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }
        .status-reject {
            background: #fee2e2;
            color: #991b1b;
        }
        .sig-name {
            font-size: 10pt;
            font-weight: bold;
            color: #0f172a;
            border-top: 1px solid #94a3b8;
            padding-top: 4px;
        }
        .sig-nip {
            font-size: 8pt;
            color: #64748b;
        }
        .footer-note {
            margin-top: 30px;
            font-size: 8pt;
            color: #94a3b8;
            text-align: center;
            border-top: 1px dashed #e2e8f0;
            padding-top: 8px;
        }
    </style>
</head>
<body>

    <!-- KOP RESMI -->
    <div class="header">
        <table>
            <tr>
                @if($logoKop)
                    <td style="width: 20%; vertical-align: middle;">
                        <img src="{{ $logoKop }}" class="logo">
                    </td>
                @endif
                <td style="vertical-align: middle; {{ $logoKop ? 'padding-left: 10px;' : 'text-align: center;' }}">
                    <div class="institution-name">{{ $institution }}</div>
                    <div class="institution-sub">{{ $address }}</div>
                    <div class="institution-sub">Telp/WA: {{ $phone }} | Email: {{ $email }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- JUDUL DOKUMEN -->
    <div class="doc-title">Formulir Pengajuan Dana & Realisasi Anggaran (BKK)</div>
    <div class="doc-no">Nomor Registrasi: {{ $pengajuan->nomor_pengajuan }}</div>

    <!-- INFORMASI PENGAJUAN -->
    <div class="section-box">
        <div class="section-title">I. Identitas Pemohon & Referensi Resmi</div>
        <table class="info-table">
            <tr>
                <td class="label">Nama Pemohon (Amil)</td>
                <td class="colon">:</td>
                <td class="value">{{ $pengajuan->amil?->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">NIP / Jabatan</td>
                <td class="colon">:</td>
                <td class="value">{{ $pengajuan->amil?->nip ?? '-' }} / {{ $pengajuan->amil?->jabatan_label ?? 'Petugas Amil' }}</td>
            </tr>
            <tr>
                <td class="label">Divisi / Bidang</td>
                <td class="colon">:</td>
                <td class="value">{{ $pengajuan->divisi?->nama ?? ($pengajuan->amil?->divisi?->nama ?? 'Umum') }}</td>
            </tr>
            <tr>
                <td class="label">No. Dokumen Referensi</td>
                <td class="colon">:</td>
                <td class="value">{{ $pengajuan->nomor_dokumen_referensi ? $pengajuan->nomor_dokumen_referensi : '(Tidak ada nomor memo khusus)' }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Pengajuan</td>
                <td class="colon">:</td>
                <td class="value">{{ $pengajuan->created_at ? $pengajuan->created_at->format('d/m/Y H:i') : '-' }} WIB</td>
            </tr>
            <tr>
                <td class="label">Tanggal Kebutuhan Dana</td>
                <td class="colon">:</td>
                <td class="value" style="color: #059669;">{{ $pengajuan->tgl_kebutuhan ? $pengajuan->tgl_kebutuhan->format('d F Y') : '-' }}</td>
            </tr>
        </table>
    </div>

    <!-- ALOKASI DAN KEPERLUAN -->
    <div class="section-box">
        <div class="section-title">II. Rincian Kebutuhan & Pos Anggaran</div>
        <table class="info-table">
            <tr>
                <td class="label">Keperluan / Judul</td>
                <td class="colon">:</td>
                <td class="value">{{ $pengajuan->judul }}</td>
            </tr>
            <tr>
                <td class="label">Pos Kategori Anggaran</td>
                <td class="colon">:</td>
                <td class="value">{{ $pengajuan->kategoriKeuangan?->nama ?? 'Penyaluran / Operasional' }}</td>
            </tr>
            @if($pengajuan->program)
                <tr>
                    <td class="label">Program / Campaign Terkait</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $pengajuan->program->nama }}</td>
                </tr>
            @endif
            <tr>
                <td class="label">Uraian / Deskripsi RAB</td>
                <td class="colon">:</td>
                <td class="value" style="font-weight: normal; color: #334155;">{{ $pengajuan->deskripsi }}</td>
            </tr>
        </table>
    </div>

    <!-- NOMINAL ANGGARAN -->
    <div class="amount-box">
        <div class="amount-label">
            {{ $pengajuan->nominal_disetujui ? 'Total Dana Yang Disetujui (ACC)' : 'Total Dana Yang Diajukan' }}
        </div>
        <div class="amount-value">
            Rp {{ number_format((float) $pengajuan->nominal_final, 0, ',', '.') }}
        </div>
        <div class="amount-terbilang">
            Terbilang: {{ $terbilang }}
        </div>
        @if($pengajuan->nominal_disetujui && $pengajuan->nominal_disetujui != $pengajuan->nominal_diajukan)
            <div style="font-size: 8.5pt; color: #64748b; margin-top: 4px;">
                (Nominal pengajuan awal: Rp {{ number_format((float) $pengajuan->nominal_diajukan, 0, ',', '.') }})
            </div>
        @endif
    </div>

    <!-- STATUS PENCAIRAN (JIKA ADA) -->
    @if($pengajuan->status === 'dicairkan')
        <div class="section-box" style="background: #f0fdf4; border-color: #86efac;">
            <div class="section-title" style="color: #166534;">III. Rincian Realisasi Pencairan Kas</div>
            <table class="info-table">
                <tr>
                    <td class="label">Sumber Rekening / Kas</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $pengajuan->akunKeuangan?->nama ?? 'Kas Tunai Kantor' }}</td>
                </tr>
                <tr>
                    <td class="label">Metode Pencairan</td>
                    <td class="colon">:</td>
                    <td class="value">{{ strtoupper($pengajuan->metode_pencairan) }}</td>
                </tr>
                <tr>
                    <td class="label">Tanggal Pencairan</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $pengajuan->tgl_pencairan ? $pengajuan->tgl_pencairan->format('d F Y') : '-' }}</td>
                </tr>
            </table>
        </div>
    @endif

    <!-- TABEL OTORISASI BERJENJANG 3 PIHAK -->
    <div class="signatures">
        <table>
            <tr>
                <!-- 1. Pemohon (Amil) -->
                <td>
                    <div class="sig-role">Pemohon (Amil)</div>
                    <div class="sig-status status-acc">DIAJUKAN</div>
                    <div class="sig-name">{{ $pengajuan->amil?->nama ?? 'Amil Pemohon' }}</div>
                    <div class="sig-nip">NIP: {{ $pengajuan->amil?->nip ?? '-' }}</div>
                </td>

                <!-- 2. Mengetahui / Verifikasi (Bendahara) -->
                <td>
                    <div class="sig-role">Verifikasi (Bendahara)</div>
                    @if(in_array($pengajuan->status, ['diverifikasi_bendahara', 'disetujui', 'dicairkan']))
                        <div class="sig-status status-acc">TERVERIFIKASI</div>
                    @elseif($pengajuan->status === 'ditolak' && $pengajuan->bendahara_id)
                        <div class="sig-status status-reject">DITOLAK</div>
                    @else
                        <div class="sig-status status-pending">MENUNGGU REVIEW</div>
                    @endif
                    <div class="sig-name">{{ $pengajuan->bendahara?->name ?? 'Bendahara Keuangan' }}</div>
                    <div class="sig-nip">Tgl: {{ $pengajuan->tgl_verifikasi_bendahara ? $pengajuan->tgl_verifikasi_bendahara->format('d/m/Y') : '-' }}</div>
                </td>

                <!-- 3. Menyetujui (Manager / Direktur) -->
                <td>
                    <div class="sig-role">Menyetujui (Manager)</div>
                    @if(in_array($pengajuan->status, ['disetujui', 'dicairkan']))
                        <div class="sig-status status-acc">DISETUJUI (ACC)</div>
                    @elseif($pengajuan->status === 'ditolak')
                        <div class="sig-status status-reject">DITOLAK</div>
                    @else
                        <div class="sig-status status-pending">MENUNGGU ACC</div>
                    @endif
                    <div class="sig-name">{{ $pengajuan->manager?->name ?? 'Manager / Pimpinan' }}</div>
                    <div class="sig-nip">Tgl: {{ $pengajuan->tgl_acc_manager ? $pengajuan->tgl_acc_manager->format('d/m/Y') : '-' }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer-note">
        Dokumen ini diterbitkan secara otomatis oleh Sistem Informasi Keuangan {{ $appName }} sebagai bukti resmi pengajuan dana kasbon internal.
    </div>

</body>
</html>
