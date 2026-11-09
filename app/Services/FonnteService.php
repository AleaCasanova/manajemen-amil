<?php

namespace App\Services;

use App\Models\Donasi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Throwable;

class FonnteService
{
    protected string $logFile = 'wa_kwitansi_logs.json';

    protected string $configFile = 'fonnte_config.json';

    /**
     * Ambil Token API Fonnte dari .env / config atau file konfigurasi lokal (tanpa menyentuh database)
     */
    public function getToken(): ?string
    {
        $envToken = config('services.fonnte.token');
        if (! empty($envToken) && is_string($envToken)) {
            return trim($envToken);
        }

        try {
            if (Storage::disk('local')->exists($this->configFile)) {
                $content = Storage::disk('local')->get($this->configFile);
                $data = json_decode($content, true);
                if (! empty($data['token']) && is_string($data['token'])) {
                    return trim($data['token']);
                }
            }
        } catch (Throwable) {
            // Abaikan jika gagal membaca file
        }

        return null;
    }

    /**
     * Simpan Token API Fonnte ke file konfigurasi lokal (tanpa menyentuh database)
     */
    public function saveToken(string $token): void
    {
        Storage::disk('local')->put($this->configFile, json_encode([
            'token' => trim($token),
            'updated_at' => Carbon::now('Asia/Jakarta')->toDateTimeString(),
        ], JSON_PRETTY_PRINT));
    }

    /**
     * Cek apakah Token Fonnte sudah dikonfigurasi
     */
    public function isConfigured(): bool
    {
        return ! empty($this->getToken());
    }

    /**
     * Bersihkan dan format nomor WhatsApp menjadi awalan 62
     */
    public function formatPhoneNumber(?string $rawPhone): string
    {
        $cleanPhone = preg_replace('/[^0-9]/', '', (string) $rawPhone);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62'.substr($cleanPhone, 1);
        }

