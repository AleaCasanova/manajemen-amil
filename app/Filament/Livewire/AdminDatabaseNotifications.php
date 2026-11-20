<?php

namespace App\Filament\Livewire;

use App\Models\User;
use App\Services\ReviewNotificationService;
use Filament\Livewire\DatabaseNotifications as BaseDatabaseNotifications;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;
use Livewire\Attributes\On;

class AdminDatabaseNotifications extends BaseDatabaseNotifications
{
    public function getNotificationsQuery(): Builder|Relation
    {
        $user = $this->getUser();

        if ($user instanceof User) {
            ReviewNotificationService::syncPendingForUser($user);
        }

        return parent::getNotificationsQuery();
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

                    continue; // Lindungi pengajuan yang belum disetujui/ditolak agar tidak hilang
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
}
