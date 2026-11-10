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

        if ($amil && ! $amil->canAccessDonasi()) {
            return redirect()->route('amil.dashboard')->with('error', 'Petugas magang tidak memiliki akses ke database donatur.');
        }

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

        if ($amil && ! $amil->canAccessDonasi()) {
            return redirect()->route('amil.dashboard')->with('error', 'Petugas magang tidak memiliki akses ke database donatur.');
        }

        $kategoris = DonaturKategori::all();

        return view('amil.donatur.create', compact('amil', 'kategoris'));
    }

    /**
     * Simpan donatur baru
     */
    public function store(Request $request)
    {
        $amil = Auth::user()->amil;

        if ($amil && ! $amil->canAccessDonasi()) {
            return redirect()->route('amil.dashboard')->with('error', 'Petugas magang tidak memiliki akses ke pendaftaran donatur.');
        }

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'jenis' => ['required', 'in:individu,lembaga'],
            'kategori_id' => ['required', 'exists:donatur_kategoris,id'],
            'no_hp' => ['required', 'string', 'max:25'],
            'email' => ['nullable', 'email', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'kecamatan' => ['required', 'string', 'max:100'],
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
            'kecamatan' => $validated['kecamatan'] ?? null,
            'kota' => $validated['kota'] ?? (! empty($validated['kecamatan']) && $validated['kecamatan'] !== 'Luar Cilacap' ? 'Cilacap' : null),
            'provinsi' => $validated['provinsi'] ?? (! empty($validated['kecamatan']) && $validated['kecamatan'] !== 'Luar Cilacap' ? 'Jawa Tengah' : null),
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

        if ($amil && ! $amil->canAccessDonasi()) {
            return redirect()->route('amil.dashboard')->with('error', 'Petugas magang tidak memiliki akses ke database donatur.');
        }

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

    /**
     * Perbarui data domisili / kecamatan donatur binaan
     */
    public function update(Request $request, Donatur $donatur)
    {
        $amil = Auth::user()->amil;

        if ($amil && ! $amil->canAccessDonasi()) {
            return redirect()->route('amil.dashboard')->with('error', 'Petugas magang tidak memiliki akses ke database donatur.');
        }

        if ($donatur->amil_pic_id !== $amil->id && $donatur->created_by !== Auth::id() && ! Auth::user()->hasRole('Admin')) {
            abort(403, 'Akses Ditolak: Anda hanya diperkenankan mengubah data donatur binaan Anda sendiri.');
        }

        $validated = $request->validate([
            'kecamatan' => ['required', 'string', 'max:100'],
            'kota' => ['nullable', 'string', 'max:100'],
            'alamat' => ['nullable', 'string'],
        ]);

        $kecamatan = trim($validated['kecamatan']);
        $kota = ! empty($validated['kota'])
            ? trim($validated['kota'])
            : ($kecamatan !== 'Luar Cilacap' ? 'Cilacap' : ($donatur->kota ?? null));

        $donatur->update([
            'kecamatan' => $kecamatan,
            'kota' => $kota,
            'provinsi' => $kecamatan !== 'Luar Cilacap' ? ($donatur->provinsi ?: 'Jawa Tengah') : $donatur->provinsi,
            'alamat' => $validated['alamat'] ?? $donatur->alamat,
        ]);

        return redirect()->route('amil.donatur.show', $donatur->id)
            ->with('success', "Data domisili & kecamatan donatur {$donatur->nama} berhasil diperbarui!");
    }
}
