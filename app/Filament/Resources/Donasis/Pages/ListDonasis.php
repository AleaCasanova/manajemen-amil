<?php

namespace App\Filament\Resources\Donasis\Pages;

use App\Exports\DonasiExport;
use App\Filament\Resources\Donasis\DonasiResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListDonasis extends ListRecords
{
    protected static string $resource = DonasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportExcel')
                ->label('Ekspor Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    return Excel::download(new DonasiExport, 'rekap-donasi-'.date('Ymd-His').'.xlsx');
                }),
            CreateAction::make(),
        ];
    }
}
