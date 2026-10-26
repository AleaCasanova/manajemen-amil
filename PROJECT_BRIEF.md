# PROJECT BRIEF: SISTEM MANAJEMEN AMIL (GSC)

Dokumen ini adalah referensi utama dan memori proyek (system memory) untuk pengembangan aplikasi **Sistem Manajemen Amil**. Seluruh agen AI dan pengembang wajib membaca dokumen ini sebelum melakukan perubahan atau penambahan fitur.

---

## 1. Ringkasan Proyek
- **Nama Aplikasi**: Sistem Manajemen Amil (GSC - Gerakan Sedekah Cepat)
- **Tujuan**: Mempermudah pengelolaan amil zakat/infak, pencatatan donatur & donasi, absensi berbasis lokasi, perizinan cuti, agenda kegiatan, mustahik, penyaluran dana, dan pelaporan keuangan ZISWAF secara transparan dan akuntabel.
- **Database**: MySQL (`amilgsc`)
- **Teknologi Stack**:
  - **Backend**: Laravel (PHP 8.4)
  - **Role & Permission**: `spatie/laravel-permission`
  - **Audit Log**: `spatie/laravel-activitylog`
  - **Panel Admin**: Filament Admin Panel
  - **Panel User (Amil)**: Blade / Livewire & Tailwind CSS
  - **Database Engine**: MySQL / MariaDB (Laragon)

---

## 2. Aktor & Role Pengguna

### A. Administrator (Admin)
Bertanggung jawab memantau dan mengelola seluruh sistem:
- Memantau Dashboard metrik (donasi harian/bulanan/tahunan, top amil, grafik tren, amil belum absen).
- Manajemen data Amil (CRUD, pembuatan akun login otomatis, nonaktifkan akun, reset password, penetapan target donasi).
- Review dan verifikasi laporan Aktivitas Harian amil (kunjungan donatur, jemput donasi, sosialisasi).
- Manajemen Agenda kerja & penugasan amil.
- Publikasi Pengumuman (target: semua / divisi tertentu / amil tertentu).
- Persetujuan/Penolakan Pengajuan Izin & Cuti amil + pantau Saldo Cuti.
- Monitoring & Koreksi Absensi Amil (radius geofencing, jam masuk/pulang, keterlambatan, ekspor).
- Verifikasi Donasi masuk, penerbitan kwitansi, rekap & laporan donasi (ekspor Excel/PDF).
- CRM Donatur (riwayat donasi, donatur rutin, donatur lapse / tidak aktif).
- Program / Campaign donasi (target dana, periode, progres).
- Pendataan Mustahik (8 Asnaf) & Pencatatan Penyaluran dana.
- Laporan Keuangan & Audit Log (siapa mengubah apa dan kapan).
- Pengaturan Sistem (data lembaga, jam kerja, radius lokasi kantor, rekening bank, template kwitansi).

### B. Amil (User)
Pengguna lapangan/operasional yang bertugas sehari-hari:
- Melakukan Presensi/Absensi (Clock-in & Clock-out berbasis Geolocation & foto selfie).
- Menginput data Donatur baru & riwayat Donasi donatur binaan / PIC.
- Mengirimkan Laporan Aktivitas Harian (foto kegiatan, deskripsi, lokasi).
- Mengajukan Izin / Cuti / Sakit dengan lampiran dan memantau sisa saldo cuti.
- Melihat Agenda penugasan kerja dan kalender kegiatan.
- Membaca Pengumuman lembaga.
- Memantau pencapaian target bulanan pribadi.

---

## 3. Struktur Database (22 Tabel Utama)

