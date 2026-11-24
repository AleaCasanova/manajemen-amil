<?php

namespace App\Filament\Resources\AkunKeuangans;

use App\Filament\Resources\AkunKeuangans\Pages\CreateAkunKeuangan;
use App\Filament\Resources\AkunKeuangans\Pages\EditAkunKeuangan;
use App\Filament\Resources\AkunKeuangans\Pages\ListAkunKeuangans;
use App\Filament\Resources\AkunKeuangans\Schemas\AkunKeuanganForm;
use App\Filament\Resources\AkunKeuangans\Tables\AkunKeuangansTable;
use App\Models\AkunKeuangan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;

class AkunKeuanganResource extends Resource
{
    protected static ?string $model = AkunKeuangan::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-library';

    protected static string|\UnitEnum|null $navigationGroup = 'Keuangan & Donasi';

    protected static ?string $navigationLabel = 'Akun Kas & Bank';

    protected static ?string $modelLabel = 'Akun Kas / Bank';

    protected static ?string $pluralModelLabel = 'Daftar Akun Kas & Bank';

    protected static ?int $navigationSort = 3;

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
            return url('/'.config('filament.admin_path', 'portal-internal-gsc').'/akun-keuangans');
        }
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withMutasiTotals()
            ->with('kategoriKeuangan')
            ->withCount('transaksiKeuangans');
    }

    public static function form(Schema $schema): Schema
    {
        return AkunKeuanganForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AkunKeuangansTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAkunKeuangans::route('/'),
            'create' => CreateAkunKeuangan::route('/create'),
            'edit' => EditAkunKeuangan::route('/{record}/edit'),
        ];
    }
}
