<?php

namespace App\Filament\Resources\KategoriKeuangans\Pages;

use App\Filament\Resources\KategoriKeuangans\KategoriKeuanganResource;
use App\Models\KategoriKeuangan;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListKategoriKeuangans extends ListRecords
{
    protected static string $resource = KategoriKeuanganResource::class;

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua COA')
                ->badge(KategoriKeuangan::count()),
            'aset' => Tab::make('100 — Aset')
                ->modifyQueryUsing(fn ($query) => $query->kelompok('aset'))
                ->badge(KategoriKeuangan::kelompok('aset')->count())
                ->badgeColor('info'),
            'kewajiban' => Tab::make('200 — Kewajiban')
                ->modifyQueryUsing(fn ($query) => $query->kelompok('kewajiban'))
                ->badge(KategoriKeuangan::kelompok('kewajiban')->count())
                ->badgeColor('warning'),
            'saldo_dana' => Tab::make('300 — Saldo Dana')
                ->modifyQueryUsing(fn ($query) => $query->kelompok('saldo_dana'))
                ->badge(KategoriKeuangan::kelompok('saldo_dana')->count())
                ->badgeColor('gray'),
            'penerimaan' => Tab::make('400 — Penerimaan')
                ->modifyQueryUsing(fn ($query) => $query->kelompok('penerimaan'))
                ->badge(KategoriKeuangan::kelompok('penerimaan')->count())
                ->badgeColor('success'),
            'penyaluran' => Tab::make('500 — Penyaluran & Beban')
                ->modifyQueryUsing(fn ($query) => $query->kelompok('penyaluran'))
                ->badge(KategoriKeuangan::kelompok('penyaluran')->count())
                ->badgeColor('danger'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('+ Tambah Akun COA')
                ->icon('heroicon-o-plus'),
        ];
    }
}
