<?php

namespace App\Filament\Resources\IzinCutis;

use App\Filament\Resources\IzinCutis\Pages\ListIzinCutis;
use App\Filament\Resources\IzinCutis\Tables\IzinCutisTable;
use App\Models\Amil;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class IzinCutiResource extends Resource
{
    protected static ?string $model = Amil::class;

    protected static ?string $slug = 'izin-cutis';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static string|\UnitEnum|null $navigationGroup = 'Manajemen Amil & SDM';

    protected static ?string $navigationLabel = 'Izin & Cuti';

    protected static ?string $modelLabel = 'Izin & Cuti Amil';

    protected static ?string $pluralModelLabel = 'Izin & Cuti Amil';

    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['divisi', 'izinCutis', 'saldoCutis']);
    }

    public static function table(Table $table): Table
    {
        return IzinCutisTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIzinCutis::route('/'),
        ];
    }
}
