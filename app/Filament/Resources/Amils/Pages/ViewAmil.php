<?php

namespace App\Filament\Resources\Amils\Pages;

use App\Filament\Resources\Amils\AmilResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAmil extends ViewRecord
{
    protected static string $resource = AmilResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
