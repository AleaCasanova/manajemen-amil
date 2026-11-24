<?php

namespace App\Filament\Resources\PengajuanDanas\Pages;

use App\Filament\Resources\PengajuanDanas\PengajuanDanaResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPengajuanDana extends ViewRecord
{
    protected static string $resource = PengajuanDanaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cetak')
                ->label('Cetak Form PDF')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->url(fn (): string => route('amil.pengajuan-dana.cetak', $this->record->id))
                ->openUrlInNewTab(),
            EditAction::make(),
        ];
    }
}
