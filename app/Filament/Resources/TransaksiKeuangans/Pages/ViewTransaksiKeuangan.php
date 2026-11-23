<?php

namespace App\Filament\Resources\TransaksiKeuangans\Pages;

use App\Filament\Resources\TransaksiKeuangans\TransaksiKeuanganResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTransaksiKeuangan extends ViewRecord
{
    protected static string $resource = TransaksiKeuanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
