<?php

namespace App\Http\Controllers\Amil;

use App\Http\Controllers\Controller;
use App\Models\AktivitasHarian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AktivitasController extends Controller
{
    /**
     * Tampilkan riwayat laporan aktivitas amil
     */
    public function index(Request $request)
    {
        $amil = Auth::user()->amil;

        if ($amil && $amil->isRelawan()) {
            return redirect()->route('amil.dashboard')->with('info', 'Petugas relawan tidak diwajibkan untuk membuat laporan aktivitas harian.');
        }

        $query = AktivitasHarian::where('amil_id', $amil->id)
            ->latest('tanggal');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%")
                    ->orWhere('lokasi', 'like', "%{$search}%");
            });
        }

        $aktivitas = $query->paginate(15)->withQueryString();

        return view('amil.aktivitas.index', compact('amil', 'aktivitas'));
    }

    /**
     * Form buat laporan aktivitas baru
     */
    public function create()
    {
        $amil = Auth::user()->amil;

        if ($amil && $amil->isRelawan()) {
            return redirect()->route('amil.dashboard')->with('info', 'Petugas relawan tidak diwajibkan untuk membuat laporan aktivitas harian.');
        }

        return view('amil.aktivitas.create', compact('amil'));
    }

    /**
     * Simpan laporan aktivitas amil
     */
    public function store(Request $request)
    {
        $amil = Auth::user()->amil;

        if ($amil && $amil->isRelawan()) {
            return redirect()->route('amil.dashboard')->with('info', 'Petugas relawan tidak diwajibkan untuk membuat laporan aktivitas harian.');
        }

        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'foto' => ['nullable', 'image', 'max:4096'],
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('aktivitas/foto', 'public');
        }

        AktivitasHarian::create([
            'amil_id' => $amil->id,
            'tanggal' => $validated['tanggal'],
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'],
            'lokasi' => $validated['lokasi'] ?? null,
            'lat' => $validated['lat'] ?? null,
            'lng' => $validated['lng'] ?? null,
            'foto' => $fotoPath,
            'status' => 'diajukan',
        ]);

        return redirect()->route('amil.aktivitas.index')
            ->with('success', 'Laporan aktivitas harian berhasil dikirim ke Administrator!');
    }
}
