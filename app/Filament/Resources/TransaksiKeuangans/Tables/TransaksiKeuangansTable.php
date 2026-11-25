<?php

namespace App\Filament\Resources\TransaksiKeuangans\Tables;

use App\Models\KategoriKeuangan;
use App\Models\TransaksiKeuangan;
use App\Services\TransaksiKeuanganService;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TransaksiKeuangansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('tgl_transaksi', 'desc')
            ->groups([
                Group::make('akunKeuangan.nama')
                    ->label('Rekening Kas / Bank')
                    ->collapsible(),
                Group::make('tgl_transaksi')
                    ->label('Tanggal')
                    ->date(),
                Group::make('kategoriKeuangan.nama')
                    ->label('Akun COA'),
            ])
            ->columns([
                TextColumn::make('no')
                    ->label('No')
                    ->rowIndex()
                    ->alignCenter(),

                TextColumn::make('tgl_transaksi')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable()
                    ->description(fn (TransaksiKeuangan $r): ?string => $r->no_voucher ? "Voucher: {$r->no_voucher}" : $r->kode_transaksi),

                TextColumn::make('kode_transaksi')
                    ->label('Kode TRX')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('tipe_transaksi')
                    ->label('Jenis / Tipe')
                    ->badge()
                    ->color(fn (TransaksiKeuangan $r): string => match (true) {
                        $r->is_transfer => 'info',
                        str_starts_with((string) $r->tipe_transaksi, 'cash_advance') => 'warning',
                        str_starts_with((string) $r->tipe_transaksi, 'cash_bon') => 'gray',
                        $r->jenis === 'masuk' => 'success',
                        default => 'danger',
                    })
                    ->formatStateUsing(fn (string $state, TransaksiKeuangan $r): string => match ($state) {
                        'transfer' => $r->jenis === 'masuk' ? 'TRANSFER MASUK' : 'TRANSFER KELUAR',
                        'cash_advance_keluar' => 'CA KELUAR',
                        'cash_advance_kembali' => 'CA KEMBALI',
                        'cash_advance_realisasi' => 'LPJ CA',
                        'cash_bon_keluar' => 'KASBON KELUAR',
                        'cash_bon_kembali' => 'KASBON LUNAS',
                        default => $r->jenis === 'masuk' ? 'MASUK' : 'KELUAR',
                    }),

                TextColumn::make('kategoriKeuangan.nama')
                    ->label('Akun COA')
                    ->badge()
                    ->color(fn (TransaksiKeuangan $r): string => match ($r->kategoriKeuangan?->pos_dana) {
                        'zakat' => 'success',
                        'infak_terikat' => 'primary',
                        'infak_umum' => 'info',
                        'amil' => 'warning',
                        'non_halal' => 'danger',
                        default => 'gray',
                    })
                    ->description(fn (TransaksiKeuangan $r): ?string => $r->kategoriKeuangan?->kode_coa)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('akunKeuangan.nama')
                    ->label('Akun Kas / Bank')
                    ->searchable()
                    ->description(function (TransaksiKeuangan $r): ?string {
                        $parts = array_filter([
                            $r->akunKeuangan?->kode_coa,
                            $r->akunKeuangan?->nomor_rekening ? 'Rek: '.$r->akunKeuangan->nomor_rekening : null,
                        ]);

                        return $parts !== [] ? implode(' • ', $parts) : null;
                    }),

                TextColumn::make('nominal')
                    ->label('Nominal (Rp)')
                    ->weight('bold')
                    ->sortable()
                    ->formatStateUsing(fn (TransaksiKeuangan $r): string => ($r->jenis === 'masuk' ? '+ ' : '- ').'Rp '.number_format((float) $r->nominal, 0, ',', '.'))
                    ->color(fn (TransaksiKeuangan $r): string => $r->jenis === 'masuk' ? 'success' : 'danger'),

                TextColumn::make('pihak_terkait')
                    ->label('Uraian / Pihak Terkait')
                    ->searchable()
                    ->default('-')
                    ->description(fn (TransaksiKeuangan $r): ?string => $r->sub_program)
                    ->toggleable(),

                TextColumn::make('pjAmil.nama')
                    ->label('PJ')
                    ->default('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('divisi.nama')
                    ->label('Departemen')
                    ->default('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('reff')
                    ->label('Reff')
                    ->default('-')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(35)
                    ->tooltip(fn ($record) => $record->keterangan)
                    ->searchable(),

                ImageColumn::make('bukti_transaksi')
                    ->label('Bukti')
                    ->disk('public')
                    ->square()
                    ->toggleable(),

                TextColumn::make('creator.name')
                    ->label('Petugas')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('jenis')
                    ->label('Arah Kas')
                    ->options([
                        'masuk' => 'Kas Masuk (Debet)',
                        'keluar' => 'Kas Keluar (Kredit)',
                    ]),

                SelectFilter::make('tipe_transaksi')
                    ->label('Tipe Transaksi')
                    ->options(TransaksiKeuangan::TIPE_LABELS),

                SelectFilter::make('akun_keuangan_id')
                    ->label('Akun Kas / Bank')
                    ->relationship('akunKeuangan', 'nama')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('kategori_keuangan_id')
                    ->label('Akun COA')
                    ->relationship('kategoriKeuangan', 'nama', fn (Builder $query): Builder => $query->where('is_postable', true)->orderBy('kode_coa'))
                    ->getOptionLabelFromRecordUsing(fn (KategoriKeuangan $record): string => $record->label_lengkap)
                    ->searchable()
                    ->preload(),

                SelectFilter::make('pos_dana')
                    ->label('Pos Dana Syar\'i')
                    ->options(KategoriKeuangan::POS_DANA_LABELS)
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['value'])) {
                            return $query;
                        }

                        return $query->whereHas('kategoriKeuangan', fn (Builder $q) => $q->where('pos_dana', $data['value']));
                    }),

                SelectFilter::make('pj_amil_id')
                    ->label('PJ Amil')
                    ->relationship('pjAmil', 'nama')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('divisi_id')
                    ->label('Departemen')
                    ->relationship('divisi', 'nama')
                    ->searchable()
                    ->preload(),

                Filter::make('tgl_transaksi')
                    ->form([
                        DatePicker::make('dari_tanggal')->label('Dari Tanggal'),
                        DatePicker::make('sampai_tanggal')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('tgl_transaksi', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('tgl_transaksi', '<=', $date),
                            );
                    }),

                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),

                EditAction::make()
                    ->hidden(fn (TransaksiKeuangan $r): bool => $r->is_transfer || $r->cash_advance_id !== null),

                DeleteAction::make()
                    ->using(function (TransaksiKeuangan $record): bool {
                        if ($record->is_transfer) {
                            app(TransaksiKeuanganService::class)->hapusTransfer($record);

                            return true;
                        }

                        return (bool) $record->delete();
                    }),

                RestoreAction::make()
                    ->using(function (TransaksiKeuangan $record): bool {
                        if ($record->is_transfer) {
                            app(TransaksiKeuanganService::class)->pulihkanTransfer($record);

                            return true;
                        }

                        return (bool) $record->restore();
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
