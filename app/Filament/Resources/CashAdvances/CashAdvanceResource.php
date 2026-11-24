<?php

namespace App\Filament\Resources\CashAdvances;

use App\Filament\Resources\CashAdvances\Pages\CreateCashAdvance;
use App\Filament\Resources\CashAdvances\Pages\ListCashAdvances;
use App\Filament\Resources\CashAdvances\Pages\ViewCashAdvance;
use App\Filament\Resources\CashAdvances\Schemas\CashAdvanceForm;
use App\Filament\Resources\CashAdvances\Schemas\CashAdvanceInfolist;
use App\Filament\Resources\CashAdvances\Tables\CashAdvancesTable;
use App\Models\CashAdvance;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;

class CashAdvanceResource extends Resource
{
    protected static ?string $model = CashAdvance::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static string|\UnitEnum|null $navigationGroup = 'Keuangan & Donasi';

    protected static ?string $navigationLabel = 'Cash Advance & Kasbon';

    protected static ?string $modelLabel = 'Cash Advance / Kasbon';

    protected static ?string $pluralModelLabel = 'Cash Advance & Kasbon';

    protected static ?int $navigationSort = 2;

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
            return url('/'.config('filament.admin_path', 'portal-internal-gsc').'/cash-advances');
        }
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with([
            'amil',
            'divisi',
            'program',
            'pengajuanDana',
            'akunKeuangan',
            'akunPenyelesaian',
            'creator',
        ]);
    }

    public static function form(Schema $schema): Schema
    {
        return CashAdvanceForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CashAdvanceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CashAdvancesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCashAdvances::route('/'),
            'create' => CreateCashAdvance::route('/create'),
            'view' => ViewCashAdvance::route('/{record}'),
        ];
    }
}
