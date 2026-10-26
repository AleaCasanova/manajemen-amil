<?php

namespace App\Filament\Resources\Absensis\Pages;

use App\Filament\Resources\Absensis\AbsensiResource;
use App\Models\Absensi;
use App\Models\Amil;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListAbsensis extends ListRecords
{
    protected static string $resource = AbsensiResource::class;

    protected function getHeaderActions(): array
    {
        return [
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
                    TimePicker::make('jam_masuk')
                        ->label('Jam Masuk')
                        ->default('08:00'),
                    TimePicker::make('jam_pulang')
                        ->label('Jam Pulang')
                        ->default('16:30'),
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
                        ->required(),
                    Select::make('tipe_masuk')
                        ->label('Tipe Presensi')
                        ->options([
                            'kantor' => '🏢 Hadir di Kantor',
                            'dinas_luar' => '🚗 Tugas Luar / Lapangan',
                        ])
                        ->default('kantor')
                        ->required(),
                    Textarea::make('keterangan')
                        ->label('Keterangan')
                        ->placeholder('Catatan atau alasan jika terlambat/izin/tugas luar...')
                        ->columnSpanFull(),
                ])
                ->action(function (array $data): void {
                    $amil = Amil::find($data['amil_id']);

                    Absensi::updateOrCreate(
                        [
                            'amil_id' => $data['amil_id'],
                            'tanggal' => $data['tanggal'],
                        ],
                        [
                            'jam_masuk' => $data['jam_masuk'],
                            'jam_pulang' => $data['jam_pulang'],
                            'tipe_masuk' => $data['tipe_masuk'] ?? 'kantor',
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
