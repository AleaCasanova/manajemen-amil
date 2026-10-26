<?php

namespace App\Filament\Resources\LokasiKantors\Pages;

use App\Filament\Resources\LokasiKantors\LokasiKantorResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLokasiKantor extends EditRecord
{
    protected static string $resource = LokasiKantorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
