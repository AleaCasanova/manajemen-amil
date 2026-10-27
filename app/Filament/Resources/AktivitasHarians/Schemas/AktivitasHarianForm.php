<?php

namespace App\Filament\Resources\AktivitasHarians\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

class AktivitasHarianForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informasi Kegiatan & Petugas Amil')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(4)
                            ->components([
                                TextInput::make('judul')
                                    ->label('Judul Aktivitas')
                                    ->required()
                                    ->placeholder('Contoh: Penyaluran Sembako RW 04'),
                                Select::make('amil_id')
                                    ->label('Petugas Amil')
                                    ->relationship('amil', 'nama')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                DatePicker::make('tanggal')
                                    ->label('Tanggal Kegiatan')
                                    ->default(now())
                                    ->required(),
                                Select::make('status')
                                    ->label('Status Review')
                                    ->options([
                                        'menunggu' => 'Menunggu Review',
                                        'disetujui' => 'Disetujui',
                                        'ditolak' => 'Ditolak',
                                    ])
                                    ->default('disetujui')
                                    ->required(),
                            ]),
                    ]),

                Section::make('Uraian Narasi & Hasil Kegiatan')
                    ->icon('heroicon-o-document-text')
                    ->columnSpanFull()
                    ->components([
                        Textarea::make('deskripsi')
                            ->label('Deskripsi Lengkap Kegiatan')
                            ->rows(4)
                            ->required()
                            ->columnSpanFull()
                            ->placeholder('Jelaskan detail jalannya kegiatan, output, dan pihak terkait yang ditemui'),
                    ]),

                Section::make('Lokasi & Titik Koordinat Lapangan')
                    ->icon('heroicon-o-map-pin')
                    ->columnSpanFull()
                    ->components([
                        TextInput::make('lokasi')
                            ->label('Nama Lokasi / Tempat Kegiatan')
                            ->placeholder('Nama masjid, kelurahan, alamat warga, dsb')
                            ->columnSpanFull(),
                        View::make('filament.forms.components.map-picker')
                            ->viewData([
                                'latField' => 'lat',
                                'lngField' => 'lng',
                                'addressField' => 'lokasi',
                                'defaultLat' => -7.7027157,
                                'defaultLng' => 109.0270034,
                            ])
                            ->columnSpanFull(),
                        Grid::make(2)
                            ->components([
                                TextInput::make('lat')
                                    ->label('GPS Latitude')
                                    ->numeric(),
                                TextInput::make('lng')
                                    ->label('GPS Longitude')
                                    ->numeric(),
                            ]),
                    ]),

                Section::make('Foto Dokumentasi Lapangan')
                    ->icon('heroicon-o-camera')
                    ->columnSpanFull()
                    ->components([
                        FileUpload::make('foto')
                            ->label('Foto Bukti Dokumentasi Kegiatan')
                            ->image()
                            ->disk('public')
                            ->directory('aktivitas/foto')
                            ->openable()
                            ->downloadable()
                            ->maxSize(5120)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
