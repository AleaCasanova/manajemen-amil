<?php

namespace App\Filament\Resources\Absensis\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class AbsensiForm
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
                    ->required(),
                TimePicker::make('jam_masuk'),
                TimePicker::make('jam_pulang'),
                TextInput::make('lat_masuk')
                    ->numeric(),
                TextInput::make('lng_masuk')
                    ->numeric(),
                TextInput::make('lat_pulang')
                    ->numeric(),
                TextInput::make('lng_pulang')
                    ->numeric(),
                FileUpload::make('foto_masuk')
                    ->label('Foto Selfie Masuk')
                    ->image()
                    ->disk('public')
                    ->directory('absensi/masuk')
                    ->openable()
                    ->downloadable(),
                FileUpload::make('foto_pulang')
                    ->label('Foto Selfie Pulang')
                    ->image()
                    ->disk('public')
                    ->directory('absensi/pulang')
                    ->openable()
                    ->downloadable(),
                Select::make('status')
                    ->options([
                        'hadir' => 'Hadir',
                        'terlambat' => 'Terlambat',
                        'izin' => 'Izin',
                        'cuti' => 'Cuti',
                        'alpa' => 'Alpa',
                    ])
                    ->default('hadir')
                    ->required(),
                Textarea::make('keterangan')
                    ->columnSpanFull(),
            ]);
    }
}
