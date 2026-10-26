<?php

namespace App\Filament\Resources\JenisDanas\Pages;

use App\Filament\Resources\JenisDanas\JenisDanaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListJenisDanas extends ListRecords
{
    protected static string $resource = JenisDanaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
