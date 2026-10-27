<?php

namespace App\Filament\Resources\JenisDanas\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class JenisDanaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Klasifikasi & Ketentuan Jenis Dana')
                    ->icon('heroicon-o-banknotes')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(3)
                            ->components([
                                TextInput::make('nama')
                                    ->label('Nama Jenis Dana')
                                    ->required()
                                    ->placeholder('Contoh: Zakat Maal, Infak Umum'),
                                Select::make('kelompok')
                                    ->label('Kelompok ZISWAF')
                                    ->options([
                                        'zakat' => 'Zakat',
                                        'infak' => 'Infak',
                                        'dskl' => 'DSKL (Dana Sosial Keagamaan Lainnya)',
                                        'sedekah' => 'Sedekah',
                                        'wakaf' => 'Wakaf',
                                        'lainnya' => 'Lainnya',
                                    ])
                                    ->default('zakat')
                                    ->required(),
                                TextInput::make('hak_amil_persen')
                                    ->label('Persentase Hak Amil (%)')
                                    ->suffix('%')
                                    ->required()
                                    ->numeric()
                                    ->default(12.5)
                                    ->helperText('Batas maksimal syariah (default: 12.5% / 1/8)'),
                            ]),
                        Textarea::make('deskripsi')
                            ->label('Deskripsi / Peruntukan Dana')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Jelaskan akad dan ketentuan penggunaan dana ini'),
                    ]),
            ]);
    }
}
