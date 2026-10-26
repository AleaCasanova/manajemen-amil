@extends('layouts.amil')

@section('title', 'Kwitansi Donasi Diterbitkan')

@section('content')
<div class="max-w-2xl mx-auto py-4">
    <!-- Success Badge & Heading -->
    <div class="text-center mb-6">
        <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-3 shadow-inner">
            <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Alhamdulillah, Donasi Berhasil Diterima!</h1>
        <p class="text-sm text-slate-500 mt-1">Kwitansi resmi telah diterbitkan dan tersimpan dalam sistem</p>
    </div>

    <!-- Kwitansi Summary Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 relative overflow-hidden mb-6">
        <!-- Top Green Accent Ribbon -->
        <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-emerald-500 via-teal-500 to-emerald-600"></div>

        <div class="flex items-center justify-between pb-5 border-b border-slate-100">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Nomor Kwitansi</span>
                <span class="text-base sm:text-lg font-mono font-bold text-emerald-700">{{ $donasi->no_kwitansi }}</span>
            </div>
            <div class="text-right">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Tanggal & Waktu</span>
                @php
                    $waktuDonasi = \Carbon\Carbon::parse($donasi->tgl_donasi)->timezone('Asia/Jakarta');
                    $jamFormatted = ($waktuDonasi->format('H:i') === '00:00' && $donasi->created_at)
                        ? \Carbon\Carbon::parse($donasi->created_at)->timezone('Asia/Jakarta')->translatedFormat('H:i')
                        : $waktuDonasi->translatedFormat('H:i');
                @endphp
                <span class="text-sm font-semibold text-slate-700">{{ $waktuDonasi->translatedFormat('d F Y') }}, {{ $jamFormatted }} WIB</span>
            </div>
        </div>

        <!-- Nominal Highlight -->
        <div class="py-6 text-center border-b border-slate-100">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">Total Nominal Donasi</span>
            <div class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                Rp {{ number_format($donasi->jumlah, 0, ',', '.') }}
            </div>
            <p class="text-xs text-slate-500 italic mt-2">
                "{{ \App\Services\DonasiService::terbilang($donasi->jumlah) }} rupiah"
            </p>
        </div>

        <!-- Details Grid -->
        <div class="py-5 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs border-b border-slate-100">
            <div>
                <span class="text-slate-400 block mb-0.5">Nama Donatur</span>
                <span class="font-bold text-slate-800 text-sm">
                    {{ $donasi->donatur?->is_anonim ? 'Hamba Allah (Anonim)' : ($donasi->donatur?->nama ?? 'Donatur Umum') }}
                </span>
                @if ($donasi->atas_nama)
                    <span class="block text-slate-500 text-[11px] mt-0.5">a.n: {{ $donasi->atas_nama }}</span>
                @endif
            </div>

            <div>
                <span class="text-slate-400 block mb-0.5">Nomor Kontak / WhatsApp</span>
                <span class="font-semibold text-slate-700">{{ $donasi->donatur?->no_hp ?? '-' }}</span>
            </div>

            <div>
                <span class="text-slate-400 block mb-0.5">Jenis Dana & Program</span>
                <span class="font-bold text-emerald-700">{{ $donasi->jenisDana?->nama ?? 'Donasi' }}</span>
                <span class="block text-slate-500">{{ $donasi->program?->nama ?? 'Penerimaan Bebas' }}</span>
            </div>

            <div>
                <span class="text-slate-400 block mb-0.5">Metode Pembayaran</span>
                <span class="font-semibold text-slate-800 uppercase inline-flex items-center gap-1.5">
                    @if ($donasi->metode_bayar === 'tunai')
                        💵 Tunai
                    @elseif ($donasi->metode_bayar === 'transfer')
                        🏦 Transfer ({{ $donasi->bank ?? 'Bank' }})
                    @elseif ($donasi->metode_bayar === 'qris')
                        📱 QRIS
                    @else
                        💳 {{ $donasi->metode_bayar }}
                    @endif
                </span>
            </div>

            <div>
                <span class="text-slate-400 block mb-0.5">Amil Penghimpun</span>
                <span class="font-semibold text-slate-700">{{ $donasi->amil?->nama ?? 'Amil Resmi' }} ({{ $donasi->amil?->nip ?? '-' }})</span>
            </div>

            <div>
                <span class="text-slate-400 block mb-0.5">Status Verifikasi</span>
                @if ($donasi->status === 'terverifikasi')
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                        Terverifikasi Bendahara
                    </span>
                @elseif ($donasi->status === 'ditolak')
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                        Ditolak
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600 animate-pulse"></span>
                        Menunggu Verifikasi Admin
                    </span>
                @endif
            </div>
        </div>

        @if ($donasi->niat)
            <!-- Doa / Niat Box -->
            <div class="mt-4 p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                <span class="text-slate-400 block text-[11px] font-semibold mb-1">Doa / Niat Khusus:</span>
                <p class="text-slate-700 italic">"{{ $donasi->niat }}"</p>
            </div>
        @endif
    </div>

    @php
        // Siapkan Link WhatsApp
        $rawPhone = $donasi->donatur?->no_hp ?? '';
        $cleanPhone = preg_replace('/[^0-9]/', '', $rawPhone);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        }

        $namaPenerima = $donasi->donatur?->is_anonim ? 'Bapak/Ibu' : ($donasi->donatur?->nama ?? 'Bapak/Ibu');
        $verifyUrl = route('kwitansi.verify', $donasi->no_kwitansi);

        $waText = "Assalamu'alaikum Warahmatullahi Wabarakatuh,\n\n"
            . "Yth. *{$namaPenerima}*,\n"
            . "Terima kasih atas kepercayaan Anda menyalurkan donasi melalui *Gerakan Sedekah Cepat (GSC)*.\n\n"
            . "Berikut adalah rincian kwitansi elektronik Anda:\n"
            . "📄 *No. Kwitansi:* {$donasi->no_kwitansi}\n"
            . "📅 *Waktu Transaksi:* " . $waktuDonasi->translatedFormat('d F Y, H:i') . " WIB\n"
            . "🤲 *Jenis Dana:* " . ($donasi->jenisDana?->nama ?? 'Donasi') . "\n"
            . "💰 *Nominal:* Rp " . number_format($donasi->jumlah, 0, ',', '.') . "\n"
            . "👤 *Amil Penghimpun:* " . ($donasi->amil?->nama ?? 'Amil GSC') . "\n\n"
            . "🔗 *Cek & Verifikasi Keaslian Kwitansi Resmi:*\n"
            . "{$verifyUrl}\n\n"
            . "Semoga Allah SWT membalas dengan pahala berlipat ganda, membersihkan harta, dan melimpahkan keberkahan bagi Anda dan keluarga. Aamiin ya Rabbal 'Alamin.\n\n"
            . "_Salam hangat,_\n*Gerakan Sedekah Cepat*";

        $waLink = "https://wa.me/{$cleanPhone}?text=" . urlencode($waText);
    @endphp

    <!-- ACTION BUTTONS -->
    <div class="space-y-3">
        @if ($cleanPhone)
            <!-- Kirim WhatsApp Button -->
            <a href="{{ $waLink }}" target="_blank" class="w-full py-3.5 px-4 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-sm shadow-lg shadow-emerald-600/25 transition flex items-center justify-center gap-2.5">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                    <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.145.39-.086.159.058 1.011.477 1.184.564.173.087.289.13.332.203.043.072.043.419-.101.824z"/>
                </svg>
                <span>Kirim Bukti Kwitansi via WhatsApp</span>
            </a>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <!-- Download PDF Button -->
            <a href="{{ route('kwitansi.download', $donasi->id) }}" class="py-3 px-4 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-sm transition flex items-center justify-center gap-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                <span>Download Kwitansi (PDF)</span>
            </a>

            <!-- Lihat / Print PDF Stream -->
            <a href="{{ route('kwitansi.stream', $donasi->id) }}" target="_blank" class="py-3 px-4 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-sm transition flex items-center justify-center gap-2">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <span>Lihat / Cetak Langsung</span>
            </a>
        </div>

        <div class="pt-3 flex items-center justify-between">
            <a href="{{ route('amil.dashboard') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
                ← Kembali ke Dashboard
            </a>

            <a href="{{ route('amil.donasi.create') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 transition">
                + Input Donasi Baru Lagi
            </a>
        </div>
    </div>
</div>
@endsection
