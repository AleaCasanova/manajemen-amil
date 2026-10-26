<?php

namespace App\Filament\Resources\Amils;

use App\Filament\Resources\Amils\Pages\CreateAmil;
use App\Filament\Resources\Amils\Pages\EditAmil;
use App\Filament\Resources\Amils\Pages\ListAmils;
use App\Filament\Resources\Amils\Pages\ViewAmil;
use App\Filament\Resources\Amils\Schemas\AmilForm;
use App\Filament\Resources\Amils\Schemas\AmilInfolist;
use App\Filament\Resources\Amils\Tables\AmilsTable;
use App\Models\Amil;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class AmilResource extends Resource
{
    protected static ?string $model = Amil::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|\UnitEnum|null $navigationGroup = 'Manajemen Amil & SDM';

    protected static ?string $navigationLabel = 'Manajemen Amil';

    protected static ?string $modelLabel = 'Amil';

    protected static ?string $pluralModelLabel = 'Manajemen Amil';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return AmilForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AmilInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AmilsTable::configure($table);
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
            'index' => ListAmils::route('/'),
            'create' => CreateAmil::route('/create'),
            'view' => ViewAmil::route('/{record}'),
            'edit' => EditAmil::route('/{record}/edit'),
        ];
    }
}
