<?php

namespace App\Filament\Resources\AkunKeuangans\Pages;

use App\Filament\Resources\AkunKeuangans\AkunKeuanganResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAkunKeuangans extends ListRecords
{
    protected static string $resource = AkunKeuanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('+ Tambah Akun Kas / Bank')
                ->icon('heroicon-o-plus'),
        ];
    }
}
