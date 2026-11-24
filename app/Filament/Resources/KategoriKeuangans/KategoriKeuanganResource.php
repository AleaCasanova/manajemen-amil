<?php

namespace App\Filament\Resources\KategoriKeuangans;

use App\Filament\Resources\KategoriKeuangans\Pages\CreateKategoriKeuangan;
use App\Filament\Resources\KategoriKeuangans\Pages\EditKategoriKeuangan;
use App\Filament\Resources\KategoriKeuangans\Pages\ListKategoriKeuangans;
use App\Filament\Resources\KategoriKeuangans\Schemas\KategoriKeuanganForm;
use App\Filament\Resources\KategoriKeuangans\Tables\KategoriKeuangansTable;
use App\Models\KategoriKeuangan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;

class KategoriKeuanganResource extends Resource
{
    protected static ?string $model = KategoriKeuangan::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-list-bullet';

    protected static string|\UnitEnum|null $navigationGroup = 'Keuangan & Donasi';

    protected static ?string $navigationLabel = 'Master COA';

    protected static ?string $modelLabel = 'Akun COA';

    protected static ?string $pluralModelLabel = 'Master Chart of Accounts (COA)';

    protected static ?int $navigationSort = 4;

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
            return url('/'.config('filament.admin_path', 'portal-internal-gsc').'/kategori-keuangans');
        }
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('parent')
            ->withCount(['transaksiKeuangans', 'children']);
    }

    public static function form(Schema $schema): Schema
    {
        return KategoriKeuanganForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KategoriKeuangansTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKategoriKeuangans::route('/'),
            'create' => CreateKategoriKeuangan::route('/create'),
            'edit' => EditKategoriKeuangan::route('/{record}/edit'),
        ];
    }
}
