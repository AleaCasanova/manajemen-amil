<?php

namespace App\Filament\Resources\KategoriKeuangans\Schemas;

use App\Models\KategoriKeuangan;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class KategoriKeuanganForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Akun Chart of Accounts (COA)')
                    ->icon('heroicon-o-list-bullet')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(3)
                            ->components([
                                TextInput::make('kode_coa')
                                    ->label('Kode COA')
                                    ->placeholder('Contoh: 501.01.001')
                                    ->maxLength(30)
                                    ->unique(ignoreRecord: true),

                                TextInput::make('nama')
                                    ->label('Nama Akun / Pos')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(2),
                            ]),

                        Grid::make(3)
                            ->components([
                                Select::make('parent_id')
                                    ->label('Induk COA (Header)')
                                    ->options(fn (): array => KategoriKeuangan::query()
                                        ->where('level', '<', 3)
                                        ->orderBy('kode_coa')
                                        ->get()
                                        ->mapWithKeys(fn (KategoriKeuangan $k): array => [$k->id => $k->label_lengkap])
                                        ->all())
                                    ->searchable()
                                    ->preload(),

                                Select::make('level')
                                    ->label('Level Hirarki')
                                    ->options([
                                        1 => 'Level 1 (Kelompok Utama)',
                                        2 => 'Level 2 (Sub-Kelompok / Header)',
                                        3 => 'Level 3 (Akun Detail / Transaksi)',
                                    ])
                                    ->default(3)
                                    ->required(),

                                Select::make('kelompok')
                                    ->label('Kelompok Akun')
                                    ->options(KategoriKeuangan::KELOMPOK_LABELS)
                                    ->default('penyaluran')
                                    ->required(),
                            ]),

                        Grid::make(3)
                            ->components([
                                Select::make('pos_dana')
                                    ->label('Pos Dana Syariah')
                                    ->options(KategoriKeuangan::POS_DANA_LABELS)
                                    ->default('netral')
                                    ->required(),

                                Select::make('jenis')
                                    ->label('Arah Arus Kas Default')
                                    ->options([
                                        'masuk' => 'Masuk (Penerimaan)',
                                        'keluar' => 'Keluar (Pengeluaran / Beban)',
                                        'netral' => 'Netral (Neraca / Aset / Kewajiban)',
                                    ])
                                    ->default('keluar')
                                    ->required(),

                                Select::make('saldo_normal')
                                    ->label('Saldo Normal')
                                    ->options([
                                        'debit' => 'Debit',
                                        'kredit' => 'Kredit',
                                    ])
                                    ->default('debit')
                                    ->required(),
                            ]),

                        Grid::make(2)
                            ->components([
                                Toggle::make('is_postable')
                                    ->label('Dapat Dipilih Saat Input Transaksi (Postable)')
                                    ->helperText('Nonaktifkan jika akun ini hanya bertindak sebagai Header / Induk kelompok.')
                                    ->default(true)
                                    ->inline(false),

                                Toggle::make('is_active')
                                    ->label('Status Aktif')
                                    ->default(true)
                                    ->inline(false),
                            ]),

                        Textarea::make('deskripsi')
                            ->label('Deskripsi / Catatan Akun')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
