<?php

namespace App\Filament\Resources\Donaturs\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DonaturForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Identitas & Klasifikasi Donatur')
                    ->icon('heroicon-o-identification')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(3)
                            ->components([
                                TextInput::make('kode_donatur')
                                    ->label('Kode Donatur')
                                    ->default(fn () => 'DNT-'.date('Ym').rand(1000, 9999))
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->readOnly(),
                                TextInput::make('nama')
                                    ->label('Nama Lengkap / Instansi')
                                    ->required()
                                    ->placeholder('Nama donatur atau perusahaan'),
                                Select::make('jenis')
                                    ->label('Tipe Donatur')
                                    ->options([
                                        'individu' => 'Perorangan / Individu',
                                        'lembaga' => 'Perusahaan / Lembaga / Komunitas',
                                    ])
                                    ->default('individu')
                                    ->required(),
                            ]),
                        Grid::make(2)
                            ->components([
                                Select::make('kategori_id')
                                    ->label('Kategori Donatur')
                                    ->relationship('kategori', 'nama')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->createOptionForm([
                                        TextInput::make('nama')->required(),
                                        Textarea::make('deskripsi'),
                                    ]),
                                Select::make('amil_pic_id')
                                    ->label('Amil PIC / Pembina Donatur')
                                    ->relationship('amilPic', 'nama')
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Pilih petugas amil penanggung jawab'),
                            ]),
                    ]),

                Section::make('Kontak & Komunikasi')
                    ->icon('heroicon-o-phone')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(2)
                            ->components([
                                TextInput::make('no_hp')
                                    ->label('Nomor WhatsApp / HP')
                                    ->tel()
                                    ->required()
                                    ->unique('donaturs', 'no_hp', ignoreRecord: true)
                                    ->placeholder('08xxxxxxxxxx')
                                    ->helperText('Sistem otomatis mencegah duplikasi nomor WhatsApp donatur'),
                                TextInput::make('email')
                                    ->label('Alamat Email')
                                    ->email()
                                    ->placeholder('donatur@email.com'),
                            ]),
                        Grid::make(2)
                            ->components([
                                Toggle::make('consent')
                                    ->label('Persetujuan Menerima Laporan & Informasi (Consent)')
                                    ->default(true)
                                    ->helperText('Donatur bersedia dihubungi via WA/Email untuk laporan program'),
                                Toggle::make('is_anonim')
                                    ->label('Donatur Anonim (Hamba Allah)')
                                    ->default(false)
                                    ->helperText('Jika aktif, nama pada kwitansi dicantumkan sebagai Hamba Allah'),
                            ]),
                    ]),

                Section::make('Alamat Domisili')
                    ->icon('heroicon-o-map-pin')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(3)
                            ->components([
                                TextInput::make('provinsi')
                                    ->label('Provinsi')
                                    ->placeholder('Jawa Barat'),
                                TextInput::make('kota')
                                    ->label('Kota / Kabupaten')
                                    ->placeholder('Bandung'),
                                TextInput::make('kecamatan')
                                    ->label('Kecamatan'),
                            ]),
                        Grid::make(2)
                            ->components([
                                TextInput::make('kelurahan')
                                    ->label('Kelurahan / Desa'),
                                TextInput::make('kode_pos')
                                    ->label('Kode Pos')
                                    ->numeric(),
                            ]),
                        Textarea::make('alamat')
                            ->label('Alamat Lengkap (Jalan, RT/RW, No Rumah)')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Section::make('Informasi Perpajakan & Ulang Tahun (Opsional)')
                    ->icon('heroicon-o-document-text')
                    ->collapsible()
                    ->collapsed()
                    ->columnSpanFull()
                    ->components([
                        Grid::make(3)
                            ->components([
                                TextInput::make('nik')
                                    ->label('NIK (Bukti Setor Pajak)')
                                    ->placeholder('16 digit NIK'),
                                TextInput::make('npwp')
                                    ->label('NPWP (Jika Badan / Perusahaan)')
                                    ->placeholder('15/16 digit NPWP'),
                                DatePicker::make('tgl_lahir')
                                    ->label('Tanggal Lahir')
                                    ->helperText('Untuk program ucapan milad / ulang tahun donatur'),
                            ]),
                    ]),
            ]);
    }
}
