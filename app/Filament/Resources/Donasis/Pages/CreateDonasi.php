<?php

namespace App\Filament\Resources\Donasis\Pages;

use App\Filament\Resources\Donasis\DonasiResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDonasi extends CreateRecord
{
    protected static string $resource = DonasiResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['amil_id']) && auth()->user()->amil) {
            $data['amil_id'] = auth()->user()->amil->id;
        }

        if ($data['status'] === 'terverifikasi' && empty($data['verified_by'])) {
            $data['verified_by'] = auth()->id();
            $data['verified_at'] = now();
        }

        return $data;
    }
}
