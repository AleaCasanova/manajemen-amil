<?php

namespace App\Filament\Resources\Penyalurans\Pages;

use App\Filament\Resources\Penyalurans\PenyaluranResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPenyalurans extends ListRecords
{
    protected static string $resource = PenyaluranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
