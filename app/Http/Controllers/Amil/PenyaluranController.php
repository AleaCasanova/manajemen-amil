<?php

namespace App\Http\Controllers\Amil;

use App\Http\Controllers\Controller;
use App\Models\JenisDana;
use App\Models\Mustahik;
use App\Models\Penyaluran;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenyaluranController extends Controller
{
    /**
     * Tampilkan riwayat penyaluran bantuan amil
     */
    public function index(Request $request)
    {
        $amil = Auth::user()->amil;

        if ($amil && ! $amil->canAccessPenyaluran()) {
            return redirect()->route('amil.dashboard')->with('error', 'Fitur penyaluran dana hanya dapat diakses oleh petugas staff/ketua.');
        }

        $query = Penyaluran::where('amil_id', $amil->id)
            ->with(['mustahik', 'program', 'jenisDana'])
            ->latest('tgl_salur');

        if ($request->filled('q')) {
            $search = $request->q;
            $query->whereHas('mustahik', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $penyalurans = $query->paginate(15)->withQueryString();

        return view('amil.penyaluran.index', compact('amil', 'penyalurans'));
    }

    /**
     * Form input penyaluran bantuan baru
     */
    public function create()
    {
        $amil = Auth::user()->amil;

        if ($amil && ! $amil->canAccessPenyaluran()) {
            return redirect()->route('amil.dashboard')->with('error', 'Fitur penyaluran dana hanya dapat diakses oleh petugas staff/ketua.');
        }

        $mustahiks = Mustahik::where('status_verifikasi', 'terverifikasi')
            ->orderBy('nama', 'asc')
            ->get();

        $programs = Program::where('status', 'aktif')->get();
        $jenisDanas = JenisDana::all();

        return view('amil.penyaluran.create', compact('amil', 'mustahiks', 'programs', 'jenisDanas'));
    }

    /**
     * Simpan data penyaluran bantuan
     */
    public function store(Request $request)
    {
        $amil = Auth::user()->amil;

        if ($amil && ! $amil->canAccessPenyaluran()) {
            return redirect()->route('amil.dashboard')->with('error', 'Fitur penyaluran dana hanya dapat diakses oleh petugas staff/ketua.');
        }

        $validated = $request->validate([
            'mustahik_id' => ['required', 'exists:mustahiks,id'],
            'program_id' => ['nullable', 'exists:programs,id'],
            'jenis_dana_id' => ['required', 'exists:jenis_danas,id'],
            'jumlah' => ['required', 'numeric', 'min:1000'],
            'tgl_salur' => ['required', 'date'],
            'bukti' => ['nullable', 'image', 'max:4096'],
            'catatan' => ['nullable', 'string'],
        ]);

        $buktiPath = null;
        if ($request->hasFile('bukti')) {
            $buktiPath = $request->file('bukti')->store('penyaluran/bukti', 'public');
        }

        Penyaluran::create([
            'mustahik_id' => $validated['mustahik_id'],
            'program_id' => $validated['program_id'] ?? null,
            'jenis_dana_id' => $validated['jenis_dana_id'],
            'jumlah' => $validated['jumlah'],
            'tgl_salur' => $validated['tgl_salur'],
            'bukti' => $buktiPath,
            'amil_id' => $amil->id,
            'catatan' => $validated['catatan'] ?? null,
        ]);

        return redirect()->route('amil.penyaluran.index')
            ->with('success', 'Alhamdulillah! Penyaluran bantuan berhasil dicatat dan didokumentasikan.');
    }
}
