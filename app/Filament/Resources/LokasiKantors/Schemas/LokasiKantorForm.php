<?php

namespace App\Filament\Resources\LokasiKantors\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

class LokasiKantorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_lokasi')
                    ->required()
                    ->default('Kantor Utama'),
                Textarea::make('alamat')
                    ->columnSpanFull(),
                View::make('filament.forms.components.map-picker')
                    ->viewData([
                        'latField' => 'lat',
                        'lngField' => 'lng',
                        'radiusField' => 'radius_meter',
                        'addressField' => 'alamat',
                        'defaultLat' => -7.7027157,
                        'defaultLng' => 109.0270034,
                    ])
                    ->columnSpanFull(),
                TextInput::make('lat')
                    ->label('Latitude (Lat)')
                    ->required()
                    ->numeric()
                    ->live(onBlur: true),
                TextInput::make('lng')
                    ->label('Longitude (Lng)')
                    ->required()
                    ->numeric()
                    ->live(onBlur: true),
                TextInput::make('radius_meter')
                    ->label('Radius Toleransi (Meter)')
                    ->helperText('Batas jarak maksimal presensi amil dari lokasi ini (100 meter)')
                    ->required()
                    ->numeric()
                    ->default(100)
                    ->live(debounce: 250),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
