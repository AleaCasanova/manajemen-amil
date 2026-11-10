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
                            if (Carbon::today()->isSunday()) {
                                return 'Libur (Minggu)';
                            }
                            if (Carbon::today()->isSaturday() && $record->isMagang()) {
                                return 'Libur (Sabtu Magang)';
                            }
                            if (! $record->needsAttendance()) {
                                return 'Bebas Absen (Relawan)';
                            }

                            return 'Belum Presensi';
                        }

                        $jam = $absen->jam_masuk ? Carbon::parse($absen->jam_masuk)->format('H:i') : null;
                        $jamPulang = $absen->jam_pulang ? Carbon::parse($absen->jam_pulang)->format('H:i') : null;
                        $isDinas = $absen->tipe_masuk === 'dinas_luar' || $absen->tipe_pulang === 'dinas_luar';
                        $suffix = $isDinas ? ' (Tugas Luar)' : '';

                        $statusText = match ($absen->status) {
                            'hadir' => $jam ? "Hadir {$jam}{$suffix}" : "Hadir Tepat Waktu{$suffix}",
                            'terlambat' => $jam ? "Terlambat {$jam}{$suffix}" : "Terlambat{$suffix}",
                            'izin' => 'Izin',
                            'cuti' => 'Cuti',
                            'alpa' => 'Alpa',
                            default => ucfirst($absen->status),
                        };

                        if ($jamPulang) {
                            $pulangLabel = $absen->isPulangAwal() ? "Pulang Awal {$jamPulang}" : "Pulang {$jamPulang}";

                            return "{$statusText} • {$pulangLabel}";
                        }

                        return $statusText;
                    })
                    ->description(function (Amil $record): ?string {
                        $today = Carbon::today()->toDateString();
                        $absen = $record->absensis->firstWhere('tanggal', $today);

                        if ($absen) {
                            $notes = [];
                            if ($absen->keterangan) {
                                $notes[] = $absen->keterangan;
                            }
                            if ($absen->keterangan_pulang) {
                                $notes[] = $absen->keterangan_pulang;
                            }
                            if (! empty($notes)) {
                                return implode(' | ', $notes);
                            }
                        }

                        return null;
                    })
                    ->badge()
                    ->color(function (Amil $record): string {
                        $today = Carbon::today()->toDateString();
                        $absen = $record->absensis->firstWhere('tanggal', $today);

                        if (! $absen) {
                            if (Carbon::today()->isSunday() || (Carbon::today()->isSaturday() && $record->isMagang())) {
                                return 'info';
                            }

                            return 'gray';
                        }

                        if ($absen->tipe_masuk === 'dinas_luar' || $absen->tipe_pulang === 'dinas_luar') {
                            return 'info';
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

                TextColumn::make('total_jam_kerja')
                    ->label('Total Jam Kerja')
                    ->state(function (Amil $record): string {
                        $stats = $record->getTotalJamKerjaBulan();

                        return $stats['formatted'];
                    })
                    ->description(function (Amil $record): string {
                        $stats = $record->getTotalJamKerjaBulan();

                        return "Rata-rata: {$stats['rata_rata_jam_harian']}j/hari";
                    })
                    ->badge()
                    ->color('success'),

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
                        $isMagang = $record->isMagang();

                        while ($start->lte($end)) {
                            if ($isMagang) {
                                // Magang: 5 hari kerja (Senin - Jumat, Sabtu & Minggu libur)
                                if (! $start->isWeekend()) {
                                    $workingDaysPassed++;
                                }
                            } else {
                                // Staff, Driver, Manajemen: 6 hari kerja (Senin - Sabtu, hanya Minggu libur)
                                if (! $start->isSunday()) {
                                    $workingDaysPassed++;
                                }
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

                        $schemeLabel = $isMagang ? '5 Hari/Mg' : '6 Hari/Mg';

                        return '
                        <div style="min-width: 140px; max-width: 170px; padding: 2px 0;">
                            <div style="display: flex; justify-content: space-between; align-items: baseline; font-size: 11px; margin-bottom: 4px; line-height: 1.2;">
                                <span style="color: '.$barColor.'; font-weight: 700;">'.$persen.'%</span>
                                <span style="color: #94a3b8; font-size: 10px; font-weight: 600;">'.$hadir.'/'.$target.' Hari <span style="font-size: 9px; opacity: 0.85;">('.$schemeLabel.')</span></span>
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
                            ->placeholder('Contoh: Presensi manual diinput oleh admin / alasan izin / kegiatan dinas luar')
                            ->columnSpanFull(),
                    ])
                    ->action(function (Amil $record, array $data): void {
                        $isHadir = in_array($data['status'], ['hadir', 'terlambat'], true);

                        Absensi::updateOrCreate(
                            [
                                'amil_id' => $record->id,
                                'tanggal' => $data['tanggal'],
                            ],
                            [
                                'jam_masuk' => $isHadir ? ($data['jam_masuk'] ?? null) : null,
                                'jam_pulang' => $isHadir ? ($data['jam_pulang'] ?? null) : null,
                                'tipe_masuk' => $isHadir ? ($data['tipe_masuk'] ?? 'kantor') : 'kantor',
                                'tipe_pulang' => $isHadir ? ($data['tipe_masuk'] ?? 'kantor') : 'kantor',
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
