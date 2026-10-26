<?php

namespace App\Filament\Resources\Mustahiks\Pages;

use App\Filament\Resources\Mustahiks\MustahikResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMustahiks extends ListRecords
{
    protected static string $resource = MustahikResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
