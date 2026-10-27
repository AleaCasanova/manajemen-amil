<?php

namespace App\Filament\Resources\IzinCutis\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class IzinCutiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informasi Pengajuan Izin / Cuti Amil')
                    ->icon('heroicon-o-calendar-days')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(4)
                            ->components([
                                Select::make('amil_id')
                                    ->label('Petugas Amil')
                                    ->relationship('amil', 'nama')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('jenis')
                                    ->label('Jenis Pengajuan')
                                    ->options([
                                        'cuti' => 'Cuti Tahunan',
                                        'izin' => 'Izin',
                                        'sakit' => 'Sakit',
                                    ])
                                    ->default('cuti')
                                    ->required(),
                                DatePicker::make('tgl_mulai')
                                    ->label('Tanggal Mulai')
                                    ->default(now())
                                    ->required(),
                                DatePicker::make('tgl_selesai')
                                    ->label('Tanggal Selesai')
                                    ->default(now())
                                    ->required(),
                            ]),
                    ]),

                Section::make('Alasan Permohonan & Berkas Lampiran')
                    ->icon('heroicon-o-paper-clip')
                    ->columnSpanFull()
                    ->components([
                        Textarea::make('alasan')
                            ->label('Alasan / Keperluan Permohonan')
                            ->rows(3)
                            ->required()
                            ->columnSpanFull()
                            ->placeholder('Jelaskan keperluan izin atau alasan cuti...'),
                        FileUpload::make('lampiran')
                            ->label('Lampiran Berkas / Surat Dokter')
                            ->disk('public')
                            ->directory('izin-cuti/lampiran')
                            ->openable()
                            ->downloadable()
                            ->maxSize(5120)
                            ->columnSpanFull(),
                    ]),

                Section::make('Status Persetujuan Admin')
                    ->icon('heroicon-o-check-circle')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(2)
                            ->components([
                                Select::make('status')
                                    ->label('Status Persetujuan')
                                    ->options([
                                        'diajukan' => 'Menunggu Review',
                                        'disetujui' => 'Disetujui',
                                        'ditolak' => 'Ditolak',
                                    ])
                                    ->default('disetujui')
                                    ->required(),
                                Textarea::make('catatan_admin')
                                    ->label('Catatan Verifikasi Admin')
                                    ->rows(2)
                                    ->placeholder('Catatan atau alasan penolakan jika ditolak'),
                            ]),
                    ]),
            ]);
    }
}
