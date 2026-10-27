<?php

namespace App\Filament\Resources\JamKerjas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class JamKerjaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Konfigurasi Jam Operasional Kerja')
                    ->icon('heroicon-o-clock')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(2)
                            ->components([
                                TextInput::make('nama')
                                    ->label('Nama Shift / Kebijakan Jam Kerja')
                                    ->required()
                                    ->default('Jam Kerja Reguler'),
                                Toggle::make('is_active')
                                    ->label('Jadikan Shift Aktif Utama')
                                    ->default(true)
                                    ->helperText('Shift aktif akan diterapkan sebagai acuan presensi kehadiran amil'),
                            ]),
                        Grid::make(3)
                            ->components([
                                TimePicker::make('jam_masuk')
                                    ->label('Jam Masuk (Batas Datang)')
                                    ->required(),
                                TimePicker::make('jam_pulang')
                                    ->label('Jam Pulang (Clock Out)')
                                    ->required(),
                                TextInput::make('toleransi_menit')
                                    ->label('Toleransi Keterlambatan (Menit)')
                                    ->helperText('Contoh: 15 menit sebelum dianggap terlambat')
                                    ->required()
                                    ->numeric()
                                    ->default(15),
                            ]),
                    ]),
            ]);
    }
}
