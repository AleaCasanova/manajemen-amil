<?php

namespace App\Filament\Resources\Penyalurans\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PenyaluranForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('mustahik_id')
                    ->label('Penerima Manfaat (Mustahik)')
                    ->relationship('mustahik', 'nama')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('program_id')
                    ->label('Program Penyaluran')
                    ->relationship('program', 'nama')
                    ->searchable()
                    ->preload(),

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

                DatePicker::make('tgl_salur')
                    ->label('Tanggal Penyaluran')
                    ->default(now())
                    ->required(),

                Select::make('amil_id')
                    ->label('Amil Penanggung Jawab / Penyalur')
                    ->relationship('amil', 'nama')
                    ->searchable()
                    ->preload()
                    ->required(),

                FileUpload::make('bukti')
                    ->label('Foto Berita Acara / Dokumentasi Penyerahan')
                    ->image()
                    ->disk('public')
                    ->directory('penyaluran/bukti')
                    ->openable()
                    ->downloadable()
                    ->maxSize(5120)
                    ->columnSpanFull(),

                Textarea::make('catatan')
                    ->label('Catatan Tambahan')
                    ->placeholder('Rincian bantuan yang diserahkan...')
                    ->columnSpanFull(),
            ]);
    }
}
