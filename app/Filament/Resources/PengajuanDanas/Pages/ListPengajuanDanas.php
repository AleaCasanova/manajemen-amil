<?php

namespace App\Filament\Resources\PengajuanDanas\Pages;

use App\Exports\PengajuanDanaExport;
use App\Filament\Resources\PengajuanDanas\PengajuanDanaResource;
use App\Models\PengajuanDana;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Maatwebsite\Excel\Facades\Excel;

class ListPengajuanDanas extends ListRecords
{
    protected static string $resource = PengajuanDanaResource::class;

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua')
                ->badge(PengajuanDana::count()),

            'diajukan' => Tab::make('Review Bendahara')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'diajukan'))
                ->badge(PengajuanDana::where('status', 'diajukan')->count())
                ->badgeColor('warning'),

            'menunggu_manager' => Tab::make('Menunggu ACC Manager')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'diverifikasi_bendahara'))
                ->badge(PengajuanDana::where('status', 'diverifikasi_bendahara')->count())
                ->badgeColor('info'),

            'siap_cair' => Tab::make('Siap Dicairkan')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'disetujui'))
                ->badge(PengajuanDana::where('status', 'disetujui')->count())
                ->badgeColor('primary'),

            'dicairkan' => Tab::make('Selesai Dicairkan')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'dicairkan'))
                ->badge(PengajuanDana::where('status', 'dicairkan')->count())
                ->badgeColor('success'),

            'ditolak' => Tab::make('Ditolak')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'ditolak'))
                ->badge(PengajuanDana::where('status', 'ditolak')->count())
                ->badgeColor('danger'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportExcel')
                ->label('Ekspor Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    return Excel::download(
                        new PengajuanDanaExport,
                        'rekap-pengajuan-dana-'.date('Ymd-His').'.xlsx'
                    );
                }),

            CreateAction::make()
                ->label('+ Buat Pengajuan Baru')
                ->icon('heroicon-o-plus'),
        ];
    }
}
