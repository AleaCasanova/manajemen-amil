<?php

namespace App\Filament\Resources\TransaksiKeuangans\Tables;

use App\Models\TransaksiKeuangan;
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
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TransaksiKeuangansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('tgl_transaksi', 'desc')
            ->columns([
                TextColumn::make('kode_transaksi')
                    ->label('Kode TRX')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->color('primary'),

                TextColumn::make('tgl_transaksi')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('jenis')
                    ->label('Arah Kas')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'masuk' => 'success',
                        'keluar' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => $state === 'masuk' ? 'MASUK' : 'KELUAR'),

                TextColumn::make('kategoriKeuangan.nama')
                    ->label('Pos Kategori')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('akunKeuangan.nama')
                    ->label('Akun Kas / Bank')
                    ->searchable()
                    ->description(fn (TransaksiKeuangan $r) => $r->akunKeuangan?->nomor_rekening ? 'Rek: '.$r->akunKeuangan->nomor_rekening : null),

                TextColumn::make('nominal')
                    ->label('Nominal (Rp)')
                    ->weight('bold')
                    ->sortable()
                    ->formatStateUsing(fn (TransaksiKeuangan $r) => ($r->jenis === 'masuk' ? '+ ' : '- ').'Rp '.number_format((float) $r->nominal, 0, ',', '.'))
                    ->color(fn (TransaksiKeuangan $r) => $r->jenis === 'masuk' ? 'success' : 'danger'),

                TextColumn::make('pihak_terkait')
                    ->label('Pihak Terkait')
                    ->searchable()
                    ->default('-')
                    ->toggleable(),

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
                        'masuk' => 'Kas Masuk',
                        'keluar' => 'Kas Keluar',
                    ]),

                SelectFilter::make('akun_keuangan_id')
                    ->label('Akun Kas / Bank')
                    ->relationship('akunKeuangan', 'nama')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('kategori_keuangan_id')
                    ->label('Pos Kategori')
                    ->relationship('kategoriKeuangan', 'nama')
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
                                $data['dari_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tgl_transaksi', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tgl_transaksi', '<=', $date),
                            );
                    }),

                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
