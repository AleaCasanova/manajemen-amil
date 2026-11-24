<?php

namespace App\Filament\Resources\AkunKeuangans\Schemas;

use App\Models\AkunKeuangan;
use App\Models\KategoriKeuangan;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AkunKeuanganForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas Akun Kas / Rekening Bank')
                    ->icon('heroicon-o-building-library')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(3)
                            ->components([
                                TextInput::make('kode_coa')
                                    ->label('Kode COA')
                                    ->placeholder('Contoh: 101.02.009')
                                    ->maxLength(30)
                                    ->unique(ignoreRecord: true),

                                Select::make('kategori_keuangan_id')
                                    ->label('Tautan Master COA Aset')
                                    ->options(fn (): array => KategoriKeuangan::query()
                                        ->where('kelompok', 'aset')
                                        ->orderBy('kode_coa')
                                        ->get()
                                        ->mapWithKeys(fn (KategoriKeuangan $k): array => [$k->id => $k->label_lengkap])
                                        ->all())
                                    ->searchable()
                                    ->preload(),

                                TextInput::make('nama')
                                    ->label('Nama Akun / Dompet')
                                    ->placeholder('Contoh: BSI Ops Cilacap')
                                    ->required()
                                    ->maxLength(255),
                            ]),

                        Grid::make(3)
                            ->components([
                                Select::make('tipe')
                                    ->label('Tipe Akun')
                                    ->options([
                                        'kas' => 'Kas Tunai',
                                        'bank' => 'Rekening Bank',
                                        'ewallet' => 'E-Wallet / Digital',
                                    ])
                                    ->default('bank')
                                    ->required(),

                                Select::make('fungsi')
                                    ->label('Fungsi Rekening')
                                    ->options(AkunKeuangan::FUNGSI_LABELS)
                                    ->default('operasional')
                                    ->required(),

                                Toggle::make('is_active')
                                    ->label('Akun Aktif')
                                    ->default(true)
                                    ->inline(false),
                            ]),

                        Grid::make(3)
                            ->components([
                                TextInput::make('nama_bank')
                                    ->label('Nama Bank / Penyelenggara')
                                    ->placeholder('Contoh: Bank Syariah Indonesia (BSI)')
                                    ->maxLength(255),

                                TextInput::make('nomor_rekening')
                                    ->label('Nomor Rekening')
                                    ->placeholder('Contoh: 7123456789')
                                    ->maxLength(100),

                                TextInput::make('atas_nama')
                                    ->label('Atas Nama')
                                    ->default('Yayasan Gerak Sedekah Cilacap')
                                    ->maxLength(255),
                            ]),

                        Grid::make(2)
                            ->components([
                                TextInput::make('saldo_awal')
                                    ->label('Saldo Awal Periode')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->required(),

                                TextInput::make('saldo_minimal')
                                    ->label('Saldo Minimal Mengendap (Alert)')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->helperText('Batas minimum saldo rekening yang dipertahankan.'),
                            ]),

                        Textarea::make('keterangan')
                            ->label('Keterangan Tambahan')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
