<?php

namespace App\Filament\Resources\Amils\Pages;

use App\Filament\Resources\Amils\AmilResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditAmil extends EditRecord
{
    protected static string $resource = AmilResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if ($this->record && $this->record->user) {
            $data['email'] = $this->record->user->email;
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($this->record && $this->record->user) {
            $user = $this->record->user;
            $user->name = $data['nama'];

            if (! empty($data['email'])) {
                $user->email = $data['email'];
            }

            if (! empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }

            $user->is_active = ($data['status'] === 'aktif');
            $user->save();
        }

        unset($data['email'], $data['password']);

        return $data;
    }
}
