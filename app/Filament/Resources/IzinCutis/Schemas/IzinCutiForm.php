<?php

namespace App\Filament\Resources\IzinCutis\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class IzinCutiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('amil_id')
                    ->label('Petugas Amil')
                    ->relationship('amil', 'nama')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('jenis')
                    ->label('Jenis Pengajuan')
                    ->options(['cuti' => 'Cuti', 'izin' => 'Izin', 'sakit' => 'Sakit'])
                    ->default('izin')
                    ->required(),
                DatePicker::make('tgl_mulai')
                    ->label('Tanggal Mulai')
                    ->required(),
                DatePicker::make('tgl_selesai')
                    ->label('Tanggal Selesai')
                    ->required(),
                Textarea::make('alasan')
                    ->label('Alasan Permohonan')
                    ->required()
                    ->columnSpanFull(),
                FileUpload::make('lampiran')
                    ->label('Lampiran Berkas / Surat Dokter')
                    ->disk('public')
                    ->directory('izin-cuti/lampiran')
                    ->openable()
                    ->downloadable()
                    ->maxSize(5120),
                Select::make('status')
                    ->options(['diajukan' => 'Diajukan', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak'])
                    ->default('diajukan')
                    ->required(),
                TextInput::make('approved_by')
                    ->numeric(),
                Textarea::make('catatan_admin')
                    ->columnSpanFull(),
            ]);
    }
}
