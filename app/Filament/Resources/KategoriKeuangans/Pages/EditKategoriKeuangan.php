<?php

namespace App\Filament\Resources\KategoriKeuangans\Pages;

use App\Filament\Resources\KategoriKeuangans\KategoriKeuanganResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditKategoriKeuangan extends EditRecord
{
    protected static string $resource = KategoriKeuanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (): bool => $this->record->transaksiKeuangans()->count() === 0
                    && $this->record->children()->count() === 0),
        ];
    }
}
