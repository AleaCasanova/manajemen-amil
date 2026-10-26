<?php

namespace App\Filament\Resources\AktivitasHarians\Pages;

use App\Filament\Resources\AktivitasHarians\AktivitasHarianResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAktivitasHarian extends ViewRecord
{
    protected static string $resource = AktivitasHarianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
