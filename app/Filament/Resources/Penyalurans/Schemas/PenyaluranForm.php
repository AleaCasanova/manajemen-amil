<?php

namespace App\Filament\Resources\Penyalurans\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PenyaluranForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Penerima Manfaat (Mustahik) & Petugas Penyalur')
                    ->icon('heroicon-o-user-group')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(2)
                            ->components([
                                Select::make('mustahik_id')
                                    ->label('Penerima Manfaat (Mustahik)')
                                    ->relationship('mustahik', 'nama')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->helperText('Pilih mustahik yang telah terverifikasi'),
                                Select::make('amil_id')
                                    ->label('Amil Penanggung Jawab / Penyalur')
                                    ->relationship('amil', 'nama')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->helperText('Petugas amil yang menyerahkan bantuan'),
                            ]),
                    ]),

                Section::make('Rincian Dana & Program Penyaluran')
                    ->icon('heroicon-o-banknotes')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(3)
                            ->components([
                                Select::make('program_id')
                                    ->label('Program Penyaluran')
                                    ->relationship('program', 'nama')
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Opsional (Program Umum)'),
                                Select::make('jenis_dana_id')
                                    ->label('Sumber Jenis Dana')
                                    ->relationship('jenisDana', 'nama')
                                    ->preload()
                                    ->required(),
                                TextInput::make('jumlah')
                                    ->label('Nominal Bantuan (Rp)')
                                    ->prefix('Rp')
                                    ->required()
                                    ->numeric()
                                    ->minValue(1000),
                            ]),
                        DatePicker::make('tgl_salur')
                            ->label('Tanggal Penyaluran')
                            ->default(now())
                            ->required(),
                    ]),

                Section::make('Dokumentasi & Berita Acara Penyerahan')
                    ->icon('heroicon-o-photo')
                    ->columnSpanFull()
                    ->components([
                        FileUpload::make('bukti')
                            ->label('Foto Berita Acara / Dokumentasi Penyerahan')
                            ->image()
                            ->disk('public')
                            ->directory('penyaluran/bukti')
                            ->openable()
                            ->downloadable()
                            ->maxSize(5120)
                            ->columnSpanFull(),
                        Textarea::make('keterangan')
                            ->label('Catatan Tambahan Penyaluran')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Tambahkan catatan situasi penyerahan atau kondisi penerima manfaat'),
                    ]),
            ]);
    }
}
