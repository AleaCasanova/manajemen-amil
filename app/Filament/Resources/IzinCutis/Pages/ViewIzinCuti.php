<?php

namespace App\Filament\Resources\IzinCutis\Pages;

use App\Filament\Resources\IzinCutis\IzinCutiResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewIzinCuti extends ViewRecord
{
    protected static string $resource = IzinCutiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
