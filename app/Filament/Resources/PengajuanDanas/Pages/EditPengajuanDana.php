<?php

namespace App\Filament\Resources\PengajuanDanas\Pages;

use App\Filament\Resources\PengajuanDanas\PengajuanDanaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPengajuanDana extends EditRecord
{
    protected static string $resource = PengajuanDanaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
