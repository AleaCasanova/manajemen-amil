<?php

namespace App\Filament\Resources\Absensis\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AbsensiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informasi Amil & Status Kehadiran')
                    ->icon('heroicon-o-check-badge')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(3)
                            ->components([
                                Select::make('amil_id')
                                    ->label('Petugas Amil')
                                    ->relationship('amil', 'nama')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                DatePicker::make('tanggal')
                                    ->label('Tanggal Presensi')
                                    ->default(now())
                                    ->required(),
                                Select::make('status')
                                    ->label('Status Kehadiran')
                                    ->options([
                                        'hadir' => 'Hadir Tepat Waktu',
                                        'terlambat' => 'Terlambat',
                                        'izin' => 'Izin',
                                        'cuti' => 'Cuti',
                                        'alpa' => 'Alpa / Tanpa Keterangan',
                                    ])
                                    ->default('hadir')
                                    ->required(),
                            ]),
                    ]),

                Section::make('Waktu Kerja & Titik Koordinat GPS')
                    ->icon('heroicon-o-map-pin')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(2)
                            ->components([
                                TimePicker::make('jam_masuk')->label('Jam Masuk'),
                                TimePicker::make('jam_pulang')->label('Jam Pulang'),
                            ]),
                        Grid::make(4)
                            ->components([
                                TextInput::make('lat_masuk')->label('Latitude Masuk')->numeric(),
                                TextInput::make('lng_masuk')->label('Longitude Masuk')->numeric(),
                                TextInput::make('lat_pulang')->label('Latitude Pulang')->numeric(),
                                TextInput::make('lng_pulang')->label('Longitude Pulang')->numeric(),
                            ]),
                    ]),

                Section::make('Foto Selfie Presensi (Kamera Masuk & Pulang)')
                    ->icon('heroicon-o-camera')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(2)
                            ->components([
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
                            ]),
                    ]),

                Section::make('Catatan Tambahan Kehadiran')
                    ->icon('heroicon-o-chat-bubble-bottom-center-text')
                    ->columnSpanFull()
                    ->components([
                        Textarea::make('keterangan')
                            ->label('Keterangan / Alasan Khusus')
                            ->rows(2)
                            ->columnSpanFull()
                            ->placeholder('Catatan dispensasi, alasan keterlambatan, atau penugasan khusus luar kota'),
                    ]),
            ]);
    }
}
