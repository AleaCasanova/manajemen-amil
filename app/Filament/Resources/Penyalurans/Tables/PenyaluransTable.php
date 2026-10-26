<?php

namespace App\Filament\Resources\Penyalurans\Tables;

use App\Models\Penyaluran;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PenyaluransTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('mustahik.nama')
                    ->label('Penerima (Mustahik)')
                    ->description(fn (Penyaluran $record): string => 'Asnaf: '.ucfirst($record->mustahik?->asnaf ?? '-'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('program.nama')
                    ->label('Program')
                    ->placeholder('Program Umum')
                    ->searchable(),

                TextColumn::make('jenisDana.nama')
                    ->label('Sumber Dana')
                    ->badge()
                    ->color('info'),

                TextColumn::make('jumlah')
                    ->label('Nominal Bantuan')
                    ->money('IDR', locale: 'id')
                    ->weight('bold')
                    ->color('emerald')
                    ->sortable(),

                TextColumn::make('tgl_salur')
                    ->label('Tanggal Salur')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('amil.nama')
                    ->label('Amil Penyalur')
                    ->searchable(),

                ImageColumn::make('bukti')
                    ->label('Dokumentasi')
                    ->disk('public')
                    ->square(),

                TextColumn::make('created_at')
                    ->label('Tercatat Pada')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('program_id')
                    ->label('Program')
                    ->relationship('program', 'nama'),
                SelectFilter::make('jenis_dana_id')
                    ->label('Jenis Dana')
                    ->relationship('jenisDana', 'nama'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
