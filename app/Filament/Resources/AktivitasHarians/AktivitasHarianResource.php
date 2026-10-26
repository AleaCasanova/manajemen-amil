<?php

namespace App\Filament\Resources\AktivitasHarians;

use App\Filament\Resources\AktivitasHarians\Pages\ListAktivitasHarians;
use App\Filament\Resources\AktivitasHarians\Tables\AktivitasHariansTable;
use App\Models\Amil;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AktivitasHarianResource extends Resource
{
    protected static ?string $model = Amil::class;

    protected static ?string $slug = 'aktivitas-harians';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Manajemen Amil & SDM';

    protected static ?string $navigationLabel = 'Aktivitas Harian';

    protected static ?string $modelLabel = 'Aktivitas Harian Amil';

    protected static ?string $pluralModelLabel = 'Aktivitas Harian Amil';

    protected static ?int $navigationSort = 4;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['divisi', 'aktivitasHarians']);
    }

    public static function table(Table $table): Table
    {
        return AktivitasHariansTable::configure($table);
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
            'index' => ListAktivitasHarians::route('/'),
        ];
    }
}
