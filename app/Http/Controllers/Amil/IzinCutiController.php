<?php

namespace App\Http\Controllers\Amil;

use App\Http\Controllers\Controller;
use App\Models\IzinCuti;
use App\Models\SaldoCuti;
use App\Models\User;
use Carbon\Carbon;
use Filament\Notifications\Actions\Action as FilamentAction;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IzinCutiController extends Controller
{
    /**
     * Tampilkan riwayat pengajuan izin/cuti dan saldo cuti amil
     */
    public function index()
    {
        $amil = Auth::user()->amil;
        $tahunSekarang = Carbon::now()->year;

        // Dapatkan atau inisialisasi saldo cuti amil tahun ini
        $saldoCuti = SaldoCuti::firstOrCreate(
            ['amil_id' => $amil->id, 'tahun' => $tahunSekarang],
            ['jatah' => 12, 'terpakai' => 0]
        );

        $sisaCuti = max(0, $saldoCuti->jatah - $saldoCuti->terpakai);

        $pengajuans = IzinCuti::where('amil_id', $amil->id)
            ->latest()
            ->paginate(15);

        return view('amil.izin-cuti.index', compact('amil', 'saldoCuti', 'sisaCuti', 'pengajuans'));
    }

    /**
     * Form pengajuan izin/cuti baru
     */
    public function create()
    {
        $amil = Auth::user()->amil;
        $tahunSekarang = Carbon::now()->year;

        $saldoCuti = SaldoCuti::firstOrCreate(
            ['amil_id' => $amil->id, 'tahun' => $tahunSekarang],
            ['jatah' => 12, 'terpakai' => 0]
        );

        $sisaCuti = max(0, $saldoCuti->jatah - $saldoCuti->terpakai);

        return view('amil.izin-cuti.create', compact('amil', 'saldoCuti', 'sisaCuti'));
    }

    /**
     * Simpan pengajuan izin/cuti
     */
    public function store(Request $request)
    {
        $amil = Auth::user()->amil;
        $tahunSekarang = Carbon::now()->year;

        $validated = $request->validate([
            'jenis' => ['required', 'in:cuti,izin,sakit'],
            'tgl_mulai' => ['required', 'date'],
            'tgl_selesai' => ['required', 'date', 'after_or_equal:tgl_mulai'],
            'alasan' => ['required', 'string', 'min:5'],
            'lampiran' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:3072'],
        ]);

        $mulai = Carbon::parse($validated['tgl_mulai']);
        $selesai = Carbon::parse($validated['tgl_selesai']);
        $durasiHari = $mulai->diffInDays($selesai) + 1;

        // Jika jenis cuti, periksa sisa saldo cuti
        if ($validated['jenis'] === 'cuti') {
            $saldoCuti = SaldoCuti::firstOrCreate(
                ['amil_id' => $amil->id, 'tahun' => $tahunSekarang],
                ['jatah' => 12, 'terpakai' => 0]
            );

            $sisa = $saldoCuti->jatah - $saldoCuti->terpakai;

            if ($durasiHari > $sisa) {
                return back()->withErrors([
                    'tgl_selesai' => "Saldo cuti Anda tersisa {$sisa} hari, sedangkan pengajuan cuti ini membutuhkan {$durasiHari} hari kerja.",
                ])->withInput();
            }
        }

        $lampiranPath = null;
        if ($request->hasFile('lampiran')) {
            $lampiranPath = $request->file('lampiran')->store('izin-cuti/lampiran', 'public');
        }

        $pengajuan = IzinCuti::create([
            'amil_id' => $amil->id,
            'jenis' => $validated['jenis'],
            'tgl_mulai' => $validated['tgl_mulai'],
            'tgl_selesai' => $validated['tgl_selesai'],
            'alasan' => $validated['alasan'],
            'lampiran' => $lampiranPath,
            'status' => 'diajukan',
        ]);

        // Kirim Notifikasi ke Admin via Filament Database Notification
        try {
            $admins = User::role('admin')->get();
            if ($admins->isNotEmpty()) {
                FilamentNotification::make()
                    ->title('Pengajuan '.ucfirst($validated['jenis']).' Baru')
                    ->body("Amil {$amil->nama} mengajukan ".ucfirst($validated['jenis'])." ({$validated['tgl_mulai']} s/d {$validated['tgl_selesai']}).")
                    ->icon('heroicon-o-calendar')
                    ->actions([
                        FilamentAction::make('view')
                            ->label('Lihat Detail')
                            ->button()
                            ->url(route('filament.admin.resources.izin-cutis.index')),
                    ])
                    ->sendToDatabase($admins);
            }
        } catch (\Throwable $e) {
            // Background notification silently handled if any context error occurs
        }

        return redirect()->route('amil.izin-cuti.index')
            ->with('success', 'Pengajuan '.strtoupper($validated['jenis']).' berhasil dikirim ke Administrator.');
    }
}
