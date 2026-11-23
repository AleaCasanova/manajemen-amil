<div class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($akuns as $akun)
            @php
                $masuk = (float) $akun->transaksiKeuangans()->where('jenis', 'masuk')->sum('nominal');
                $keluar = (float) $akun->transaksiKeuangans()->where('jenis', 'keluar')->sum('nominal');
                $saldo = ((float) $akun->saldo_awal) + $masuk - $keluar;
                $badgeColor = match($akun->tipe) {
                    'kas' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
                    'bank' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
                    'e_wallet' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300',
                    default => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300',
                };
                $badgeText = match($akun->tipe) {
                    'kas' => 'Kas Tunai',
                    'bank' => 'Rekening Bank',
                    'e_wallet' => 'E-Wallet / QRIS',
                    default => strtoupper($akun->tipe),
                };
            @endphp
            <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900/60 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full {{ $badgeColor }}">
                            {{ $badgeText }}
                        </span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            ID: #{{ $akun->id }}
                        </span>
                    </div>

                    <h4 class="font-bold text-gray-900 dark:text-gray-100 text-base mb-1">
                        {{ $akun->nama }}
                    </h4>

                    @if($akun->nomor_rekening || $akun->atas_nama)
                        <div class="text-xs text-gray-600 dark:text-gray-400 mb-3 space-y-0.5">
                            @if($akun->nomor_rekening)
                                <div>No. Rek: <span class="font-mono font-medium text-gray-800 dark:text-gray-200">{{ $akun->nomor_rekening }}</span></div>
                            @endif
                            @if($akun->atas_nama)
                                <div>a.n: <span class="font-medium text-gray-800 dark:text-gray-200">{{ $akun->atas_nama }}</span></div>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="pt-3 border-t border-gray-200 dark:border-gray-800">
                    <div class="text-xs text-gray-500 dark:text-gray-400">Saldo Terkini</div>
                    <div class="text-lg font-extrabold text-emerald-600 dark:text-emerald-400">
                        Rp {{ number_format($saldo, 0, ',', '.') }}
                    </div>

                    <div class="grid grid-cols-2 gap-2 mt-2 pt-2 border-t border-gray-200/60 dark:border-gray-800/60 text-xs">
                        <div>
                            <span class="text-gray-500 dark:text-gray-400 block">Total Masuk</span>
                            <span class="font-semibold text-emerald-600 dark:text-emerald-400">+ Rp {{ number_format($masuk, 0, ',', '.') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400 block">Total Keluar</span>
                            <span class="font-semibold text-rose-600 dark:text-rose-400">- Rp {{ number_format($keluar, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-2 py-8 text-center text-gray-500 text-sm">
                Belum ada akun kas atau rekening bank yang terdaftar.
            </div>
        @endforelse
    </div>
</div>
