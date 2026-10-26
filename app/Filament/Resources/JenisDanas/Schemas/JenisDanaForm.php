<?php

namespace App\Filament\Resources\JenisDanas\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class JenisDanaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->required(),
                Select::make('kelompok')
                    ->options([
                        'zakat' => 'Zakat',
                        'infak' => 'Infak',
                        'dskl' => 'Dskl',
                        'sedekah' => 'Sedekah',
                        'wakaf' => 'Wakaf',
                        'lainnya' => 'Lainnya',
                    ])
                    ->default('zakat')
                    ->required(),
                TextInput::make('hak_amil_persen')
                    ->required()
                    ->numeric()
                    ->default(12.5),
                Textarea::make('deskripsi')
                    ->columnSpanFull(),
            ]);
    }
}
