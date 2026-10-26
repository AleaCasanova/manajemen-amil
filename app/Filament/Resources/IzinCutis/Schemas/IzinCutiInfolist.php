<?php

namespace App\Filament\Resources\IzinCutis\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class IzinCutiInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Rincian Permohonan Izin / Cuti')
                    ->icon('heroicon-o-calendar-days')
                    ->components([
                        Grid::make(4)
                            ->components([
                                TextEntry::make('amil.nama')
                                    ->label('Petugas Amil')
                                    ->weight('bold'),
                                TextEntry::make('jenis')
                                    ->label('Jenis Permohonan')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'cuti' => 'primary',
                                        'sakit' => 'danger',
                                        'izin' => 'warning',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn ($state) => strtoupper($state)),
                                TextEntry::make('rentang_tanggal')
                                    ->label('Rentang Waktu')
                                    ->state(fn ($record) => $record->tgl_mulai->format('d M Y').' s/d '.$record->tgl_selesai->format('d M Y'))
                                    ->icon('heroicon-o-calendar'),
                                TextEntry::make('status')
                                    ->label('Status Persetujuan')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'disetujui' => 'success',
                                        'ditolak' => 'danger',
                                        default => 'warning',
                                    })
                                    ->formatStateUsing(fn ($state) => strtoupper($state)),
                            ]),
                    ]),

                Section::make('Alasan & Catatan Persetujuan')
                    ->icon('heroicon-o-chat-bubble-bottom-center-text')
                    ->components([
                        Grid::make(2)
                            ->components([
                                TextEntry::make('alasan')
                                    ->label('Alasan Permohonan Amil')
                                    ->placeholder('-'),
                                TextEntry::make('catatan_admin')
                                    ->label('Catatan Tim Admin / HRD')
                                    ->placeholder('Belum ada catatan dari peninjau'),
                            ]),
                    ]),

                Section::make('Lampiran Berkas / Surat Dokter')
                    ->icon('heroicon-o-paper-clip')
                    ->components([
                        Grid::make(2)
                            ->components([
                                ImageEntry::make('lampiran')
                                    ->label('Preview Foto Lampiran')
                                    ->disk('public')
                                    ->height(240)
                                    ->placeholder('Tidak ada foto preview'),
                                TextEntry::make('lampiran')
                                    ->label('Tautan Berkas Asli (PDF / Gambar)')
                                    ->formatStateUsing(fn ($state) => $state ? '📄 Klik di sini untuk membuka berkas asli' : 'Tidak ada berkas yang dilampirkan')
                                    ->url(fn ($state) => $state ? asset('storage/'.$state) : null, true)
                                    ->color('primary'),
                            ]),
                    ]),

                Section::make('Informasi Log Sistem')
                    ->icon('heroicon-o-clock')
                    ->collapsed()
                    ->components([
                        Grid::make(2)
                            ->components([
                                TextEntry::make('created_at')
                                    ->label('Diajukan Pada')
                                    ->dateTime('d M Y, H:i:s')
                                    ->suffix(' WIB'),
                                TextEntry::make('updated_at')
                                    ->label('Pembaruan Status')
                                    ->dateTime('d M Y, H:i:s')
                                    ->suffix(' WIB'),
                            ]),
                    ]),
            ]);
    }
}
