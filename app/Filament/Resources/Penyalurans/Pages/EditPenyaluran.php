<?php

namespace App\Filament\Resources\Penyalurans\Pages;

use App\Filament\Resources\Penyalurans\PenyaluranResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPenyaluran extends EditRecord
{
    protected static string $resource = PenyaluranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
