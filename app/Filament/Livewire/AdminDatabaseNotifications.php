<?php

namespace App\Filament\Livewire;

use App\Models\User;
use App\Services\ReviewNotificationService;
use Filament\Livewire\DatabaseNotifications as BaseDatabaseNotifications;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;
use Livewire\Attributes\On;

class AdminDatabaseNotifications extends BaseDatabaseNotifications
{
    public string $activeTab = 'all';

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage('database-notifications-page');
    }

    public function getActiveTabLabel(): string
    {
        return match ($this->activeTab) {
            'donasi' => 'Donasi',
            'aktivitas_harian' => 'Laporan Aktivitas',
            'izin_cuti' => 'Izin & Cuti',
            default => 'Semua',
        };
    }

    public function getTabCounts(): array
    {
        $user = $this->getUser();
        if (! $user) {
            return ['all' => 0, 'donasi' => 0, 'aktivitas_harian' => 0, 'izin_cuti' => 0];
        }

        $base = $user->notifications()->where('data->format', 'filament');

        return [
            'all' => (clone $base)->count(),
            'donasi' => (clone $base)->where('data->review_type', 'donasi')->count(),
            'aktivitas_harian' => (clone $base)->where('data->review_type', 'aktivitas_harian')->count(),
            'izin_cuti' => (clone $base)->where('data->review_type', 'izin_cuti')->count(),
        ];
    }

    public function getNotificationsQuery(): Builder|Relation
    {
        $user = $this->getUser();

        if ($user instanceof User) {
            ReviewNotificationService::syncPendingForUser($user);
        }

        $query = parent::getNotificationsQuery();

        if ($this->activeTab !== 'all') {
            $query->where('data->review_type', $this->activeTab);
        }

        return $query;
    }

    #[On('notificationClosed')]
    public function removeNotification(string $id): void
    {
        if (! Str::isUuid($id)) {
            return;
        }

        $notif = $this->getNotificationsQuery()->where('id', $id)->first();
        if ($notif) {
            $data = is_array($notif->data) ? $notif->data : json_decode($notif->data, true);
            if (is_array($data) && isset($data['review_type'], $data['review_id'])) {
                $isPending = ReviewNotificationService::isItemPending(
                    $data['review_type'],
                    (int) $data['review_id']
                );

                if ($isPending) {
                    Notification::make()
                        ->title('Pengajuan Belum Selesai')
                        ->body('Pengingat ini tidak dapat dihapus sebelum status pengajuan disetujui atau ditolak.')
                        ->warning()
                        ->send();

                    return;
                }
            }

            $notif->delete();
        }
    }

    public function clearNotifications(): void
    {
        $notifications = $this->getNotificationsQuery()->get();
        $hasPendingSkipped = false;

        foreach ($notifications as $notif) {
            $data = is_array($notif->data) ? $notif->data : json_decode($notif->data, true);
            if (is_array($data) && isset($data['review_type'], $data['review_id'])) {
                $isPending = ReviewNotificationService::isItemPending(
                    $data['review_type'],
                    (int) $data['review_id']
                );

                if ($isPending) {
                    $hasPendingSkipped = true;

                    continue;
                }
            }

            $notif->delete();
        }

        if ($hasPendingSkipped) {
            Notification::make()
                ->title('Pengingat Dipertahankan')
                ->body('Pengajuan yang masih berstatus menunggu review tetap dipertahankan sebagai pengingat.')
                ->info()
                ->send();
        }
    }

    public function render(): View
    {
        return view('filament.database-notifications', [
            'tabCounts' => $this->getTabCounts(),
        ]);
    }
}
