<?php

namespace App\Filament\Resources\LokasiKantors\Pages;

use App\Filament\Resources\LokasiKantors\LokasiKantorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLokasiKantors extends ListRecords
{
    protected static string $resource = LokasiKantorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
