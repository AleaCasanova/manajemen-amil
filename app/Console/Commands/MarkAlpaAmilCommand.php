<?php

namespace App\Console\Commands;

use App\Models\Absensi;
use App\Models\Amil;
use App\Models\IzinCuti;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('amil:mark-alpa {--date= : Tanggal yang diperiksa (format Y-m-d), default hari ini} {--include-weekend : Jalankan meskipun hari libur/akhir pekan}')]
#[Description('Menandai amil aktif yang tidak presensi dan tidak izin/cuti sebagai Alpa secara otomatis')]
class MarkAlpaAmilCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dateInput = $this->option('date');
        $targetDate = $dateInput
            ? Carbon::createFromFormat('Y-m-d', $dateInput, 'Asia/Jakarta')->startOfDay()
            : Carbon::today('Asia/Jakarta');

        $dateString = $targetDate->toDateString();

        // Cek jika hari Minggu dan tidak pakai flag --include-weekend
        if ($targetDate->isSunday() && ! $this->option('include-weekend')) {
            $this->info("Tanggal {$dateString} adalah hari Minggu (Hari Libur). Proses Alpa dilewati.");

            return Command::SUCCESS;
        }

        $activeAmils = Amil::where('status', 'aktif')->get();

        if ($activeAmils->isEmpty()) {
            $this->warn('Tidak ada amil aktif yang terdaftar.');

            return Command::SUCCESS;
        }

        $alpaCount = 0;

        foreach ($activeAmils as $amil) {
            // 1. Cek apakah sudah ada catatan absensi hari ini (hadir/terlambat/alpa/izin/cuti)
            $existingAbsensi = Absensi::where('amil_id', $amil->id)
                ->whereDate('tanggal', $dateString)
                ->first();

            if ($existingAbsensi) {
                continue;
            }

            // 2. Cek apakah ada pengajuan Izin atau Cuti yang disetujui pada tanggal tersebut
            $hasApprovedLeave = IzinCuti::where('amil_id', $amil->id)
                ->where('status', 'disetujui')
                ->whereDate('tgl_mulai', '<=', $dateString)
                ->whereDate('tgl_selesai', '>=', $dateString)
                ->exists();

            if ($hasApprovedLeave) {
                Absensi::create([
                    'amil_id' => $amil->id,
                    'tanggal' => $dateString,
                    'status' => 'izin',
                    'keterangan' => 'Izin / Cuti resmi disetujui',
                ]);

                continue;
            }

            // 3. Jika tidak ada absensi dan tidak ada izin/cuti resmi -> Tandai ALPA
            Absensi::create([
                'amil_id' => $amil->id,
                'tanggal' => $dateString,
                'status' => 'alpa',
                'keterangan' => 'Tidak hadir tanpa keterangan (Otomatis Alpa oleh Sistem)',
            ]);

            $alpaCount++;
            $this->line("[-] Amil: {$amil->nama} ({$amil->nip}) ditandai ALPA.");
        }

        $this->info("Selesai! {$alpaCount} amil ditandai ALPA untuk tanggal {$dateString}.");

        return Command::SUCCESS;
    }
}
