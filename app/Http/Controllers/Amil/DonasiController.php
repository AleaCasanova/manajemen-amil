<?php

namespace App\Http\Controllers\Amil;

use App\Http\Controllers\Controller;
use App\Models\Donasi;
use App\Models\DonasiBarang;
use App\Models\Donatur;
use App\Models\DonaturKategori;
use App\Models\JenisDana;
use App\Models\Program;
use App\Services\DonasiService;
use App\Services\KwitansiService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DonasiController extends Controller
{
    public function __construct(
        protected DonasiService $donasiService,
        protected KwitansiService $kwitansiService
    ) {}

    /**
     * Riwayat donasi yang dihimpun oleh amil yang sedang login
     */
    public function index(Request $request)
    {
        $amil = Auth::user()->amil;

        if ($amil && ! $amil->canAccessDonasi()) {
            return redirect()->route('amil.dashboard')->with('error', 'Petugas magang tidak memiliki akses ke fitur donasi.');
        }

        $query = Donasi::where('amil_id', $amil->id)
            ->with(['donatur', 'jenisDana', 'program', 'donasiBarangs'])
            ->latest('tgl_donasi');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('no_kwitansi', 'like', "%{$search}%")
                    ->orWhereHas('donatur', function ($sub) use ($search) {
                        $sub->where('nama', 'like', "%{$search}%")
                            ->orWhere('no_hp', 'like', "%{$search}%");
                    });
            });
        }

        $donasis = $query->paginate(15)->withQueryString();

        return view('amil.donasi.index', compact('donasis', 'amil'));
    }

    /**
     * Tampilkan form input donatur & donasi baru
     */
    public function create(Request $request)
    {
        $amil = Auth::user()->amil;

        if ($amil && ! $amil->canAccessDonasi()) {
            return redirect()->route('amil.dashboard')->with('error', 'Petugas magang tidak memiliki akses ke fitur donasi.');
        }

        // Auto no kwitansi
        $noKwitansi = $this->donasiService->generateNoKwitansi();

        // Master data
        $kategoris = DonaturKategori::all();
        $jenisDanas = JenisDana::all();
        $programs = Program::where('status', 'aktif')->get();

        // Donatur untuk dropdown/selector: prioritaskan donatur binaan amil sendiri di atas, lalu donatur lembaga lainnya
        $donaturList = Donatur::orderByRaw('CASE WHEN amil_pic_id = ? OR created_by = ? THEN 0 ELSE 1 END', [$amil->id, Auth::id()])
            ->orderBy('nama', 'asc')
            ->limit(50)
            ->get();

        // Jika ada parameter donatur_id dari tombol "Donasi Lagi" atau old input
        $selectedDonatur = null;

        if ($request->filled('donatur_id')) {
            $selectedDonatur = Donatur::find($request->donatur_id);
        } elseif (old('donatur_id')) {
            $selectedDonatur = Donatur::find(old('donatur_id'));
        }

        // Map donatur untuk live search selector
        $initialDonaturs = $donaturList->map(function ($d) use ($amil) {
            return [
                'id' => $d->id,
                'nama' => $d->nama,
                'no_hp' => $d->no_hp,
                'kode_donatur' => $d->kode_donatur,
                'kota' => $d->kota,
                'alamat' => $d->alamat,
                'is_binaan_saya' => $amil && ($d->amil_pic_id == $amil->id || $d->created_by == Auth::id()),
            ];
        });

        return view('amil.donasi.create', compact(
            'amil',
            'noKwitansi',
            'kategoris',
            'jenisDanas',
            'programs',
            'donaturList',
            'initialDonaturs',
            'selectedDonatur'
        ));
    }

    /**
     * Simpan donasi dan donatur baru
     */
    public function store(Request $request)
    {
        $amil = Auth::user()->amil;

        if ($amil && ! $amil->canAccessDonasi()) {
            return redirect()->route('amil.dashboard')->with('error', 'Petugas magang tidak memiliki akses ke pencatatan donasi.');
        }

        $validated = $request->validate([
            // Mode donatur: existing atau new
            'donatur_mode' => ['required', 'in:existing,new'],
            'donatur_id' => ['required_if:donatur_mode,existing', 'nullable', 'exists:donaturs,id'],

            // Data donatur baru jika mode 'new'
            'nama_donatur' => ['required_if:donatur_mode,new', 'nullable', 'string', 'max:255'],
            'jenis_donatur' => ['required_if:donatur_mode,new', 'nullable', 'in:individu,lembaga'],
            'kategori_id' => ['required_if:donatur_mode,new', 'nullable', 'exists:donatur_kategoris,id'],
            'no_hp' => ['required_if:donatur_mode,new', 'nullable', 'string', 'max:25'],
            'email' => ['nullable', 'email', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'kota' => ['nullable', 'string', 'max:100'],
            'provinsi' => ['nullable', 'string', 'max:100'],
            'kode_pos' => ['nullable', 'string', 'max:10'],
            'nik' => ['nullable', 'string', 'max:25'],
            'npwp' => ['nullable', 'string', 'max:30'],
            'is_anonim' => ['nullable', 'boolean'],
            'consent' => ['nullable', 'boolean'],

            // Data Transaksi Donasi
            'jenis_dana_id' => ['required', 'exists:jenis_danas,id'],
            'program_id' => ['nullable', 'exists:programs,id'],
            'jumlah' => ['required', 'numeric', 'min:1000'],
            'metode_bayar' => ['required', 'in:tunai,transfer,qris,ewallet'],
            'bank' => ['required_if:metode_bayar,transfer', 'nullable', 'string', 'max:100'],
            'bukti_transfer' => ['nullable', 'image', 'max:5120'],
            'lampiran_foto' => ['nullable', 'image', 'max:5120'],
            'tgl_donasi' => ['required', 'date'],
            'niat' => ['nullable', 'string'],
            'atas_nama' => ['nullable', 'string', 'max:255'],
            'peruntukan' => ['nullable', 'string', 'max:255'],
            'pesan' => ['nullable', 'string'],
            'catatan' => ['nullable', 'string'],

            // Donasi barang (opsional)
            'ada_barang' => ['nullable', 'boolean'],
            'barang_deskripsi' => ['required_if:ada_barang,1', 'nullable', 'string'],
            'barang_estimasi' => ['nullable', 'numeric'],
        ]);

        return DB::transaction(function () use ($request, $amil, $validated) {
            // 1. Tentukan Donatur
            if ($validated['donatur_mode'] === 'existing') {
                $donatur = Donatur::findOrFail($validated['donatur_id']);
            } else {
                // Cek duplikasi nomor HP jika mode new
                $existingPhone = $this->donasiService->checkDuplicatePhone($validated['no_hp']);

                if ($existingPhone) {
                    $donatur = $existingPhone;
                } else {
                    $donatur = Donatur::create([
                        'kode_donatur' => 'DNT-'.date('Ym').rand(1000, 9999),
                        'nama' => $validated['nama_donatur'],
                        'jenis' => $validated['jenis_donatur'],
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
                }
            }

            // 2. Upload Bukti Transfer & Dokumentasi jika ada
            $buktiPath = null;
            if ($request->hasFile('bukti_transfer')) {
                $buktiPath = $request->file('bukti_transfer')->store('donasis/bukti', 'public');
            }

            $lampiranFotoPath = null;
            if ($request->hasFile('lampiran_foto')) {
                $lampiranFotoPath = $request->file('lampiran_foto')->store('donasis/dokumentasi', 'public');
            }

            // 3. Simpan Donasi
            $tglDonasi = Carbon::parse($validated['tgl_donasi']);
            if ($tglDonasi->isToday() && $tglDonasi->format('H:i:s') === '00:00:00') {
                $tglDonasi = $tglDonasi->setTime(now()->hour, now()->minute, now()->second);
            }

            $donasi = Donasi::create([
                'no_kwitansi' => $this->donasiService->generateNoKwitansi($tglDonasi, lock: true),
                'donatur_id' => $donatur->id,
                'amil_id' => $amil->id,
                'jenis_dana_id' => $validated['jenis_dana_id'],
                'program_id' => $validated['program_id'] ?? null,
                'jumlah' => $validated['jumlah'],
                'metode_bayar' => $validated['metode_bayar'],
                'bank' => in_array($validated['metode_bayar'], ['transfer', 'ewallet']) ? ($validated['bank'] ?? null) : null,
                'bukti_transfer' => $buktiPath,
                'lampiran_foto' => $lampiranFotoPath,
                'tgl_donasi' => $tglDonasi->format('Y-m-d H:i:s'),
                'niat' => $validated['niat'] ?? null,
                'atas_nama' => $validated['atas_nama'] ?? null,
                'peruntukan' => $validated['peruntukan'] ?? 'bebas',
                'pesan' => $validated['pesan'] ?? null,
                'catatan' => $validated['catatan'] ?? null,
                'status' => 'menunggu_verifikasi',
            ]);

            // 4. Simpan Donasi Barang jika ada
            if ($request->boolean('ada_barang') && ! empty($validated['barang_deskripsi'])) {
                DonasiBarang::create([
                    'donasi_id' => $donasi->id,
                    'deskripsi' => $validated['barang_deskripsi'],
                    'estimasi_nilai' => $validated['barang_estimasi'] ?? 0,
                ]);
            }

            return redirect()->route('amil.donasi.success', $donasi->id)
                ->with('success', 'Alhamdulillah! Donasi berhasil dicatat dan kwitansi telah diterbitkan.');
        });
    }

    /**
     * Halaman konfirmasi sukses donasi
     */
    public function success(Donasi $donasi)
    {
        $amil = Auth::user()->amil;

        // Validasi amil hanya bisa melihat donasi miliknya
        if ($donasi->amil_id !== $amil->id && ! Auth::user()->hasRole('Admin')) {
            abort(403);
        }

        $donasi->load(['donatur', 'jenisDana', 'program', 'amil', 'donasiBarangs']);

        return view('amil.donasi.success', compact('donasi', 'amil'));
    }

    /**
     * Endpoint API internal untuk cek nomor HP donatur (AJAX realtime)
     */
    public function checkPhone(Request $request)
    {
        $phone = $request->query('phone');

        if (! $phone || strlen($phone) < 8) {
            return response()->json(['exists' => false]);
        }

        $donatur = $this->donasiService->checkDuplicatePhone($phone);

        if ($donatur) {
            return response()->json([
                'exists' => true,
                'donatur' => [
                    'id' => $donatur->id,
                    'nama' => $donatur->nama,
                    'kode_donatur' => $donatur->kode_donatur,
                    'jenis' => $donatur->jenis,
                    'kategori_id' => $donatur->kategori_id,
                    'no_hp' => $donatur->no_hp,
                    'email' => $donatur->email,
                    'alamat' => $donatur->alamat,
                    'kota' => $donatur->kota,
                    'provinsi' => $donatur->provinsi,
                ],
            ]);
        }

        return response()->json(['exists' => false]);
    }

    /**
     * Endpoint API internal untuk live search donatur (nama, no_hp, kode_donatur)
     */
    public function searchDonatur(Request $request)
    {
        $q = trim($request->query('q', ''));
        $amil = Auth::user()->amil;

        $query = Donatur::query();

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('nama', 'like', "%{$q}%")
                    ->orWhere('no_hp', 'like', "%{$q}%")
                    ->orWhere('kode_donatur', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if ($amil) {
            $query->orderByRaw('CASE WHEN amil_pic_id = ? OR created_by = ? THEN 0 ELSE 1 END', [$amil->id, Auth::id()]);
        }

        $donaturs = $query->orderBy('nama', 'asc')
            ->limit(30)
            ->get(['id', 'nama', 'no_hp', 'kode_donatur', 'kota', 'alamat', 'amil_pic_id', 'created_by']);

        return response()->json([
            'donaturs' => $donaturs->map(function ($d) use ($amil) {
                return [
                    'id' => $d->id,
                    'nama' => $d->nama,
                    'no_hp' => $d->no_hp,
                    'kode_donatur' => $d->kode_donatur,
                    'kota' => $d->kota,
                    'alamat' => $d->alamat,
                    'is_binaan_saya' => $amil && ($d->amil_pic_id == $amil->id || $d->created_by == Auth::id()),
                ];
            }),
        ]);
    }
}
