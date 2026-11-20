@php
    use Filament\Notifications\View\NotificationsIconAlias;
    use Filament\Support\Enums\Alignment;
    use Filament\Support\Icons\Heroicon;
    use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
    use Filament\Support\View\Components\BadgeComponent;
    use Illuminate\Contracts\Pagination\Paginator;

    $notifications = $this->getNotifications();
    $unreadNotificationsCount = $this->getUnreadNotificationsCount();
    $totalNotificationsCount = $tabCounts['all'] ?? 0;
    $hasAnyNotifications = $totalNotificationsCount > 0;
    $hasFilteredNotifications = $notifications->count() > 0;
    $isPaginated = $notifications instanceof Paginator && $notifications->hasPages();
    $pollingInterval = $this->getPollingInterval();
@endphp

<div class="fi-no-database">
    <style>
        .gsc-notif-header-wrapper {
            display: flex;
            flex-direction: column;
            gap: 12px;
            width: 100%;
        }
        .gsc-notif-header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }
        .gsc-notif-tabs-bar {
            display: flex;
            align-items: center;
            gap: 6px;
            overflow-x: auto;
            padding: 10px 0 4px 0;
            border-top: 1px solid #e2e8f0;
            scrollbar-width: none;
            -ms-overflow-style: none;
            width: 100%;
        }
        .gsc-notif-tabs-bar::-webkit-scrollbar {
            display: none;
        }
        .dark .gsc-notif-tabs-bar {
            border-top-color: rgba(255, 255, 255, 0.08);
        }
        .gsc-notif-tab-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #475569;
            transition: all 0.15s ease-in-out;
            white-space: nowrap;
            line-height: 1.2;
            text-decoration: none;
        }
        .dark .gsc-notif-tab-btn {
            background: #1e293b;
            color: #cbd5e1;
            border-color: rgba(255, 255, 255, 0.1);
        }
        .gsc-notif-tab-btn:hover {
            background: #f1f5f9;
            color: #0f172a;
            border-color: #cbd5e1;
        }
        .dark .gsc-notif-tab-btn:hover {
            background: #334155;
            color: #ffffff;
        }
        .gsc-notif-tab-btn.active {
            background: #059669;
            color: #ffffff;
            border-color: #059669;
            box-shadow: 0 1px 3px rgba(5, 150, 105, 0.35);
        }
        .dark .gsc-notif-tab-btn.active {
            background: #059669;
            color: #ffffff;
            border-color: #059669;
        }
        .gsc-notif-tab-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 1px 6px;
            font-size: 10px;
            font-weight: 700;
            border-radius: 9999px;
            line-height: 1.2;
            background: #f1f5f9;
            color: #475569;
        }
        .dark .gsc-notif-tab-badge {
            background: rgba(255, 255, 255, 0.1);
            color: #cbd5e1;
        }
        .gsc-notif-tab-badge.highlight {
            background: #fef3c7;
            color: #b45309;
        }
        .dark .gsc-notif-tab-badge.highlight {
            background: rgba(217, 119, 6, 0.25);
            color: #fde68a;
        }
        .gsc-notif-tab-btn.active .gsc-notif-tab-badge {
            background: rgba(255, 255, 255, 0.25);
            color: #ffffff;
        }
        .gsc-notif-empty-box {
            text-align: center;
            padding: 42px 20px;
        }
        .gsc-notif-empty-icon {
            width: 48px;
            height: 48px;
            margin: 0 auto 12px auto;
            border-radius: 50%;
            background: #ecfdf5;
            color: #059669;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .dark .gsc-notif-empty-icon {
            background: rgba(5, 150, 105, 0.15);
            color: #34d399;
        }
        .gsc-notif-empty-title {
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
        }
        .dark .gsc-notif-empty-title {
            color: #f1f5f9;
        }
        .gsc-notif-empty-desc {
            font-size: 11px;
            color: #64748b;
            line-height: 1.4;
        }
        .dark .gsc-notif-empty-desc {
            color: #94a3b8;
        }
    </style>

    <x-filament::modal
        :alignment="$hasAnyNotifications ? null : Alignment::Center"
        aria-labelledby="database-notifications.heading"
        close-button
        :description="$hasAnyNotifications ? null : __('filament-notifications::database.modal.empty.description')"
        :extra-modal-window-attribute-bag="
            new FilamentComponentAttributeBag([
                'autofocus' => true,
                'tabindex' => '-1',
            ])
        "
        :heading="$hasAnyNotifications ? null : __('filament-notifications::database.modal.empty.heading')"
        :icon="$hasAnyNotifications ? null : Heroicon::OutlinedBellSlash"
        :icon-alias="
            $hasAnyNotifications
            ? null
            : NotificationsIconAlias::DATABASE_MODAL_EMPTY_STATE
        "
        :icon-color="$hasAnyNotifications ? null : 'gray'"
        id="database-notifications"
        slide-over
        :sticky-header="$hasAnyNotifications"
        teleport="body"
        width="md"
        class="fi-no-database"
        :attributes="
            new FilamentComponentAttributeBag([
                'wire:poll.' . $pollingInterval => $pollingInterval ? '' : false,
            ])
        "
    >
        @if ($trigger = $this->getTrigger())
            <x-slot name="trigger">
                {{ $trigger->with(['unreadNotificationsCount' => $unreadNotificationsCount]) }}
            </x-slot>
        @endif

        @if ($hasAnyNotifications)
            <x-slot name="header">
                <div class="gsc-notif-header-wrapper">
                    <div class="gsc-notif-header-top">
                        <h2
                            id="database-notifications.heading"
                            class="fi-modal-heading"
                        >
                            {{ __('filament-notifications::database.modal.heading') }}

                            @if ($unreadNotificationsCount)
                                <span
                                    {{
                                        (new FilamentComponentAttributeBag)->color(BadgeComponent::class, 'primary')->class([
                                            'fi-badge fi-size-xs',
                                        ])
                                    }}
                                >
                                    {{ $unreadNotificationsCount }}
                                </span>
                            @endif
                        </h2>

                        <div class="fi-ac">
                            @if ($unreadNotificationsCount && $this->markAllNotificationsAsReadAction?->isVisible())
                                {{ $this->markAllNotificationsAsReadAction }}
                            @endif

                            @if ($this->clearNotificationsAction?->isVisible())
                                {{ $this->clearNotificationsAction }}
                            @endif
                        </div>
                    </div>

                    {{-- Navigasi Tab Kategori (Sticky bersama Header) --}}
                    <div class="gsc-notif-tabs-bar">
                        {{-- Tab: Semua --}}
                        <button
                            type="button"
                            wire:click="setTab('all')"
                            class="gsc-notif-tab-btn {{ $activeTab === 'all' ? 'active' : '' }}"
                        >
                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                            <span>Semua</span>
                            @if(($tabCounts['all'] ?? 0) > 0)
                                <span class="gsc-notif-tab-badge">
                                    {{ $tabCounts['all'] }}
                                </span>
                            @endif
                        </button>

                        {{-- Tab: Donasi --}}
                        <button
                            type="button"
                            wire:click="setTab('donasi')"
                            class="gsc-notif-tab-btn {{ $activeTab === 'donasi' ? 'active' : '' }}"
                        >
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            <span>Donasi</span>
                            @if(($tabCounts['donasi'] ?? 0) > 0)
                                <span class="gsc-notif-tab-badge highlight">
                                    {{ $tabCounts['donasi'] }}
                                </span>
                            @endif
                        </button>

                        {{-- Tab: Laporan Aktivitas --}}
                        <button
                            type="button"
                            wire:click="setTab('aktivitas_harian')"
                            class="gsc-notif-tab-btn {{ $activeTab === 'aktivitas_harian' ? 'active' : '' }}"
                        >
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span>Laporan</span>
                            @if(($tabCounts['aktivitas_harian'] ?? 0) > 0)
                                <span class="gsc-notif-tab-badge highlight">
                                    {{ $tabCounts['aktivitas_harian'] }}
                                </span>
                            @endif
                        </button>

                        {{-- Tab: Izin & Cuti --}}
                        <button
                            type="button"
                            wire:click="setTab('izin_cuti')"
                            class="gsc-notif-tab-btn {{ $activeTab === 'izin_cuti' ? 'active' : '' }}"
                        >
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span>Izin & Cuti</span>
                            @if(($tabCounts['izin_cuti'] ?? 0) > 0)
                                <span class="gsc-notif-tab-badge highlight">
                                    {{ $tabCounts['izin_cuti'] }}
                                </span>
                            @endif
                        </button>
                    </div>
                </div>
            </x-slot>

            @if ($hasFilteredNotifications)
                <div
                    aria-label="{{ __('filament-notifications::database.modal.heading') }}"
                    role="list"
                    class="fi-no-notifications"
                >
                    @foreach ($notifications as $notification)
                        <div
                            role="listitem"
                            wire:key="{{ $notification->getKey() }}.database-notifications.ctn"
                            @class([
                                'fi-no-notification-read-ctn' => ! $notification->unread(),
                                'fi-no-notification-unread-ctn' => $notification->unread(),
                            ])
                        >
                            @if ($notification->unread())
                                <span class="fi-sr-only">
                                    {{ __('filament-notifications::database.modal.unread_label') }}
                                </span>
                            @endif

                            {{ $this->getNotification($notification)->inline() }}
                        </div>
                    @endforeach
                </div>
            @else
                <div class="gsc-notif-empty-box">
                    <div class="gsc-notif-empty-icon">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="gsc-notif-empty-title">Tidak ada pengajuan {{ strtolower($this->getActiveTabLabel()) }}</div>
                    <div class="gsc-notif-empty-desc">Semua data pada kategori ini telah selesai ditinjau.</div>
                </div>
            @endif

            @if ($broadcastChannel = $this->getBroadcastChannel())
                @script
                    <script>
                        setUpFilamentBroadcastNotifications({
                            $wire,
                            broadcastChannel: @js($broadcastChannel),
                            event: '.database-notifications.sent',
                            onNotification: () => $wire.call('$refresh'),
                        })
                    </script>
                @endscript
            @endif

            @if ($isPaginated && $hasFilteredNotifications)
                <x-slot name="footer">
                    <x-filament::pagination :paginator="$notifications" />
                </x-slot>
            @endif
        @endif
    </x-filament::modal>
</div>
