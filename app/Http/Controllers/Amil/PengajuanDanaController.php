<?php

namespace App\Http\Controllers\Amil;

use App\Http\Controllers\Controller;
use App\Models\KategoriKeuangan;
use App\Models\PengajuanDana;
use App\Models\Program;
use App\Services\PengajuanDanaPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengajuanDanaController extends Controller
{
    /**
     * Tampilkan riwayat pengajuan dana amil yang login
     */
    public function index(Request $request)
    {
        $amil = Auth::user()->amil;

        if (! $amil) {
            return redirect()->route('amil.dashboard')->with('error', 'Akun amil tidak ditemukan.');
        }

        $query = PengajuanDana::where('amil_id', $amil->id)
            ->with(['program', 'kategoriKeuangan', 'transaksiKeuangan'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_pengajuan', 'like', "%{$search}%")
                    ->orWhere('judul', 'like', "%{$search}%")
                    ->orWhere('nomor_dokumen_referensi', 'like', "%{$search}%");
            });
        }

        $pengajuans = $query->paginate(10)->withQueryString();

        // Kartu Statistik Pengajuan Amil
        $allPengajuans = PengajuanDana::where('amil_id', $amil->id)->get();
        $totalNominalDiajukan = $allPengajuans->sum('nominal_diajukan');
        $countMenunggu = $allPengajuans->whereIn('status', ['diajukan', 'diverifikasi_bendahara'])->count();
        $countDisetujui = $allPengajuans->where('status', 'disetujui')->count();
        $countDicairkan = $allPengajuans->where('status', 'dicairkan')->count();

        return view('amil.pengajuan_dana.index', compact(
            'amil',
            'pengajuans',
            'totalNominalDiajukan',
            'countMenunggu',
            'countDisetujui',
            'countDicairkan'
        ));
    }

    /**
     * Form pengajuan dana baru
     */
    public function create()
    {
        $amil = Auth::user()->amil;

        if (! $amil) {
            return redirect()->route('amil.dashboard')->with('error', 'Akun amil tidak ditemukan.');
        }

        $programs = Program::where('status', 'aktif')->orderBy('nama')->get();
        $kategoriKeuangans = KategoriKeuangan::active()
            ->postable()
            ->untukArahKas('keluar')
            ->orderBy('kode_coa')
            ->orderBy('nama')
            ->get();

        return view('amil.pengajuan_dana.create', compact('amil', 'programs', 'kategoriKeuangans'));
    }

    /**
     * Simpan pengajuan dana baru
     */
    public function store(Request $request)
    {
        $amil = Auth::user()->amil;

        if (! $amil) {
            return redirect()->route('amil.dashboard')->with('error', 'Akun amil tidak ditemukan.');
        }

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:200'],
            'kategori_keuangan_id' => ['nullable', 'exists:kategori_keuangans,id'],
            'program_id' => ['nullable', 'exists:programs,id'],
            'nominal_diajukan' => ['required', 'numeric', 'min:1000'],
            'tgl_kebutuhan' => ['required', 'date'],
            'nomor_dokumen_referensi' => ['nullable', 'string', 'max:100'],
            'dokumen_pendukung' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,docx,xlsx', 'max:5120'],
            'deskripsi' => ['required', 'string'],
        ], [
            'judul.required' => 'Keperluan / judul pengajuan wajib diisi.',
            'nominal_diajukan.required' => 'Nominal pengajuan dana wajib diisi.',
            'nominal_diajukan.min' => 'Nominal minimal pengajuan adalah Rp 1.000.',
            'tgl_kebutuhan.required' => 'Tanggal kebutuhan dana wajib diisi.',
            'deskripsi.required' => 'Uraian rincian anggaran / RAB wajib diisi.',
            'dokumen_pendukung.max' => 'Ukuran dokumen pendukung maksimal 5 MB.',
        ]);

        $dokumenPath = null;
        if ($request->hasFile('dokumen_pendukung')) {
            $dokumenPath = $request->file('dokumen_pendukung')->store('pengajuan-dana/dokumen', 'public');
        }

        $pengajuan = PengajuanDana::create([
            'amil_id' => $amil->id,
            'divisi_id' => $amil->divisi_id,
            'program_id' => $validated['program_id'] ?? null,
            'kategori_keuangan_id' => $validated['kategori_keuangan_id'] ?? null,
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'],
            'nominal_diajukan' => $validated['nominal_diajukan'],
            'tgl_kebutuhan' => $validated['tgl_kebutuhan'],
            'nomor_dokumen_referensi' => $validated['nomor_dokumen_referensi'] ?? null,
            'dokumen_pendukung' => $dokumenPath,
            'status' => 'diajukan',
        ]);

        return redirect()->route('amil.pengajuan-dana.show', $pengajuan->id)
            ->with('success', "Pengajuan dana resmi berhasil dikirim dengan Nomor Registrasi: {$pengajuan->nomor_pengajuan}. Menunggu verifikasi Bendahara.");
    }

    /**
     * Tampilkan detail dan tracking progress pengajuan dana
     */
    public function show(PengajuanDana $pengajuanDana)
    {
        $amil = Auth::user()->amil;

        if ($amil && $pengajuanDana->amil_id !== $amil->id && ! Auth::user()->isAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat pengajuan ini.');
        }

        $pengajuanDana->loadMissing(['amil.divisi', 'program', 'kategoriKeuangan', 'bendahara', 'manager', 'akunKeuangan']);

        return view('amil.pengajuan_dana.show', compact('amil', 'pengajuanDana'));
    }

    /**
     * Unduh / cetak formulir resmi pengajuan dana PDF
     */
    public function cetak(PengajuanDana $pengajuanDana, PengajuanDanaPdfService $pdfService)
    {
        $amil = Auth::user()->amil;

        if ($amil && $pengajuanDana->amil_id !== $amil->id && ! Auth::user()->isAdmin()) {
            abort(403, 'Akses ditolak.');
        }

        return $pdfService->stream($pengajuanDana);
    }
}
