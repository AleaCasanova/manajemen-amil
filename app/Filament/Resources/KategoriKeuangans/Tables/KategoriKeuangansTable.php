<?php

namespace App\Filament\Resources\KategoriKeuangans\Tables;

use App\Models\KategoriKeuangan;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class KategoriKeuangansTable
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
                    ->color(fn (KategoriKeuangan $record): string => $record->is_postable ? 'primary' : 'gray')
                    ->placeholder('-'),

                TextColumn::make('nama')
                    ->label('Nama Akun')
                    ->searchable()
                    ->sortable()
                    ->weight(fn (KategoriKeuangan $record): string => $record->is_postable ? 'normal' : 'bold')
                    ->formatStateUsing(function (string $state, KategoriKeuangan $record): string {
                        $indent = str_repeat('— ', max(0, ((int) $record->level) - 1));

                        return $indent.$state;
                    })
                    ->description(fn (KategoriKeuangan $record): ?string => $record->parent
                        ? 'Induk: '.$record->parent->label_lengkap
                        : null),

                TextColumn::make('kelompok')
                    ->label('Kelompok')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'aset' => 'info',
                        'kewajiban' => 'warning',
                        'saldo_dana' => 'gray',
                        'penerimaan' => 'success',
                        'penyaluran' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => KategoriKeuangan::KELOMPOK_LABELS[$state] ?? ucfirst((string) $state)),

                TextColumn::make('pos_dana')
                    ->label('Pos Dana')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'zakat' => 'success',
                        'infak_terikat' => 'primary',
                        'infak_umum' => 'info',
                        'amil' => 'warning',
                        'non_halal' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => KategoriKeuangan::POS_DANA_LABELS[$state] ?? ucfirst((string) $state)),

                TextColumn::make('jenis')
                    ->label('Arah Kas')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'masuk' => 'success',
                        'keluar' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => strtoupper($state)),

                IconColumn::make('is_postable')
                    ->label('Postable')
                    ->boolean(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('pos_dana')
                    ->label('Pos Dana')
                    ->options(KategoriKeuangan::POS_DANA_LABELS),
                SelectFilter::make('jenis')
                    ->label('Arah Arus Kas')
                    ->options([
                        'masuk' => 'Masuk',
                        'keluar' => 'Keluar',
                        'netral' => 'Netral (Neraca)',
                    ]),
                TernaryFilter::make('is_postable')
                    ->label('Akun Postable'),
                TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->visible(fn (KategoriKeuangan $record): bool => (int) ($record->transaksi_keuangans_count ?? 0) === 0
                        && (int) ($record->children_count ?? 0) === 0),
            ]);
    }
}
