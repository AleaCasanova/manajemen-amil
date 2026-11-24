<?php

namespace App\Services;

use App\Models\Amil;
use App\Models\CashAdvance;
use App\Models\CashAdvanceRealisasi;
use App\Models\KategoriKeuangan;
use App\Models\TransaksiKeuangan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class CashAdvanceService
{
    /**
     * Cairkan Cash Advance (101.09.000) atau Cash Bon (101.06.001) dan otomatis catat kas keluar di Buku Kas.
     *
     * @param  array{
     *     tipe?: string|null,
     *     amil_id?: int|string|null,
     *     nama_pj?: string|null,
     *     divisi_id?: int|string|null,
     *     program_id?: int|string|null,
     *     pengajuan_dana_id?: int|string|null,
     *     akun_keuangan_id: int|string,
     *     tgl_pencairan?: string|null,
     *     tgl_jatuh_tempo?: string|null,
     *     nominal_dicairkan: float|int|string,
     *     keperluan: string,
     *     bukti_pencairan?: string|null,
     * }  $data
     */
    public function cairkan(array $data, ?int $userId = null): CashAdvance
    {
        $tipe = $data['tipe'] ?? 'cash_advance';
        if (! in_array($tipe, ['cash_advance', 'cash_bon'], true)) {
            throw new InvalidArgumentException('Tipe uang muka tidak valid.');
        }

        $nominal = (float) ($data['nominal_dicairkan'] ?? 0);
        if ($nominal <= 0) {
            throw new InvalidArgumentException('Nominal pencairan harus lebih besar dari nol.');
        }

        $amilId = ! empty($data['amil_id']) ? (int) $data['amil_id'] : null;
        $namaPj = trim((string) ($data['nama_pj'] ?? ''));
        if ($namaPj === '' && $amilId) {
            $namaPj = Amil::find($amilId)?->nama ?? 'Amil';
        }
        if ($namaPj === '') {
            throw new InvalidArgumentException('Nama penanggung jawab wajib diisi.');
        }

        return DB::transaction(function () use ($data, $tipe, $nominal, $amilId, $namaPj, $userId): CashAdvance {
            $actorId = $userId ?? Auth::id();
            $tglPencairan = $data['tgl_pencairan'] ?? now()->toDateString();
            $coaId = $this->resolveCoaPiutang($tipe);

            $cashAdvance = CashAdvance::create([
                'tipe' => $tipe,
                'amil_id' => $amilId,
                'nama_pj' => $namaPj,
                'divisi_id' => ! empty($data['divisi_id']) ? (int) $data['divisi_id'] : null,
                'program_id' => ! empty($data['program_id']) ? (int) $data['program_id'] : null,
                'pengajuan_dana_id' => ! empty($data['pengajuan_dana_id']) ? (int) $data['pengajuan_dana_id'] : null,
                'akun_keuangan_id' => (int) $data['akun_keuangan_id'],
                'tgl_pencairan' => $tglPencairan,
                'tgl_jatuh_tempo' => $data['tgl_jatuh_tempo'] ?? null,
                'nominal_dicairkan' => $nominal,
                'keperluan' => $data['keperluan'],
                'status' => 'aktif',
                'bukti_pencairan' => $data['bukti_pencairan'] ?? null,
                'created_by' => $actorId,
            ]);

            $label = $tipe === 'cash_bon' ? 'Cash Bon' : 'CA';

            TransaksiKeuangan::create([
                'jenis' => 'keluar',
                'tipe_transaksi' => $tipe === 'cash_bon' ? 'cash_bon_keluar' : 'cash_advance_keluar',
                'is_transfer' => false,
                'kategori_keuangan_id' => $coaId,
                'akun_keuangan_id' => $cashAdvance->akun_keuangan_id,
                'program_id' => $cashAdvance->program_id,
                'cash_advance_id' => $cashAdvance->id,
                'nominal' => $nominal,
                'tgl_transaksi' => $tglPencairan,
                'pihak_terkait' => $namaPj,
                'pj_amil_id' => $amilId,
                'divisi_id' => $cashAdvance->divisi_id,
                'keterangan' => "{$label} [{$cashAdvance->kode_ca}]: {$cashAdvance->keperluan}",
                'bukti_transaksi' => $cashAdvance->bukti_pencairan,
                'created_by' => $actorId,
            ]);

            return $cashAdvance->fresh(['akunKeuangan', 'amil', 'transaksiKeuangans']);
        });
    }

    /**
     * Selesaikan LPJ Cash Advance menggunakan metode gross settlement seperti pembukuan Bendahara GSC:
     * 1) Catat masuk penutupan CA penuh (COA 101.09.000)
     * 2) Catat keluar masing-masing item beban riil (COA 501/502/503)
     *
     * @param  array<int, array{
     *     kategori_keuangan_id: int|string,
     *     uraian: string,
     *     nominal: float|int|string,
     *     tgl_realisasi?: string|null,
     *     bukti_nota?: string|null,
     * }>  $items
     */
    public function selesaikanLpj(
        CashAdvance $cashAdvance,
        array $items,
        int $akunPenyelesaianId,
        string $tglSelesai,
        ?string $catatan = null,
        ?int $userId = null
    ): CashAdvance {
        if ($cashAdvance->tipe !== 'cash_advance') {
            throw new InvalidArgumentException('Penyelesaian LPJ hanya berlaku untuk Cash Advance.');
        }

        if ($cashAdvance->status !== 'aktif') {
            throw new InvalidArgumentException('Cash Advance ini sudah diselesaikan atau dibatalkan.');
        }

        $validItems = collect($items)
            ->filter(fn (array $item): bool => (float) ($item['nominal'] ?? 0) > 0)
            ->values();

        return DB::transaction(function () use ($cashAdvance, $validItems, $akunPenyelesaianId, $tglSelesai, $catatan, $userId): CashAdvance {
            $ca = CashAdvance::whereKey($cashAdvance->id)->lockForUpdate()->firstOrFail();

            if ($ca->status !== 'aktif') {
                throw new InvalidArgumentException('Cash Advance ini sudah diselesaikan.');
            }

            $actorId = $userId ?? Auth::id();
            $coaCaId = $this->resolveCoaPiutang('cash_advance');

            // 1. Transaksi masuk penutupan penuh CA (COA 101.09.000)
            TransaksiKeuangan::create([
                'jenis' => 'masuk',
                'tipe_transaksi' => 'cash_advance_kembali',
                'is_transfer' => false,
                'kategori_keuangan_id' => $coaCaId,
                'akun_keuangan_id' => $akunPenyelesaianId,
                'program_id' => $ca->program_id,
                'cash_advance_id' => $ca->id,
                'nominal' => $ca->nominal_dicairkan,
                'tgl_transaksi' => $tglSelesai,
                'pihak_terkait' => $ca->nama_pj,
                'pj_amil_id' => $ca->amil_id,
                'divisi_id' => $ca->divisi_id,
                'keterangan' => "Pengembalian CA [{$ca->kode_ca}]: {$ca->keperluan}",
                'created_by' => $actorId,
            ]);

            // 2. Transaksi keluar beban riil per item nota LPJ
            $totalRealisasi = 0.0;
            foreach ($validItems as $item) {
                $nominalItem = (float) $item['nominal'];
                $tglItem = $item['tgl_realisasi'] ?? $tglSelesai;
                $uraian = trim((string) $item['uraian']);
                $buktiNota = $item['bukti_nota'] ?? null;

                $trxRealisasi = TransaksiKeuangan::create([
                    'jenis' => 'keluar',
                    'tipe_transaksi' => 'cash_advance_realisasi',
                    'is_transfer' => false,
                    'kategori_keuangan_id' => (int) $item['kategori_keuangan_id'],
                    'akun_keuangan_id' => $akunPenyelesaianId,
                    'program_id' => $ca->program_id,
                    'cash_advance_id' => $ca->id,
                    'nominal' => $nominalItem,
                    'tgl_transaksi' => $tglItem,
                    'pihak_terkait' => $ca->nama_pj,
                    'pj_amil_id' => $ca->amil_id,
                    'divisi_id' => $ca->divisi_id,
                    'keterangan' => "LPJ CA [{$ca->kode_ca}]: {$uraian}",
                    'bukti_transaksi' => $buktiNota,
                    'created_by' => $actorId,
                ]);

                CashAdvanceRealisasi::create([
                    'cash_advance_id' => $ca->id,
                    'kategori_keuangan_id' => (int) $item['kategori_keuangan_id'],
                    'transaksi_keuangan_id' => $trxRealisasi->id,
                    'tgl_realisasi' => $tglItem,
                    'uraian' => $uraian,
                    'nominal' => $nominalItem,
                    'bukti_nota' => $buktiNota,
                ]);

                $totalRealisasi += $nominalItem;
            }

            $selisih = (float) $ca->nominal_dicairkan - $totalRealisasi;
            $dikembalikan = max(0.0, $selisih);
            $reimburse = $selisih < 0 ? abs($selisih) : 0.0;

            $ca->update([
                'nominal_realisasi' => $totalRealisasi,
                'nominal_dikembalikan' => $dikembalikan,
                'nominal_reimburse' => $reimburse,
                'status' => 'selesai',
                'tgl_selesai' => $tglSelesai,
                'akun_penyelesaian_id' => $akunPenyelesaianId,
                'catatan_penyelesaian' => $catatan,
            ]);

            return $ca->fresh(['realisasis', 'transaksiKeuangans']);
        });
    }

    /**
     * Catat pelunasan / cicilan pengembalian Cash Bon (COA 101.06.001).
     */
    public function bayarCashBon(
        CashAdvance $cashBon,
        float $nominalBayar,
        int $akunPenerimaId,
        string $tglBayar,
        ?string $catatan = null,
        ?string $buktiPath = null,
        ?int $userId = null
    ): CashAdvance {
        if ($cashBon->tipe !== 'cash_bon') {
            throw new InvalidArgumentException('Pembayaran pengembalian hanya berlaku untuk Cash Bon.');
        }

        if (! in_array($cashBon->status, ['aktif', 'sebagian'], true)) {
            throw new InvalidArgumentException('Cash Bon ini sudah lunas atau dibatalkan.');
        }

        if ($nominalBayar <= 0) {
            throw new InvalidArgumentException('Nominal pembayaran harus lebih besar dari nol.');
        }

        return DB::transaction(function () use ($cashBon, $nominalBayar, $akunPenerimaId, $tglBayar, $catatan, $buktiPath, $userId): CashAdvance {
            $cb = CashAdvance::whereKey($cashBon->id)->lockForUpdate()->firstOrFail();

            $sisaTanggungan = $cb->sisa_tanggungan;
            if ($nominalBayar > ($sisaTanggungan + 0.01)) {
                throw new InvalidArgumentException('Nominal pembayaran melebihi sisa tanggungan Cash Bon.');
            }

            $actorId = $userId ?? Auth::id();
            $coaCbId = $this->resolveCoaPiutang('cash_bon');

            TransaksiKeuangan::create([
                'jenis' => 'masuk',
                'tipe_transaksi' => 'cash_bon_kembali',
                'is_transfer' => false,
                'kategori_keuangan_id' => $coaCbId,
                'akun_keuangan_id' => $akunPenerimaId,
                'program_id' => $cb->program_id,
                'cash_advance_id' => $cb->id,
                'nominal' => $nominalBayar,
                'tgl_transaksi' => $tglBayar,
                'pihak_terkait' => $cb->nama_pj,
                'pj_amil_id' => $cb->amil_id,
                'divisi_id' => $cb->divisi_id,
                'keterangan' => "Pelunasan Cash Bon [{$cb->kode_ca}]: ".($catatan ?: $cb->keperluan),
                'bukti_transaksi' => $buktiPath,
                'created_by' => $actorId,
            ]);

            $totalDikembalikan = (float) $cb->nominal_dikembalikan + $nominalBayar;
            $lunas = $totalDikembalikan >= ((float) $cb->nominal_dicairkan - 0.01);

            $cb->update([
                'nominal_dikembalikan' => min((float) $cb->nominal_dicairkan, $totalDikembalikan),
                'status' => $lunas ? 'selesai' : 'sebagian',
                'tgl_selesai' => $lunas ? $tglBayar : null,
                'akun_penyelesaian_id' => $akunPenerimaId,
                'catatan_penyelesaian' => $catatan,
            ]);

            return $cb->fresh(['transaksiKeuangans']);
        });
    }

    private function resolveCoaPiutang(string $tipe): int
    {
        $kode = $tipe === 'cash_bon' ? '101.06.001' : '101.09.000';
        $nama = $tipe === 'cash_bon' ? 'Cash Bon' : 'Cash Advance';

        $coa = KategoriKeuangan::where('kode_coa', $kode)->first();
        if ($coa) {
            return (int) $coa->id;
        }

        $created = KategoriKeuangan::firstOrCreate(
            ['nama' => $nama, 'jenis' => 'netral'],
            [
                'kode_coa' => $kode,
                'kelompok' => 'aset',
                'pos_dana' => 'netral',
                'level' => 3,
                'saldo_normal' => 'debit',
                'is_postable' => true,
                'is_active' => true,
                'deskripsi' => $tipe === 'cash_bon' ? 'Piutang kasbon / salah transfer sementara' : 'Uang muka kerja operasional / program',
            ]
        );

        return (int) $created->id;
    }
}
