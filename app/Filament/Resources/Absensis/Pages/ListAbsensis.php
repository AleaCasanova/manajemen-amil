<?php

namespace App\Filament\Resources\Absensis\Pages;

use App\Exports\AbsensiExport;
use App\Filament\Resources\Absensis\AbsensiResource;
use App\Models\Absensi;
use App\Models\Amil;
use App\Services\AbsensiService;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListAbsensis extends ListRecords
{
    protected static string $resource = AbsensiResource::class;

    public function mount(): void
    {
        app(AbsensiService::class)->autoSyncPastAlpa();
        parent::mount();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportExcel')
                ->label('Unduh Rekap Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->modalHeading('Unduh Rekap Presensi Amil')
                ->modalDescription('Pilih periode bulan dan tahun presensi yang ingin diunduh ke Excel.')
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
                ])
                ->action(function (array $data) {
                    $month = (int) $data['bulan'];
                    $year = (int) $data['tahun'];

                    $query = $this->getFilteredSortedTableQuery() ?? Amil::query()->where('status', 'aktif');
                    $amils = (clone $query)->with(['divisi', 'absensis'])->get();

                    $filename = sprintf(
                        'rekap-absensi-amil-%04d-%02d-%s.xlsx',
                        $year,
                        $month,
                        now('Asia/Jakarta')->format('His')
                    );

                    return Excel::download(
                        new AbsensiExport($amils, $month, $year),
                        $filename
                    );
                }),

            Action::make('catat_presensi_baru')
                ->label('Catat Presensi Amil')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->modalHeading('Catat Presensi Petugas Amil')
                ->modalDescription('Input data presensi harian untuk amil yang bersangkutan.')
                ->form([
                    Select::make('amil_id')
                        ->label('Pilih Amil')
                        ->options(Amil::where('status', 'aktif')->pluck('nama', 'id'))
                        ->searchable()
                        ->preload()
                        ->required(),
                    DatePicker::make('tanggal')
                        ->label('Tanggal Presensi')
                        ->default(now()->toDateString())
                        ->required(),
                    Select::make('status')
                        ->label('Status Kehadiran')
                        ->options([
                            'hadir' => 'Hadir Tepat Waktu',
                            'terlambat' => 'Terlambat',
                            'izin' => 'Izin',
                            'cuti' => 'Cuti',
                            'alpa' => 'Alpa',
                        ])
                        ->default('hadir')
                        ->live()
                        ->required(),
                    TimePicker::make('jam_masuk')
                        ->label('Jam Masuk')
                        ->default('08:00')
                        ->visible(fn ($get): bool => in_array($get('status'), ['hadir', 'terlambat'], true)),
                    TimePicker::make('jam_pulang')
                        ->label('Jam Pulang')
                        ->default('16:30')
                        ->visible(fn ($get): bool => in_array($get('status'), ['hadir', 'terlambat'], true)),
                    Select::make('tipe_masuk')
                        ->label('Tipe Presensi')
                        ->options([
                            'kantor' => '🏢 Hadir di Kantor',
                            'dinas_luar' => '🚗 Tugas Luar / Lapangan',
                        ])
                        ->default('kantor')
                        ->visible(fn ($get): bool => in_array($get('status'), ['hadir', 'terlambat'], true))
                        ->required(fn ($get): bool => in_array($get('status'), ['hadir', 'terlambat'], true)),
                    Textarea::make('keterangan')
                        ->label('Keterangan')
                        ->placeholder('Catatan atau alasan jika terlambat/izin/tugas luar...')
                        ->columnSpanFull(),
                ])
                ->action(function (array $data): void {
                    $amil = Amil::find($data['amil_id']);
                    $isHadir = in_array($data['status'], ['hadir', 'terlambat'], true);

                    Absensi::updateOrCreate(
                        [
                            'amil_id' => $data['amil_id'],
                            'tanggal' => $data['tanggal'],
                        ],
                        [
                            'jam_masuk' => $isHadir ? ($data['jam_masuk'] ?? null) : null,
                            'jam_pulang' => $isHadir ? ($data['jam_pulang'] ?? null) : null,
                            'tipe_masuk' => $isHadir ? ($data['tipe_masuk'] ?? 'kantor') : 'kantor',
                            'tipe_pulang' => $isHadir ? ($data['tipe_masuk'] ?? 'kantor') : 'kantor',
                            'status' => $data['status'],
                            'keterangan' => $data['keterangan'] ?? 'Presensi dicatat admin',
                        ]
                    );

                    Notification::make()
                        ->title('Presensi Tersimpan')
                        ->body("Presensi untuk {$amil?->nama} pada {$data['tanggal']} berhasil dicatat.")
                        ->success()
                        ->send();
                }),
        ];
    }
}
