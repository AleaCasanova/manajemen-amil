<?php

namespace App\Filament\Resources\Donaturs\Pages;

use App\Filament\Resources\Donaturs\DonaturResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDonatur extends ViewRecord
{
    protected static string $resource = DonaturResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
