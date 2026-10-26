<?php

namespace App\Http\Controllers\Amil;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Agenda;
use App\Models\Donasi;
use App\Models\Donatur;
use App\Models\Pengumuman;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $amil = $user->amil;

        if (! $amil) {
            abort(403, 'Profil data Amil tidak ditemukan untuk akun ini.');
        }

        // 1. Status Absensi Hari Ini
        $absensiHariIni = Absensi::where('amil_id', $amil->id)
            ->whereDate('tanggal', today())
            ->first();

        // 2. Metrik Donasi Bulan Ini
        $donasiBulanIni = Donasi::where('amil_id', $amil->id)
            ->whereYear('tgl_donasi', now()->year)
            ->whereMonth('tgl_donasi', now()->month)
            ->where('status', 'terverifikasi')
            ->sum('jumlah');

        $donasiMenungguVerifikasi = Donasi::where('amil_id', $amil->id)
            ->where('status', 'menunggu_verifikasi')
            ->sum('jumlah');

        // 3. Progres Target Bulanan
        $targetBulanan = (float) $amil->target_bulanan;
        $progresPersen = $targetBulanan > 0 ? min(round(($donasiBulanIni / $targetBulanan) * 100, 1), 100) : 0;

        // 4. Total Donatur Binaan Saya
        $totalDonaturSaya = Donatur::where('amil_pic_id', $amil->id)->count();

        // 5. Transaksi Donasi Terakhir Amil Ini
        $donasiTerakhir = Donasi::where('amil_id', $amil->id)
            ->with(['donatur', 'jenisDana', 'program'])
            ->latest('tgl_donasi')
            ->take(5)
            ->get();

        // 6. Pengumuman Terbaru
        $pengumumanTerbaru = Pengumuman::latest('published_at')
            ->where(function ($q) use ($amil) {
                $q->where('target', 'semua')
                    ->orWhere('target', $amil->divisi?->nama ?? '');
            })
            ->take(3)
            ->get();

        // 7. Agenda Amil Terjadwal
        $agendaHariIni = Agenda::whereHas('amils', function ($q) use ($amil) {
            $q->where('amils.id', $amil->id);
        })
            ->whereDate('mulai', '>=', today())
            ->orderBy('mulai', 'asc')
            ->take(3)
            ->get();

        return view('amil.dashboard', compact(
            'amil',
            'absensiHariIni',
            'donasiBulanIni',
            'donasiMenungguVerifikasi',
            'targetBulanan',
            'progresPersen',
            'totalDonaturSaya',
            'donasiTerakhir',
            'pengumumanTerbaru',
            'agendaHariIni'
        ));
    }
}
