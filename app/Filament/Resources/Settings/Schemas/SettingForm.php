<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Konfigurasi Parameter Sistem')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(3)
                            ->components([
                                TextInput::make('key')
                                    ->label('Kunci Pengaturan (Key)')
                                    ->required()
                                    ->placeholder('app_name, wa_gateway, dll'),
                                TextInput::make('grup')
                                    ->label('Kelompok (Group)')
                                    ->required()
                                    ->default('general'),
                                TextInput::make('tipe')
                                    ->label('Tipe Data')
                                    ->required()
                                    ->default('string'),
                            ]),
                        Textarea::make('value')
                            ->label('Nilai Pengaturan (Value)')
                            ->rows(3)
                            ->columnSpanFull(),
                        TextInput::make('deskripsi')
                            ->label('Keterangan / Fungsi Pengaturan')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
