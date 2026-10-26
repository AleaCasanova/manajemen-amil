<?php

namespace App\Filament\Resources\Absensis\Tables;

use App\Models\Absensi;
use App\Models\Amil;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AbsensisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto')
                    ->label('Foto')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(fn (Amil $record): string => 'https://ui-avatars.com/api/?name='.urlencode($record->nama).'&color=10b981&background=ecfdf5'),

                TextColumn::make('nama')
                    ->label('Nama Amil')
                    ->description(fn (Amil $record): string => ($record->nip ?? '-').' • '.($record->divisi?->nama ?? '-'))
                    ->searchable(['nama', 'nip'])
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('status_hari_ini')
                    ->label('Presensi Hari Ini')
                    ->state(function (Amil $record): string {
                        $today = Carbon::today()->toDateString();
                        $absen = $record->absensis->firstWhere('tanggal', $today);

                        if (! $absen) {
                            return 'Belum Presensi';
                        }

                        $jam = $absen->jam_masuk ? Carbon::parse($absen->jam_masuk)->format('H:i') : null;

                        return match ($absen->status) {
                            'hadir' => $jam ? "Hadir ({$jam})" : 'Hadir Tepat Waktu',
                            'terlambat' => $jam ? "Terlambat ({$jam})" : 'Terlambat',
                            'izin' => 'Izin',
                            'cuti' => 'Cuti',
                            'alpa' => 'Alpa',
                            default => ucfirst($absen->status),
                        };
                    })
                    ->badge()
                    ->color(function (Amil $record): string {
                        $today = Carbon::today()->toDateString();
                        $absen = $record->absensis->firstWhere('tanggal', $today);

                        if (! $absen) {
                            return 'gray';
                        }

                        return match ($absen->status) {
                            'hadir' => 'success',
                            'terlambat' => 'danger',
                            'izin' => 'info',
                            'cuti' => 'warning',
                            'alpa' => 'danger',
                            default => 'gray',
                        };
                    }),

                TextColumn::make('kehadiran_bulan_ini')
                    ->label('Hadir Bulan Ini')
                    ->state(function (Amil $record): string {
                        $now = Carbon::now();
                        $hadir = $record->absensis
                            ->filter(fn ($a) => Carbon::parse($a->tanggal)->month === $now->month && Carbon::parse($a->tanggal)->year === $now->year)
                            ->whereIn('status', ['hadir', 'terlambat'])
                            ->count();

                        return $hadir.' Hari';
                    })
                    ->badge()
                    ->color('gray'),

                TextColumn::make('persentase_kehadiran')
                    ->label('Tingkat Kehadiran')
                    ->state(function (Amil $record): string {
                        $now = Carbon::now();
                        $monthAbsens = $record->absensis
                            ->filter(fn ($a) => Carbon::parse($a->tanggal)->month === $now->month && Carbon::parse($a->tanggal)->year === $now->year);

                        $hadir = $monthAbsens->whereIn('status', ['hadir', 'terlambat'])->count();

                        // Hitung hari kerja dari tanggal 1 s/d hari ini di bulan berjalan
                        $start = $now->copy()->startOfMonth();
                        $end = $now->copy();
                        $workingDaysPassed = 0;
                        while ($start->lte($end)) {
                            if (! $start->isWeekend()) {
                                $workingDaysPassed++;
                            }
                            $start->addDay();
                        }

                        $target = max(1, $workingDaysPassed);
                        $persen = min(100, round(($hadir / $target) * 100));

                        $barColor = match (true) {
                            $persen >= 90 => '#10b981',
                            $persen >= 75 => '#f59e0b',
                            default => '#ef4444',
                        };

                        return '
                        <div style="min-width: 130px; max-width: 160px; padding: 2px 0;">
                            <div style="display: flex; justify-content: space-between; align-items: baseline; font-size: 11px; margin-bottom: 4px; line-height: 1.2;">
                                <span style="color: '.$barColor.'; font-weight: 700;">'.$persen.'%</span>
                                <span style="color: #94a3b8; font-size: 10px; font-weight: 600;">'.$hadir.'/'.$target.' Hari</span>
                            </div>
                            <div style="width: 100%; height: 6px; background-color: rgba(148, 163, 184, 0.25); border-radius: 9999px; overflow: hidden;">
                                <div style="width: '.$persen.'%; height: 100%; background-color: '.$barColor.'; border-radius: 9999px; transition: width 0.3s ease-in-out;"></div>
                            </div>
                        </div>';
                    })
                    ->html(),

                TextColumn::make('terlambat_bulan_ini')
                    ->label('Keterlambatan')
                    ->state(function (Amil $record): string {
                        $now = Carbon::now();
                        $terlambat = $record->absensis
                            ->filter(fn ($a) => Carbon::parse($a->tanggal)->month === $now->month && Carbon::parse($a->tanggal)->year === $now->year)
                            ->where('status', 'terlambat')
                            ->count();

                        return $terlambat > 0 ? $terlambat.'x Terlambat' : '0x';
                    })
                    ->badge()
                    ->color(function (Amil $record): string {
                        $now = Carbon::now();
                        $terlambat = $record->absensis
                            ->filter(fn ($a) => Carbon::parse($a->tanggal)->month === $now->month && Carbon::parse($a->tanggal)->year === $now->year)
                            ->where('status', 'terlambat')
                            ->count();

                        return $terlambat > 0 ? 'danger' : 'gray';
                    }),
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
                Action::make('riwayat_absensi')
                    ->label('Riwayat Absensi')
                    ->icon('heroicon-o-calendar-days')
                    ->color('primary')
                    ->slideOver()
                    ->modalWidth('5xl')
                    ->modalHeading(fn (Amil $record): string => "Riwayat Absensi: {$record->nama}")
                    ->modalDescription(fn (Amil $record): string => "NIP: {$record->nip} • {$record->divisi?->nama}")
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalContent(fn (Amil $record) => view('filament.modals.absensi-history', ['amil' => $record])),

                Action::make('catat_absensi_amil')
                    ->label('Catat Presensi')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->modalHeading(fn (Amil $record): string => "Catat Presensi Manual — {$record->nama}")
                    ->form([
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
                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->placeholder('Contoh: Presensi manual diinput oleh admin')
                            ->columnSpanFull(),
                    ])
                    ->action(function (Amil $record, array $data): void {
                        Absensi::updateOrCreate(
                            [
                                'amil_id' => $record->id,
                                'tanggal' => $data['tanggal'],
                            ],
                            [
                                'jam_masuk' => $data['jam_masuk'],
                                'jam_pulang' => $data['jam_pulang'],
                                'status' => $data['status'],
                                'keterangan' => $data['keterangan'] ?? 'Presensi manual dicatat oleh admin',
                            ]
                        );

                        Notification::make()
                            ->title('Presensi Berhasil Dicatat')
                            ->body("Presensi tanggal {$data['tanggal']} untuk {$record->nama} tersimpan.")
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
