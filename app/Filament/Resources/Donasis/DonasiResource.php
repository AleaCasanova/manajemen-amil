<?php

namespace App\Filament\Resources\Donasis;

use App\Filament\Resources\Donasis\Pages\CreateDonasi;
use App\Filament\Resources\Donasis\Pages\EditDonasi;
use App\Filament\Resources\Donasis\Pages\ListDonasis;
use App\Filament\Resources\Donasis\Pages\ViewDonasi;
use App\Filament\Resources\Donasis\Schemas\DonasiForm;
use App\Filament\Resources\Donasis\Schemas\DonasiInfolist;
use App\Filament\Resources\Donasis\Tables\DonasisTable;
use App\Models\Donasi;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class DonasiResource extends Resource
{
    protected static ?string $model = Donasi::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static string|\UnitEnum|null $navigationGroup = 'Fundraising & Donasi';

    protected static ?string $navigationLabel = 'Rekap Donasi';

    protected static ?string $modelLabel = 'Donasi';

    protected static ?string $pluralModelLabel = 'Rekap & Verifikasi Donasi';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return DonasiForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DonasiInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DonasisTable::configure($table);
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
            'index' => ListDonasis::route('/'),
            'create' => CreateDonasi::route('/create'),
            'view' => ViewDonasi::route('/{record}'),
            'edit' => EditDonasi::route('/{record}/edit'),
        ];
    }
}
