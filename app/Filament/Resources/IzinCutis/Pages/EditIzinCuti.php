<?php

namespace App\Filament\Resources\IzinCutis\Pages;

use App\Filament\Resources\IzinCutis\IzinCutiResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditIzinCuti extends EditRecord
{
    protected static string $resource = IzinCutiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
