<?php

namespace App\Filament\Resources\JamKerjas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class JamKerjaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->required()
                    ->default('Jam Kerja Reguler'),
                TimePicker::make('jam_masuk')
                    ->required(),
                TimePicker::make('jam_pulang')
                    ->required(),
                TextInput::make('toleransi_menit')
                    ->required()
                    ->numeric()
                    ->default(15),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
