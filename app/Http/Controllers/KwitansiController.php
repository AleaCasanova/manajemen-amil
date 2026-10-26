<?php

namespace App\Http\Controllers;

use App\Models\Donasi;
use App\Services\KwitansiService;

class KwitansiController extends Controller
{
    /**
     * Tampilkan halaman verifikasi keaslian kwitansi saat QR code di-scan
     */
    public function verify(string $no_kwitansi)
    {
        // Decode jika encoded slash
        $decodedNo = str_replace('-', '/', $no_kwitansi);

        $donasi = Donasi::where('no_kwitansi', $no_kwitansi)
            ->orWhere('no_kwitansi', $decodedNo)
            ->with(['donatur', 'amil', 'jenisDana', 'program', 'verifiedByUser'])
            ->first();

        return view('kwitansi.verify', compact('donasi', 'no_kwitansi'));
    }

    /**
     * Download / Stream file PDF Kwitansi
     */
    public function download(Donasi $donasi, KwitansiService $kwitansiService)
    {
        return $kwitansiService->download($donasi);
    }

    /**
     * Stream PDF Kwitansi untuk preview di browser
     */
    public function stream(Donasi $donasi, KwitansiService $kwitansiService)
    {
        return $kwitansiService->stream($donasi);
    }
}
