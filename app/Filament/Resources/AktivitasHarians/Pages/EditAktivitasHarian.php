<?php

namespace App\Filament\Resources\AktivitasHarians\Pages;

use App\Filament\Resources\AktivitasHarians\AktivitasHarianResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditAktivitasHarian extends EditRecord
{
    protected static string $resource = AktivitasHarianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
