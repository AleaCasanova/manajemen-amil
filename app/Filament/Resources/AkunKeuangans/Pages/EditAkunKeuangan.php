<?php

namespace App\Filament\Resources\AkunKeuangans\Pages;

use App\Filament\Resources\AkunKeuangans\AkunKeuanganResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAkunKeuangan extends EditRecord
{
    protected static string $resource = AkunKeuanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (): bool => $this->record->transaksiKeuangans()->count() === 0),
        ];
    }
}
