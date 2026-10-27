<?php

namespace App\Http\Controllers\Amil;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\JamKerja;
use App\Models\LokasiKantor;
use App\Services\AbsensiService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AbsensiController extends Controller
{
    public function __construct(
        protected AbsensiService $absensiService
    ) {}

    /**
     * Tampilan utama absensi amil
     */
    public function index()
    {
        $amil = Auth::user()->amil;

        if ($amil && ! $amil->needsAttendance()) {
            return redirect()->route('amil.dashboard')->with('info', 'Petugas relawan tidak diwajibkan untuk presensi kehadiran.');
        }

        $today = Carbon::today('Asia/Jakarta');
        $absensiToday = $this->absensiService->getTodayAbsensi($amil);

        $jamKerja = JamKerja::where('is_active', true)->first();
        $kantor = LokasiKantor::where('is_active', true)->first();
        $kantorAktif = LokasiKantor::where('is_active', true)->get();

        // Riwayat absensi 30 hari terakhir
        $history = Absensi::where('amil_id', $amil->id)
            ->latest('tanggal')
            ->take(30)
            ->get();

        $clockOutEligibility = $this->absensiService->checkClockOutEligibility(Carbon::now('Asia/Jakarta'));

        return view('amil.absensi.index', compact('amil', 'absensiToday', 'jamKerja', 'kantor', 'kantorAktif', 'history', 'clockOutEligibility'));
    }

    /**
     * Clock In (Presensi Masuk)
     */
    public function clockIn(Request $request)
    {
        $amil = Auth::user()->amil;

        if ($amil && ! $amil->needsAttendance()) {
            return redirect()->route('amil.dashboard')->with('info', 'Petugas relawan tidak diwajibkan untuk presensi kehadiran.');
        }

        // Cek apakah sudah absen hari ini
        $existing = $this->absensiService->getTodayAbsensi($amil);
        if ($existing && $existing->jam_masuk) {
            return back()->with('error', 'Anda sudah melakukan presensi masuk hari ini pada pukul '.substr($existing->jam_masuk, 0, 5).' WIB.');
        }

        $tipePresensi = $request->input('tipe_presensi', 'kantor');
        $isDinasLuar = $tipePresensi === 'dinas_luar';

        $rules = [
            'lat' => ['required', 'numeric'],
            'lng' => ['required', 'numeric'],
            'foto' => ['nullable'], // string base64 atau null jika file
            'foto_file' => ['nullable', 'image', 'max:5120'], // file upload langsung
            'tipe_presensi' => ['nullable', 'in:kantor,dinas_luar'],
            'keterangan' => $isDinasLuar
                ? ['required', 'string', 'min:5', 'max:255']
                : ['nullable', 'string', 'max:255'],
        ];

        $messages = [
            'keterangan.required' => 'Untuk presensi Tugas Luar, Anda wajib mengisi rincian kegiatan / penugasan lapangan!',
            'keterangan.min' => 'Rincian tugas lapangan minimal 5 karakter.',
        ];

        $request->validate($rules, $messages);

        if (! $request->hasFile('foto_file') && ! $request->filled('foto')) {
            return back()->with('error', 'Harap ambil foto selfie melalui kamera atau pilih file foto!');
        }

        $now = Carbon::now('Asia/Jakarta');

        $nearestOfficeName = null;
        // 1. Validasi Geofencing (Hanya jika di kantor)
        if (! $isDinasLuar) {
            $locCheck = $this->absensiService->validateLocation((float) $request->lat, (float) $request->lng);
            if (! $locCheck['valid']) {
                $distMeters = round($locCheck['distance_meters']);
                $nearestName = $locCheck['nearest_office']?->nama_lokasi ?? 'kantor terdekat';
                $maxRadius = $locCheck['nearest_office']?->radius_meter ?? 100;

                return back()->with('error', "Presensi gagal! Anda berada di luar radius kantor ({$distMeters} meter dari {$nearestName}, batas maksimal {$maxRadius} meter). Jika sedang dinas luar, silakan aktifkan mode 'Tugas Luar'.");
            }
            $nearestOfficeName = $locCheck['nearest_office']?->nama_lokasi;
        }

        // 2. Simpan Foto Selfie (dari File atau Base64)
        if ($request->hasFile('foto_file')) {
            $fotoPath = $request->file('foto_file')->store('absensi/masuk', 'public');
        } else {
            $fotoPath = $this->storeBase64Image($request->foto, 'absensi/masuk');
        }

        // 3. Tentukan status terlambat / hadir
        $status = $this->absensiService->determineStatus($now);

        // Keterangan kehadiran (jika normal kantor, catat nama kantor terdekat)
        $keteranganMasuk = $request->keterangan;
        if (! $isDinasLuar && ! $keteranganMasuk && $nearestOfficeName) {
            $keteranganMasuk = 'Presensi di '.$nearestOfficeName;
        }

        // 4. Simpan Record
        if ($existing) {
            $existing->update([
                'jam_masuk' => $now->format('H:i:s'),
                'lat_masuk' => $request->lat,
                'lng_masuk' => $request->lng,
                'foto_masuk' => $fotoPath,
                'tipe_masuk' => $tipePresensi,
                'status' => $status,
                'keterangan' => $keteranganMasuk ?? $existing->keterangan,
            ]);
        } else {
            Absensi::create([
                'amil_id' => $amil->id,
                'tanggal' => $now->toDateString(),
                'jam_masuk' => $now->format('H:i:s'),
                'lat_masuk' => $request->lat,
                'lng_masuk' => $request->lng,
                'foto_masuk' => $fotoPath,
                'tipe_masuk' => $tipePresensi,
                'status' => $status,
                'keterangan' => $keteranganMasuk,
            ]);
        }

        $lokasiLabel = $isDinasLuar ? ' (Mode Tugas Luar)' : ($nearestOfficeName ? " di {$nearestOfficeName}" : '');
        $pesan = $status === 'terlambat'
            ? "Presensi masuk{$lokasiLabel} berhasil dicatat, namun status Anda tercatat TERLAMBAT."
            : "Alhamdulillah, presensi masuk{$lokasiLabel} tepat waktu berhasil dicatat!";

        return redirect()->route('amil.absensi.index')->with('success', $pesan);
    }

    /**
     * Clock Out (Presensi Pulang)
     */
    public function clockOut(Request $request)
    {
        $amil = Auth::user()->amil;

        if ($amil && ! $amil->needsAttendance()) {
            return redirect()->route('amil.dashboard')->with('info', 'Petugas relawan tidak diwajibkan untuk presensi kehadiran.');
        }

        $existing = $this->absensiService->getTodayAbsensi($amil);
        if (! $existing || ! $existing->jam_masuk) {
            return back()->with('error', 'Anda belum melakukan presensi masuk hari ini!');
        }

        if ($existing->jam_pulang) {
            return back()->with('error', 'Anda sudah melakukan presensi pulang hari ini pada pukul '.substr($existing->jam_pulang, 0, 5).' WIB.');
        }

        $tipePresensi = $request->input('tipe_presensi', 'kantor');
        $isDinasLuar = $tipePresensi === 'dinas_luar';

        $rules = [
            'lat' => ['required', 'numeric'],
            'lng' => ['required', 'numeric'],
            'foto' => ['nullable'],
            'foto_file' => ['nullable', 'image', 'max:5120'],
            'tipe_presensi' => ['nullable', 'in:kantor,dinas_luar'],
            'keterangan_pulang' => $isDinasLuar
                ? ['required', 'string', 'min:5', 'max:255']
                : ['nullable', 'string', 'max:255'],
        ];

        $messages = [
            'keterangan_pulang.required' => 'Untuk presensi pulang Tugas Luar, Anda wajib mengisi rincian kegiatan / catatan tugas!',
            'keterangan_pulang.min' => 'Rincian tugas minimal 5 karakter.',
        ];

        $request->validate($rules, $messages);

        if (! $request->hasFile('foto_file') && ! $request->filled('foto')) {
            return back()->with('error', 'Harap ambil foto selfie melalui kamera atau pilih file foto!');
        }

        $now = Carbon::now('Asia/Jakarta');

        // Validasi waktu buka presensi pulang (terkunci sebelum 15:30 untuk mode kantor)
        $eligibility = $this->absensiService->checkClockOutEligibility($now, $isDinasLuar);
        if (! $eligibility['can_clock_out']) {
            return back()->with('error', $eligibility['message']);
        }

        $nearestOfficeName = null;
        // Validasi Geofencing (Hanya jika di kantor)
        if (! $isDinasLuar) {
            $locCheck = $this->absensiService->validateLocation((float) $request->lat, (float) $request->lng);
            if (! $locCheck['valid']) {
                $distMeters = round($locCheck['distance_meters']);
                $nearestName = $locCheck['nearest_office']?->nama_lokasi ?? 'kantor terdekat';
                $maxRadius = $locCheck['nearest_office']?->radius_meter ?? 100;

                return back()->with('error', "Presensi pulang gagal! Anda berada di luar radius kantor ({$distMeters} meter dari {$nearestName}, batas maksimal {$maxRadius} meter). Jika sedang dinas luar, silakan aktifkan mode 'Tugas Luar'.");
            }
            $nearestOfficeName = $locCheck['nearest_office']?->nama_lokasi;
        }

        // Simpan Foto Selfie (dari File atau Base64)
        if ($request->hasFile('foto_file')) {
            $fotoPath = $request->file('foto_file')->store('absensi/pulang', 'public');
        } else {
            $fotoPath = $this->storeBase64Image($request->foto, 'absensi/pulang');
        }

        $keteranganPulang = $request->keterangan_pulang;
        if ($eligibility['is_early']) {
            $prefixEarly = "Pulang Lebih Awal ({$eligibility['minutes_early']} menit sebelum jam {$eligibility['regular_end_time']} WIB)";
            $keteranganPulang = $keteranganPulang ? "{$prefixEarly} - {$keteranganPulang}" : $prefixEarly;
        }

        $existing->update([
            'jam_pulang' => $now->format('H:i:s'),
            'lat_pulang' => $request->lat,
            'lng_pulang' => $request->lng,
            'foto_pulang' => $fotoPath,
            'tipe_pulang' => $tipePresensi,
            'keterangan_pulang' => $keteranganPulang,
        ]);

        $lokasiLabel = $isDinasLuar ? ' (Mode Tugas Luar)' : ($nearestOfficeName ? " di {$nearestOfficeName}" : '');

        $pesanSukses = $eligibility['is_early']
            ? "Presensi pulang{$lokasiLabel} berhasil dicatat dengan catatan Pulang Lebih Awal ({$eligibility['minutes_early']} menit sebelum jam {$eligibility['regular_end_time']} WIB). Selamat beristirahat."
            : "Alhamdulillah! Presensi pulang{$lokasiLabel} berhasil dicatat tepat waktu. Selamat beristirahat.";

        return redirect()->route('amil.absensi.index')
            ->with('success', $pesanSukses);
    }

    /**
     * API cek lokasi live realtime untuk frontend
     */
    public function checkLocation(Request $request)
    {
        $request->validate([
            'lat' => ['required', 'numeric'],
            'lng' => ['required', 'numeric'],
        ]);

        $result = $this->absensiService->validateLocation((float) $request->lat, (float) $request->lng);

        return response()->json([
            'valid' => $result['valid'],
            'distance_meters' => $result['distance_meters'],
            'office_name' => $result['nearest_office']?->nama_lokasi ?? 'Kantor Pusat',
            'max_radius' => $result['nearest_office']?->radius_meter ?? 100,
        ]);
    }

    /**
     * Helper simpan foto base64 ke disk public storage
     */
    protected function storeBase64Image(string $base64Data, string $folder): string
    {
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
            $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
            $type = strtolower($type[1]); // jpg, png, webp
        } else {
            $type = 'jpg';
        }

        $image = base64_decode($base64Data);
        $fileName = $folder.'/'.uniqid().'_'.time().'.'.$type;

        Storage::disk('public')->put($fileName, $image);

        return $fileName;
    }
}
