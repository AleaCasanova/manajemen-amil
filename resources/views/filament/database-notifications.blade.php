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
                <div>
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
            </x-slot>

            {{-- Tab Bar Pilihan Kategori --}}
            <div class="-mx-6 -mt-6 mb-4 px-4 py-2.5 border-b border-gray-200 dark:border-white/10 bg-gray-50/80 dark:bg-white/[0.03]">
                <div class="flex items-center gap-1.5 overflow-x-auto pb-0.5 text-xs">
                    {{-- Tab: Semua --}}
                    <button
                        type="button"
                        wire:click="setTab('all')"
                        @class([
                            'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full font-medium transition-all whitespace-nowrap cursor-pointer text-xs',
                            'bg-emerald-600 text-white shadow-xs' => $activeTab === 'all',
                            'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/70 border border-gray-200 dark:border-gray-700' => $activeTab !== 'all',
                        ])
                    >
                        <span>Semua</span>
                        @if(($tabCounts['all'] ?? 0) > 0)
                            <span @class([
                                'px-1.5 py-0.2 rounded-full text-[10px] font-bold',
                                'bg-white/25 text-white' => $activeTab === 'all',
                                'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200' => $activeTab !== 'all',
                            ])>
                                {{ $tabCounts['all'] }}
                            </span>
                        @endif
                    </button>

                    {{-- Tab: Donasi --}}
                    <button
                        type="button"
                        wire:click="setTab('donasi')"
                        @class([
                            'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full font-medium transition-all whitespace-nowrap cursor-pointer text-xs',
                            'bg-emerald-600 text-white shadow-xs' => $activeTab === 'donasi',
                            'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/70 border border-gray-200 dark:border-gray-700' => $activeTab !== 'donasi',
                        ])
                    >
                        <x-filament::icon icon="heroicon-m-banknotes" class="w-3.5 h-3.5" />
                        <span>Donasi</span>
                        @if(($tabCounts['donasi'] ?? 0) > 0)
                            <span @class([
                                'px-1.5 py-0.2 rounded-full text-[10px] font-bold',
                                'bg-white/25 text-white' => $activeTab === 'donasi',
                                'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300' => $activeTab !== 'donasi',
                            ])>
                                {{ $tabCounts['donasi'] }}
                            </span>
                        @endif
                    </button>

                    {{-- Tab: Laporan --}}
                    <button
                        type="button"
                        wire:click="setTab('aktivitas_harian')"
                        @class([
                            'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full font-medium transition-all whitespace-nowrap cursor-pointer text-xs',
                            'bg-emerald-600 text-white shadow-xs' => $activeTab === 'aktivitas_harian',
                            'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/70 border border-gray-200 dark:border-gray-700' => $activeTab !== 'aktivitas_harian',
                        ])
                    >
                        <x-filament::icon icon="heroicon-m-document-text" class="w-3.5 h-3.5" />
                        <span>Laporan</span>
                        @if(($tabCounts['aktivitas_harian'] ?? 0) > 0)
                            <span @class([
                                'px-1.5 py-0.2 rounded-full text-[10px] font-bold',
                                'bg-white/25 text-white' => $activeTab === 'aktivitas_harian',
                                'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300' => $activeTab !== 'aktivitas_harian',
                            ])>
                                {{ $tabCounts['aktivitas_harian'] }}
                            </span>
                        @endif
                    </button>

                    {{-- Tab: Izin & Cuti --}}
                    <button
                        type="button"
                        wire:click="setTab('izin_cuti')"
                        @class([
                            'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full font-medium transition-all whitespace-nowrap cursor-pointer text-xs',
                            'bg-emerald-600 text-white shadow-xs' => $activeTab === 'izin_cuti',
                            'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/70 border border-gray-200 dark:border-gray-700' => $activeTab !== 'izin_cuti',
                        ])
                    >
                        <x-filament::icon icon="heroicon-m-calendar" class="w-3.5 h-3.5" />
                        <span>Izin & Cuti</span>
                        @if(($tabCounts['izin_cuti'] ?? 0) > 0)
                            <span @class([
                                'px-1.5 py-0.2 rounded-full text-[10px] font-bold',
                                'bg-white/25 text-white' => $activeTab === 'izin_cuti',
                                'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300' => $activeTab !== 'izin_cuti',
                            ])>
                                {{ $tabCounts['izin_cuti'] }}
                            </span>
                        @endif
                    </button>
                </div>
            </div>

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
                <div class="py-12 px-6 text-center text-sm text-gray-500 dark:text-gray-400">
                    <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-emerald-50 dark:bg-emerald-950/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                        <x-filament::icon icon="heroicon-o-check-circle" class="w-7 h-7" />
                    </div>
                    <p class="font-semibold text-gray-800 dark:text-gray-200">Tidak ada {{ strtolower($this->getActiveTabLabel()) }} yang menunggu</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Semua pengajuan pada kategori ini telah selesai ditinjau.</p>
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
