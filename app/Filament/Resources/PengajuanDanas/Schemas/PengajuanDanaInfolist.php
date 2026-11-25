<?php

namespace App\Filament\Resources\PengajuanDanas\Schemas;

use App\Models\PengajuanDana;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PengajuanDanaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informasi Pengajuan & Referensi Resmi')
                    ->icon('heroicon-o-document-text')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(4)
                            ->components([
                                TextEntry::make('nomor_pengajuan')
                                    ->label('Nomor Pengajuan')
                                    ->badge()
                                    ->color('primary')
                                    ->copyable(),

                                TextEntry::make('status')
                                    ->label('Status Otorisasi')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'diajukan' => 'warning',
                                        'diverifikasi_bendahara' => 'info',
                                        'disetujui' => 'primary',
                                        'dicairkan' => 'success',
                                        'ditolak' => 'danger',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn ($record) => $record->status_label),

                                TextEntry::make('nominal_diajukan')
                                    ->label('Nominal Diajukan')
                                    ->money('IDR', locale: 'id')
                                    ->weight('bold'),

                                TextEntry::make('nominal_disetujui')
                                    ->label('Nominal Disetujui (ACC)')
                                    ->money('IDR', locale: 'id')
                                    ->weight('bold')
                                    ->color('success')
                                    ->placeholder('(Menunggu ACC Manager)'),
                            ]),

                        Grid::make(3)
                            ->components([
                                TextEntry::make('amil.nama')
                                    ->label('Pemohon (Amil)')
                                    ->helperText(fn (PengajuanDana $r) => $r->amil?->nip ? 'NIP: '.$r->amil->nip : null),

                                TextEntry::make('divisi.nama')
                                    ->label('Divisi / Bidang')
                                    ->default('Umum'),

                                TextEntry::make('tgl_kebutuhan')
                                    ->label('Tanggal Kebutuhan')
                                    ->date('d F Y')
                                    ->color('primary'),
                            ]),

                        Grid::make(3)
                            ->components([
                                TextEntry::make('nomor_dokumen_referensi')
                                    ->label('Ref. Memo / Proposal')
                                    ->default('-')
                                    ->badge(),

                                TextEntry::make('kategoriKeuangan.nama')
                                    ->label('Pos Kategori Anggaran')
                                    ->badge(),

                                TextEntry::make('program.nama')
                                    ->label('Program Terkait')
                                    ->default('-'),
                            ]),

                        TextEntry::make('judul')
                            ->label('Keperluan / Judul')
                            ->columnSpanFull()
                            ->weight('bold'),

                        TextEntry::make('deskripsi')
                            ->label('Uraian Kebutuhan & RAB')
                            ->columnSpanFull(),
                    ]),

                Section::make('Riwayat Otorisasi Berjenjang & Pencairan')
                    ->icon('heroicon-o-check-badge')
                    ->columnSpanFull()
                    ->components([
                        Grid::make(3)
                            ->components([
                                TextEntry::make('bendahara.name')
                                    ->label('Verifikator (Bendahara)')
                                    ->default('Belum diverifikasi')
                                    ->helperText(fn (PengajuanDana $r) => $r->tgl_verifikasi_bendahara ? $r->tgl_verifikasi_bendahara->format('d/m/Y H:i') : null),

                                TextEntry::make('manager.name')
                                    ->label('Penyetuju (Manager)')
                                    ->default('Belum di-ACC')
                                    ->helperText(fn (PengajuanDana $r) => $r->tgl_acc_manager ? $r->tgl_acc_manager->format('d/m/Y H:i') : null),

                                TextEntry::make('akunKeuangan.nama')
                                    ->label('Rekening Kas Pencairan')
                                    ->default('Belum dicairkan')
                                    ->helperText(fn (PengajuanDana $r) => $r->tgl_pencairan ? 'Cair: '.$r->tgl_pencairan->format('d/m/Y') : null),
                            ]),

                        TextEntry::make('catatan_bendahara')
                            ->label('Catatan Bendahara')
                            ->visible(fn (PengajuanDana $r) => filled($r->catatan_bendahara))
                            ->columnSpanFull(),

                        TextEntry::make('catatan_manager')
                            ->label('Catatan Manager')
                            ->visible(fn (PengajuanDana $r) => filled($r->catatan_manager))
                            ->columnSpanFull(),

                        TextEntry::make('alasan_penolakan')
                            ->label('Alasan Penolakan')
                            ->color('danger')
                            ->visible(fn (PengajuanDana $r) => filled($r->alasan_penolakan))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
