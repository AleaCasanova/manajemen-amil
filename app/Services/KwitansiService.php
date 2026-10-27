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

        $logoKop = null;
        if (file_exists(public_path('image/logo_kop.png'))) {
            $logoKop = 'data:image/png;base64,'.base64_encode(file_get_contents(public_path('image/logo_kop.png')));
        } elseif (file_exists(public_path('logo_gsc.png'))) {
            $logoKop = 'data:image/png;base64,'.base64_encode(file_get_contents(public_path('logo_gsc.png')));
        }

        $capGsc = null;
        if (file_exists(public_path('image/cap_gsc.jpeg'))) {
            $capGsc = 'data:image/jpeg;base64,'.base64_encode(file_get_contents(public_path('image/cap_gsc.jpeg')));
        }

        $ttdBendahara = null;
        if (file_exists(public_path('image/ttd_bendahara.jpeg'))) {
            $ttdBendahara = 'data:image/jpeg;base64,'.base64_encode(file_get_contents(public_path('image/ttd_bendahara.jpeg')));
        }

        $data = [
            'donasi' => $donasi,
            'qrCode' => $qrCodeData,
            'terbilang' => $terbilang,
            'logoKop' => $logoKop,
            'capGsc' => $capGsc,
            'ttdBendahara' => $ttdBendahara,
            'app_name' => Setting::get('app_name', 'Sistem Manajemen Amil GSC'),
            'institution_name' => Setting::get('app_institution', 'GERAK SEDEKAH CILACAP (GSC)'),
            'address' => Setting::get('app_address', 'Jalan Sulawesi, Perum Puri Tanjung Intan No.B2, Karang Lor, Gunungsimping, Cilacap Tengah 53224'),
            'phone' => Setting::get('app_phone', '0852-9000-xxxx'),
            'email' => Setting::get('app_email', 'info@geraksedekahcilacap.org'),
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
