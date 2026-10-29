# BUKU PANDUAN & MODUL OPERASIONAL SISTEM MANAJEMEN AMIL
## YAYASAN GERAK SEDEKAH CILACAP (GSC)

> **Dokumen Resmi:** Modul Sosialisasi, Presentasi, dan SOP Implementasi Sistem Digital Manajemen Amil & Donasi ZISWAF.  
> **Tanggal Rilis Operasional:** Mulai Berlaku Senin, 2 November 2026  
> **Lembaga:** Yayasan Gerak Sedekah Cilacap (GSC)  
> **Kontak Resmi:** Telp/WA: `+62 857-0122-3333` | Email: `geraksedekahcilacap@gmail.com`  
> **Alamat Kantor:** Jalan Sulawesi, Perum Puri Tanjung Intan No. B2, Karang Lor, Gunungsimping, Cilacap Tengah 53224  

---

## DAFTAR ISI
1. [Pendahuluan & Tujuan Digitalisasi](#1-pendahuluan--tujuan-digitalisasi)
2. [Akses Sistem & Kompatibilitas Perangkat](#2-akses-sistem--kompatibilitas-perangkat)
3. [Struktur Pengguna & Pembagian Tugas Per Role](#3-struktur-pengguna--pembagian-tugas-per-role)
4. [Standar Operasional Prosedur (SOP) Alur Kerja Utama](#4-standar-operasional-prosedur-sop-alur-kerja-utama)
   - [SOP 1: Presensi Harian (GPS & Kamera Selfie)](#sop-1-presensi-harian-gps--kamera-selfie)
   - [SOP 2: Penghimpunan Donasi & Penerbitan Kwitansi Otomatis](#sop-2-penghimpunan-donasi--penerbitan-kwitansi-otomatis)
   - [SOP 3: Verifikasi Keuangan oleh Bendahara](#sop-3-verifikasi-keuangan-oleh-bendahara)
   - [SOP 4: Pendataan Mustahik & Penyaluran Donasi](#sop-4-pendataan-mustahik--penyaluran-donasi)
   - [SOP 5: Pengajuan Izin & Cuti Amil](#sop-5-pengajuan-izin--cuti-amil)
5. [Panduan Rinci Setiap Fitur Sistem](#5-panduan-rinci-setiap-fitur-sistem)
6. [Skenario Simulasi Live Demo Presentasi](#6-skenario-simulasi-live-demo-presentasi)
7. [Tanya Jawab Populer (FAQ) & Panduan Kendala Lapangan](#7-tanya-jawab-populer-faq--panduan-kendala-lapangan)

---

## 1. PENDAHULUAN & TUJUAN DIGITALISASI

Sistem Manajemen Amil GSC dikembangkan sebagai lompatan digital untuk mewujudkan pengelolaan Zakat, Infak, Sedekah, dan Wakaf (ZISWAF) yang **Akuntabel, Transparan, Cepat, dan Modern**.

### Manfaat Utama bagi Lembaga:
1. **Untuk Donatur:**
   - Menerima bukti kuitansi resmi instan format PDF A5 Landscape berseri unik (`KWT-GSC/...`).
   - Kuitansi dapat diverifikasi keasliannya secara publik melalui pemindaian QR Code resmi.
   - Bukti kuitansi dikirimkan langsung ke nomor WhatsApp donatur dalam 1 kali klik.
2. **Untuk Amil / Petugas Lapangan:**
   - Tidak perlu lagi mencatat kuitansi secara manual di buku kertas atau menyalin berulang kali.
   - Presensi kehadiran transparan berbasis GPS Geofencing radius kantor dan kamera swafoto (*selfie*).
   - Memantau capaian target donasi pribadi secara *real-time* dari dashboard smartphone.
3. **Untuk Bendahara & Manajemen / Pengurus:**
   - Pengawasan mutasi donasi masuk detik per detik tanpa selisih pembukuan.
   - Penatausahaan donasi natura/barang dengan estimasi nilai terstandar.
   - Laporan keuangan, rekap absensi harian, dan penyaluran mustahik otomatis siap ekspor.

---

## 2. AKSES SISTEM & KOMPATIBILITAS PERANGKAT

Sistem berbasis *Progressive Web Application (PWA)* yang responsif, ringan, dan dapat diakses dari berbagai peramban web (*browser*):

| Kategori Portal | Tautan URL Akses | Sasaran Pengguna | Perangkat Rekomendasi |
| :--- | :--- | :--- | :--- |
| **Portal Amil Lapangan** | `http://[domain-gsc]/amil/login` atau `/login` | Seluruh Amil, Driver Ambulans, Relawan, Magang | Smartphone (Chrome / Safari / Edge) |
| **Panel Admin & Keuangan** | `http://[domain-gsc]/admin` | Super Admin, Pimpinan Yayasan, Bendahara | Laptop / Komputer PC Desktop |
| **Verifikasi Kwitansi Publik** | `http://[domain-gsc]/verifikasi-kwitansi/{no_kwitansi}` | Donatur, Auditor, Publik Umum | Semua Perangkat via Scan QR Code |

> [!TIP]
> **Akses Cepat di Smartphone:**  
> Buka tautan portal di browser Chrome Android atau Safari iOS, lalu pilih menu **"Tambahkan ke Layar Utama" (Add to Home Screen)**. Aplikasi akan terpasang di HP layaknya aplikasi native tanpa memakan memori besar.

---

## 3. STRUKTUR PENGGUNA & PEMBAGIAN TUGAS PER ROLE

Aplikasi membagi hak akses ke dalam **4 Tingkatan Peran Kerja (Roles)**:

```
┌─────────────────────────────────────────────────────────────┐
│                 PIMPINAN & SUPER ADMIN                      │
│        (Monitoring Eksekutif, Master Data, Audit Log)        │
└──────────────┬───────────────────────────────┬──────────────┘
               │                               │
┌──────────────▼──────────────┐ ┌──────────────▼──────────────┐
│     BENDAHARA / KEUANGAN    │ │   AMIL TETAP / FUNDRAISER   │
│  (Validasi Dana, Tanda      │ │  (Presensi, Input Donasi,   │
│   Tangan/Cap, Laporan Kas)  │ │   CRM Donatur, Penyaluran)  │
└─────────────────────────────┘ └──────────────┬──────────────┘
                                               │
                                ┌──────────────▼──────────────┐
                                │  RELAWAN & MAHASISWA MAGANG │
                                │   (Driver Ambulans Siaga,   │
                                │    Presensi Tugas Luar)     │
                                └─────────────────────────────┘
```

### 1. Super Admin & Pimpinan Yayasan (Role: Admin)
* **Wewenang:** Akses penuh ke Panel Admin (`/admin`).
* **Tugas Harian:**
  1. Menambah, mengedit, atau menonaktifkan akun Amil serta menetapkan target donasi bulanan.
  2. Mengatur jam kerja kantor, radius geofencing lokasi kantor pusat/cabang, dan master data jenis dana.
  3. Mempublikasikan pengumuman yayasan dan mengatur penugasan amil pada agenda kegiatan.
  4. Meninjau dan menyetujui pengajuan izin & cuti amil.
  5. Memantau dashboard penerimaan ZISWAF dan audit log aktivitas sistem.

### 2. Bendahara & Divisi Keuangan (Role: Admin / Bendahara)
* **Wewenang:** Akses verifikasi finansial dan penerbitan kwitansi sah di panel `/admin`.
* **Tugas Harian:**
  1. Memeriksa mutasi rekening bank donasi (BSI, Mandiri, dll.) atau setoran tunai amil.
  2. Melakukan verifikasi transaksi donasi (`Menunggu Verifikasi` $\rightarrow$ `Terverifikasi`).
  3. Memastikan cap digital resmi GSC dan tanda tangan bendahara tersemat pada kwitansi yang sah.
  4. Mengontrol alokasi porsi hak amil sesuai syariat pada tiap kelompok dana (Zakat 12.5%, Infak 20%, DSKL 10%).

### 3. Amil Tetap & Staff Lapangan (Role: Amil)
* **Wewenang:** Akses portal operasional amil (`/amil`).
* **Tugas Harian:**
  1. Melakukan presensi masuk jam 08:00 WIB dan presensi pulang jam 16:30 WIB dengan swafoto & GPS.
  2. Menghimpun donasi dari donatur perorangan, korporat, atau kotak infak.
  3. Menerbitkan kuitansi elektronik langsung saat donasi diterima dan mengirimkan bukti via WhatsApp.
  4. Mendata mustahik penerima manfaat dan mencatatkan bukti penyaluran program.
  5. Membuat laporan aktivitas lapangan harian beserta foto dokumentasi.

### 4. Relawan & Mahasiswa Magang (Role: Amil - Relawan / Magang / Driver)
* **Wewenang:** Akses portal amil (`/amil`) dengan tanda badge status khusus.
* **Tugas Harian:**
  1. Melakukan presensi masuk/pulang menggunakan mode **"Tugas Luar"** saat bertugas di luar kantor (contoh: operasional Driver Ambulans Siaga Gratis, pengantaran pasien dhuafa, tanggap bencana).
  2. Membantu penghimpunan donasi insidental atau pendistribusian logistik program.
  3. Mengisi laporan aktivitas lapangan harian.

---

## 4. STANDAR OPERASIONAL PROSEDUR (SOP) ALUR KERJA UTAMA

### SOP 1: Presensi Harian (GPS & Kamera Selfie)

```
[ Buka Portal Amil ] ──> [ Deteksi GPS Realtime ] ──> [ Pilih Mode: Kantor / Tugas Luar ]
                                                                  │
              ┌───────────────────────────────────────────────────┴─────────────────┐
              ▼                                                                     ▼
      [ Mode Kantor ]                                                      [ Mode Tugas Luar ]
  (Harus <100m dari Kantor)                                             (Wajib Isi Alasan & Tugas)
              │                                                                     │
              └───────────────────────────────┬─────────────────────────────────────┘
                                              ▼
                             [ Ambil Swafoto via Kamera HP ]
                                              ▼
                        [ Jam 08:00 WIB: Tekan "Presensi Masuk" ]
                                              │
                                    (Aktivitas Kerja Harian)
                                              │
                                              ▼
                        [ Jam 16:30 WIB: Tekan "Presensi Pulang" ]
                           *Catatan: Terkunci sebelum 15:30 WIB
```

1. **Jadwal Kerja:** Jam kerja standar pukul **08:00 s.d 16:30 WIB** (toleransi keterlambatan 15 menit).
2. **Presensi Masuk:**
   - Aktifkan GPS pada smartphone dan izinkan akses kamera pada peramban.
   - Jika berada di kantor, pastikan indikator lokasi berwarna hijau *(Dalam Radius Kantor)*.
   - Jika sedang berdinas di luar/antar pasien, klik tombol **"Tugas Luar"** dan tuliskan keterangan tugas.
   - Ambil foto selfie wajah terbaru, lalu klik **"Presensi Masuk Sekarang"**.
3. **Presensi Pulang:**
   - Sistem **mengunci tombol presensi pulang kantor sebelum pukul 15:30 WIB** guna menjamin kedisiplinan kerja.
   - Presensi kepulangan antara 15:30 - 16:30 WIB otomatis dicatat dengan notasi *"Pulang Lebih Awal"*.
   - Khusus amil bermode Tugas Luar, tombol pulang dapat dilakukan fleksibel sesuai penugasan lapangan.

---

### SOP 2: Penghimpunan Donasi & Penerbitan Kwitansi Otomatis

```
[ Donatur Menyerahkan Donasi ] ──> [ Amil Buka Menu "Input Donasi" ]
                                               │
                                               ▼
                              [ Cek / Pilih Donatur via No HP ]
                            (Otomatis Auto-Fill Data Donatur)
                                               │
                                               ▼
                         [ Pilih Jenis Dana, Program & Nominal ]
                             (Tunai / Transfer Bank / QRIS)
                                               │
                                               ▼
                              [ Tekan "Simpan & Terbitkan" ]
                                               │
               ┌───────────────────────────────┴───────────────────────────────┐
               ▼                                                               ▼
   [ Download / Cetak PDF Kwitansi ]                        [ 1-Klik Kirim Bukti via WhatsApp ]
(Format Resmi Landscape A5 Cyan GSC)                     (Format Pesan Ramah & Link QR Code Asli)
```

1. Buka menu **Donasi** $\rightarrow$ **Input Donasi Baru**.
2. Ketik nomor WhatsApp donatur pada kolom pencarian:
   - Jika donatur sudah pernah berdonasi, data nama dan alamat akan terisi otomatis (*auto-complete*).
   - Jika donatur baru, cukup lengkapi nama dan kontak donatur.
   - Jika donatur tidak ingin namanya dipublikasikan, aktifkan centang **"Hamba Allah (Anonim)"**.
3. Pilih **Jenis Dana** (Zakat Maal, Infak Terikat Program, Sedekah Subuh, dll.).
4. Masukkan **Nominal Uang**. Jika donatur menyertakan barang natura (contoh: beras, sembako, ambulans), isi rincian barang pada kolom Donasi Natura.
5. Pilih **Metode Pembayaran** (Tunai, Transfer Bank BSI/Mandiri, atau QRIS).
6. Tekan tombol **"Simpan & Terbitkan Kwitansi"**.
7. Sistem langsung menampilkan halaman sukses berlatar cyan resmi:
   - Tekan tombol **"Kirim Bukti Kwitansi via WhatsApp"** untuk langsung membuka obrolan WA ke donatur beserta draf ucapan terima kasih, doa keberkahan, nomor kwitansi, dan tautan verifikasi.
   - Tekan **"Download Kwitansi (PDF)"** jika donatur meminta lembar fisik cetak.

---

### SOP 3: Verifikasi Keuangan oleh Bendahara

1. Setiap donasi yang diinput amil berstatus awal **"Menunggu Verifikasi"**.
2. Petugas Bendahara membuka panel `/admin` menu **Donasi**.
3. Bendahara mencocokkan mutasi rekening koran bank atau fisik uang tunai.
4. Klik tombol aksi **Verifikasi**:
   - Jika dana telah masuk valid, ubah status menjadi **"Terverifikasi"**.
   - Dokumen kwitansi otomatis mendapatkan cap stempel resmi GSC dan tanda tangan digital Bendahara.
   - Status di tautan QR Code publik donatur otomatis berubah menjadi badge hijau/cyan **"KWITANSI VALID & ASLI - TERVERIFIKASI BENDAHARA"**.
   - Jika transaksi fiktif/batal, ubah status menjadi **"Ditolak"** dengan alasan yang jelas.

---

### SOP 4: Pendataan Mustahik & Penyaluran Donasi

1. Amil / Bagian Program melakukan *assessment* calon penerima manfaat.
2. Daftarkan data penerima di menu **Mustahik** dengan menetapkan kategori asnaf syar'i (Fakir, Miskin, Fisabilillah, Ibnu Sabil, Gharimin, Riqab, Mualaf, Amil).
3. Saat bantuan diberikan:
   - Buka menu **Penyaluran**.
   - Masukkan mustahik tujuan, program terkait, jenis dana sumber, serta nominal/barang yang diserahkan.
   - Unggah foto dokumentasi serah terima bantuan sebagai bukti pertanggungjawaban publik.

---

### SOP 5: Pengajuan Izin & Cuti Amil

1. Amil berhalangan hadir wajib mengajukan permohonan melalui menu **Izin & Cuti**.
2. Pilih jenis permohonan:
   - **Cuti Tahunan** (otomatis mengurangi kuota saldo cuti 12 hari/tahun).
   - **Izin Sakit** (wajib melampirkan foto surat dokter/keterangan medis).
   - **Izin Keperluan Mendesak**.
3. Admin/HRD menerima notifikasi di dashboard admin untuk menyetujui (*Approve*) atau menolak (*Reject*).
4. Status kehadiran pada rekapitulasi absensi harian otomatis terkonversi menjadi `Izin` atau `Cuti` (bukan Alpa).

---

## 5. PANDUAN RINCI SETIAP FITUR SISTEM

### A. Tampilan Kuitansi Resmi PDF (Revisi Standar GSC)
Format kuitansi dirancang elegan berstandar landscape A5 dengan palet warna **Cyan Resmi GSC (`#0891b2`)**:
* **Kop Instansi:** Logo resmi GSC, alamat kantor pusat Cilacap, No Telp/WA: `+62 857-0122-3333`, dan Email: `geraksedekahcilacap@gmail.com`.
* **Isian Data Rapi Terpisah (7 Baris Eksklusif):**
  1. *Telah Diterima Dari:* Nama donatur (dan nama peruntukan a.n jika ada).
  2. *Alamat Donatur:* Alamat domisili donatur.
  3. *No. Kontak / WhatsApp:* Nomor ponsel donatur.
  4. *Jenis Dana:* Kategori akad zakat / infak / sedekah.
  5. *Program Penyaluran:* Aksen cyan nama program donasi pilihan donatur.
  6. *Metode Pembayaran:* Tunai / Transfer Bank / QRIS.
  7. *Waktu Transaksi:* Tanggal dan jam akurat WIB.
* **Kotak Nominal Berseri:** Nominal angka tebal, kalimat terbilang rupiah, dan garis putus-putus aksen cyan.
* **Fitur Validasi QR Code:** Setiap lembar kuitansi memiliki QR Code unik yang jika dipindai kamera HP akan langsung membuka laman verifikasi server resmi untuk membuktikan keaslian dokumen.
* **Legalisasi Sah:** Kolom tanda tangan penyerah (donatur) dan cap basah tanda tangan Bendahara GSC.

---

## 6. SKENARIO SIMULASI LIVE DEMO PRESENTASI

Gunakan urutan skenario ini saat mendemokan aplikasi di hadapan staf dan pimpinan besok:

| Menit Ke- | Topik Demo | Yang Harus Dilakukan / Ditampilkan di Layar |
| :---: | :--- | :--- |
| **00 - 05** | **Pembukaan & Urgensi** | Jelaskan latar belakang transformasi dari pembukuan manual ke sistem digital GSC yang akuntabel & profesional. |
| **05 - 12** | **Demo Presensi Amil (HP)** | 1. Buka portal amil di smartphone.<br>2. Tunjukkan deteksi GPS akurat radius kantor.<br>3. Ambil swafoto dan lakukan *Clock-in*.<br>4. Perlihatkan status kehadiran langsung tercatat di dashboard. |
| **12 - 20** | **Demo Input Donasi & Kwitansi** | 1. Masukkan donasi baru senilai Rp 500.000 atas nama salah satu staf/peserta.<br>2. Klik Simpan & Terbitkan Kwitansi.<br>3. Tunjukkan kuitansi PDF beraksen Cyan yang rapi dan terpisah barisnya.<br>4. Klik tombol WhatsApp: perlihatkan draf pesan otomatis yang siap terkirim ke donatur. |
| **20 - 25** | **Demo Scan QR Code** | Minta salah satu pimpinan memindai QR Code kuitansi dari layar HP mereka untuk membuktikan status keaslian kwitansi langsung dari web server. |
| **25 - 32** | **Demo Panel Admin (PC)** | 1. Tunjukkan Dashboard Eksekutif penerimaan dana.<br>2. Tunjukkan menu Verifikasi Keuangan oleh Bendahara.<br>3. Tunjukkan laporan absensi dan monitoring amil. |
| **32 - 40** | **Tanya Jawab & Penutup** | Bahas komitmen penerapan mulai Senin dan sosialisasi kepada seluruh jajaran amil. |

---

## 7. TANYA JAWAB POPULER (FAQ) & PANDUAN KENDALA LAPANGAN

**Q1: Bagaimana jika amil bertugas mengantar pasien ambulans ke luar kota dan tidak bisa absen di kantor?**  
> **Jawab:** Amil cukup mengklik tombol **"Tugas Luar"** pada halaman presensi, kemudian menuliskan rincian tugas (contoh: *Mengantar pasien dhuafa rujukan ke RSUD Margono*), lalu mengambil foto selfie di lokasi tugas. Sistem akan memvalidasi presensi tanpa batasan radius kantor.

**Q2: Mengapa tombol Presensi Pulang berwarna abu-abu / tidak bisa diklik di siang hari?**  
> **Jawab:** Sistem menerapkan aturan kedisiplinan kantor di mana tombol presensi pulang **hanya dapat ditekan mulai pukul 15:30 WIB**. Hal ini mencegah amil yang bertugas di kantor melakukan clock-out sebelum jam kerja semestinya.

**Q3: Bagaimana jika ada donatur yang ingin berdonasi tanpa mencantumkan namanya di kwitansi?**  
> **Jawab:** Saat menginput donasi, centang kotak **"Hamba Allah (Anonim)"**. Pada kuitansi PDF maupun laman web verifikasi publik, nama donatur akan otomatis tersamar menjadi **"HAMBA ALLAH"**.

**Q4: Apakah sistem dapat berjalan jika jaringan internet di lokasi donatur sedang lambat?**  
> **Jawab:** Tampilan portal amil telah dioptimasi dengan kompresi data ringan. Selama ponsel memiliki sinyal minimal untuk bertukar teks, data donasi dan presensi tetap dapat terkirim lancar.

---
*(Dokumen ini disahkan sebagai panduan operasional resmi Yayasan Gerak Sedekah Cilacap)*
