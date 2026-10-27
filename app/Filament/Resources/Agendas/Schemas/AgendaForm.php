<?php

namespace App\Filament\Resources\Agendas\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AgendaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informasi & Jadwal Agenda')
                    ->icon('heroicon-o-calendar-days')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(3)
                            ->components([
                                TextInput::make('judul')
                                    ->label('Nama Agenda / Kegiatan')
                                    ->required()
                                    ->placeholder('Nama agenda kegiatan'),
                                Select::make('status')
                                    ->label('Status Agenda')
                                    ->options([
                                        'terjadwal' => 'Terjadwal',
                                        'berlangsung' => 'Sedang Berlangsung',
                                        'selesai' => 'Selesai',
                                        'dibatalkan' => 'Dibatalkan',
                                    ])
                                    ->default('terjadwal')
                                    ->required(),
                                TextInput::make('lokasi')
                                    ->label('Lokasi / Tempat Pelaksanaan')
                                    ->placeholder('Contoh: Kantor Utama GSC / Aula'),
                            ]),
                        Grid::make(2)
                            ->components([
                                DateTimePicker::make('mulai')
                                    ->label('Waktu Mulai')
                                    ->default(now())
                                    ->required(),
                                DateTimePicker::make('selesai')
                                    ->label('Waktu Selesai')
                                    ->default(now()->addHours(2))
                                    ->required(),
                            ]),
                    ]),

                Section::make('Uraian & Rincian Agenda')
                    ->icon('heroicon-o-document-text')
                    ->columnSpanFull()
                    ->components([
                        Textarea::make('deskripsi')
                            ->label('Deskripsi / Catatan Agenda')
                            ->rows(4)
                            ->columnSpanFull()
                            ->placeholder('Jelaskan susunan acara, instruksi kehadiran, atau catatan penting'),
                    ]),
            ]);
    }
}
