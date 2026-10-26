<?php

namespace App\Filament\Resources\JamKerjas;

use App\Filament\Resources\JamKerjas\Pages\CreateJamKerja;
use App\Filament\Resources\JamKerjas\Pages\EditJamKerja;
use App\Filament\Resources\JamKerjas\Pages\ListJamKerjas;
use App\Filament\Resources\JamKerjas\Schemas\JamKerjaForm;
use App\Filament\Resources\JamKerjas\Tables\JamKerjasTable;
use App\Models\JamKerja;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class JamKerjaResource extends Resource
{
    protected static ?string $model = JamKerja::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static string|\UnitEnum|null $navigationGroup = 'Master Data & Pengaturan';

    protected static ?string $navigationLabel = 'Jadwal & Jam Kerja';

    protected static ?string $modelLabel = 'Jam Kerja';

    protected static ?string $pluralModelLabel = 'Pengaturan Jam Kerja';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return JamKerjaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JamKerjasTable::configure($table);
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
            'index' => ListJamKerjas::route('/'),
            'create' => CreateJamKerja::route('/create'),
            'edit' => EditJamKerja::route('/{record}/edit'),
        ];
    }
}
