<?php

namespace App\Filament\Resources\AktivitasHarians\Pages;

use App\Exports\AktivitasHarianExport;
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
use Maatwebsite\Excel\Facades\Excel;

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

    public function setujuiSemuaAktivitas(int $amilId): void
    {
        $amil = Amil::findOrFail($amilId);
        $pendingActivities = AktivitasHarian::where('amil_id', $amilId)
            ->where('status', 'diajukan')
            ->get();

        $count = $pendingActivities->count();

        if ($count === 0) {
            Notification::make()
                ->title('Tidak Ada Laporan Pending')
                ->body("Semua laporan untuk {$amil->nama} sudah berstatus disetujui atau ditolak.")
                ->info()
                ->send();

            return;
        }

        foreach ($pendingActivities as $aktivitas) {
            $aktivitas->update(['status' => 'disetujui']);
        }

        Notification::make()
            ->title('Semua Laporan Berhasil Disetujui')
            ->body("Sebanyak {$count} laporan aktivitas dari {$amil->nama} berhasil disetujui sekaligus.")
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
            Action::make('exportExcel')
                ->label('Unduh Rekap Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->modalHeading('Unduh Rekap Laporan Aktivitas Harian')
                ->modalDescription('Pilih periode bulan dan filter status aktivitas yang ingin diunduh dalam format Excel.')
                ->modalSubmitActionLabel('Unduh File Excel')
                ->form([
                    Select::make('bulan')
                        ->label('Pilih Bulan')
                        ->options([
                            1 => 'Januari',
                            2 => 'Februari',
                            3 => 'Maret',
                            4 => 'April',
                            5 => 'Mei',
                            6 => 'Juni',
                            7 => 'Juli',
                            8 => 'Agustus',
                            9 => 'September',
                            10 => 'Oktober',
                            11 => 'November',
                            12 => 'Desember',
                        ])
                        ->default((int) now('Asia/Jakarta')->month)
                        ->required(),
                    Select::make('tahun')
                        ->label('Pilih Tahun')
                        ->options(function () {
                            $current = (int) now('Asia/Jakarta')->year;
                            $years = [];
                            for ($y = $current - 2; $y <= $current + 1; $y++) {
                                $years[$y] = (string) $y;
                            }

                            return $years;
                        })
                        ->default((int) now('Asia/Jakarta')->year)
                        ->required(),
                    Select::make('status')
                        ->label('Status Review Laporan')
                        ->options([
                            'semua' => 'Semua Status (Disetujui, Menunggu Review & Ditolak)',
                            'disetujui' => 'Hanya yang Disetujui (Terverifikasi)',
                            'diajukan' => 'Hanya yang Menunggu Review',
                            'ditolak' => 'Hanya yang Ditolak',
                        ])
                        ->default('semua')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $month = (int) $data['bulan'];
                    $year = (int) $data['tahun'];
                    $status = $data['status'] ?? 'semua';

                    $query = $this->getFilteredSortedTableQuery() ?? Amil::query()->where('status', 'aktif');
                    $amils = (clone $query)->with(['divisi', 'aktivitasHarians'])->get();

                    $filename = sprintf(
                        'rekap-aktivitas-amil-%04d-%02d-%s.xlsx',
                        $year,
                        $month,
                        now('Asia/Jakarta')->format('His')
                    );

                    return Excel::download(
                        new AktivitasHarianExport($amils, $month, $year, $status),
                        $filename
                    );
                }),

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
