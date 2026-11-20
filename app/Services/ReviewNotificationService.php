<?php

namespace App\Services;

use App\Models\AktivitasHarian;
use App\Models\Donasi;
use App\Models\IzinCuti;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReviewNotificationService
{
    /**
     * Dapatkan semua user administrator yang aktif
     */
    public static function getAdmins(): Collection
    {
        return User::where('is_active', true)
            ->where(function ($q) {
                $q->whereHas('roles', fn ($r) => $r->whereIn('name', ['Admin', 'admin']))
                    ->orWhere('role_id', 21)
                    ->orWhere('role_id', 1);
            })
            ->get();
    }

    /**
     * Cek apakah suatu item review masih berstatus menunggu / pending
     */
    public static function isItemPending(string $type, int $id): bool
    {
        return match ($type) {
            'izin_cuti' => IzinCuti::where('id', $id)->where('status', 'diajukan')->exists(),
            'aktivitas_harian' => AktivitasHarian::where('id', $id)->where('status', 'diajukan')->exists(),
            'donasi' => Donasi::where('id', $id)->where('status', 'menunggu_verifikasi')->exists(),
            default => false,
        };
    }

    /**
     * Kirim notifikasi pengajuan Izin/Cuti ke semua admin
     */
    public static function notifyAllAdminsForIzinCuti(IzinCuti $izin): void
    {
        if ($izin->status !== 'diajukan') {
            return;
        }

        $admins = static::getAdmins();
        foreach ($admins as $admin) {
            static::createIzinCutiNotificationForUser($admin, $izin);
        }
    }

    /**
     * Buat notifikasi pengajuan Izin/Cuti untuk user admin tertentu
     */
    public static function createIzinCutiNotificationForUser(User $user, IzinCuti $izin): void
    {
        $izin->loadMissing('amil');
        $namaAmil = $izin->amil?->nama ?? 'Amil';
        $jenisLabel = ucfirst($izin->jenis);
        $tglMulai = Carbon::parse($izin->tgl_mulai)->format('d M Y');
        $tglSelesai = Carbon::parse($izin->tgl_selesai)->format('d M Y');

        $notif = Notification::make()
            ->title("Pengajuan {$jenisLabel}: {$namaAmil}")
            ->body("{$namaAmil} mengajukan {$jenisLabel} ({$tglMulai} s/d {$tglSelesai}). Alasan: ".Str::limit($izin->alasan, 50))
            ->icon('heroicon-o-calendar')
            ->warning()
            ->actions([
                Action::make('review')
                    ->label('Review Izin/Cuti')
                    ->button()
                    ->url(route('filament.admin.resources.izin-cutis.index')),
            ]);

        static::saveReviewNotification($user, $notif, 'izin_cuti', (int) $izin->id, $izin->created_at ?? now());
    }

    /**
     * Kirim notifikasi laporan aktivitas ke semua admin
     */
    public static function notifyAllAdminsForAktivitas(AktivitasHarian $aktivitas): void
    {
        if ($aktivitas->status !== 'diajukan') {
            return;
        }

        $admins = static::getAdmins();
        foreach ($admins as $admin) {
            static::createAktivitasNotificationForUser($admin, $aktivitas);
        }
    }

    /**
     * Buat notifikasi laporan aktivitas untuk user admin tertentu
     */
    public static function createAktivitasNotificationForUser(User $user, AktivitasHarian $aktivitas): void
    {
        $aktivitas->loadMissing('amil');
        $namaAmil = $aktivitas->amil?->nama ?? 'Amil';
        $tgl = Carbon::parse($aktivitas->tanggal)->format('d M Y');
        $lokasiInfo = $aktivitas->lokasi ? ' di '.Str::limit($aktivitas->lokasi, 30) : '';

        $notif = Notification::make()
            ->title("Laporan Aktivitas: {$namaAmil}")
            ->body('"'.Str::limit($aktivitas->judul, 40)."\" ({$tgl}){$lokasiInfo}.")
            ->icon('heroicon-o-document-text')
            ->warning()
            ->actions([
                Action::make('review')
                    ->label('Review Laporan')
                    ->button()
                    ->url(route('filament.admin.resources.aktivitas-harians.index')),
            ]);

        static::saveReviewNotification($user, $notif, 'aktivitas_harian', (int) $aktivitas->id, $aktivitas->created_at ?? now());
    }

    /**
     * Kirim notifikasi donasi menunggu verifikasi ke semua admin
     */
    public static function notifyAllAdminsForDonasi(Donasi $donasi): void
    {
        if ($donasi->status !== 'menunggu_verifikasi') {
            return;
        }

        $admins = static::getAdmins();
        foreach ($admins as $admin) {
            static::createDonasiNotificationForUser($admin, $donasi);
        }
    }

    /**
     * Buat notifikasi donasi untuk user admin tertentu
     */
    public static function createDonasiNotificationForUser(User $user, Donasi $donasi): void
    {
        $donasi->loadMissing(['donatur', 'amil']);
        $donaturNama = $donasi->donatur?->is_anonim ? 'Hamba Allah (Anonim)' : ($donasi->donatur?->nama ?? 'Donatur');
        $amilNama = $donasi->amil?->nama ?? '-';
        $nominal = number_format($donasi->jumlah, 0, ',', '.');
        $metode = ucfirst($donasi->metode_bayar ?? 'Transfer');

        $notif = Notification::make()
            ->title("Donasi Masuk: {$donaturNama}")
            ->body("Rp {$nominal} ({$donasi->no_kwitansi}) • Amil: {$amilNama} • Metode: {$metode}")
            ->icon('heroicon-o-banknotes')
            ->warning()
            ->actions([
                Action::make('review')
                    ->label('Verifikasi Donasi')
                    ->button()
                    ->url(route('filament.admin.resources.donasis.index', ['tableFilters[status][value]' => 'menunggu_verifikasi'])),
            ]);

        static::saveReviewNotification($user, $notif, 'donasi', (int) $donasi->id, $donasi->created_at ?? now());
    }

    /**
     * Simpan notifikasi ke tabel notifications secara langsung & synchronous
     */
    protected static function saveReviewNotification(User $user, Notification $notif, string $type, int $id, mixed $createdAt): void
    {
        // Cek jika sudah ada notifikasi untuk item ini agar tidak dobel
        $exists = DB::table('notifications')
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->where('data->review_type', $type)
            ->where('data->review_id', $id)
            ->exists();

        if ($exists) {
            return;
        }

        $message = $notif->getDatabaseMessage();
        $message['review_type'] = $type;
        $message['review_id'] = $id;

        DB::table('notifications')->insert([
            'id' => (string) Str::uuid(),
            'type' => 'Filament\Notifications\DatabaseNotification',
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => json_encode($message),
            'read_at' => null,
            'created_at' => $createdAt instanceof Carbon ? $createdAt : Carbon::parse($createdAt),
            'updated_at' => now(),
        ]);
    }

    /**
     * Hapus notifikasi jika status pengajuan sudah disetujui / ditolak / dihapus
     */
    public static function removeNotification(string $type, int $id): void
    {
        DB::table('notifications')
            ->where('data->review_type', $type)
            ->where('data->review_id', $id)
            ->delete();
    }

    /**
     * Sinkronkan semua pengajuan yang masih status review untuk seorang user admin
     */
    public static function syncPendingForUser(User $user): void
    {
        if (! $user->isAdmin()) {
            return;
        }

        // 1. Data yang saat ini sedang menunggu review
        $pendingCutis = IzinCuti::with('amil')->where('status', 'diajukan')->get();
        $pendingAktivitas = AktivitasHarian::with('amil')->where('status', 'diajukan')->get();
        $pendingDonasis = Donasi::with(['amil', 'donatur'])->where('status', 'menunggu_verifikasi')->get();

        // 2. Ambil notifikasi review yang ada di database saat ini
        $existingNotifs = DB::table('notifications')
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->get();

        $existingKeys = [];
        $obsoleteIds = [];

        foreach ($existingNotifs as $n) {
            $data = json_decode($n->data, true);
            if (is_array($data) && isset($data['review_type'], $data['review_id'])) {
                $type = $data['review_type'];
                $id = (int) $data['review_id'];
                $key = "{$type}_{$id}";
                $existingKeys[$key] = true;

                $isStillPending = match ($type) {
                    'izin_cuti' => $pendingCutis->contains('id', $id),
                    'aktivitas_harian' => $pendingAktivitas->contains('id', $id),
                    'donasi' => $pendingDonasis->contains('id', $id),
                    default => false,
                };

                if (! $isStillPending) {
                    $obsoleteIds[] = $n->id;
                }
            }
        }

        // Hapus yang sudah bukan pending
        if (! empty($obsoleteIds)) {
            DB::table('notifications')->whereIn('id', $obsoleteIds)->delete();
        }

        // 3. Masukkan pending cuti yang belum ada di notifikasi
        foreach ($pendingCutis as $cuti) {
            if (! isset($existingKeys["izin_cuti_{$cuti->id}"])) {
                static::createIzinCutiNotificationForUser($user, $cuti);
            }
        }

        // 4. Masukkan pending aktivitas yang belum ada di notifikasi
        foreach ($pendingAktivitas as $akt) {
            if (! isset($existingKeys["aktivitas_harian_{$akt->id}"])) {
                static::createAktivitasNotificationForUser($user, $akt);
            }
        }

        // 5. Masukkan pending donasi yang belum ada di notifikasi
        foreach ($pendingDonasis as $donasi) {
            if (! isset($existingKeys["donasi_{$donasi->id}"])) {
                static::createDonasiNotificationForUser($user, $donasi);
            }
        }
    }
}
