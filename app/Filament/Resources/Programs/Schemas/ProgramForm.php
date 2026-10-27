<?php

namespace App\Filament\Resources\Programs\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProgramForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informasi & Target Program ZISWAF')
                    ->icon('heroicon-o-sparkles')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(3)
                            ->components([
                                TextInput::make('nama')
                                    ->label('Nama Program / Campaign')
                                    ->required()
                                    ->placeholder('Contoh: Sedekah Paket Sembako'),
                                TextInput::make('target_dana')
                                    ->label('Target Penghimpunan (Rp)')
                                    ->prefix('Rp')
                                    ->required()
                                    ->numeric()
                                    ->default(0),
                                Select::make('status')
                                    ->label('Status Program')
                                    ->options([
                                        'aktif' => 'Aktif',
                                        'selesai' => 'Selesai',
                                        'ditutup' => 'Ditutup',
                                        'akan_datang' => 'Akan Datang',
                                    ])
                                    ->default('aktif')
                                    ->required(),
                            ]),
                        Grid::make(2)
                            ->components([
                                DatePicker::make('tgl_mulai')
                                    ->label('Tanggal Mulai Pelaksanaan'),
                                DatePicker::make('tgl_selesai')
                                    ->label('Tanggal Selesai (Batas Akhir)'),
                            ]),
                    ]),

                Section::make('Deskripsi & Rincian Program')
                    ->icon('heroicon-o-document-text')
                    ->columnSpanFull()
                    ->components([
                        Textarea::make('deskripsi')
                            ->label('Uraian Lengkap Mengenai Program')
                            ->rows(4)
                            ->columnSpanFull()
                            ->placeholder('Jelaskan latar belakang, tujuan, dan sasaran dari program ini'),
                    ]),
            ]);
    }
}
