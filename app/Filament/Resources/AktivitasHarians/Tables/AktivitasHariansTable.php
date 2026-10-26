<?php

namespace App\Filament\Resources\AktivitasHarians\Tables;

use App\Models\AktivitasHarian;
use App\Models\Amil;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\View;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AktivitasHariansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto')
                    ->label('Foto')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(fn (Amil $record): string => 'https://ui-avatars.com/api/?name='.urlencode($record->nama).'&color=059669&background=d1fae5'),

                TextColumn::make('nama')
                    ->label('Nama Amil')
                    ->description(fn (Amil $record): string => ($record->nip ?? '-').' • '.($record->divisi?->nama ?? '-'))
                    ->searchable(['nama', 'nip'])
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('laporan_bulan_ini')
                    ->label('Laporan Bulan Ini')
                    ->state(function (Amil $record): string {
                        $now = Carbon::now();
                        $count = $record->aktivitasHarians
                            ->filter(fn ($a) => Carbon::parse($a->tanggal)->month === $now->month && Carbon::parse($a->tanggal)->year === $now->year)
                            ->count();

                        return $count.' Aktivitas';
                    })
                    ->badge()
                    ->color('gray'),

                TextColumn::make('menunggu_verifikasi')
                    ->label('Status Review')
                    ->state(function (Amil $record): string {
                        $pending = $record->aktivitasHarians->where('status', 'diajukan')->count();

                        return $pending > 0 ? "{$pending} Perlu Review" : 'Semua Terverifikasi';
                    })
                    ->badge()
                    ->color(function (Amil $record): string {
                        $pending = $record->aktivitasHarians->where('status', 'diajukan')->count();

                        return $pending > 0 ? 'warning' : 'success';
                    }),

                TextColumn::make('aktivitas_terakhir')
                    ->label('Aktivitas Terakhir')
                    ->state(function (Amil $record): string {
                        $last = $record->aktivitasHarians->sortByDesc('tanggal')->first();
                        if (! $last) {
                            return 'Belum ada laporan';
                        }

                        $tgl = Carbon::parse($last->tanggal)->format('d M Y');

                        return "{$last->judul} ({$tgl})";
                    })
                    ->limit(45)
                    ->tooltip(function (Amil $record): ?string {
                        $last = $record->aktivitasHarians->sortByDesc('tanggal')->first();

                        return $last ? "{$last->judul} - ".Carbon::parse($last->tanggal)->format('d M Y') : null;
                    }),

                TextColumn::make('lokasi_terakhir')
                    ->label('Lokasi Terakhir')
                    ->state(function (Amil $record): string {
                        $last = $record->aktivitasHarians->sortByDesc('tanggal')->first();

                        return $last?->lokasi ?: '-';
                    })
                    ->icon('heroicon-m-map-pin')
                    ->limit(30)
                    ->placeholder('-'),
            ])
            ->filters([
                SelectFilter::make('divisi_id')
                    ->relationship('divisi', 'nama')
                    ->label('Filter Divisi'),
                SelectFilter::make('status')
                    ->options([
                        'aktif' => 'Amil Aktif',
                        'nonaktif' => 'Nonaktif',
                    ])
                    ->default('aktif')
                    ->label('Status Amil'),
            ])
            ->recordActions([
                Action::make('riwayat_aktivitas')
                    ->label('Riwayat Aktivitas')
                    ->icon('heroicon-o-document-text')
                    ->color('success')
                    ->slideOver()
                    ->modalWidth('5xl')
                    ->modalHeading(fn (Amil $record): string => "Laporan Aktivitas: {$record->nama}")
                    ->modalDescription(fn (Amil $record): string => "NIP: {$record->nip} • {$record->divisi?->nama}")
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalContent(fn (Amil $record) => view('filament.modals.aktivitas-history', ['amil' => $record])),

                Action::make('buat_laporan_amil')
                    ->label('Buat Laporan')
                    ->icon('heroicon-o-plus-circle')
                    ->color('primary')
                    ->modalHeading(fn (Amil $record): string => "Buat Laporan Aktivitas — {$record->nama}")
                    ->form([
                        DatePicker::make('tanggal')
                            ->label('Tanggal Kegiatan')
                            ->default(now()->toDateString())
                            ->required(),
                        TextInput::make('judul')
                            ->label('Judul Aktivitas')
                            ->required(),
                        Textarea::make('deskripsi')
                            ->label('Deskripsi Kegiatan')
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('lokasi')
                            ->label('Lokasi / Tempat Kegiatan'),
                        View::make('filament.forms.components.map-picker')
                            ->viewData([
                                'latField' => 'lat',
                                'lngField' => 'lng',
                                'addressField' => 'lokasi',
                                'defaultLat' => -7.7027157,
                                'defaultLng' => 109.0270034,
                            ])
                            ->columnSpanFull(),
                        TextInput::make('lat')
                            ->label('GPS Latitude')
                            ->numeric(),
                        TextInput::make('lng')
                            ->label('GPS Longitude')
                            ->numeric(),
                        FileUpload::make('foto')
                            ->label('Foto Dokumentasi')
                            ->image()
                            ->disk('public')
                            ->directory('aktivitas/foto')
                            ->columnSpanFull(),
                        Select::make('status')
                            ->label('Status Laporan')
                            ->options([
                                'diajukan' => 'Menunggu Review',
                                'disetujui' => 'Langsung Disetujui',
                            ])
                            ->default('disetujui')
                            ->required(),
                    ])
                    ->action(function (Amil $record, array $data): void {
                        AktivitasHarian::create([
                            'amil_id' => $record->id,
                            'tanggal' => $data['tanggal'],
                            'judul' => $data['judul'],
                            'deskripsi' => $data['deskripsi'],
                            'lokasi' => $data['lokasi'] ?? null,
                            'lat' => $data['lat'] ?? null,
                            'lng' => $data['lng'] ?? null,
                            'foto' => $data['foto'] ?? null,
                            'status' => $data['status'],
                        ]);

                        Notification::make()
                            ->title('Laporan Aktivitas Berhasil Disimpan')
                            ->body("Aktivitas '{$data['judul']}' untuk {$record->nama} tersimpan.")
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
