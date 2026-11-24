<?php

namespace App\Filament\Resources\CashAdvances\Tables;

use App\Models\AkunKeuangan;
use App\Models\CashAdvance;
use App\Models\KategoriKeuangan;
use App\Services\CashAdvanceService;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class CashAdvancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('tgl_pencairan', 'desc')
            ->columns([
                TextColumn::make('kode_ca')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->badge()
                    ->color('primary')
                    ->copyable(),

                TextColumn::make('tgl_pencairan')
                    ->label('Tgl Cair')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('tipe')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'cash_bon' ? 'gray' : 'info')
                    ->formatStateUsing(fn (string $state): string => $state === 'cash_bon' ? 'Cash Bon' : 'Cash Advance'),

                TextColumn::make('nama_pj')
                    ->label('Penanggung Jawab')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (CashAdvance $record): ?string => $record->divisi?->nama),

                TextColumn::make('keperluan')
                    ->label('Keperluan')
                    ->searchable()
                    ->limit(35)
                    ->tooltip(fn (CashAdvance $record): string => (string) $record->keperluan),

                TextColumn::make('akunKeuangan.nama')
                    ->label('Sumber Dana')
                    ->badge()
                    ->color('info')
                    ->toggleable(),

                TextColumn::make('nominal_dicairkan')
                    ->label('Dicairkan')
                    ->money('IDR', locale: 'id')
                    ->weight('bold')
                    ->sortable(),

                TextColumn::make('nominal_realisasi')
                    ->label('Realisasi LPJ')
                    ->money('IDR', locale: 'id')
                    ->color('info')
                    ->toggleable(),

                TextColumn::make('sisa_tanggungan')
                    ->label('Sisa Tanggungan')
                    ->state(fn (CashAdvance $record): float => $record->sisa_tanggungan)
                    ->money('IDR', locale: 'id')
                    ->weight('bold')
                    ->color(fn (CashAdvance $record): string => $record->sisa_tanggungan > 0 ? 'warning' : 'success'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (CashAdvance $record): string => $record->status_color)
                    ->formatStateUsing(fn (CashAdvance $record): string => $record->status_label),
            ])
            ->filters([
                SelectFilter::make('tipe')
                    ->label('Jenis Uang Muka')
                    ->options([
                        'cash_advance' => 'Cash Advance',
                        'cash_bon' => 'Cash Bon',
                    ]),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'aktif' => 'Aktif / Belum Lunas',
                        'sebagian' => 'Cicilan Sebagian',
                        'selesai' => 'Selesai / Lunas',
                        'dibatalkan' => 'Dibatalkan',
                    ]),
                SelectFilter::make('akun_keuangan_id')
                    ->label('Akun Sumber')
                    ->relationship('akunKeuangan', 'nama')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                Action::make('selesaikanLpj')
                    ->label('Selesaikan LPJ')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('success')
                    ->visible(fn (CashAdvance $record): bool => $record->tipe === 'cash_advance' && $record->status === 'aktif')
                    ->modalHeading(fn (CashAdvance $record): string => "Penyelesaian LPJ {$record->kode_ca}")
                    ->modalDescription(fn (CashAdvance $record): string => "PJ: {$record->nama_pj} — Dicairkan: Rp ".number_format((float) $record->nominal_dicairkan, 0, ',', '.'))
                    ->modalWidth('4xl')
                    ->form([
                        Grid::make(2)
                            ->components([
                                Select::make('akun_penyelesaian_id')
                                    ->label('Akun Kas / Bank Penyelesaian')
                                    ->options(fn (): array => self::getAkunOptions())
                                    ->default(fn (CashAdvance $record): int => (int) $record->akun_keuangan_id)
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                DatePicker::make('tgl_selesai')
                                    ->label('Tanggal Penyelesaian LPJ')
                                    ->default(today())
                                    ->required(),
                            ]),

                        Repeater::make('items')
                            ->label('Rincian Nota / Beban Realisasi')
                            ->defaultItems(1)
                            ->minItems(1)
                            ->live()
                            ->schema([
                                Grid::make(4)
                                    ->components([
                                        Select::make('kategori_keuangan_id')
                                            ->label('Akun COA Beban')
                                            ->options(fn (): array => KategoriKeuangan::query()
                                                ->active()
                                                ->postable()
                                                ->untukArahKas('keluar')
                                                ->orderBy('kode_coa')
                                                ->orderBy('nama')
                                                ->get()
                                                ->mapWithKeys(fn (KategoriKeuangan $k): array => [$k->id => $k->label_lengkap])
                                                ->all())
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->columnSpan(2),

                                        TextInput::make('nominal')
                                            ->label('Nominal')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->minValue(1)
                                            ->live(onBlur: true)
                                            ->required(),

                                        DatePicker::make('tgl_realisasi')
                                            ->label('Tgl Nota')
                                            ->default(today()),
                                    ]),

                                Grid::make(2)
                                    ->components([
                                        TextInput::make('uraian')
                                            ->label('Uraian Pengeluaran')
                                            ->placeholder('Contoh: Konsumsi kegiatan santunan')
                                            ->required()
                                            ->maxLength(255),

                                        FileUpload::make('bukti_nota')
                                            ->label('Foto Nota (Opsional)')
                                            ->disk('public')
                                            ->directory('bukti-lpj-ca')
                                            ->image()
                                            ->maxSize(2048),
                                    ]),
                            ]),

                        Grid::make(3)
                            ->components([
                                TextEntry::make('total_realisasi_preview')
                                    ->label('Total Realisasi Nota')
                                    ->state(function (Get $get): string {
                                        $total = collect($get('items') ?? [])->sum(fn (array $item): float => (float) ($item['nominal'] ?? 0));

                                        return 'Rp '.number_format($total, 0, ',', '.');
                                    }),

                                TextEntry::make('selisih_lpj_preview')
                                    ->label('Status Selisih vs Uang Muka')
                                    ->state(function (Get $get, CashAdvance $record): string {
                                        $realisasi = collect($get('items') ?? [])->sum(fn (array $item): float => (float) ($item['nominal'] ?? 0));
                                        $selisih = (float) $record->nominal_dicairkan - $realisasi;

                                        if ($selisih > 0.01) {
                                            return 'Sisa Dikembalikan: Rp '.number_format($selisih, 0, ',', '.');
                                        }

                                        if ($selisih < -0.01) {
                                            return 'Kurang Bayar (Reimburse): Rp '.number_format(abs($selisih), 0, ',', '.');
                                        }

                                        return 'Pas (Nol Selisih)';
                                    }),

                                TextEntry::make('catatan_metode')
                                    ->label('Metode Pembukuan')
                                    ->state('Gross Settlement (101.09.000 + COA Beban)'),
                            ]),

                        Textarea::make('catatan')
                            ->label('Catatan Penyelesaian LPJ')
                            ->rows(2),
                    ])
                    ->action(function (CashAdvance $record, array $data): void {
                        $ca = app(CashAdvanceService::class)->selesaikanLpj(
                            $record,
                            $data['items'] ?? [],
                            (int) $data['akun_penyelesaian_id'],
                            (string) $data['tgl_selesai'],
                            $data['catatan'] ?? null,
                            Auth::id()
                        );

                        Notification::make()
                            ->title('LPJ Cash Advance Diselesaikan')
                            ->body("{$ca->kode_ca} selesai. Total realisasi: Rp ".number_format((float) $ca->nominal_realisasi, 0, ',', '.'))
                            ->success()
                            ->send();
                    }),

                Action::make('bayarCashBon')
                    ->label('Bayar / Lunasi')
                    ->icon('heroicon-o-banknotes')
                    ->color('warning')
                    ->visible(fn (CashAdvance $record): bool => $record->tipe === 'cash_bon' && in_array($record->status, ['aktif', 'sebagian'], true))
                    ->modalHeading(fn (CashAdvance $record): string => "Pembayaran Cash Bon {$record->kode_ca}")
                    ->modalDescription(fn (CashAdvance $record): string => "PJ: {$record->nama_pj} — Sisa Tanggungan: Rp ".number_format($record->sisa_tanggungan, 0, ',', '.'))
                    ->modalWidth('lg')
                    ->form([
                        Grid::make(2)
                            ->components([
                                Select::make('akun_penerima_id')
                                    ->label('Masuk ke Rekening / Kas')
                                    ->options(fn (): array => self::getAkunOptions())
                                    ->default(fn (CashAdvance $record): int => (int) $record->akun_keuangan_id)
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                DatePicker::make('tgl_bayar')
                                    ->label('Tanggal Pengembalian')
                                    ->default(today())
                                    ->required(),
                            ]),

                        TextInput::make('nominal_bayar')
                            ->label('Nominal Pembayaran / Cicilan')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(fn (CashAdvance $record): float => $record->sisa_tanggungan)
                            ->minValue(1)
                            ->maxValue(fn (CashAdvance $record): float => $record->sisa_tanggungan)
                            ->required(),

                        FileUpload::make('bukti_path')
                            ->label('Bukti Transfer / Setor (Opsional)')
                            ->disk('public')
                            ->directory('bukti-kasbon')
                            ->image()
                            ->maxSize(2048),

                        Textarea::make('catatan')
                            ->label('Catatan Pembayaran')
                            ->rows(2),
                    ])
                    ->action(function (CashAdvance $record, array $data): void {
                        $cb = app(CashAdvanceService::class)->bayarCashBon(
                            $record,
                            (float) $data['nominal_bayar'],
                            (int) $data['akun_penerima_id'],
                            (string) $data['tgl_bayar'],
                            $data['catatan'] ?? null,
                            $data['bukti_path'] ?? null,
                            Auth::id()
                        );

                        Notification::make()
                            ->title('Pembayaran Cash Bon Tercatat')
                            ->body("{$cb->kode_ca} — Status: {$cb->status_label}. Sisa tanggungan: Rp ".number_format($cb->sisa_tanggungan, 0, ',', '.'))
                            ->success()
                            ->send();
                    }),

                ViewAction::make(),
            ]);
    }

    /**
     * @return array<int, string>
     */
    private static function getAkunOptions(): array
    {
        return AkunKeuangan::withMutasiTotals()
            ->where('is_active', true)
            ->orderBy('kode_coa')
            ->orderBy('id')
            ->get()
            ->mapWithKeys(function (AkunKeuangan $akun): array {
                $saldo = number_format($akun->saldo_sekarang, 0, ',', '.');
                $prefix = $akun->kode_coa ? "[{$akun->kode_coa}] " : '';

                return [$akun->id => "{$prefix}{$akun->nama} (Saldo: Rp {$saldo})"];
            })
            ->all();
    }
}
