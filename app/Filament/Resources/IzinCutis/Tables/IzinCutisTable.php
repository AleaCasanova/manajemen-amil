<?php

namespace App\Filament\Resources\IzinCutis\Tables;

use App\Models\Amil;
use App\Models\IzinCuti;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class IzinCutisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto')
                    ->label('Foto')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(fn (Amil $record): string => 'https://ui-avatars.com/api/?name='.urlencode($record->nama).'&color=f59e0b&background=fef3c7'),

                TextColumn::make('nama')
                    ->label('Nama Amil')
                    ->description(fn (Amil $record): string => ($record->nip ?? '-').' • '.($record->divisi?->nama ?? '-'))
                    ->searchable(['nama', 'nip'])
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('saldo_cuti_grafik')
                    ->label('Sisa & Kuota Cuti')
                    ->state(function (Amil $record): string {
                        $tahun = Carbon::now()->year;
                        $saldo = $record->saldoCutis->firstWhere('tahun', $tahun)
                            ?? $record->saldoCutis->sortByDesc('tahun')->first();

                        $jatah = $saldo?->jatah ?? 12;
                        $terpakai = $saldo?->terpakai ?? 0;
                        $sisa = max(0, $jatah - $terpakai);
                        $persen = $jatah > 0 ? min(100, round(($terpakai / $jatah) * 100)) : 0;

                        $barColor = match (true) {
                            $persen >= 80 => '#ef4444',
                            $persen >= 50 => '#f59e0b',
                            default => '#10b981',
                        };

                        return '
                        <div style="min-width: 145px; max-width: 175px; padding: 2px 0;">
                            <div style="display: flex; justify-content: space-between; align-items: baseline; font-size: 11px; margin-bottom: 4px; line-height: 1.2;">
                                <span style="color: #10b981; font-weight: 700;">Sisa '.$sisa.' Hari</span>
                                <span style="color: #94a3b8; font-size: 10px; font-weight: 600;">'.$persen.'% diambil</span>
                            </div>
                            <div style="width: 100%; height: 6px; background-color: rgba(148, 163, 184, 0.25); border-radius: 9999px; overflow: hidden; margin-bottom: 4px;">
                                <div style="width: '.$persen.'%; height: 100%; background-color: '.$barColor.'; border-radius: 9999px; transition: width 0.3s ease-in-out;"></div>
                            </div>
                            <div style="font-size: 10px; color: #94a3b8; line-height: 1.2;">
                                Terpakai '.$terpakai.' / '.$jatah.' hari ('.$tahun.')
                            </div>
                        </div>';
                    })
                    ->html(),

                TextColumn::make('pengajuan_pending')
                    ->label('Perlu Review')
                    ->state(function (Amil $record): string {
                        $pending = $record->izinCutis->where('status', 'diajukan')->count();

                        return $pending > 0 ? "{$pending} Menunggu Review" : '0 Menunggu';
                    })
                    ->badge()
                    ->color(function (Amil $record): string {
                        $pending = $record->izinCutis->where('status', 'diajukan')->count();

                        return $pending > 0 ? 'warning' : 'gray';
                    }),

                TextColumn::make('total_izin_sakit')
                    ->label('Rekap Izin & Sakit')
                    ->state(function (Amil $record): string {
                        $nowYear = Carbon::now()->year;
                        $thisYear = $record->izinCutis
                            ->filter(fn ($i) => Carbon::parse($i->tgl_mulai)->year === $nowYear && $i->status === 'disetujui');

                        $izin = $thisYear->where('jenis', 'izin')->count();
                        $sakit = $thisYear->where('jenis', 'sakit')->count();

                        return "{$izin} Izin • {$sakit} Sakit";
                    })
                    ->badge()
                    ->color('gray'),

                TextColumn::make('permohonan_terakhir')
                    ->label('Permohonan Terakhir')
                    ->state(function (Amil $record): string {
                        $last = $record->izinCutis->sortByDesc('tgl_mulai')->first();
                        if (! $last) {
                            return '-';
                        }

                        $tgl = Carbon::parse($last->tgl_mulai)->format('d M Y');

                        return strtoupper($last->jenis)." ({$tgl})";
                    })
                    ->description(function (Amil $record): ?string {
                        $last = $record->izinCutis->sortByDesc('tgl_mulai')->first();
                        if (! $last) {
                            return null;
                        }

                        return match ($last->status) {
                            'disetujui' => 'Status: Disetujui',
                            'ditolak' => 'Status: Ditolak',
                            'diajukan' => 'Status: Menunggu Review',
                            default => ucfirst($last->status),
                        };
                    })
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
                Action::make('kelola_cuti')
                    ->label('Kelola Izin & Cuti')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->color('warning')
                    ->slideOver()
                    ->modalWidth('5xl')
                    ->modalHeading(fn (Amil $record): string => "Kelola Izin & Cuti: {$record->nama}")
                    ->modalDescription(fn (Amil $record): string => "NIP: {$record->nip} • {$record->divisi?->nama}")
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalContent(fn (Amil $record) => view('filament.modals.izin-cuti-history', ['amil' => $record])),

                Action::make('buat_pengajuan_amil')
                    ->label('Buat Pengajuan')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->modalHeading(fn (Amil $record): string => "Buat Pengajuan Izin/Cuti — {$record->nama}")
                    ->form([
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
                            ->label('Status Persetujuan')
                            ->options([
                                'diajukan' => 'Menunggu Review',
                                'disetujui' => 'Langsung Disetujui',
                            ])
                            ->default('disetujui')
                            ->required(),
                    ])
                    ->action(function (Amil $record, array $data): void {
                        $izin = IzinCuti::create([
                            'amil_id' => $record->id,
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
                            ->title('Pengajuan Berhasil Disimpan')
                            ->body("Pengajuan {$data['jenis']} untuk {$record->nama} tersimpan.")
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
