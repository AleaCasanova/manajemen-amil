<?php

namespace App\Filament\Resources\PengajuanDanas;

use App\Filament\Resources\PengajuanDanas\Pages\CreatePengajuanDana;
use App\Filament\Resources\PengajuanDanas\Pages\EditPengajuanDana;
use App\Filament\Resources\PengajuanDanas\Pages\ListPengajuanDanas;
use App\Filament\Resources\PengajuanDanas\Pages\ViewPengajuanDana;
use App\Filament\Resources\PengajuanDanas\Schemas\PengajuanDanaForm;
use App\Filament\Resources\PengajuanDanas\Schemas\PengajuanDanaInfolist;
use App\Filament\Resources\PengajuanDanas\Tables\PengajuanDanasTable;
use App\Models\PengajuanDana;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Route;

class PengajuanDanaResource extends Resource
{
    protected static ?string $model = PengajuanDana::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static string|\UnitEnum|null $navigationGroup = 'Keuangan & Donasi';

    protected static ?string $navigationLabel = 'Pengajuan Dana Amil';

    protected static ?string $modelLabel = 'Pengajuan Dana';

    protected static ?string $pluralModelLabel = 'Pengajuan Dana & Kasbon Amil';

    protected static ?int $navigationSort = 5;

    public static function shouldRegisterNavigation(): bool
    {
        try {
            return Route::has(static::getRouteBaseName().'.index');
        } catch (\Throwable) {
            return false;
        }
    }

    public static function getNavigationUrl(): string
    {
        try {
            return static::getUrl();
        } catch (\Throwable) {
            return url('/'.config('filament.admin_path', 'portal-internal-gsc').'/pengajuan-danas');
        }
    }

    public static function form(Schema $schema): Schema
    {
        return PengajuanDanaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PengajuanDanaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PengajuanDanasTable::configure($table);
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
            'index' => ListPengajuanDanas::route('/'),
            'create' => CreatePengajuanDana::route('/create'),
            'view' => ViewPengajuanDana::route('/{record}'),
            'edit' => EditPengajuanDana::route('/{record}/edit'),
        ];
    }
}
