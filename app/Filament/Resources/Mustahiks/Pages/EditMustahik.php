<?php

namespace App\Filament\Resources\Mustahiks\Pages;

use App\Filament\Resources\Mustahiks\MustahikResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditMustahik extends EditRecord
{
    protected static string $resource = MustahikResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
