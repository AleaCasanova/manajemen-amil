<?php

namespace App\Filament\Resources\Mustahiks\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MustahikForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Identitas Mustahik & Asnaf')
                    ->icon('heroicon-o-user')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(3)
                            ->components([
                                TextInput::make('nama')
                                    ->label('Nama Lengkap Mustahik')
                                    ->required()
                                    ->placeholder('Nama lengkap penerima manfaat'),
                                TextInput::make('nik')
                                    ->label('Nomor Induk Kependudukan (NIK)')
                                    ->placeholder('16 digit NIK'),
                                Select::make('asnaf')
                                    ->label('Golongan Asnaf')
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
                            ]),
                        Select::make('status_verifikasi')
                            ->label('Status Kelayakan & Verifikasi')
                            ->options([
                                'menunggu' => 'Menunggu Verifikasi',
                                'terverifikasi' => 'Terverifikasi (Layak)',
                                'ditolak' => 'Ditolak (Tidak Layak)',
                            ])
                            ->default('menunggu')
                            ->required(),
                    ]),

                Section::make('Domisili & Kondisi Ekonomi Mustahik')
                    ->icon('heroicon-o-home')
                    ->columnSpanFull()
                    ->components([
                        Textarea::make('alamat')
                            ->label('Alamat Lengkap Tempat Tinggal')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Alamat lengkap domisili mustahik'),
                        Textarea::make('kondisi')
                            ->label('Uraian Kondisi Ekonomi & Alasan Bantuan')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Catatan hasil survei kondisi ekonomi atau kebutuhan mendesak'),
                    ]),
            ]);
    }
}
