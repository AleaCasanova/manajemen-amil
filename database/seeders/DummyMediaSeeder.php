<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\AktivitasHarian;
use App\Models\Amil;
use App\Models\Donasi;
use App\Models\IzinCuti;
use App\Models\Pengumuman;
use App\Models\Penyaluran;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class DummyMediaSeeder extends Seeder
{
    /**
     * Generate file gambar nyata dan pasangkan ke database
     */
    public function run(): void
    {
        $publicDisk = Storage::disk('public');

        // Pastikan subfolder storage publik tersedia
        $directories = [
            'amils/photos',
            'absensi/masuk',
            'absensi/pulang',
            'donasis/bukti',
            'donasis/dokumentasi',
            'aktivitas/foto',
            'penyaluran/bukti',
            'izin-cuti/lampiran',
            'pengumuman/lampiran',
        ];

        foreach ($directories as $dir) {
            $publicDisk->makeDirectory($dir);
        }

        // Helper untuk membuat gambar PNG nyata
        $createImage = function (string $relativePath, int $w, int $h, string $title, string $subtitle, array $bgColor, array $textColor) {
            $fullPath = Storage::disk('public')->path($relativePath);
            File::ensureDirectoryExists(dirname($fullPath));

            $img = imagecreatetruecolor($w, $h);

            // Background
            $bg = imagecolorallocate($img, $bgColor[0], $bgColor[1], $bgColor[2]);
            imagefill($img, 0, 0, $bg);

            // Border
            $borderColor = imagecolorallocate($img, max(0, $bgColor[0] - 40), max(0, $bgColor[1] - 40), max(0, $bgColor[2] - 40));
            imagerectangle($img, 0, 0, $w - 1, $h - 1, $borderColor);
            imagerectangle($img, 1, 1, $w - 2, $h - 2, $borderColor);

            // Watermark / Header Bar
            $headerColor = imagecolorallocate($img, min(255, $bgColor[0] + 30), min(255, $bgColor[1] + 30), min(255, $bgColor[2] + 30));
            imagefilledrectangle($img, 0, 0, $w, 36, $headerColor);

            $white = imagecolorallocate($img, 255, 255, 255);
            $text = imagecolorallocate($img, $textColor[0], $textColor[1], $textColor[2]);
            $subText = imagecolorallocate($img, 100, 116, 139);

            // Header text
            imagestring($img, 3, 15, 10, 'YAYASAN GERAK SEDEKAH CILACAP (GSC)', $white);

            // Main text centered vertically
            imagestring($img, 5, 20, ($h / 2) - 20, $title, $text);
            imagestring($img, 4, 20, ($h / 2) + 10, $subtitle, $subText);
            imagestring($img, 2, 20, $h - 25, 'Dokumen Digital Terverifikasi - Sistem Manajemen Amil', $subText);

            imagepng($img, $fullPath);
            imagedestroy($img);

            return $relativePath;
        };

        // 1. UPDATE AMILS (Foto Profil)
        $amils = Amil::all();
        $amilBgColors = [
            [16, 185, 129], // emerald
            [59, 130, 246], // blue
            [139, 92, 246], // violet
            [245, 158, 11], // amber
            [236, 72, 153], // pink
        ];

        foreach ($amils as $idx => $amil) {
            $path = 'amils/photos/amil_profil_'.($idx + 1).'.png';
            $bg = $amilBgColors[$idx % count($amilBgColors)];
            $createImage($path, 400, 400, $amil->nama, 'NIP: '.$amil->nip, $bg, [255, 255, 255]);
            $amil->update(['foto' => $path]);
        }

        // 2. UPDATE ABSENSI (Selfie Masuk & Pulang)
        $absensis = Absensi::with('amil')->get();
        foreach ($absensis as $idx => $abs) {
            $namaAmil = $abs->amil?->nama ?? 'Amil GSC';
            $tgl = $abs->tanggal;

            $pathMasuk = 'absensi/masuk/selfie_masuk_'.($idx + 1).'.png';
            $createImage(
                $pathMasuk,
                640,
                480,
                'SELFIE PRESENSI MASUK',
                $namaAmil.' | '.$tgl.' '.($abs->jam_masuk ?? '08:00'),
                [240, 253, 244],
                [6, 95, 70]
            );

            $pathPulang = 'absensi/pulang/selfie_pulang_'.($idx + 1).'.png';
            $createImage(
                $pathPulang,
                640,
                480,
                'SELFIE PRESENSI PULANG',
                $namaAmil.' | '.$tgl.' '.($abs->jam_pulang ?? '16:30'),
                [254, 242, 242],
                [153, 27, 27]
            );

            $abs->update([
                'foto_masuk' => $pathMasuk,
                'foto_pulang' => $pathPulang,
            ]);
        }

        // 3. UPDATE DONASI (Bukti Transfer & Dokumentasi)
        $donasis = Donasi::with('donatur')->get();
        foreach ($donasis as $idx => $dns) {
            $pathBukti = 'donasis/bukti/bukti_donasi_'.($idx + 1).'.png';
            $createImage(
                $pathBukti,
                600,
                750,
                'BUKTI TRANSFER DONASI',
                'No: '.$dns->no_kwitansi.' | Rp '.number_format((float) $dns->jumlah, 0, ',', '.'),
                [241, 245, 249],
                [30, 41, 59]
            );

            $pathDok = 'donasis/dokumentasi/dokumentasi_donasi_'.($idx + 1).'.png';
            $createImage(
                $pathDok,
                700,
                500,
                'FOTO DOKUMENTASI PENYERAHAN',
                'Kwitansi: '.$dns->no_kwitansi.' | Donatur: '.($dns->donatur?->nama ?? 'Muzakki'),
                [236, 253, 245],
                [4, 120, 87]
            );

            $dns->update([
                'bukti_transfer' => $pathBukti,
                'lampiran_foto' => $pathDok,
            ]);
        }

        // 4. UPDATE AKTIVITAS HARIAN (Foto Lapangan)
        $aktivitasList = AktivitasHarian::with('amil')->get();
        foreach ($aktivitasList as $idx => $akt) {
            $pathAkt = 'aktivitas/foto/aktivitas_kegiatan_'.($idx + 1).'.png';
            $createImage(
                $pathAkt,
                720,
                480,
                'DOKUMENTASI LAPORAN AKTIVITAS',
                $akt->judul.' ('.$akt->tanggal.')',
                [239, 246, 255],
                [30, 64, 175]
            );

            $akt->update(['foto' => $pathAkt]);
        }

        // 5. UPDATE PENYALURAN (Foto Berita Acara Serah Terima)
        $penyalurans = Penyaluran::with('mustahik')->get();
        foreach ($penyalurans as $idx => $salur) {
            $pathBast = 'penyaluran/bukti/serah_terima_'.($idx + 1).'.png';
            $createImage(
                $pathBast,
                720,
                480,
                'BERITA ACARA SERAH TERIMA BANTUAN',
                'Mustahik: '.($salur->mustahik?->nama ?? 'Penerima').' | Rp '.number_format((float) $salur->jumlah, 0, ',', '.'),
                [254, 243, 199],
                [146, 64, 14]
            );

            $salur->update(['bukti' => $pathBast]);
        }

        // 6. UPDATE IZIN & CUTI (Surat Dokter / Dokumen)
        $izinCutis = IzinCuti::all();
        foreach ($izinCutis as $idx => $ic) {
            $pathLampiran = 'izin-cuti/lampiran/berkas_permohonan_'.($idx + 1).'.png';
            $createImage(
                $pathLampiran,
                600,
                800,
                'SURAT LAMPIRAN PENGAJUAN '.strtoupper($ic->jenis),
                'Tgl: '.$ic->tgl_mulai.' s/d '.$ic->tgl_selesai.' | '.$ic->alasan,
                [248, 250, 252],
                [51, 65, 85]
            );

            $ic->update(['lampiran' => $pathLampiran]);
        }

        // 7. UPDATE PENGUMUMAN (Poster Banner)
        $pengumumans = Pengumuman::all();
        foreach ($pengumumans as $idx => $pg) {
            $pathPoster = 'pengumuman/lampiran/poster_pengumuman_'.($idx + 1).'.png';
            $createImage(
                $pathPoster,
                800,
                450,
                'POSTER PENGUMUMAN RESMI',
                $pg->judul,
                [238, 242, 255],
                [67, 56, 202]
            );

            $pg->update(['lampiran' => $pathPoster]);
        }
    }
}
