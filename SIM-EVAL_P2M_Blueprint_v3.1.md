# SIM-EVAL P2M — Blueprint Sistem v3.1 (Edisi Frontend & UI/UX)

## Sistem Informasi Monitoring & Evaluasi Pre-Test / Post-Test Sosialisasi Terpadu
### Seksi Pencegahan dan Pemberdayaan Masyarakat (P2M) Badan Narkotika Nasional Kota Surabaya

| Atribut | Isi |
|---|---|
| Status dokumen | **Final untuk Fase Pengembangan (Slicing & Integrasi)** |
| Versi | 3.1 (Penyelarasan dengan SRS v2.1 — Arsitektur, Keamanan, Alur Waiting Room) |
| Arsip pendahulu | `SIM-EVAL_P2M_Blueprint_v3.0.md` |

### Riwayat Versi
- **1.0 - 2.0**: Desain arsitektur *dual-engine* awal (Kertas & PWA), logika nilai selisih dan N-Gain.
- **3.0**: Penambahan arsitektur modular UI/UX, Alur *Waiting Room*, Timer Otomatis, Master Lokasi, Instruksi Tanda Silang (X), Pengacakan Soal, dan sinkronisasi Pre/Post-test.
- **3.1 (Dokumen ini)**: Penyelarasan dengan SRS v2.1 — penambahan lapisan API Helper pada arsitektur, klarifikasi proteksi password pada modul Lokasi, dan pemisahan eksplisit dua *Waiting Room* (Pre-Test & Post-Test).

---

## 1. Arsitektur Frontend & Teknologi

Sistem dibangun menggunakan pendekatan **Monolitik Modular** berbasis kerangka kerja *backend* dan *vanilla javascript*, dengan tiga lapisan yang terpisah secara jelas (*multi-layer architecture*):

1. **Lapisan Tampilan (Blade View)**: Kerangka Laravel dengan *Blade Components* untuk memecah UI layaknya komponen modern (Atomic Design).
2. **Lapisan Logika Interaktif (Vanilla JS / ES6 Modules)**: Diproses melalui Vite. Logika dipisah per domain (misal: `ScannerModule.js`, `QuizEngine.js`, `TimerManager.js`).
3. **Lapisan Operasi Asinkron (API Helper)**: Modul JS terpisah yang membungkus seluruh komunikasi ke peladen (fetch/AJAX) — menangani pemanggilan endpoint, penanganan error jaringan, serta jembatan sinkronisasi data luring (offline sync) ke IndexedDB. Lapisan ini memastikan modul UI (Blade view) dan modul logika interaktif tidak memanggil API secara langsung, melainkan melalui helper terpusat ini agar mudah diuji dan dipelihara.

**Kapabilitas Luring (PWA)**: *Service Worker* dan IndexedDB untuk menyimpan *cache* soal dan jawaban saat koneksi terputus (Offline-First) sebelum disinkronkan ke peladen melalui Lapisan API Helper.

**Pustaka Eksternal Klien**:
- `Select2` / `Choices.js`: Untuk fitur pencarian *dropdown* dinamis (Master Lokasi).
- `Html5-Qrcode` & `OpenCV.js`: Untuk pemindaian OMR tanda silang (X) pada mode *hybrid/offline*.
- `SweetAlert2`: Untuk notifikasi peringatan waktu dan konfirmasi keamanan.

---

## 2. Struktur Antarmuka & Alur Pengguna (UI/UX Flow)

### 2.1. Panel Super Admin
Fokus pada manajemen hak akses tingkat tinggi.
- **Profil**: Pengaturan akun Super Admin.
- **List Profil**: Manajemen akun staf/operator penyuluh.
- **Logout**: Keluar dari sesi.

### 2.2. Panel Staf / Operator (Sidebar)
Fokus pada operasional lapangan dan manajemen konten. Setiap akun staf/operator memiliki password login individual (satu akun, satu password — tidak dibagikan bersama).

- **Daftar Kegiatan**:
  - Menampilkan maksimal 10 kegiatan terbaru dengan pagination.
  - **Tombol Tambah Sosialisasi**: *Pop-up form* terintegrasi dengan *Dropdown* Master Lokasi.
  - Aksi per baris: Edit, Detail, Hapus (membutuhkan **re-autentikasi password akun staf/operator yang sedang login**, demi keamanan dan pencatatan pelaku aksi).
  - Di halaman Detail: Fungsi pemindaian soal *hybrid*, unduh laporan Excel dinamis, pantauan hasil anak secara *real-time* (online) / pasca-scan (offline), dan *generate* QR / Kode Join.
