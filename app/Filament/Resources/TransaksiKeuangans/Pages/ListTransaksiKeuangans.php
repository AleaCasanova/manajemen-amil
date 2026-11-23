<?php

namespace App\Filament\Resources\TransaksiKeuangans\Pages;

use App\Exports\TransaksiKeuanganExport;
use App\Filament\Resources\TransaksiKeuangans\TransaksiKeuanganResource;
use App\Filament\Resources\TransaksiKeuangans\Widgets\BukuKasOverviewWidget;
use App\Models\AkunKeuangan;
use App\Models\TransaksiKeuangan;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Maatwebsite\Excel\Facades\Excel;

class ListTransaksiKeuangans extends ListRecords
{
    protected static string $resource = TransaksiKeuanganResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            BukuKasOverviewWidget::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua Transaksi')
                ->badge(TransaksiKeuangan::count()),
            'masuk' => Tab::make('Kas Masuk (Pemasukan)')
                ->modifyQueryUsing(fn ($query) => $query->where('jenis', 'masuk'))
                ->badge(TransaksiKeuangan::where('jenis', 'masuk')->count())
                ->badgeColor('success'),
            'keluar' => Tab::make('Kas Keluar (Pengeluaran)')
                ->modifyQueryUsing(fn ($query) => $query->where('jenis', 'keluar'))
                ->badge(TransaksiKeuangan::where('jenis', 'keluar')->count())
                ->badgeColor('danger'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('ringkasanAkun')
                ->label('Saldo Kas & Bank')
                ->icon('heroicon-o-building-library')
                ->color('gray')
                ->modalHeading('Daftar Rekening & Akun Kas Yayasan')
                ->modalDescription('Pantauan rincian saldo dan akumulasi mutasi setiap dompet / rekening bank yayasan.')
                ->modalContent(fn () => view('filament.modals.akun-keuangan-modal', [
                    'akuns' => AkunKeuangan::with('transaksiKeuangans')->where('is_active', true)->get(),
                ]))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Tutup'),

            Action::make('tambahAkun')
                ->label('+ Akun / Bank')
                ->icon('heroicon-o-plus-circle')
                ->color('info')
                ->form([
                    TextInput::make('nama')
                        ->label('Nama Akun / Bank')
                        ->placeholder('Contoh: Kas Kecil Kantor / Bank Syariah')
                        ->required(),
                    Select::make('tipe')
                        ->label('Tipe Akun')
                        ->options([
                            'kas' => 'Kas Tunai / Fisik',
                            'bank' => 'Rekening Bank',
                            'e_wallet' => 'E-Wallet / QRIS',
                        ])
                        ->default('kas')
                        ->required(),
                    TextInput::make('nomor_rekening')
                        ->label('Nomor Rekening (Opsional)'),
                    TextInput::make('atas_nama')
                        ->label('Atas Nama Rekening'),
                    TextInput::make('saldo_awal')
                        ->label('Saldo Awal (Rp)')
                        ->numeric()
                        ->default(0),
                    Textarea::make('keterangan')
                        ->label('Keterangan Tambahan'),
                ])
                ->action(function (array $data): void {
                    AkunKeuangan::create($data);
                    Notification::make()
                        ->title('Akun Kas / Bank Berhasil Ditambahkan')
                        ->success()
                        ->send();
                }),

            Action::make('exportExcel')
                ->label('Ekspor Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    return Excel::download(
                        new TransaksiKeuanganExport,
                        'buku-kas-keuangan-'.date('Ymd-His').'.xlsx'
                    );
                }),

            CreateAction::make()
                ->label('+ Catat Transaksi')
                ->icon('heroicon-o-plus'),
        ];
    }
}
