<?php

namespace App\Filament\Resources\JenisDanas\Pages;

use App\Filament\Resources\JenisDanas\JenisDanaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditJenisDana extends EditRecord
{
    protected static string $resource = JenisDanaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
