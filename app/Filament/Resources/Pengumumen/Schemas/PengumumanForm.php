<?php

namespace App\Filament\Resources\Pengumumen\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PengumumanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('judul')
                    ->label('Judul Pengumuman')
                    ->required(),
                Textarea::make('isi')
                    ->label('Isi Pengumuman')
                    ->required()
                    ->columnSpanFull(),
                FileUpload::make('lampiran')
                    ->label('Lampiran / Poster Banner')
                    ->image()
                    ->disk('public')
                    ->directory('pengumuman/lampiran')
                    ->openable()
                    ->downloadable()
                    ->maxSize(5120),
                TextInput::make('target')
                    ->required()
                    ->default('semua'),
                DateTimePicker::make('published_at'),
                TextInput::make('created_by')
                    ->numeric(),
            ]);
    }
}
