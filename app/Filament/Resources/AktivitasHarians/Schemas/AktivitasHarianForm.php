<?php

namespace App\Filament\Resources\AktivitasHarians\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

class AktivitasHarianForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('amil_id')
                    ->label('Petugas Amil')
                    ->relationship('amil', 'nama')
                    ->searchable()
                    ->preload()
                    ->required(),
                DatePicker::make('tanggal')
                    ->label('Tanggal Kegiatan')
                    ->required(),
                TextInput::make('judul')
                    ->label('Judul Aktivitas')
                    ->required(),
                Textarea::make('deskripsi')
                    ->label('Deskripsi Kegiatan')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('lokasi')
                    ->label('Lokasi / Tempat Kegiatan'),
                View::make('filament.forms.components.map-picker')
                    ->viewData([
                        'latField' => 'lat',
                        'lngField' => 'lng',
                        'addressField' => 'lokasi',
                        'defaultLat' => -7.7027157,
                        'defaultLng' => 109.0270034,
                    ])
                    ->columnSpanFull(),
                TextInput::make('lat')
                    ->label('GPS Latitude')
                    ->numeric(),
                TextInput::make('lng')
                    ->label('GPS Longitude')
                    ->numeric(),
                FileUpload::make('foto')
                    ->label('Foto Dokumentasi Kegiatan')
                    ->image()
                    ->disk('public')
                    ->directory('aktivitas/foto')
                    ->openable()
                    ->downloadable()
                    ->maxSize(5120),
                Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'diajukan' => 'Diajukan',
                        'disetujui' => 'Disetujui',
                        'ditolak' => 'Ditolak',
                    ])
                    ->default('disetujui')
                    ->required(),
            ]);
    }
}
