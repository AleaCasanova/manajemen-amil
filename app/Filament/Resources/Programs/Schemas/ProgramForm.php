<?php

namespace App\Filament\Resources\Programs\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProgramForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->required(),
                Textarea::make('deskripsi')
                    ->columnSpanFull(),
                TextInput::make('target_dana')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                DatePicker::make('tgl_mulai'),
                DatePicker::make('tgl_selesai'),
                Select::make('status')
                    ->options([
                        'aktif' => 'Aktif',
                        'selesai' => 'Selesai',
                        'ditutup' => 'Ditutup',
                        'akan_datang' => 'Akan datang',
                    ])
                    ->default('aktif')
                    ->required(),
            ]);
    }
}
