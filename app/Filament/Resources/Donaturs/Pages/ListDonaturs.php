<?php

namespace App\Filament\Resources\Donaturs\Pages;

use App\Exports\DonaturExport;
use App\Filament\Resources\Donaturs\DonaturResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListDonaturs extends ListRecords
{
    protected static string $resource = DonaturResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportExcel')
                ->label('Ekspor Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    return Excel::download(
                        new DonaturExport,
                        'rekap-data-donatur-'.date('Ymd-His').'.xlsx'
                    );
                }),
            CreateAction::make(),
        ];
    }
}
