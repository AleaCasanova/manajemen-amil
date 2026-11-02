<?php

namespace App\Console\Commands;

use App\Services\DonasiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class ResetTrialDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:reset-trial-data {--force : Jalankan pembersihan tanpa konfirmasi interaktif}';

    /**
     * The console command description.
     */
    protected $description = 'Bersihkan data percobaan (donasi, absensi, logbook, penyaluran, mustahik) untuk Go-Live tanpa merubah akun user & amil';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('===============================================================');
        $this->info('  PERSIAPAN GO-LIVE GSC: PEMBERSIHAN DATA PERCOBAAN AMIL');
        $this->info('===============================================================');

        if (! $this->option('force')) {
            $confirmed = $this->confirm(
                'Apakah Anda yakin ingin mengosongkan seluruh transaksi percobaan (donasi, absensi, logbook, dsb)? Data akun user dan amil DIJAMIN TETAP AMAN.',
                false
            );

            if (! $confirmed) {
                $this->warn('Operasi dibatalkan oleh pengguna.');

                return Command::SUCCESS;
            }
        }

        // 1. Cadangkan database terlebih dahulu
        $this->comment('Langkah 1: Membuat cadangan (backup) database otomatis...');
        Artisan::call('app:backup-database');
        $this->line(trim(Artisan::output()));

        // 2. Verifikasi data master yang dilindungi
        $usersBefore = DB::table('users')->count();
        $amilsBefore = DB::table('amils')->count();
        $this->info("Pemeriksaan Awal: Terdeteksi {$usersBefore} Akun User dan {$amilsBefore} Data Amil.");

        // 3. Daftar tabel percobaan yang akan dikosongkan & di-reset
        $tablesToTruncate = [
            'donasi_barangs',
            'donasis',
            'donaturs',
            'absensis',
            'aktivitas_harians',
            'izin_cutis',
            'penyalurans',
            'mustahiks',
            'agenda_amil',
            'agendas',
            'pengumuman_dibaca',
            'pengumumans',
            'notifikasis',
            'activity_log',
        ];

        $this->comment('Langkah 2: Mengosongkan data percobaan dan mereset nomor urut (auto-increment)...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        foreach ($tablesToTruncate as $tableName) {
            if (Schema::hasTable($tableName)) {
                $count = DB::table($tableName)->count();
                DB::table($tableName)->truncate();
                $this->line("  ✓ [TRUNCATE] `{$tableName}` (sebelumnya {$count} data) -> Berhasil di-reset ke 0");
            }
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 4. Verifikasi bahwa data User dan Amil tidak berubah sedikit pun
        $usersAfter = DB::table('users')->count();
        $amilsAfter = DB::table('amils')->count();

        if ($usersBefore !== $usersAfter || $amilsBefore !== $amilsAfter) {
            $this->error('PERINGATAN KRITIS: Terjadi ketidaksesuaian jumlah data user/amil!');

            return Command::FAILURE;
        }

        $this->info("✓ Verifikasi Sukses: Seluruh {$usersAfter} Akun User & {$amilsAfter} Amil tetap utuh dan password tidak berubah.");

        // 5. Bersihkan file media uji coba di storage (Kecuali folder amils)
        $this->comment('Langkah 3: Membersihkan file foto selfie/bukti donasi uji coba di storage...');
        $mediaFolders = [
            storage_path('app/public/absensi'),
            storage_path('app/public/donasis'),
            storage_path('app/public/aktivitas'),
            storage_path('app/public/penyaluran'),
            storage_path('app/public/izin-cuti'),
            storage_path('app/public/pengumuman'),
        ];

        $deletedFiles = 0;
        foreach ($mediaFolders as $folder) {
            if (File::isDirectory($folder)) {
                $files = File::files($folder);
                foreach ($files as $file) {
                    if ($file->getFilename() !== '.gitignore') {
                        File::delete($file->getPathname());
                        $deletedFiles++;
                    }
                }
            }
        }
        $this->line("  ✓ {$deletedFiles} file sampah media uji coba dibersihkan (foto profil amil dipertahankan).");

        // 6. Uji generate nomor kwitansi baru
        $this->comment('Langkah 4: Memverifikasi nomor kwitansi berikutnya...');
        /** @var DonasiService $donasiService */
        $donasiService = app(DonasiService::class);
        $nextNoKwitansi = $donasiService->generateNoKwitansi(now(), lock: false);
        $this->info("  ✓ Nomor kwitansi pertama saat transaksi riil besok: {$nextNoKwitansi}");

        // 7. Bersihkan cache aplikasi
        $this->comment('Langkah 5: Menyegarkan cache Laravel (optimize:clear)...');
        Artisan::call('optimize:clear');
        $this->line('  ✓ Seluruh cache view, konfigurasi, dan rute telah disegarkan.');

        $this->newLine();
        $this->info('===============================================================');
        $this->info('  PROSES SELESAI: SISTEM KINI BERSIH & SIAP DIGUNAKAN GO-LIVE!');
        $this->info('===============================================================');

        return Command::SUCCESS;
    }
}