1. `users` : id, name, email, password, role_id, is_active, remember_token, timestamps
2. `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions` (Spatie)
3. `divisis` : id, nama, deskripsi, timestamps
4. `amils` : id, user_id (FK users), nip, nama, jabatan, divisi_id (FK divisis), no_hp, alamat, foto, tgl_bergabung, target_bulanan, status, timestamps
5. `donatur_kategoris` : id, nama, deskripsi, timestamps
6. `donaturs` : id, kode_donatur, nama, jenis (individu/lembaga), kategori_id (FK donatur_kategoris), no_hp, email, alamat, kelurahan, kecamatan, kota, provinsi, nik, npwp, tgl_lahir, is_anonim, consent, amil_pic_id (FK amils), created_by (FK users), timestamps
7. `jenis_danas` : id, nama, kelompok (zakat/infak/dskl/sedekah/wakaf/lainnya), hak_amil_persen, deskripsi, timestamps
8. `programs` : id, nama, deskripsi, target_dana, tgl_mulai, tgl_selesai, status (aktif/selesai/ditutup/akan_datang), timestamps
9. `donasis` : id, no_kwitansi, donatur_id (FK donaturs), amil_id (FK amils), jenis_dana_id (FK jenis_danas), program_id (FK programs), jumlah, metode_bayar, bank, bukti_transfer, tgl_donasi, niat, pesan, catatan, status (menunggu_verifikasi/terverifikasi/ditolak), verified_by (FK users), verified_at, timestamps
10. `donasi_barangs` : id, donasi_id (FK donasis), deskripsi, estimasi_nilai, timestamps
11. `absensis` : id, amil_id (FK amils), tanggal, jam_masuk, jam_pulang, lat_masuk, lng_masuk, lat_pulang, lng_pulang, foto_masuk, foto_pulang, status (hadir/terlambat/izin/cuti/alpa), keterangan, timestamps
12. `jam_kerjas` : id, nama, jam_masuk, jam_pulang, toleransi_menit, is_active, timestamps
13. `lokasi_kantors` : id, nama_lokasi, alamat, lat, lng, radius_meter, is_active, timestamps
14. `izin_cutis` : id, amil_id (FK amils), jenis (cuti/izin/sakit), tgl_mulai, tgl_selesai, alasan, lampiran, status (diajukan/disetujui/ditolak), approved_by (FK users), catatan_admin, timestamps
15. `saldo_cutis` : id, amil_id (FK amils), tahun, jatah, terpakai, timestamps
16. `aktivitas_harians` : id, amil_id (FK amils), tanggal, judul, deskripsi, lokasi, foto, status (draft/diajukan/disetujui/ditolak), timestamps
17. `agendas` : id, judul, deskripsi, lokasi, mulai, selesai, status, created_by (FK users), timestamps
18. `agenda_amil` : id, agenda_id (FK agendas), amil_id (FK amils), timestamps
19. `pengumumans` : id, judul, isi, lampiran, target, published_at, created_by (FK users), timestamps
20. `pengumuman_dibaca` : id, pengumuman_id (FK pengumumans), user_id (FK users), read_at, timestamps
21. `mustahiks` : id, nama, nik, alamat, asnaf (8 asnaf), kondisi, status_verifikasi, timestamps
22. `penyalurans` : id, mustahik_id (FK mustahiks), program_id (FK programs), jenis_dana_id (FK jenis_danas), jumlah, tgl_salur, bukti, amil_id (FK amils), catatan, timestamps
23. `notifikasis` : id, user_id, judul, pesan, tipe, url, data, is_read, read_at, timestamps
24. `activity_log` : tabel spatie/laravel-activitylog
25. `settings` : id, key, value, tipe, grup, deskripsi, timestamps

---

## 4. Rincian Modul Admin (Prioritas Tahap Saat Ini)

1. **Dashboard Eksekutif**:
   - Widget total donasi hari ini, bulan ini, dan tahun ini.
   - Grafik tren penerimaan donasi bulanan.
   - Daftar Amil terbaik / pencapaian target donasi (Top Amil).
   - Donatur baru bergabung & status absensi amil hari ini.
2. **Manajemen Amil**:
   - CRUD Amil: NIP, nama, jabatan, divisi, no HP, alamat, foto, tgl bergabung, target bulanan, status.
   - Fitur otomatis buat akun login `User` saat amil didaftarkan.
   - Aksi cepat: Reset password, aktifkan / nonaktifkan akun amil.
3. **Laporan Aktivitas Harian**:
   - Monitoring aktivitas amil (kunjungan, jemput donasi, sosialisasi).
   - Filter per amil, tanggal, dan status.
   - Dialog preview foto aktivitas & input komentar / penilaian admin.
4. **Manajemen Agenda**:
   - Jadwal agenda kegiatan, rapat, sosialisasi, penyaluran.
   - Penugasan satu atau banyak amil (`agenda_amil`).
   - Status agenda: terjadwal, berlangsung, selesai, dibatalkan.
