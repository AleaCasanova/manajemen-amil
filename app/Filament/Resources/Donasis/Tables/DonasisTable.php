<?php

namespace App\Filament\Resources\Donasis\Tables;

use App\Models\Donasi;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class DonasisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_kwitansi')
                    ->label('No. Kwitansi')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),
                ImageColumn::make('bukti_transfer')
                    ->label('Bukti')
                    ->disk('public')
                    ->square()
                    ->toggleable(),
                ImageColumn::make('lampiran_foto')
                    ->label('Dokumentasi')
                    ->disk('public')
                    ->square()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('tgl_donasi')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('donatur.nama')
                    ->label('Donatur')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn (Donasi $record): string => $record->donatur->is_anonim ? 'Hamba Allah (Anonim)' : $record->donatur->nama)
                    ->description(fn (Donasi $record): string => $record->atas_nama ? "a.n: {$record->atas_nama}" : ($record->donatur->kode_donatur ?? '')),
                TextColumn::make('jenisDana.nama')
                    ->label('Jenis Dana')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('program.nama')
                    ->label('Program')
                    ->default('Umum')
                    ->searchable(),
                TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('metode_bayar')
                    ->label('Metode')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                TextColumn::make('status')
                    ->label('Status Verifikasi')
                    ->badge()
                    ->colors([
                        'warning' => 'menunggu_verifikasi',
                        'success' => 'terverifikasi',
                        'danger' => 'ditolak',
                    ])
                    ->formatStateUsing(fn ($state) => strtoupper(str_replace('_', ' ', $state))),
                TextColumn::make('amil.nama')
                    ->label('Amil Penerima')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('verifiedByUser.name')
                    ->label('Diverifikasi Oleh')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'menunggu_verifikasi' => 'Menunggu Verifikasi',
                        'terverifikasi' => 'Terverifikasi',
                        'ditolak' => 'Ditolak',
                    ]),
                SelectFilter::make('jenis_dana_id')
                    ->label('Jenis Dana')
                    ->relationship('jenisDana', 'nama'),
                SelectFilter::make('program_id')
                    ->label('Program')
                    ->relationship('program', 'nama'),
                SelectFilter::make('amil_id')
                    ->label('Amil')
                    ->relationship('amil', 'nama'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('verifikasi')
                    ->label('Verifikasi')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Donasi $record): bool => $record->status !== 'terverifikasi')
                    ->requiresConfirmation()
                    ->modalHeading('Verifikasi Donasi Masuk')
                    ->modalDescription(fn (Donasi $record): string => 'Konfirmasi bahwa dana donasi sebesar Rp '.number_format($record->jumlah, 0, ',', '.').' telah diterima dan sah?')
                    ->action(function (Donasi $record): void {
                        $record->update([
                            'status' => 'terverifikasi',
                            'verified_by' => auth()->id(),
                            'verified_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Donasi Berhasil Diverifikasi')
                            ->body("Donasi {$record->no_kwitansi} telah diverifikasi. Kwitansi resmi kini sah dicetak.")
                            ->success()
                            ->send();
                    }),
                Action::make('tolak')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Donasi $record): bool => $record->status === 'menunggu_verifikasi')
                    ->requiresConfirmation()
                    ->form([
                        Textarea::make('catatan')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->placeholder('Contoh: Bukti transfer tidak valid atau dana tidak masuk ke rekening.'),
                    ])
                    ->action(function (Donasi $record, array $data): void {
                        $record->update([
                            'status' => 'ditolak',
                            'catatan' => $data['catatan'],
                            'verified_by' => auth()->id(),
                            'verified_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Donasi Ditolak')
                            ->body("Donasi {$record->no_kwitansi} ditandai sebagai ditolak.")
                            ->warning()
                            ->send();
                    }),
                Action::make('cetakKwitansi')
                    ->label('Cetak Kwitansi')
                    ->icon('heroicon-o-printer')
                    ->color('primary')
                    ->url(fn (Donasi $record): string => route('kwitansi.stream', $record->id), true),

                // Aksi Jurnal Koreksi / Pembatalan Resmi untuk donasi yang sudah terverifikasi
                Action::make('jurnalKoreksi')
                    ->label('Jurnal Koreksi / Batal')
                    ->icon('heroicon-o-arrow-path-rounded-square')
                    ->color('danger')
                    ->modalHeading('Jurnal Koreksi / Pembatalan Donasi')
                    ->modalDescription('PERINGATAN: Donasi ini telah terverifikasi. Pembatalan hanya dapat dilakukan melalui pencatatan Berita Acara / Jurnal Koreksi.')
                    ->form([
                        Textarea::make('alasan_koreksi')
                            ->label('Nomor Berita Acara & Alasan Koreksi')
                            ->required()
                            ->placeholder('Contoh: BA/KOR/2026/01 - Donatur salah nominal transfer / terjadi double input'),
                    ])
                    ->visible(fn (Donasi $record): bool => $record->status === 'terverifikasi')
                    ->action(function (Donasi $record, array $data): void {
                        $record->update([
                            'status' => 'ditolak',
                            'catatan' => '[JURNAL KOREKSI: '.now()->format('d/m/Y H:i').' oleh '.auth()->user()->name.'] '.$data['alasan_koreksi'],
                            'verified_by' => auth()->id(),
                            'verified_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Jurnal Koreksi Berhasil Dicatat')
                            ->body("Donasi {$record->no_kwitansi} telah dibatalkan melalui jurnal koreksi.")
                            ->warning()
                            ->send();
                    }),

                // Edit & Hapus HANYA BISA jika status BELUM terverifikasi
                EditAction::make()
                    ->visible(fn (Donasi $record): bool => $record->status !== 'terverifikasi'),
                DeleteAction::make()
                    ->visible(fn (Donasi $record): bool => $record->status !== 'terverifikasi'),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->before(function (Collection $records, DeleteBulkAction $action) {
                            $verified = $records->filter(fn (Donasi $record) => $record->status === 'terverifikasi');
                            if ($verified->isNotEmpty()) {
                                Notification::make()
                                    ->title('Aksi Ditolak: Transaksi Telah Terverifikasi')
                                    ->body('Donasi yang sudah terverifikasi ('.$verified->pluck('no_kwitansi')->take(3)->join(', ').') tidak dapat dihapus massal. Gunakan menu Jurnal Koreksi untuk membatalkan donasi resmi.')
                                    ->danger()
                                    ->send();

                                $action->cancel();
                            }
                        }),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