        return $cleanPhone;
    }

    /**
     * Cek riwayat apakah kwitansi donasi ini sudah pernah dikirim via WhatsApp Resmi
     *
     * @return array{no_kwitansi: string, target: string, sent_at: string, sent_by: string}|null
     */
    public function getSentLog(int $donasiId): ?array
    {
        $logs = $this->getAllLogs();
        $key = (string) $donasiId;

        return $logs[$key] ?? null;
    }

    /**
     * Ambil seluruh data log pengiriman dari file JSON di storage
     *
     * @return array<string, array{no_kwitansi: string, target: string, sent_at: string, sent_by: string}>
     */
    protected function getAllLogs(): array
    {
        try {
            if (! Storage::disk('local')->exists($this->logFile)) {
                return [];
            }

            $content = Storage::disk('local')->get($this->logFile);
            $decoded = json_decode($content, true);

            return is_array($decoded) ? $decoded : [];
        } catch (Throwable) {
            return [];
        }
    }

    /**
     * Catat bahwa kwitansi donasi telah berhasil dikirim via WhatsApp Resmi
     *
     * @return array{no_kwitansi: string, target: string, sent_at: string, sent_by: string}
     */
    public function markAsSent(Donasi $donasi, string $targetPhone, string $senderName): array
    {
        $logs = $this->getAllLogs();
        $entry = [
            'no_kwitansi' => (string) $donasi->no_kwitansi,
            'target' => $targetPhone,
            'sent_at' => Carbon::now('Asia/Jakarta')->translatedFormat('d M Y, H:i').' WIB',
            'sent_by' => $senderName,
        ];

        $logs[(string) $donasi->id] = $entry;

        Storage::disk('local')->put($this->logFile, json_encode($logs, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return $entry;
    }

    /**
     * Susun teks pesan WhatsApp Kwitansi Resmi LAZ Gerak Sedekah Cilacap
     */
    public function buildKwitansiMessage(Donasi $donasi): string
    {
        $donasi->loadMissing(['donatur', 'jenisDana', 'program', 'amil', 'donasiBarangs']);

        $waktuDonasi = Carbon::parse($donasi->tgl_donasi)->timezone('Asia/Jakarta');
        if ($waktuDonasi->format('H:i') === '00:00' && $donasi->created_at) {
            $waktuDonasi = Carbon::parse($donasi->created_at)->timezone('Asia/Jakarta');
        }

        $namaPenerima = $donasi->donatur?->is_anonim
            ? 'Bapak/Ibu'
            : ($donasi->donatur?->nama ?? 'Bapak/Ibu');

        $verifyUrl = route('kwitansi.verify', $donasi->no_kwitansi);
        $downloadUrl = route('kwitansi.download', $donasi->id);

        $barangText = '';
        if ($donasi->donasiBarangs && $donasi->donasiBarangs->isNotEmpty()) {
            $barangList = $donasi->donasiBarangs->map(function ($b): string {
                return $b->deskripsi.($b->estimasi_nilai > 0 ? ' (Est: Rp '.number_format((float) $b->estimasi_nilai, 0, ',', '.').')' : '');
            })->implode(', ');
            $barangText = "\n📦 *Donasi Barang/Natura:* {$barangList}";
        }

        return "Assalamu'alaikum Warahmatullahi Wabarakatuh,\n\n"
            ."Yth. *{$namaPenerima}*,\n"
            ."Terima kasih atas kepercayaan Anda menyalurkan donasi melalui *LAZ Gerak Sedekah Cilacap*.\n\n"
            ."Berikut adalah rincian kwitansi elektronik Anda:\n"
            ."📄 *No. Kwitansi:* {$donasi->no_kwitansi}\n"
            .'📅 *Waktu Transaksi:* '.$waktuDonasi->translatedFormat('d F Y, H:i')." WIB\n"
            .'🤲 *Jenis Dana:* '.($donasi->jenisDana?->nama ?? 'Donasi')."\n"
            .'📌 *Program:* '.($donasi->program?->nama ?? 'Penerimaan Umum')."\n"
            .'💰 *Nominal:* Rp '.number_format((float) $donasi->jumlah, 0, ',', '.')."{$barangText}\n"
            .'👤 *Amil Penghimpun:* '.($donasi->amil?->nama ?? 'Amil GSC')."\n\n"
            ."🔗 *Cek Keaslian & Unduh Kwitansi Resmi (PDF):*\n"
            ."{$verifyUrl}\n"
            ."📥 *Link Unduh Langsung PDF:*\n"
            ."{$downloadUrl}\n\n"
            ."Semoga Allah SWT membalas dengan pahala berlipat ganda, membersihkan harta, dan melimpahkan keberkahan bagi Anda dan keluarga. Aamiin ya Rabbal 'Alamin.\n\n"
            ."_Salam hangat,_\n*LAZ Gerak Sedekah Cilacap*";
    }

    /**
     * Kirim pesan kwitansi via API Fonnte (Nomor WhatsApp Admin Resmi)
     *
     * @return array{success: bool, already_sent?: bool, message: string, log?: array{no_kwitansi: string, target: string, sent_at: string, sent_by: string}}
     */
    public function sendKwitansi(Donasi $donasi, string $senderName = 'Admin / Amil GSC'): array
    {
        // 1. Cek apakah sudah pernah dikirim (Anti-Spam)
        $existingLog = $this->getSentLog((int) $donasi->id);
        if ($existingLog !== null) {
            return [
                'success' => false,
                'already_sent' => true,
                'message' => "Kwitansi ini sudah pernah dikirim ke nomor {$existingLog['target']} pada {$existingLog['sent_at']} oleh {$existingLog['sent_by']}. Pengiriman dibatalkan otomatis untuk mencegah spam.",
                'log' => $existingLog,
            ];
        }

        // 2. Validasi nomor HP donatur
        $cleanPhone = $this->formatPhoneNumber($donasi->donatur?->no_hp);
        if (empty($cleanPhone) || strlen($cleanPhone) < 9) {
            return [
                'success' => false,
                'message' => 'Nomor WhatsApp donatur tidak valid atau kosong.',
            ];
        }

        // 3. Cek Token Fonnte
        $token = $this->getToken();
        if (empty($token)) {
            return [
                'success' => false,
                'message' => 'Token API Fonnte belum diatur. Silakan daftar di fonnte.com, tautkan nomor WA Admin Resmi, lalu masukkan Token ke pengaturan sistem / .env (FONNTE_TOKEN).',
            ];
        }

        $message = $this->buildKwitansiMessage($donasi);
        $apiUrl = (string) config('services.fonnte.url', 'https://api.fonnte.com/send');

        try {
            $response = Http::timeout(20)
                ->withHeaders([
                    'Authorization' => $token,
                ])
                ->asForm()
                ->post($apiUrl, [
                    'target' => $cleanPhone,
                    'message' => $message,
                    'countryCode' => '62',
                ]);

            $json = $response->json();

            if ($response->successful() && is_array($json) && ! empty($json['status'])) {
                $log = $this->markAsSent($donasi, $cleanPhone, $senderName);

                return [
                    'success' => true,
                    'message' => "Alhamdulillah! Kwitansi berhasil dikirim ke WhatsApp donatur ({$cleanPhone}) melalui Nomor Resmi Admin LAZ Gerak Sedekah Cilacap.",
                    'log' => $log,
                ];
            }

            $reason = is_array($json) ? ($json['reason'] ?? 'Periksa status koneksi perangkat WhatsApp Admin di dashboard Fonnte.') : 'Respons server Fonnte tidak dikenali.';

            return [
                'success' => false,
                'message' => "Gagal mengirim WhatsApp via Fonnte: {$reason}",
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => 'Gagal menghubungi server Fonnte: '.$e->getMessage(),
            ];
        }
    }
}
