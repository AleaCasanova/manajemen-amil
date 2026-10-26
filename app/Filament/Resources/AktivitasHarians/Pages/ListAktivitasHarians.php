<?php

namespace App\Filament\Resources\AktivitasHarians\Pages;

use App\Filament\Resources\AktivitasHarians\AktivitasHarianResource;
use App\Models\AktivitasHarian;
use App\Models\Amil;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\View;

class ListAktivitasHarians extends ListRecords
{
    protected static string $resource = AktivitasHarianResource::class;

    public function setujuiAktivitas(int $id): void
    {
        $aktivitas = AktivitasHarian::findOrFail($id);
        $aktivitas->update(['status' => 'disetujui']);

        Notification::make()
            ->title('Laporan Aktivitas Disetujui')
            ->body("Aktivitas '{$aktivitas->judul}' dari {$aktivitas->amil?->nama} telah disetujui.")
            ->success()
            ->send();
    }

    public function tolakAktivitas(int $id): void
    {
        $aktivitas = AktivitasHarian::findOrFail($id);
        $aktivitas->update(['status' => 'ditolak']);

        Notification::make()
            ->title('Laporan Aktivitas Ditolak')
            ->body("Aktivitas '{$aktivitas->judul}' dari {$aktivitas->amil?->nama} telah ditolak.")
            ->warning()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('buat_laporan_baru')
                ->label('Buat Laporan Aktivitas')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->modalHeading('Buat Laporan Aktivitas Harian')
                ->form([
                    Select::make('amil_id')
                        ->label('Pilih Amil')
                        ->options(Amil::where('status', 'aktif')->pluck('nama', 'id'))
                        ->searchable()
                        ->preload()
                        ->required(),
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
                        ->label('Foto Dokumentasi Kegiatan')
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
                ->action(function (array $data): void {
                    AktivitasHarian::create([
                        'amil_id' => $data['amil_id'],
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
                        ->success()
                        ->send();
                }),
        ];
    }
}
