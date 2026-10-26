<?php

namespace App\Filament\Resources\Mustahiks\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MustahikForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->required(),
                TextInput::make('nik'),
                Textarea::make('alamat')
                    ->columnSpanFull(),
                Select::make('asnaf')
                    ->options([
                        'fakir' => 'Fakir',
                        'miskin' => 'Miskin',
                        'amil' => 'Amil',
                        'mualaf' => 'Mualaf',
                        'riqab' => 'Riqab',
                        'gharim' => 'Gharim',
                        'fisabilillah' => 'Fisabilillah',
                        'ibnu_sabil' => 'Ibnu sabil',
                    ])
                    ->default('fakir')
                    ->required(),
                Textarea::make('kondisi')
                    ->columnSpanFull(),
                Select::make('status_verifikasi')
                    ->options(['menunggu' => 'Menunggu', 'terverifikasi' => 'Terverifikasi', 'ditolak' => 'Ditolak'])
                    ->default('menunggu')
                    ->required(),
            ]);
    }
}
