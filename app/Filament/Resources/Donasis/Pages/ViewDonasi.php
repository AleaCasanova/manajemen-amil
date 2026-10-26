<?php

namespace App\Filament\Resources\Donasis\Pages;

use App\Filament\Resources\Donasis\DonasiResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDonasi extends ViewRecord
{
    protected static string $resource = DonasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