- **Lokasi**:
  - Manajemen tabel Master Lokasi (tampil 10 baris dengan fitur *Filter*).
  - Penambahan lokasi baru bisa dilakukan dari menu ini, atau langsung dari *pop-up* pembuatan kegiatan jika entri belum tersedia.
  - Aksi: Edit, Hapus (**membutuhkan re-autentikasi password akun staf/operator yang sedang login**, konsisten dengan proteksi hapus pada modul Kegiatan dan Bank Soal).
- **Bank Soal**:
  - Daftar manajemen paket soal dan pelacakan penggunaan paket soal di berbagai kegiatan.
  - **Tombol Tambah Soal Baru**: Fitur *toggle* "Acak Urutan Soal" (berlaku khusus untuk mode digital/online — lihat §2.3 poin 3), serta fitur "Samakan dengan Pre-Test" untuk mengotomatisasi penyusunan Post-Test tanpa kerja ganda.
  - Aksi: Edit, Detail (Print versi kertas), Hapus (**membutuhkan re-autentikasi password akun staf/operator yang sedang login**).

> **Catatan Keamanan**: Tujuan re-autentikasi password pada aksi Hapus bukan untuk membedakan tingkat otorisasi (semua staf/operator memiliki hak akses yang sama), melainkan untuk mencegah penghapusan tidak sah ketika sesi login masih terbuka di perangkat bersama (misal: laptop Staf A ditinggal dan digunakan Staf B). Password yang diminta adalah milik akun yang sedang aktif, sehingga sistem otomatis mencatat identitas pelaku penghapusan.

### 2.3. Pengalaman Peserta (Engine B - Digital PWA)
Mengadopsi alur *Sandwich Workflow* interaktif (bergaya Kahoot/Quizizz).

1. **Penyambutan**: Peserta melakukan *scan QR* atau mengakses tautan, diarahkan ke antarmuka "Selamat Datang". Tanpa perlu registrasi akun, peserta memasukkan **Kode Join (6 karakter)**, diikuti input Nama dan Sekolah.
2. **Waiting Room #1 (Pre-Test)**: Peserta masuk ke ruang tunggu pertama. Sesi ditahan (dikunci) hingga staf memulai sesi Pre-Test secara serentak dari proyektor/laptop.
3. **Pengerjaan Soal (Pre-Test)**: Tampilan 1 layar = 1 soal dengan navigasi di bawah. Urutan soal diacak **per peserta** berdasarkan konfigurasi bank soal (opsi A/B/C/D tetap statis, tidak diacak) — sehingga peserta yang duduk bersebelahan mendapat isi soal yang sama namun urutan tampil berbeda, guna mencegah saling mencontek melalui layar.
4. **Timer & Auto-Submit**: Terdapat indikator waktu mundur. Pada sisa 15 detik, layar memunculkan peringatan (berkedip/notifikasi). Saat waktu habis, sistem memicu paksa pengiriman jawaban tanpa intervensi peserta.
5. **Waiting Room #2 (Post-Test)**: Setelah pengerjaan Pre-Test selesai/waktu habis, peserta otomatis masuk ke ruang tunggu kedua (terpisah dari Waiting Room #1). Data nama dan identitas terkunci permanen (tersinkronisasi dari sesi Pre-Test). Sesi kembali ditahan hingga materi sosialisasi selesai dan staf secara terpisah membuka sesi Post-Test.
6. **Pengerjaan Soal (Post-Test)**: Mengikuti aturan pengacakan yang sama seperti poin 3 (acak per peserta untuk mode online).

---

## 3. Kebijakan Operasional Mode Kertas (Engine A)

Untuk siswa SD, Lapas, atau kondisi *blankspot*, sistem menggunakan kertas.

- **Pembaruan Instruksi**: Kertas *template* cetak menggunakan metode **Silang (X)** pada opsi A/B/C/D untuk menjamin akurasi deteksi *Optical Mark Recognition* (OMR) yang lebih tinggi dibandingkan metode centang.
- **Urutan Soal**: Berbeda dengan mode digital, urutan soal pada lembar kertas **tetap baku/urut** (tidak diacak per peserta), karena satu lembar cetak digunakan secara massal untuk seluruh peserta dalam satu kegiatan.
- Alur pengisian identitas tetap mempertahankan kelaziman: Nama dan sekolah ditulis tangan pada sesi Pre-test, dikumpulkan, lalu dibagikan kembali lembar baru untuk Post-test.
