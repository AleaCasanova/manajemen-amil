<?php

namespace App\Filament\Resources\Penyalurans;

use App\Filament\Resources\Penyalurans\Pages\CreatePenyaluran;
use App\Filament\Resources\Penyalurans\Pages\EditPenyaluran;
use App\Filament\Resources\Penyalurans\Pages\ListPenyalurans;
use App\Filament\Resources\Penyalurans\Pages\ViewPenyaluran;
use App\Filament\Resources\Penyalurans\Schemas\PenyaluranForm;
use App\Filament\Resources\Penyalurans\Schemas\PenyaluranInfolist;
use App\Filament\Resources\Penyalurans\Tables\PenyaluransTable;
use App\Models\Penyaluran;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class PenyaluranResource extends Resource
{
    protected static ?string $model = Penyaluran::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-gift';

    protected static string|\UnitEnum|null $navigationGroup = 'Penyaluran & Mustahik';

    protected static ?string $navigationLabel = 'Penyaluran / Distribusi';

    protected static ?string $modelLabel = 'Penyaluran';

    protected static ?string $pluralModelLabel = 'Distribusi Bantuan Mustahik';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return PenyaluranForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PenyaluranInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PenyaluransTable::configure($table);
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
            'index' => ListPenyalurans::route('/'),
            'create' => CreatePenyaluran::route('/create'),
            'view' => ViewPenyaluran::route('/{record}'),
            'edit' => EditPenyaluran::route('/{record}/edit'),
        ];
    }
}
