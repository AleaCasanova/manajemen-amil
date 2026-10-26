<?php

namespace App\Filament\Resources\Amils\Pages;

use App\Filament\Resources\Amils\AmilResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAmils extends ListRecords
{
    protected static string $resource = AmilResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
