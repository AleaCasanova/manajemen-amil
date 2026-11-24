<?php

namespace App\Services;

use App\Models\AkunKeuangan;
use App\Models\CashOpname;
use App\Models\KategoriKeuangan;
use App\Models\TransaksiKeuangan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class TransaksiKeuanganService
{
    /**
     * Catat transfer / pindah saldo antar rekening kas & bank secara berpasangan (atomik).
     *
     * @param  array{
     *     akun_asal_id: int|string,
     *     akun_tujuan_id: int|string,
     *     nominal: float|int|string,
     *     biaya_admin?: float|int|string|null,
     *     tgl_transaksi?: string|null,
     *     no_voucher?: string|null,
     *     reff?: string|null,
     *     keterangan?: string|null,
     *     bukti_transaksi?: string|null,
     * }  $data
     * @return array{
     *     keluar: TransaksiKeuangan,
     *     masuk: TransaksiKeuangan,
     *     biaya_admin: ?TransaksiKeuangan,
     *     group_kode: string,
     * }
     */
    public function buatTransfer(array $data, ?int $userId = null): array
    {
        $akunAsalId = (int) $data['akun_asal_id'];
        $akunTujuanId = (int) $data['akun_tujuan_id'];
        $nominal = (float) $data['nominal'];
        $biayaAdmin = (float) ($data['biaya_admin'] ?? 0);

        if ($akunAsalId === $akunTujuanId) {
            throw new InvalidArgumentException('Akun asal dan akun tujuan transfer tidak boleh sama.');
        }

        if ($nominal <= 0) {
            throw new InvalidArgumentException('Nominal transfer harus lebih besar dari nol.');
        }

        if ($biayaAdmin < 0) {
            throw new InvalidArgumentException('Biaya admin transfer tidak boleh bernilai negatif.');
        }

        return DB::transaction(function () use ($data, $akunAsalId, $akunTujuanId, $nominal, $biayaAdmin, $userId): array {
            $akunAsal = AkunKeuangan::findOrFail($akunAsalId);
            $akunTujuan = AkunKeuangan::findOrFail($akunTujuanId);

            $tglTransaksi = $data['tgl_transaksi'] ?? now()->toDateString();
            $groupKode = $this->generateTransferGroupKode();
            $actorId = $userId ?? Auth::id();

            $coaLawanKeluar = $this->resolveCoaUntukAkun($akunTujuan);
            $coaLawanMasuk = $this->resolveCoaUntukAkun($akunAsal);

            $keterangan = trim((string) ($data['keterangan'] ?? ''));
            if ($keterangan === '') {
                $keterangan = "Pindah saldo dari {$akunAsal->nama} ke {$akunTujuan->nama}";
            }

            $trxKeluar = TransaksiKeuangan::create([
                'no_voucher' => $data['no_voucher'] ?? null,
                'reff' => $data['reff'] ?? null,
                'jenis' => 'keluar',
                'tipe_transaksi' => 'transfer',
                'is_transfer' => true,
                'transfer_group_kode' => $groupKode,
                'kategori_keuangan_id' => $coaLawanKeluar,
                'akun_keuangan_id' => $akunAsal->id,
                'nominal' => $nominal,
                'tgl_transaksi' => $tglTransaksi,
                'pihak_terkait' => $akunTujuan->nama,
                'keterangan' => "[{$groupKode}] Transfer keluar ke {$akunTujuan->nama} — {$keterangan}",
                'bukti_transaksi' => $data['bukti_transaksi'] ?? null,
                'created_by' => $actorId,
            ]);

            $trxMasuk = TransaksiKeuangan::create([
                'no_voucher' => $data['no_voucher'] ?? null,
                'reff' => $data['reff'] ?? null,
                'jenis' => 'masuk',
                'tipe_transaksi' => 'transfer',
                'is_transfer' => true,
                'transfer_group_kode' => $groupKode,
                'transfer_pasangan_id' => $trxKeluar->id,
                'kategori_keuangan_id' => $coaLawanMasuk,
                'akun_keuangan_id' => $akunTujuan->id,
                'nominal' => $nominal,
                'tgl_transaksi' => $tglTransaksi,
                'pihak_terkait' => $akunAsal->nama,
                'keterangan' => "[{$groupKode}] Transfer masuk dari {$akunAsal->nama} — {$keterangan}",
                'bukti_transaksi' => $data['bukti_transaksi'] ?? null,
                'created_by' => $actorId,
            ]);

            $trxKeluar->update(['transfer_pasangan_id' => $trxMasuk->id]);

            $trxBiayaAdmin = null;
            if ($biayaAdmin > 0) {
                $coaAdmBank = $this->resolveCoaBiayaAdmin();
                $trxBiayaAdmin = TransaksiKeuangan::create([
                    'no_voucher' => $data['no_voucher'] ?? null,
                    'reff' => $data['reff'] ?? null,
                    'jenis' => 'keluar',
                    'tipe_transaksi' => 'reguler',
                    'is_transfer' => false,
                    'transfer_group_kode' => $groupKode,
                    'kategori_keuangan_id' => $coaAdmBank,
                    'akun_keuangan_id' => $akunAsal->id,
                    'nominal' => $biayaAdmin,
                    'tgl_transaksi' => $tglTransaksi,
                    'pihak_terkait' => 'Biaya Admin Bank / Flip',
                    'keterangan' => "[{$groupKode}] Biaya admin transfer ke {$akunTujuan->nama}",
                    'bukti_transaksi' => $data['bukti_transaksi'] ?? null,
                    'created_by' => $actorId,
                ]);
            }

            return [
                'keluar' => $trxKeluar->fresh(),
                'masuk' => $trxMasuk->fresh(),
                'biaya_admin' => $trxBiayaAdmin?->fresh(),
                'group_kode' => $groupKode,
            ];
        });
    }

    /**
     * Soft-delete seluruh transaksi yang tergabung dalam 1 grup transfer secara atomik.
     */
    public function hapusTransfer(TransaksiKeuangan $transaksi): void
    {
        DB::transaction(function () use ($transaksi): void {
            if (! empty($transaksi->transfer_group_kode)) {
                TransaksiKeuangan::where('transfer_group_kode', $transaksi->transfer_group_kode)
                    ->get()
                    ->each
                    ->delete();

                return;
            }

            $pasangan = $transaksi->transferPasangan;
            $transaksi->delete();
            $pasangan?->delete();
        });
    }

    /**
     * Pulihkan seluruh transaksi yang tergabung dalam 1 grup transfer secara atomik.
     */
    public function pulihkanTransfer(TransaksiKeuangan $transaksi): void
    {
        DB::transaction(function () use ($transaksi): void {
            if (! empty($transaksi->transfer_group_kode)) {
                TransaksiKeuangan::withTrashed()
                    ->where('transfer_group_kode', $transaksi->transfer_group_kode)
                    ->get()
                    ->each
                    ->restore();

                return;
            }

            $transaksi->restore();
            if ($transaksi->transfer_pasangan_id) {
                TransaksiKeuangan::withTrashed()->find($transaksi->transfer_pasangan_id)?->restore();
            }
        });
    }

    /**
     * Simpan hasil hitung fisik kas tunai (Cash Opname) dan update rekonsiliasi akun kas.
     */
    public function simpanCashOpname(
        AkunKeuangan $akun,
        string $tglOpname,
        array $pecahan,
        ?string $catatan = null,
        ?int $userId = null
    ): CashOpname {
        $totalFisik = CashOpname::hitungTotalFisik($pecahan);
        $saldoBuku = $akun->hitungSaldoSampaiTanggal($tglOpname);
        $selisih = $totalFisik - $saldoBuku;

        return DB::transaction(function () use ($akun, $tglOpname, $pecahan, $totalFisik, $saldoBuku, $selisih, $catatan, $userId): CashOpname {
            $opname = CashOpname::create([
                'akun_keuangan_id' => $akun->id,
                'tgl_opname' => $tglOpname,
                'pecahan' => $pecahan,
                'total_fisik' => $totalFisik,
                'saldo_buku' => $saldoBuku,
                'selisih' => $selisih,
                'catatan' => $catatan,
                'petugas_id' => $userId ?? Auth::id(),
            ]);

            $akun->update([
                'saldo_riil_terakhir' => $totalFisik,
                'tgl_rekonsiliasi_terakhir' => $tglOpname,
            ]);

            return $opname;
        });
    }

    private function generateTransferGroupKode(): string
    {
        $prefix = 'TRF-'.now()->format('Ym').'-';
        $latest = TransaksiKeuangan::withTrashed()
            ->where('transfer_group_kode', 'like', $prefix.'%')
            ->lockForUpdate()
            ->orderByDesc('id')
            ->first();

        $sequence = 1;
        if ($latest && preg_match('/'.preg_quote($prefix, '/').'(\d+)/', (string) $latest->transfer_group_kode, $matches)) {
            $sequence = ((int) $matches[1]) + 1;
        }

        return $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    private function resolveCoaUntukAkun(AkunKeuangan $akun): int
    {
        if ($akun->kategori_keuangan_id) {
            return (int) $akun->kategori_keuangan_id;
        }

        if (! empty($akun->kode_coa)) {
            $coa = KategoriKeuangan::where('kode_coa', $akun->kode_coa)->first();
            if ($coa) {
                return (int) $coa->id;
            }
        }

        $fallbackKode = $akun->tipe === 'kas' ? '101.01.001' : '101.02.000';
        $coaByKode = KategoriKeuangan::where('kode_coa', $fallbackKode)->first();
        if ($coaByKode) {
            return (int) $coaByKode->id;
        }

        $fallback = KategoriKeuangan::firstOrCreate(
            ['nama' => 'Mutasi Antar Rekening / Kas', 'jenis' => 'netral'],
            [
                'kelompok' => 'aset',
                'pos_dana' => 'netral',
                'level' => 3,
                'saldo_normal' => 'debit',
                'is_postable' => true,
                'is_active' => true,
                'deskripsi' => 'Akun perantara mutasi pindah saldo antar kas/bank',
            ]
        );

        return (int) $fallback->id;
    }

    private function resolveCoaBiayaAdmin(): int
    {
        $coa = KategoriKeuangan::where('kode_coa', '503.03.009')->first();
        if ($coa) {
            return (int) $coa->id;
        }

        $fallback = KategoriKeuangan::firstOrCreate(
            ['nama' => 'BIAYA TRANSFER/ADM BANK', 'jenis' => 'keluar'],
            [
                'kode_coa' => '503.03.009',
                'kelompok' => 'penyaluran',
                'pos_dana' => 'amil',
                'level' => 3,
                'saldo_normal' => 'debit',
                'is_postable' => true,
                'is_active' => true,
                'deskripsi' => 'Biaya administrasi transfer bank / Flip',
            ]
        );

        return (int) $fallback->id;
    }
}
