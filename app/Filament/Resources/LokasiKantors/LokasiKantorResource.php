<?php

namespace App\Filament\Resources\LokasiKantors;

use App\Filament\Resources\LokasiKantors\Pages\CreateLokasiKantor;
use App\Filament\Resources\LokasiKantors\Pages\EditLokasiKantor;
use App\Filament\Resources\LokasiKantors\Pages\ListLokasiKantors;
use App\Filament\Resources\LokasiKantors\Schemas\LokasiKantorForm;
use App\Filament\Resources\LokasiKantors\Tables\LokasiKantorsTable;
use App\Models\LokasiKantor;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class LokasiKantorResource extends Resource
{
    protected static ?string $model = LokasiKantor::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-map-pin';

    protected static string|\UnitEnum|null $navigationGroup = 'Master Data & Pengaturan';

    protected static ?string $navigationLabel = 'Lokasi & Geofence Kantor';

    protected static ?string $modelLabel = 'Lokasi Kantor';

    protected static ?string $pluralModelLabel = 'Lokasi & Geofence Kantor';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return LokasiKantorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LokasiKantorsTable::configure($table);
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
            'index' => ListLokasiKantors::route('/'),
            'create' => CreateLokasiKantor::route('/create'),
            'edit' => EditLokasiKantor::route('/{record}/edit'),
        ];
    }
}
