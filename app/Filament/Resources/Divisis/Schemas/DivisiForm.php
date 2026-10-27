<?php

namespace App\Filament\Resources\Divisis\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DivisiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informasi Divisi Organisasi')
                    ->icon('heroicon-o-user-group')
                    ->columnSpanFull()
                    ->components([
                        TextInput::make('nama')
                            ->label('Nama Divisi')
                            ->required()
                            ->placeholder('Contoh: Divisi Fundraising & Kemitraan'),
                        Textarea::make('deskripsi')
                            ->label('Tupoksi & Deskripsi Divisi')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Jelaskan tugas pokok dan fungsi dari divisi ini'),
                    ]),
            ]);
    }
}
