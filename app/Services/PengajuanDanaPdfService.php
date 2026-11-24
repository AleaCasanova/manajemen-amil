<?php

namespace App\Services;

use App\Models\PengajuanDana;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;

class PengajuanDanaPdfService
{
    public function generatePdf(PengajuanDana $pengajuan)
    {
        $pengajuan->loadMissing(['amil.divisi', 'program', 'kategoriKeuangan', 'bendahara', 'manager', 'akunKeuangan']);

        $appName = Setting::where('key', 'app_name')->value('value') ?? config('app.name', 'LAZ GSC');
        $institution = Setting::where('key', 'app_institution')->value('value') ?? 'Gerak Sedekah Cilacap';
        $phone = Setting::where('key', 'app_phone')->value('value') ?? '-';
        $email = Setting::where('key', 'app_email')->value('value') ?? '-';
        $address = Setting::where('key', 'app_address')->value('value') ?? '-';

        $logoKop = null;
        if (file_exists(public_path('image/logo_kop.png'))) {
            $logoKop = 'data:image/png;base64,'.base64_encode(file_get_contents(public_path('image/logo_kop.png')));
        } elseif (file_exists(public_path('logo_gsc.png'))) {
            $logoKop = 'data:image/png;base64,'.base64_encode(file_get_contents(public_path('logo_gsc.png')));
        }

        $terbilang = DonasiService::terbilang((float) $pengajuan->nominal_final);

        $pdf = Pdf::loadView('pengajuan_dana.pdf', compact(
            'pengajuan',
            'appName',
            'institution',
            'phone',
            'email',
            'address',
            'logoKop',
            'terbilang'
        ));

        $pdf->setPaper('a4', 'portrait');

        return $pdf;
    }

    public function download(PengajuanDana $pengajuan)
    {
        $pdf = $this->generatePdf($pengajuan);
        $filename = 'Pengajuan-Dana-'.str_replace(['/', '\\'], '-', $pengajuan->nomor_pengajuan).'.pdf';

        return $pdf->download($filename);
    }

    public function stream(PengajuanDana $pengajuan)
    {
        $pdf = $this->generatePdf($pengajuan);
        $filename = 'Pengajuan-Dana-'.str_replace(['/', '\\'], '-', $pengajuan->nomor_pengajuan).'.pdf';

        return $pdf->stream($filename);
    }
}
