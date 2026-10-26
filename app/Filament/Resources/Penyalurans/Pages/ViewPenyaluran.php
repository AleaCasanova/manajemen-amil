<?php

namespace App\Filament\Resources\Penyalurans\Pages;

use App\Filament\Resources\Penyalurans\PenyaluranResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPenyaluran extends ViewRecord
{
    protected static string $resource = PenyaluranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
