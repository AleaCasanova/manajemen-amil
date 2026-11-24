<?php

namespace App\Filament\Resources\TransaksiKeuangans;

use App\Filament\Resources\TransaksiKeuangans\Pages\CreateTransaksiKeuangan;
use App\Filament\Resources\TransaksiKeuangans\Pages\EditTransaksiKeuangan;
use App\Filament\Resources\TransaksiKeuangans\Pages\ListTransaksiKeuangans;
use App\Filament\Resources\TransaksiKeuangans\Pages\ViewTransaksiKeuangan;
use App\Filament\Resources\TransaksiKeuangans\Schemas\TransaksiKeuanganForm;
use App\Filament\Resources\TransaksiKeuangans\Schemas\TransaksiKeuanganInfolist;
use App\Filament\Resources\TransaksiKeuangans\Tables\TransaksiKeuangansTable;
use App\Filament\Resources\TransaksiKeuangans\Widgets\BukuKasOverviewWidget;
use App\Models\TransaksiKeuangan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;

class TransaksiKeuanganResource extends Resource
{
    protected static ?string $model = TransaksiKeuangan::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static string|\UnitEnum|null $navigationGroup = 'Keuangan & Donasi';

    protected static ?string $navigationLabel = 'Buku Kas & Bank';

    protected static ?string $modelLabel = 'Transaksi Keuangan';

    protected static ?string $pluralModelLabel = 'Buku Kas & Bank';

    protected static ?int $navigationSort = 1;

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
            return url('/'.config('filament.admin_path', 'portal-internal-gsc').'/transaksi-keuangans');
        }
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with([
            'akunKeuangan',
            'kategoriKeuangan',
            'program',
            'pjAmil',
            'divisi',
            'creator',
            'transferPasangan.akunKeuangan',
            'cashAdvance',
        ]);
    }

    public static function form(Schema $schema): Schema
    {
        return TransaksiKeuanganForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TransaksiKeuanganInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TransaksiKeuangansTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getWidgets(): array
    {
        return [
            BukuKasOverviewWidget::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTransaksiKeuangans::route('/'),
            'create' => CreateTransaksiKeuangan::route('/create'),
            'view' => ViewTransaksiKeuangan::route('/{record}'),
            'edit' => EditTransaksiKeuangan::route('/{record}/edit'),
        ];
    }
}
