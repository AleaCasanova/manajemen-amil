<?php

namespace App\Filament\Resources\Mustahiks\Pages;

use App\Filament\Resources\Mustahiks\MustahikResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewMustahik extends ViewRecord
{
    protected static string $resource = MustahikResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
