<?php

namespace App\Filament\Resources\LokasiKantors\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

class LokasiKantorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Identitas Kantor & Titik Geofencing GPS')
                    ->icon('heroicon-o-building-office-2')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(2)
                            ->components([
                                TextInput::make('nama_lokasi')
                                    ->label('Nama Kantor / Cabang')
                                    ->required()
                                    ->default('Kantor Utama GSC'),
                                Toggle::make('is_active')
                                    ->label('Status Lokasi Aktif')
                                    ->default(true)
                                    ->helperText('Hanya kantor aktif yang dapat dijadikan titik presensi kehadiran'),
                            ]),
                        Textarea::make('alamat')
                            ->label('Alamat Lengkap Kantor')
                            ->rows(2)
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
                        Grid::make(3)
                            ->components([
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
                                    ->helperText('Batas jarak maksimal presensi amil (default: 100 meter)')
                                    ->required()
                                    ->numeric()
                                    ->default(100)
                                    ->live(debounce: 250),
                            ]),
                    ]),
            ]);
    }
}
