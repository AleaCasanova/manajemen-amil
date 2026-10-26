<?php

namespace App\Filament\Resources\Donaturs;

use App\Filament\Resources\Donaturs\Pages\CreateDonatur;
use App\Filament\Resources\Donaturs\Pages\EditDonatur;
use App\Filament\Resources\Donaturs\Pages\ListDonaturs;
use App\Filament\Resources\Donaturs\Pages\ViewDonatur;
use App\Filament\Resources\Donaturs\Schemas\DonaturForm;
use App\Filament\Resources\Donaturs\Schemas\DonaturInfolist;
use App\Filament\Resources\Donaturs\Tables\DonatursTable;
use App\Models\Donatur;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class DonaturResource extends Resource
{
    protected static ?string $model = Donatur::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-heart';

    protected static string|\UnitEnum|null $navigationGroup = 'Fundraising & Donasi';

    protected static ?string $navigationLabel = 'Manajemen Donatur (CRM)';

    protected static ?string $modelLabel = 'Donatur';

    protected static ?string $pluralModelLabel = 'Data Donatur';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return DonaturForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DonaturInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DonatursTable::configure($table);
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
            'index' => ListDonaturs::route('/'),
            'create' => CreateDonatur::route('/create'),
            'view' => ViewDonatur::route('/{record}'),
            'edit' => EditDonatur::route('/{record}/edit'),
        ];
    }
}
