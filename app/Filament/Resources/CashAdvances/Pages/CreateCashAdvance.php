<?php

namespace App\Filament\Resources\CashAdvances\Pages;

use App\Filament\Resources\CashAdvances\CashAdvanceResource;
use App\Services\CashAdvanceService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CreateCashAdvance extends CreateRecord
{
    protected static string $resource = CashAdvanceResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(CashAdvanceService::class)->cairkan($data, Auth::id());
    }
}
