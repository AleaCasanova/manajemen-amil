<?php

namespace App\Filament\Resources\IzinCutis\Pages;

use App\Filament\Resources\IzinCutis\IzinCutiResource;
use App\Models\Amil;
use App\Models\IzinCuti;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListIzinCutis extends ListRecords
{
    protected static string $resource = IzinCutiResource::class;

    public function setujuiPengajuan(int $id): void
    {
        $izin = IzinCuti::findOrFail($id);
        $izin->approve(Auth::id());

        Notification::make()
            ->title('Pengajuan Berhasil Disetujui')
            ->body("Pengajuan {$izin->jenis} dari {$izin->amil?->nama} telah disetujui dan absensi disesuaikan.")
            ->success()
            ->send();
    }

    public function tolakPengajuan(int $id, ?string $catatan = null): void
    {
        $izin = IzinCuti::findOrFail($id);
        $catatanClean = $catatan ? trim($catatan) : 'Pengajuan ditolak oleh admin.';
        $izin->reject(Auth::id(), $catatanClean);

        Notification::make()
            ->title('Pengajuan Ditolak')
            ->body("Pengajuan {$izin->jenis} dari {$izin->amil?->nama} telah ditolak.")
            ->warning()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('buat_pengajuan_baru')
                ->label('Buat Pengajuan Cuti / Izin')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->modalHeading('Buat Pengajuan Cuti / Izin Amil')
                ->form([
                    Select::make('amil_id')
                        ->label('Pilih Amil')
                        ->options(Amil::where('status', 'aktif')->pluck('nama', 'id'))
                        ->searchable()
                        ->preload()
                        ->required(),
                    Select::make('jenis')
                        ->label('Jenis Permohonan')
                        ->options([
                            'cuti' => 'Cuti Tahunan',
                            'izin' => 'Izin',
                            'sakit' => 'Sakit',
                        ])
                        ->default('cuti')
                        ->required(),
                    DatePicker::make('tgl_mulai')
                        ->label('Tanggal Mulai')
                        ->default(now()->toDateString())
                        ->required(),
                    DatePicker::make('tgl_selesai')
                        ->label('Tanggal Selesai')
                        ->default(now()->toDateString())
                        ->required(),
                    Textarea::make('alasan')
                        ->label('Alasan / Keperluan')
                        ->required()
                        ->columnSpanFull(),
                    FileUpload::make('lampiran')
                        ->label('Lampiran Berkas / Surat Dokter')
                        ->disk('public')
                        ->directory('izin-cuti/lampiran')
                        ->columnSpanFull(),
                    Select::make('status')
                        ->label('Status Persetujuan Awal')
                        ->options([
                            'diajukan' => 'Menunggu Review',
                            'disetujui' => 'Langsung Disetujui',
                        ])
                        ->default('disetujui')
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $izin = IzinCuti::create([
                        'amil_id' => $data['amil_id'],
                        'jenis' => $data['jenis'],
                        'tgl_mulai' => $data['tgl_mulai'],
                        'tgl_selesai' => $data['tgl_selesai'],
                        'alasan' => $data['alasan'],
                        'lampiran' => $data['lampiran'] ?? null,
                        'status' => 'diajukan',
                    ]);

                    if ($data['status'] === 'disetujui') {
                        $izin->approve(Auth::id());
                    }

                    Notification::make()
                        ->title('Pengajuan Berhasil Dibuat')
                        ->body("Pengajuan untuk {$izin->amil?->nama} telah tercatat.")
                        ->success()
                        ->send();
                }),
        ];
    }
}
