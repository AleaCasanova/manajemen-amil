<?php

namespace App\Filament\Resources\Agendas\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AgendaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('judul')
                    ->required(),
                Textarea::make('deskripsi')
                    ->columnSpanFull(),
                TextInput::make('lokasi'),
                DateTimePicker::make('mulai')
                    ->required(),
                DateTimePicker::make('selesai')
                    ->required(),
                Select::make('status')
                    ->options([
                        'terjadwal' => 'Terjadwal',
                        'berlangsung' => 'Berlangsung',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                    ])
                    ->default('terjadwal')
                    ->required(),
                TextInput::make('created_by')
                    ->numeric(),
            ]);
    }
}
