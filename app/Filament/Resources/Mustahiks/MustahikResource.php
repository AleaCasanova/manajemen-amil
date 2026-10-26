<?php

namespace App\Filament\Resources\Mustahiks;

use App\Filament\Resources\Mustahiks\Pages\CreateMustahik;
use App\Filament\Resources\Mustahiks\Pages\EditMustahik;
use App\Filament\Resources\Mustahiks\Pages\ListMustahiks;
use App\Filament\Resources\Mustahiks\Pages\ViewMustahik;
use App\Filament\Resources\Mustahiks\Schemas\MustahikForm;
use App\Filament\Resources\Mustahiks\Schemas\MustahikInfolist;
use App\Filament\Resources\Mustahiks\Tables\MustahiksTable;
use App\Models\Mustahik;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class MustahikResource extends Resource
{
    protected static ?string $model = Mustahik::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|\UnitEnum|null $navigationGroup = 'Penyaluran & Mustahik';

    protected static ?string $navigationLabel = 'Mustahik (8 Asnaf)';

    protected static ?string $modelLabel = 'Mustahik';

    protected static ?string $pluralModelLabel = 'Mustahik / Penerima Manfaat';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return MustahikForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MustahikInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MustahiksTable::configure($table);
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
            'index' => ListMustahiks::route('/'),
            'create' => CreateMustahik::route('/create'),
            'view' => ViewMustahik::route('/{record}'),
            'edit' => EditMustahik::route('/{record}/edit'),
        ];
    }
}
