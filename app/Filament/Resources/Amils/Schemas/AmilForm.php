<?php

namespace App\Filament\Resources\Amils\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Password;

class AmilForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Akun Login Amil')
                    ->description('Akun sistem yang digunakan amil untuk masuk dan melakukan absensi/laporan')
                    ->icon('heroicon-o-user-circle')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(2)
                            ->components([
                                TextInput::make('email')
                                    ->label('Email Login')
                                    ->email()
                                    ->required(fn (string $operation): bool => $operation === 'create')
                                    ->unique('users', 'email', ignoreRecord: true)
                                    ->placeholder('contoh: amil@amilgsc.com')
                                    ->helperText('Digunakan untuk login ke panel amil'),
                                TextInput::make('password')
                                    ->label('Password Akun')
                                    ->password()
                                    ->revealable()
                                    ->rules(['nullable', Password::min(8)->letters()->numbers()])
                                    ->placeholder(fn (string $operation): string => $operation === 'create' ? 'Default: Password123 jika dikosongkan' : 'Kosongkan jika tidak ingin mengubah')
                                    ->helperText(fn (string $operation): string => $operation === 'create' ? 'Minimal 8 karakter kombinasi huruf & angka (Default: Password123)' : 'Biarkan kosong jika tidak diubah'),
                            ]),
                    ]),

                Section::make('Biodata & Identitas Amil')
                    ->description('Informasi lengkap mengenai amil')
                    ->icon('heroicon-o-identification')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(3)
                            ->components([
                                TextInput::make('nip')
                                    ->label('NIP (Nomor Induk Pegawai)')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->default(fn () => 'AML-'.date('Ym').rand(100, 999))
                                    ->helperText('NIP unik amil'),
                                TextInput::make('nama')
                                    ->label('Nama Lengkap')
                                    ->required()
                                    ->placeholder('Nama lengkap amil'),
                                TextInput::make('jabatan')
                                    ->label('Jabatan')
                                    ->placeholder('Contoh: Fundraising Officer, Staf Penyaluran'),
                            ]),
                        Grid::make(2)
                            ->components([
                                Select::make('divisi_id')
                                    ->label('Divisi')
                                    ->relationship('divisi', 'nama')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        TextInput::make('nama')->required(),
                                        Textarea::make('deskripsi'),
                                    ]),
                                TextInput::make('no_hp')
                                    ->label('Nomor WhatsApp / HP')
                                    ->tel()
                                    ->placeholder('08xxxxxxxxxx'),
                            ]),
                        FileUpload::make('foto')
                            ->label('Foto Profil Amil')
                            ->image()
                            ->avatar()
                            ->disk('public')
                            ->directory('amils/photos')
                            ->openable()
                            ->downloadable()
                            ->maxSize(3072)
                            ->columnSpanFull(),
                        Textarea::make('alamat')
                            ->label('Alamat Domisili')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('Kepegawaian & Target Donasi')
                    ->description('Status operasional dan target penghimpunan donasi amil')
                    ->icon('heroicon-o-briefcase')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(3)
                            ->components([
                                DatePicker::make('tgl_bergabung')
                                    ->label('Tanggal Bergabung')
                                    ->default(now())
                                    ->required(),
                                TextInput::make('target_bulanan')
                                    ->label('Target Donasi Bulanan (Rp)')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->required()
                                    ->helperText('Target penghimpunan donasi per bulan'),
                                Select::make('status')
                                    ->label('Status Keaktifan')
                                    ->options([
                                        'aktif' => 'Aktif',
                                        'nonaktif' => 'Nonaktif',
                                        'cuti' => 'Cuti',
                                        'mutasi' => 'Mutasi',
                                    ])
                                    ->default('aktif')
                                    ->required(),
                            ]),
                    ]),
            ]);
    }
}