5. **Pengumuman**:
   - Buat pengumuman dengan editor teks dan lampiran file.
   - Target pengumuman: semua amil / per divisi.
   - Indikator keterbacaan (siapa saja amil yang sudah membaca).
6. **Izin & Cuti**:
   - Pengajuan cuti, izin, sakit dari amil.
   - Aksi Setujui / Tolak dengan catatan admin.
   - Pemotongan otomatis ke `saldo_cutis` saat disetujui.
7. **Rekap Absensi**:
   - Rekap absensi harian dan bulanan per amil.
   - Jam masuk, jam pulang, status terlambat, jarak lokasi absen terhadap kantor.
   - Fitur koreksi absen manual oleh admin dan ekspor data.
8. **Rekap & Verifikasi Donasi**:
   - Daftar seluruh donasi (per amil, kategori, jenis dana, periode).
   - Verifikasi donasi transfer / QRIS (ubah status, verifier tercatat).
   - Fitur cetak kwitansi resmi donasi.
9. **Manajemen Donatur (CRM)**:
   - Data donatur individu dan korporat, PIC amil.
   - Riwayat donasi donatur, klasifikasi donatur rutin vs lapse.
10. **Mustahik & Penyaluran**:
    - Data mustahik berdasarkan 8 Asnaf Zakat.
    - Verifikasi kelayakan mustahik.
    - Pencatatan penyaluran bantuan beserta bukti serah terima dan amil penyalur.
11. **Program / Campaign**:
    - Monitoring target dana vs realisasi donasi program.
12. **Audit Log & Pengaturan**:
    - Riwayat perubahan data oleh user / admin via Spatie Activitylog.
    - Pengaturan jam kerja, radius meter kantor, data kontak & profil yayasan.

---

## 5. Status Pengerjaan (Tracking Progress)

### Langkah 0: Fondasi
- [x] Environment PHP 8.4, Composer, MySQL Laragon siap
- [x] Folder proyek dibuat di Antigravity
- [x] `PROJECT_BRIEF.md` dibuat di root
- [x] Stack disepakati: Laravel, Filament Admin, Spatie Permission & Activitylog

### Fase 1: Fondasi & Database
- [x] Konfigurasi `.env` ke database `amilgsc`
- [x] Instalasi `spatie/laravel-permission` dan `spatie/laravel-activitylog`
- [x] Pembuatan 22 file migration dan eksekusi sukses di `amilgsc`
- [x] Pembuatan 21 Eloquent Models dengan relasi, casts, dan fillable
- [x] Database seeder (`Admin` & `Amil` roles, permissions, master data awal, akun admin `admin@amilgsc.com`)
- [x] Instalasi Panel Admin Filament
- [x] Fitur Autentikasi Admin & Akses Panel (/admin/login)
- [x] Manajemen Amil (CRUD + Akun Login Otomatis + Reset Password + Target Donasi + Nonaktifkan Akun)
- [x] Modul & Menu Admin Lengkap di Filament:
  - 📊 Dashboard (Overview Stats, Tren Donasi Bulanan)
  - 👥 Manajemen Amil & SDM (Amil, Absensi, Cuti, Aktivitas Harian)
  - 📅 Agenda & Komunikasi (Agenda Penugasan, Pengumuman)
  - 💰 Fundraising & Donasi (Rekap Donasi, CRM Donatur, Program Campaign)
  - 🤝 Penyaluran & Mustahik (Mustahik 8 Asnaf, Distribusi Bantuan)
  - ⚙️ Master Data & Pengaturan (Divisi, Jenis Dana, Jam Kerja, Geofence Kantor, Settings)

