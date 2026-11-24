<?php

namespace App\Filament\Resources\CashAdvances\Schemas;

use App\Models\AkunKeuangan;
use App\Models\Amil;
use App\Models\Divisi;
use App\Models\PengajuanDana;
use App\Models\Program;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class CashAdvanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pencairan Cash Advance / Cash Bon')
                    ->description('Mencatat pencairan uang muka kegiatan (COA 101.09.000) atau kasbon karyawan (COA 101.06.001) dan otomatis memotong saldo akun sumber.')
                    ->icon('heroicon-o-credit-card')
                    ->columnSpanFull()
                    ->components([
                        Radio::make('tipe')
                            ->label('Jenis Uang Muka')
                            ->options([
                                'cash_advance' => 'Cash Advance (Uang Muka Kegiatan / LPJ — COA 101.09.000)',
                                'cash_bon' => 'Cash Bon (Pinjaman / Kasbon Karyawan — COA 101.06.001)',
                            ])
                            ->default('cash_advance')
                            ->live()
                            ->required()
                            ->columnSpanFull(),

                        Grid::make(3)
                            ->components([
                                DatePicker::make('tgl_pencairan')
                                    ->label('Tanggal Pencairan')
                                    ->default(today())
                                    ->required(),

                                DatePicker::make('tgl_jatuh_tempo')
                                    ->label('Jatuh Tempo LPJ / Pelunasan')
                                    ->helperText('Batas waktu penyampaian LPJ atau pengembalian kasbon.'),

                                Select::make('akun_keuangan_id')
                                    ->label('Sumber Dana (Kas / Bank)')
                                    ->options(function (): array {
                                        return AkunKeuangan::withMutasiTotals()
                                            ->where('is_active', true)
                                            ->orderBy('kode_coa')
                                            ->orderBy('id')
                                            ->get()
                                            ->mapWithKeys(function (AkunKeuangan $akun): array {
                                                $saldo = number_format($akun->saldo_sekarang, 0, ',', '.');
                                                $prefix = $akun->kode_coa ? "[{$akun->kode_coa}] " : '';

                                                return [$akun->id => "{$prefix}{$akun->nama} (Saldo: Rp {$saldo})"];
                                            })
                                            ->all();
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                            ]),

                        Grid::make(3)
                            ->components([
                                TextInput::make('nominal_dicairkan')
                                    ->label('Nominal Dicairkan')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->minValue(1)
                                    ->required(),

                                Select::make('amil_id')
                                    ->label('Amil / Staf Penanggung Jawab')
                                    ->options(fn (): array => Amil::orderBy('nama')->pluck('nama', 'id')->all())
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function (?string $state, Set $set): void {
                                        if (! $state) {
                                            return;
                                        }
                                        $amil = Amil::find($state);
                                        if ($amil) {
                                            $set('nama_pj', $amil->nama);
                                            if ($amil->divisi_id) {
                                                $set('divisi_id', $amil->divisi_id);
                                            }
                                        }
                                    }),

                                TextInput::make('nama_pj')
                                    ->label('Nama Penanggung Jawab (PJ)')
                                    ->placeholder('Otomatis dari Amil atau ketik manual')
                                    ->required()
                                    ->maxLength(255),
                            ]),

                        Grid::make(3)
                            ->components([
                                Select::make('divisi_id')
                                    ->label('Departemen / Divisi')
                                    ->options(fn (): array => Divisi::orderBy('nama')->pluck('nama', 'id')->all())
                                    ->searchable()
                                    ->preload(),

                                Select::make('program_id')
                                    ->label('Program / Sub-Program Terkait')
                                    ->options(fn (): array => Program::orderBy('nama')->pluck('nama', 'id')->all())
                                    ->searchable()
                                    ->preload()
                                    ->visible(fn (Get $get): bool => ($get('tipe') ?? 'cash_advance') === 'cash_advance'),

                                Select::make('pengajuan_dana_id')
                                    ->label('Tautkan Pengajuan Dana (Opsional)')
                                    ->options(fn (): array => PengajuanDana::query()
                                        ->orderByDesc('id')
                                        ->limit(100)
                                        ->get()
                                        ->mapWithKeys(fn (PengajuanDana $pd): array => [$pd->id => "{$pd->kode_pengajuan} — {$pd->judul}"])
                                        ->all())
                                    ->searchable()
                                    ->preload(),
                            ]),

                        Textarea::make('keperluan')
                            ->label('Keperluan / Tujuan Pencairan')
                            ->placeholder('Contoh: Uang muka operasional santunan Ramadhan atau pinjaman sementara...')
                            ->rows(3)
                            ->required()
                            ->columnSpanFull(),

                        FileUpload::make('bukti_pencairan')
                            ->label('Bukti Pencairan / Kuitansi (Opsional)')
                            ->disk('public')
                            ->directory('bukti-cash-advance')
                            ->image()
                            ->imageEditor()
                            ->maxSize(2048)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
