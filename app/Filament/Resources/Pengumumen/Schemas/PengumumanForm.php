<?php

namespace App\Filament\Resources\Pengumumen\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PengumumanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informasi & Publikasi Pengumuman')
                    ->icon('heroicon-o-megaphone')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(3)
                            ->components([
                                TextInput::make('judul')
                                    ->label('Judul Pengumuman')
                                    ->required()
                                    ->placeholder('Judul pengumuman resmi'),
                                Select::make('target')
                                    ->label('Sasaran Pengumuman')
                                    ->options([
                                        'semua' => 'Semua Amil & Karyawan',
                                        'fundraising' => 'Divisi Fundraising',
                                        'penyaluran' => 'Divisi Penyaluran',
                                        'operasional' => 'Divisi Operasional & Umum',
                                    ])
                                    ->default('semua')
                                    ->required(),
                                DateTimePicker::make('published_at')
                                    ->label('Jadwal Tayang / Publikasi')
                                    ->default(now()),
                            ]),
                    ]),

                Section::make('Konten Pengumuman & Poster Banner')
                    ->icon('heroicon-o-document-text')
                    ->columnSpanFull()
                    ->components([
                        Textarea::make('isi')
                            ->label('Isi Lengkap Pengumuman')
                            ->rows(5)
                            ->required()
                            ->columnSpanFull()
                            ->placeholder('Tuliskan isi pengumuman atau instruksi di sini...'),
                        FileUpload::make('lampiran')
                            ->label('Lampiran / Poster Banner')
                            ->image()
                            ->disk('public')
                            ->directory('pengumuman/lampiran')
                            ->openable()
                            ->downloadable()
                            ->maxSize(5120)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
