<?php

namespace App\Filament\Resources\PengajuanDanas\Tables;

use App\Models\AkunKeuangan;
use App\Models\PengajuanDana;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PengajuanDanasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('nomor_pengajuan')
                    ->label('No. Pengajuan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->color('primary'),

                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('amil.nama')
                    ->label('Pemohon (Amil)')
                    ->searchable()
                    ->sortable()
                    ->description(fn (PengajuanDana $r) => $r->amil?->divisi?->nama ?? null),

                TextColumn::make('judul')
                    ->label('Keperluan / Judul')
                    ->searchable()
                    ->limit(35)
                    ->tooltip(fn ($record) => $record->judul),

                TextColumn::make('nominal_diajukan')
                    ->label('Diajukan')
                    ->money('IDR', locale: 'id')
                    ->sortable(),

                TextColumn::make('nominal_disetujui')
                    ->label('Disetujui (ACC)')
                    ->money('IDR', locale: 'id')
                    ->weight('bold')
                    ->color('success')
                    ->placeholder('-'),

                TextColumn::make('status')
                    ->label('Status')
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

                TextColumn::make('nomor_dokumen_referensi')
                    ->label('No. Referensi')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('kategoriKeuangan.nama')
                    ->label('Pos Kategori')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('tgl_kebutuhan')
                    ->label('Dibutuhkan')
                    ->date('d M Y')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'diajukan' => 'Menunggu Review Bendahara',
                        'diverifikasi_bendahara' => 'Menunggu ACC Manager',
                        'disetujui' => 'Disetujui (Siap Dicairkan)',
                        'dicairkan' => 'Dana Telah Dicairkan',
                        'ditolak' => 'Ditolak',
                    ]),

                SelectFilter::make('amil_id')
                    ->label('Pemohon')
                    ->relationship('amil', 'nama')
                    ->searchable()
                    ->preload(),

                Filter::make('tgl_kebutuhan')
                    ->form([
                        DatePicker::make('dari_tanggal')->label('Kebutuhan Dari'),
                        DatePicker::make('sampai_tanggal')->label('Kebutuhan Sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tgl_kebutuhan', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tgl_kebutuhan', '<=', $date),
                            );
                    }),

                TrashedFilter::make(),
            ])
            ->recordActions([
                // 1. Verifikasi Bendahara
                Action::make('verifikasiBendahara')
                    ->label('Review Bendahara')
                    ->icon('heroicon-o-check-badge')
                    ->color('info')
                    ->visible(fn (PengajuanDana $record): bool => $record->status === 'diajukan')
                    ->modalHeading('Verifikasi & Rekomendasi Bendahara')
                    ->modalDescription(fn (PengajuanDana $r) => 'Periksa ketersediaan saldo kas untuk pengajuan Rp '.number_format($r->nominal_diajukan, 0, ',', '.'))
                    ->form([
                        Textarea::make('catatan_bendahara')
                            ->label('Catatan Hasil Verifikasi Keuangan')
                            ->placeholder('Contoh: Saldo kas BSI mencukupi. Direkomendasikan untuk di-ACC penuh.')
                            ->required(),
                    ])
                    ->action(function (PengajuanDana $record, array $data): void {
                        $record->verifikasiBendahara(Auth::id(), $data['catatan_bendahara']);
                        Notification::make()
                            ->title('Pengajuan Berhasil Diverifikasi Bendahara')
                            ->body('Status diteruskan ke Manager untuk tahap persetujuan (ACC).')
                            ->success()
                            ->send();
                    }),

                // 2. ACC Manager
                Action::make('accManager')
                    ->label('ACC Manager')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (PengajuanDana $record): bool => in_array($record->status, ['diajukan', 'diverifikasi_bendahara']))
                    ->modalHeading('Persetujuan / ACC Manager')
                    ->form([
                        TextInput::make('nominal_disetujui')
                            ->label('Nominal Dana Disetujui (Rp)')
                            ->prefix('Rp')
                            ->numeric()
                            ->default(fn (PengajuanDana $r) => $r->nominal_diajukan)
                            ->required()
                            ->helperText('Bisa disetujui penuh atau disesuaikan dengan kuota anggaran.'),
                        Textarea::make('catatan_manager')
                            ->label('Catatan / Arahan Pimpinan')
                            ->placeholder('Contoh: Disetujui, harap segera belanjakan sesuai RAB dan simpan bukti nota.'),
                    ])
                    ->action(function (PengajuanDana $record, array $data): void {
                        $record->accManager(Auth::id(), (float) $data['nominal_disetujui'], $data['catatan_manager'] ?? null);
                        Notification::make()
                            ->title('Pengajuan Dana Berhasil Di-ACC Manager')
                            ->body('Status kini Siap Dicairkan oleh Bendahara.')
                            ->success()
                            ->send();
                    }),

                // 3. Tolak Pengajuan
                Action::make('tolak')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (PengajuanDana $record): bool => in_array($record->status, ['diajukan', 'diverifikasi_bendahara']))
                    ->modalHeading('Tolak Permohonan Pengajuan Dana')
                    ->form([
                        Textarea::make('alasan_penolakan')
                            ->label('Alasan Penolakan')
                            ->placeholder('Jelaskan alasan permohonan dana ini ditolak...')
                            ->required(),
                    ])
                    ->action(function (PengajuanDana $record, array $data): void {
                        $record->tolak(Auth::id(), $data['alasan_penolakan']);
                        Notification::make()
                            ->title('Pengajuan Dana Telah Ditolak')
                            ->danger()
                            ->send();
                    }),

                // 4. Pencairan Dana (Bendahara) & Otomatis Masuk ke Buku Kas
                Action::make('cairkan')
                    ->label('Cairkan Kas')
                    ->icon('heroicon-o-banknotes')
                    ->color('primary')
                    ->visible(fn (PengajuanDana $record): bool => $record->status === 'disetujui')
                    ->modalHeading('Pencairan Kas / Realisasi Pengajuan Dana')
                    ->modalDescription(fn (PengajuanDana $r) => 'Mencairkan dana sebesar Rp '.number_format($r->nominal_final, 0, ',', '.')." untuk {$r->amil?->nama}. Transaksi kas keluar akan otomatis tercatat di Buku Kas Yayasan.")
                    ->form([
                        Select::make('akun_keuangan_id')
                            ->label('Sumber Dompet Kas / Rekening Bank')
                            ->options(function () {
                                return AkunKeuangan::where('is_active', true)
                                    ->get()
                                    ->mapWithKeys(function (AkunKeuangan $a) {
                                        $saldo = number_format($a->saldo_sekarang, 0, ',', '.');

                                        return [$a->id => "{$a->nama} (Saldo: Rp {$saldo})"];
                                    });
                            })
                            ->required()
                            ->searchable()
                            ->preload(),

                        Select::make('metode_pencairan')
                            ->label('Metode Pencairan')
                            ->options([
                                'transfer' => 'Transfer Bank',
                                'tunai' => 'Kas Tunai Langsung',
                            ])
                            ->default('transfer')
                            ->required(),

                        FileUpload::make('bukti_pencairan')
                            ->label('Bukti Struk Transfer / Kwitansi')
                            ->disk('public')
                            ->directory('pengajuan-dana/pencairan')
                            ->image()
                            ->maxSize(3072),
                    ])
                    ->action(function (PengajuanDana $record, array $data): void {
                        $trx = $record->cairkan(
                            Auth::id(),
                            (int) $data['akun_keuangan_id'],
                            $data['metode_pencairan'],
                            $data['bukti_pencairan'] ?? null
                        );

                        Notification::make()
                            ->title('Dana Berhasil Dicairkan!')
                            ->body("Transaksi pengeluaran kas otomatis tercatat di Buku Kas [{$trx->kode_transaksi}].")
                            ->success()
                            ->send();
                    }),

                // 5. Cetak Dokumen PDF
                Action::make('cetak')
                    ->label('Cetak Form')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->url(fn (PengajuanDana $record) => route('amil.pengajuan-dana.cetak', $record->id))
                    ->openUrlInNewTab(),

                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
