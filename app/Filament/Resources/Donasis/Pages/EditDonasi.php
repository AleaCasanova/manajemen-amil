<?php

namespace App\Filament\Resources\Donasis\Pages;

use App\Filament\Resources\Donasis\DonasiResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditDonasi extends EditRecord
{
    protected static string $resource = DonasiResource::class;

    protected function beforeFill(): void
    {
        if ($this->record->status === 'terverifikasi') {
            Notification::make()
                ->title('Donasi Terkunci')
                ->body('Donasi yang telah berstatus Terverifikasi dikunci dari pengubahan langsung sesuai standar kepatuhan keuangan ZISWAF. Gunakan fitur Jurnal Koreksi.')
                ->danger()
                ->send();

            $this->redirect(DonasiResource::getUrl('view', ['record' => $this->record]));
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->visible(fn (): bool => $this->record->status !== 'terverifikasi'),
        ];
    }
}
