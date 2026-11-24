<?php

namespace App\Filament\Resources\AkunKeuangans\Tables;

use App\Filament\Resources\TransaksiKeuangans\TransaksiKeuanganResource;
use App\Models\AkunKeuangan;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class AkunKeuangansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('kode_coa', 'asc')
            ->columns([
                TextColumn::make('kode_coa')
                    ->label('Kode COA')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->placeholder('-'),

                TextColumn::make('nama')
                    ->label('Nama Akun / Rekening')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(function (AkunKeuangan $record): ?string {
                        $parts = array_filter([
                            $record->nama_bank,
                            $record->nomor_rekening ? "No. Rek: {$record->nomor_rekening}" : null,
                        ]);

                        return $parts !== [] ? implode(' • ', $parts) : null;
                    }),

                TextColumn::make('tipe')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'kas' => 'warning',
                        'bank' => 'info',
                        'ewallet' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'kas' => 'Kas Tunai',
                        'bank' => 'Bank',
                        'ewallet' => 'E-Wallet',
                        default => ucfirst($state),
                    }),

                TextColumn::make('fungsi')
                    ->label('Fungsi')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (?string $state): string => AkunKeuangan::FUNGSI_LABELS[$state] ?? ($state ? ucfirst($state) : '-'))
                    ->toggleable(),

                TextColumn::make('saldo_awal')
                    ->label('Saldo Awal')
                    ->money('IDR', locale: 'id')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('saldo_sekarang')
                    ->label('Saldo Buku')
                    ->weight('bold')
                    ->state(fn (AkunKeuangan $record): float => $record->saldo_sekarang)
                    ->money('IDR', locale: 'id')
                    ->color(fn (AkunKeuangan $record): string => $record->saldo_sekarang >= 0 ? 'success' : 'danger'),

                TextColumn::make('saldo_riil_terakhir')
                    ->label('Saldo Riil / Rekonsiliasi')
                    ->money('IDR', locale: 'id')
                    ->placeholder('-')
                    ->description(fn (AkunKeuangan $record): ?string => $record->tgl_rekonsiliasi_terakhir
                        ? 'Tgl: '.$record->tgl_rekonsiliasi_terakhir->format('d M Y')
                        : null),

                TextColumn::make('selisih_rekonsiliasi')
                    ->label('Selisih')
                    ->badge()
                    ->state(fn (AkunKeuangan $record): ?string => $record->selisih_rekonsiliasi === null
                        ? null
                        : ($record->selisih_rekonsiliasi == 0
                            ? 'Cocok (Rp 0)'
                            : 'Rp '.number_format($record->selisih_rekonsiliasi, 0, ',', '.')))
                    ->color(fn (AkunKeuangan $record): string => match (true) {
                        $record->selisih_rekonsiliasi === null => 'gray',
                        abs($record->selisih_rekonsiliasi) < 0.01 => 'success',
                        default => 'danger',
                    })
                    ->placeholder('Belum Rekon'),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('tipe')
                    ->label('Tipe Akun')
                    ->options([
                        'kas' => 'Kas Tunai',
                        'bank' => 'Bank',
                        'ewallet' => 'E-Wallet',
                    ]),
                SelectFilter::make('fungsi')
                    ->label('Fungsi Rekening')
                    ->options(AkunKeuangan::FUNGSI_LABELS),
                TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
            ])
            ->recordActions([
                Action::make('rekonsiliasi')
                    ->label('Rekonsiliasi')
                    ->icon('heroicon-o-check-badge')
                    ->color('info')
                    ->modalHeading(fn (AkunKeuangan $record): string => "Rekonsiliasi Saldo: {$record->nama}")
                    ->modalDescription('Bandingkan saldo buku sistem dengan saldo riil rekening koran / brankas terkini.')
                    ->modalWidth('lg')
                    ->form([
                        Grid::make(2)
                            ->components([
                                TextEntry::make('saldo_buku_saat_ini')
                                    ->label('Saldo Buku Sistem')
                                    ->state(fn (AkunKeuangan $record): string => 'Rp '.number_format($record->saldo_sekarang, 0, ',', '.')),

                                DatePicker::make('tgl_rekonsiliasi_terakhir')
                                    ->label('Tanggal Rekonsiliasi')
                                    ->default(today())
                                    ->required(),
                            ]),

                        TextInput::make('saldo_riil_terakhir')
                            ->label('Saldo Riil (Rekening Koran / Fisik)')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(fn (AkunKeuangan $record): float => (float) ($record->saldo_riil_terakhir ?? $record->saldo_sekarang))
                            ->live(onBlur: true)
                            ->required(),

                        TextEntry::make('selisih_preview')
                            ->label('Selisih (Buku - Riil)')
                            ->state(function (Get $get, AkunKeuangan $record): string {
                                $riil = (float) ($get('saldo_riil_terakhir') ?? 0);
                                $selisih = $record->saldo_sekarang - $riil;
                                $prefix = $selisih > 0 ? '+' : '';

                                return $prefix.'Rp '.number_format($selisih, 0, ',', '.');
                            }),
                    ])
                    ->action(function (AkunKeuangan $record, array $data): void {
                        $record->update([
                            'saldo_riil_terakhir' => (float) $data['saldo_riil_terakhir'],
                            'tgl_rekonsiliasi_terakhir' => $data['tgl_rekonsiliasi_terakhir'],
                        ]);

                        Notification::make()
                            ->title('Rekonsiliasi Disimpan')
                            ->body("Saldo riil {$record->nama} diperbarui ke Rp ".number_format((float) $data['saldo_riil_terakhir'], 0, ',', '.'))
                            ->success()
                            ->send();
                    }),

                Action::make('lihatMutasi')
                    ->label('Lihat Mutasi')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('gray')
                    ->url(fn (AkunKeuangan $record): string => TransaksiKeuanganResource::getUrl('index', [
                        'filters' => [
                            'akun_keuangan_id' => ['value' => $record->id],
                        ],
                    ])),

                EditAction::make(),
                DeleteAction::make()
                    ->visible(fn (AkunKeuangan $record): bool => (int) ($record->transaksi_keuangans_count ?? 0) === 0),
            ]);
    }
}
