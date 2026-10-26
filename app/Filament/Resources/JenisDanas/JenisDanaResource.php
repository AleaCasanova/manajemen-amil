<?php

namespace App\Filament\Resources\JenisDanas;

use App\Filament\Resources\JenisDanas\Pages\CreateJenisDana;
use App\Filament\Resources\JenisDanas\Pages\EditJenisDana;
use App\Filament\Resources\JenisDanas\Pages\ListJenisDanas;
use App\Filament\Resources\JenisDanas\Schemas\JenisDanaForm;
use App\Filament\Resources\JenisDanas\Tables\JenisDanasTable;
use App\Models\JenisDana;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class JenisDanaResource extends Resource
{
    protected static ?string $model = JenisDana::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-wallet';

    protected static string|\UnitEnum|null $navigationGroup = 'Master Data & Pengaturan';

    protected static ?string $navigationLabel = 'Jenis Dana ZISWAF';

    protected static ?string $modelLabel = 'Jenis Dana';

    protected static ?string $pluralModelLabel = 'Katalog Jenis Dana';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return JenisDanaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JenisDanasTable::configure($table);
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
            'index' => ListJenisDanas::route('/'),
            'create' => CreateJenisDana::route('/create'),
            'edit' => EditJenisDana::route('/{record}/edit'),
        ];
    }
}