### Fase 2: Inti Bisnis (Donatur, Donasi, Kwitansi & Verifikasi)
- [x] Tabel Master Donasi: `donatur_kategoris`, `jenis_danas`, `programs`
- [x] Manajemen Donatur CRM (Pencegahan duplikasi No. HP/WA, NIK/NPWP, consent, anonim)
- [x] Service Layer: `DonasiService` (Generate No. Kwitansi otomatis `KW/YYYY/MM/XXXX`, Terbilang Rupiah, Deteksi Duplikat HP)
- [x] Service Layer: `KwitansiService` (PDF A5 landscape, QR Code verifikasi)
- [x] Verifikasi Donasi oleh Admin (Aksi Setujui/Verifikasi & Tolak di Filament)
- [x] Ekspor Rekap Donasi ke Excel (`maatwebsite/excel` & `DonasiExport`)
- [x] Template Resmi Kwitansi PDF (`resources/views/pdf/kwitansi.blade.php`) & Halaman Publik Validasi QR Code (`/verifikasi-kwitansi/{no_kwitansi}`)
- [x] Portal Amil Berbasis Blade + Tailwind:
  - [x] Form input Donatur & Donasi cepat untuk Amil (Tab Mode Baru/Lama, Live Duplicate Check, Live Terbilang, Bukti Transfer)
  - [x] Halaman Sukses Donasi & Tombol Kirim Kwitansi Otomatis ke WhatsApp Donatur
  - [x] Daftar "Donatur Saya" & Tombol "Donasi Lagi" serta Profil Donatur
  - [x] Riwayat Donasi Saya dengan filter status dan download PDF kwitansi
  - [x] Dashboard pribadi Amil (status absen, capaian target, pengumuman, agenda)

### Fase 3: Operasional Amil (Selesai)
- [x] Absensi Online (Geofencing GPS radius kantor + Kamera Selfie) untuk Clock In & Clock Out
- [x] Rekap Absensi Admin (Filament) dengan perhitungan keterlambatan dan catatan waktu
- [x] Modul Izin & Cuti Amil (Pengajuan izin/cuti/sakit + pengecekan kuota cuti + approval admin memotong saldo cuti otomatis)
- [x] Laporan Aktivitas Harian Amil (Jemput donasi, silaturahmi, sosialisasi + upload foto dokumentasi)
- [x] Review & Approval Aktivitas Harian oleh Admin di Filament

### Fase 4: Komunikasi, Agenda, & Pengumuman (Selesai)
- [x] Kalender & Detail Agenda Penugasan Amil di Portal Amil (`/amil/agenda`) dengan koordinasi kontak tim
- [x] Feed Pengumuman Lembaga & Penanda Keterbacaan (`/amil/pengumuman` & pivot `pengumuman_dibaca`)
- [x] Pelacakan Jumlah Pembaca Pengumuman oleh Admin di Filament
- [x] Notifikasi & Rangkuman Pengumuman / Agenda pada Dashboard Amil

### Fase 5: Penyaluran & Mustahik (8 Asnaf) (Selesai)
- [x] Manajemen Data Mustahik (Klasifikasi 8 Asnaf: Fakir, Miskin, Amil, Mualaf, Riqab, Gharim, Fisabilillah, Ibnu Sabil, NIK, kelayakan)
- [x] Pencatatan Distribusi & Penyaluran Bantuan ZISWAF beserta bukti serah terima (Admin & Portal Amil)
- [x] Form Input Penyaluran Lapangan untuk Amil (`/amil/penyaluran/create`) dengan live terbilang nominal dan dokumentasi foto
- [x] Riwayat Penyaluran Amil & Filter Pencarian Mustahik (`/amil/penyaluran`)
- [x] Verifikasi Kelayakan Mustahik oleh Admin di Filament

### 9. Keamanan dan Aturan Bisnis (Selesai & Terverifikasi)
- [x] **Hak Akses Terisolasi**: Amil hanya dapat mengakses data miliknya (donasi, donatur binaan, absensi, aktivitas, izin cuti); Admin memiliki akses penuh ke seluruh data.
- [x] **Penguncian Donasi Terverifikasi**: Donasi berstatus `terverifikasi` terkunci dari pengubahan dan penghapusan langsung; koreksi hanya bisa dilakukan via fitur Berita Acara / Jurnal Koreksi resmi.
- [x] **Integritas Presensi**: 1 kali absen masuk per hari; validasi radius GPS geofencing kantor; jam tercatat 100% menggunakan waktu server resmi (bukan jam HP/gawai).
- [x] **Kepatuhan UU PDP (Pelindungan Data Pribadi)**: Kolom sensitif NIK & NPWP dienkripsi menggunakan Laravel `encrypted` casts; masking NIK (`3204********1234`); consent dan anonimitas donatur.
- [x] **Keamanan Sistem**: Rate limiting login amil (`throttle:5,1`), password policy (min 8 karakter kombinasi huruf & angka), serta perintah backup database otomatis (`app:backup-database`) yang dijadwalkan harian.
