<?php

namespace App\Http\Controllers\Amil;

use App\Http\Controllers\Controller;
use App\Models\Donatur;
use App\Models\DonaturKategori;
use App\Services\DonasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DonaturController extends Controller
{
    public function __construct(
        protected DonasiService $donasiService
    ) {}

    /**
     * Tampilkan data donatur binaan amil
     */
    public function index(Request $request)
    {
        $amil = Auth::user()->amil;

        $query = Donatur::where('amil_pic_id', $amil->id)
            ->orWhere('created_by', Auth::id())
            ->with(['kategori'])
            ->withCount('donasis')
            ->withSum('donasis', 'jumlah')
            ->latest();

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('no_hp', 'like', "%{$search}%")
                    ->orWhere('kode_donatur', 'like', "%{$search}%")
                    ->orWhere('kota', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        $donaturs = $query->paginate(15)->withQueryString();
        $kategoris = DonaturKategori::all();

        return view('amil.donatur.index', compact('donaturs', 'kategoris', 'amil'));
    }

    /**
     * Form tambah donatur baru
     */
    public function create()
    {
        $amil = Auth::user()->amil;
        $kategoris = DonaturKategori::all();

        return view('amil.donatur.create', compact('amil', 'kategoris'));
    }

    /**
     * Simpan donatur baru
     */
    public function store(Request $request)
    {
        $amil = Auth::user()->amil;

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'jenis' => ['required', 'in:individu,lembaga'],
            'kategori_id' => ['required', 'exists:donatur_kategoris,id'],
            'no_hp' => ['required', 'string', 'max:25'],
            'email' => ['nullable', 'email', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'kota' => ['nullable', 'string', 'max:100'],
            'provinsi' => ['nullable', 'string', 'max:100'],
            'kode_pos' => ['nullable', 'string', 'max:10'],
            'nik' => ['nullable', 'string', 'max:25'],
            'npwp' => ['nullable', 'string', 'max:30'],
            'is_anonim' => ['nullable', 'boolean'],
            'consent' => ['nullable', 'boolean'],
        ]);

        // Cek duplikasi nomor HP
        $existing = $this->donasiService->checkDuplicatePhone($validated['no_hp']);

        if ($existing) {
            return back()->withErrors([
                'no_hp' => "Nomor HP ini sudah terdaftar atas nama donatur {$existing->nama} ({$existing->kode_donatur}).",
            ])->withInput();
        }

        $donatur = Donatur::create([
            'kode_donatur' => 'DNT-'.date('Ym').rand(1000, 9999),
            'nama' => $validated['nama'],
            'jenis' => $validated['jenis'],
            'kategori_id' => $validated['kategori_id'],
            'no_hp' => $validated['no_hp'],
            'email' => $validated['email'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
            'kota' => $validated['kota'] ?? null,
            'provinsi' => $validated['provinsi'] ?? null,
            'kode_pos' => $validated['kode_pos'] ?? null,
            'nik' => $validated['nik'] ?? null,
            'npwp' => $validated['npwp'] ?? null,
            'is_anonim' => $request->boolean('is_anonim'),
            'consent' => $request->has('consent') ? $request->boolean('consent') : true,
            'amil_pic_id' => $amil->id,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('amil.donatur.index')
            ->with('success', "Donatur baru {$donatur->nama} berhasil didaftarkan!");
    }

    /**
     * Detail riwayat donatur
     */
    public function show(Donatur $donatur)
    {
        $amil = Auth::user()->amil;

        // Proteksi Keamanan: Amil hanya bisa mengakses data donatur binaannya
        if ($donatur->amil_pic_id !== $amil->id && $donatur->created_by !== Auth::id() && ! Auth::user()->hasRole('Admin')) {
            abort(403, 'Akses Ditolak: Anda hanya diperkenankan mengakses data donatur binaan Anda sendiri.');
        }

        $donatur->load(['kategori', 'amilPic']);

        $donasis = $donatur->donasis()
            ->with(['jenisDana', 'program', 'amil'])
            ->latest('tgl_donasi')
            ->paginate(10);

        return view('amil.donatur.show', compact('donatur', 'donasis', 'amil'));
    }
}
