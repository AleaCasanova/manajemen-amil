<?php

namespace App\Services;

use App\Models\Donasi;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class KwitansiService
{
    /**
     * Generate objek PDF Kwitansi Donasi
     */
    public function generatePdf(Donasi $donasi)
    {
        $donasi->loadMissing(['donatur', 'amil', 'jenisDana', 'program', 'verifiedByUser']);

        // Generate QR Code untuk verifikasi kwitansi
        $verifyUrl = route('kwitansi.verify', ['no_kwitansi' => $donasi->no_kwitansi]);

        $options = new QROptions;
        $options->outputBase64 = true;
        $options->scale = 4;

        $qrCodeData = (new QRCode($options))->render($verifyUrl);

        $terbilang = DonasiService::terbilang($donasi->jumlah);

        $data = [
            'donasi' => $donasi,
            'qrCode' => $qrCodeData,
            'terbilang' => $terbilang,
            'app_name' => Setting::get('app_name', 'Sistem Manajemen Amil GSC'),
            'institution_name' => Setting::get('app_institution', 'Yayasan Gerak Sedekah Cilacap (GSC)'),
            'address' => Setting::get('app_address', 'Jl. Gerak Sedekah Cilacap No. 01, Cilacap, Jawa Tengah'),
            'phone' => Setting::get('app_phone', '081234567890'),
            'email' => Setting::get('app_email', 'info@amilgsc.com'),
        ];

        return Pdf::loadView('pdf.kwitansi', $data)
            ->setPaper('a5', 'landscape');
    }

    /**
     * Download file PDF Kwitansi
     */
    public function download(Donasi $donasi)
    {
        $pdf = $this->generatePdf($donasi);
        $filename = 'Kwitansi-'.str_replace('/', '-', $donasi->no_kwitansi).'.pdf';

        return $pdf->download($filename);
    }

    /**
     * Stream / preview file PDF Kwitansi di tab browser
     */
    public function stream(Donasi $donasi)
    {
        $pdf = $this->generatePdf($donasi);
        $filename = 'Kwitansi-'.str_replace('/', '-', $donasi->no_kwitansi).'.pdf';

        return $pdf->stream($filename);
    }
}
