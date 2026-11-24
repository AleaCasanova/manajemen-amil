<?php

namespace App\Filament\Resources\PengajuanDanas\Schemas;

use App\Models\KategoriKeuangan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PengajuanDanaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Identitas Pemohon & Keperluan')
                    ->icon('heroicon-o-user-group')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(3)
                            ->components([
                                Select::make('amil_id')
                                    ->label('Amil Pemohon')
                                    ->relationship('amil', 'nama')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Select::make('divisi_id')
                                    ->label('Divisi / Bidang')
                                    ->relationship('divisi', 'nama')
                                    ->searchable()
                                    ->preload(),

                                DatePicker::make('tgl_kebutuhan')
                                    ->label('Tanggal Kebutuhan Dana')
                                    ->default(today())
                                    ->required(),
                            ]),

                        TextInput::make('judul')
                            ->label('Keperluan / Judul Pengajuan')
                            ->placeholder('Contoh: Pengadaan 50 Paket Sembako Yatim / Servis Ambulans')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make('Nominal & Alokasi Anggaran')
                    ->icon('heroicon-o-banknotes')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(3)
                            ->components([
                                TextInput::make('nominal_diajukan')
                                    ->label('Nominal Diajukan (Rp)')
                                    ->prefix('Rp')
                                    ->numeric()
                                    ->required()
                                    ->minValue(1000),

                                Select::make('kategori_keuangan_id')
                                    ->label('Pos Kategori Pengeluaran')
                                    ->options(function () {
                                        return KategoriKeuangan::where('jenis', 'keluar')
                                            ->where('is_active', true)
                                            ->pluck('nama', 'id');
                                    })
                                    ->searchable()
                                    ->preload(),

                                Select::make('program_id')
                                    ->label('Program / Campaign Terkait')
                                    ->relationship('program', 'nama')
                                    ->searchable()
                                    ->preload(),
                            ]),

                        Textarea::make('deskripsi')
                            ->label('Uraian Kebutuhan & RAB')
                            ->rows(4)
                            ->required()
                            ->columnSpanFull()
                            ->placeholder('Rincian kebutuhan anggaran secara lengkap...'),
                    ]),

                Section::make('Referensi Resmi & Dokumen Pendukung')
                    ->icon('heroicon-o-document-text')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(2)
                            ->components([
                                TextInput::make('nomor_dokumen_referensi')
                                    ->label('No. Dokumen Referensi (Memo / Proposal / Surat Tugas)')
                                    ->placeholder('Contoh: MEMO/2026/011 / PROP-YTM-01'),

                                FileUpload::make('dokumen_pendukung')
                                    ->label('Upload Proposal / RAB (PDF/JPG/DOCX/XLSX)')
                                    ->disk('public')
                                    ->directory('pengajuan-dana/dokumen')
                                    ->maxSize(5120)
                                    ->openable()
                                    ->downloadable(),
                            ]),
                    ]),
            ]);
    }
}
