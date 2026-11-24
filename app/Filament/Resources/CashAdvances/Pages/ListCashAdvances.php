<?php

namespace App\Filament\Resources\CashAdvances\Pages;

use App\Filament\Resources\CashAdvances\CashAdvanceResource;
use App\Models\CashAdvance;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListCashAdvances extends ListRecords
{
    protected static string $resource = CashAdvanceResource::class;

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua')
                ->badge(CashAdvance::count()),
            'aktif' => Tab::make('Aktif / Belum LPJ')
                ->modifyQueryUsing(fn ($query) => $query->aktif())
                ->badge(CashAdvance::aktif()->count())
                ->badgeColor('warning'),
            'cash_advance' => Tab::make('Cash Advance (101.09.000)')
                ->modifyQueryUsing(fn ($query) => $query->cashAdvance())
                ->badge(CashAdvance::cashAdvance()->count())
                ->badgeColor('info'),
            'cash_bon' => Tab::make('Cash Bon (101.06.001)')
                ->modifyQueryUsing(fn ($query) => $query->cashBon())
                ->badge(CashAdvance::cashBon()->count())
                ->badgeColor('gray'),
            'selesai' => Tab::make('Selesai / Lunas')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'selesai'))
                ->badge(CashAdvance::where('status', 'selesai')->count())
                ->badgeColor('success'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('+ Cairkan CA / Kasbon')
                ->icon('heroicon-o-plus'),
        ];
    }
}
