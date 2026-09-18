# ANALISIS MENYELURUH CODEBASE FRONT-END DAN USERFLOW
## SISTEM INFORMASI MONITORING & EVALUASI PRE-TEST / POST-TEST (SIM-EVAL)
### Seksi Pencegahan dan Pemberdayaan Masyarakat (P2M) — BNN Kota Surabaya

---

| Atribut Dokumen | Keterangan |
|---|---|
| **Aplikasi** | SIM-EVAL P2M BNN Kota Surabaya |
| **Fokus Dokumen** | Analisis Arsitektur Front-End, Komponen UI/UX, Userflow (Alur Pengguna), dan Riwayat Perubahan Codebase |
| **Versi Codebase Terakhir** | Commit `81f564a` (*fix: perbaikan menyeluruh — status sesi, encoding, identitas Wahyu, badge mode dihapus, reauth modal light mode, DELETE JSON*) |
| **Status Dokumen** | Dokumen Tunggal Komprehensif & Rinci (*Living Architecture & Audit Document*) |
| **Penyusun** | Antigravity AI Engineering & Architecture Team |
| **Tanggal Audit** | September 2026 |

---

## DAFTAR ISI

1. [Ringkasan Eksekutif & Domain Masalah](#1-ringkasan-eksekutif--domain-masalah)
2. [Arsitektur Front-End & Multi-Layer System](#2-arsitektur-front-end--multi-layer-system)
   - 2.1 Tiga Lapisan Arsitektur (*Multi-Layer Architecture* NFR-07)
   - 2.2 Design System & Desain Visual Token (`resources/css/app.css`)
   - 2.3 Tipografi, Tema Gelap/Terang, dan Mekanisme *Zero-Flash*
3. [Analisis Mendalam Komponen & Halaman View (Blade Ecosystem)](#3-analisis-mendalam-komponen--halaman-view-blade-ecosystem)
   - 3.1 Komponen Global & Layout Utama
   - 3.2 Modul Operator: Dashboard & Kalender Interaktif
   - 3.3 Modul Operator: Daftar Kegiatan, Create, Edit, & Detail (Monitoring & OMR Scanner)
   - 3.4 Modul Operator: Bank Soal (Indeks, Dynamic Question Builder, Detail Viewer, Cetak A4)
   - 3.5 Modul Operator: Master Data Lokasi
   - 3.6 Modul Operator: Profil Operator & Manajemen Keamanan
   - 3.7 Modul Klien Peserta (*Engine B - Digital PWA*)
4. [Analisis Modul JavaScript & Manajemen State Klien](#4-analisis-modul-javascript--manajemen-state-klien)
   - 4.1 Entry Point: `resources/js/app.js`
   - 4.2 Gateway Jaringan: `resources/js/api/ApiHelper.js`
   - 4.3 Engine Evaluasi Digital: `resources/js/modules/QuizEngine.js`
   - 4.4 Pengatur Waktu & Peringatan: `resources/js/modules/TimerManager.js`
   - 4.5 Penyimpanan Luring: `resources/js/modules/OfflineStore.js`
   - 4.6 Pengamanan Operasi Kritis: `resources/js/modules/ReauthModal.js`
5. [Pemetaan Alur Pengguna (*User Flow Mapping*)](#5-pemetaan-alur-pengguna-user-flow-mapping)
   - 5.1 Alur 1: Siklus Hidup Penyelenggaraan Kegiatan Evaluasi (Operator)
   - 5.2 Alur 2: Manajemen Instrumen Soal & Pencetakan Lembar Ujian Fisik A4
   - 5.3 Alur 3: Pengalaman Peserta Digital (*Sandwich Workflow* Interaktif)
   - 5.4 Alur 4: Protokol Keamanan Re-Autentikasi Sandi (FR-43)
   - 5.5 Alur 5: Navigasi Cepat Pencarian Global Topbar
   - 5.6 Alur 6: Penjadwalan & Filter Kalender Interaktif Dashboard
6. [Kronologi & Analisis Detail Riwayat Perubahan Codebase (*Git Log Audit*)](#6-kronologi--analisis-detail-riwayat-perubahan-codebase-git-log-audit)
   - 6.1 Garis Waktu Komit Lengkap (`0c9a15d` s/d `81f564a`)
   - 6.2 Perubahan Mayor Commit `c32a2bb` (*Refactoring Kualitas Kode & Server-Side Filtering*)
   - 6.3 Perubahan Mayor Commit `c206e3a` (*Pembersihan Dashboard & Pelepasan Chart.js*)
   - 6.4 Perubahan Mayor Commit `ebeb948` (*Kalender Mandiri & Filter 3 Mode Waktu*)
   - 6.5 Perubahan Mayor Commit `c1e5f45` & `54586ef` (*Migrasi ke Halaman Khusus Kegiatan & Normalisasi Status 3 Tahap*)
   - 6.6 Perubahan Mayor Commit `81f564a` (*Perbaikan Menyeluruh Terkini*)
7. [Matriks Kepatuhan Kebutuhan Sistem (SRS v2.1 & Blueprint v3.1)](#7-matriks-kepatuhan-kebutuhan-sistem-srs-v21--blueprint-v31)
8. [Pola Desain (*Design Patterns*) & Rekomendasi Masa Depan](#8-pola-desain-design-patterns--rekomendasi-masa-depan)

---

## 1. RINGKASAN EKSEKUTIF & DOMAIN MASALAH

SIM-EVAL (*Sistem Informasi Monitoring & Evaluasi*) adalah platform terpadu yang dirancang khusus untuk **Seksi Pencegahan dan Pemberdayaan Masyarakat (P2M) BNN Kota Surabaya**. Sistem ini menyelesaikan tantangan mendasar dalam mengukur tingkat efektivitas kegiatan sosialisasi bahaya narkoba (P4GN) kepada pelajar, warga masyarakat, pekerja, hingga warga binaan lembaga pemasyarakatan.

Tantangan utama yang dihadapi di lapangan mencakup:
1. **Kondisi Lapangan yang Beragam**: Sebagian target sosialisasi (seperti sekolah perkotaan) memiliki akses gawai pintar dan internet yang prima, sedangkan target lain (seperti Lapas, kawasan terisolir, atau sekolah tanpa izin ponsel) memerlukan media evaluasi fisik (kertas).
2. **Kebutuhan Pengukuran Ilmiah**: Efektivitas pemahaman audiens diukur menggunakan rumus **Gain Ternormalisasi (*N-Gain Score*)**, yang membandingkan skor *Pre-Test* (sebelum paparan materi) dengan skor *Post-Test* (setelah sosialisasi).
3. **Integritas Ujian Digital**: Mencegah peserta saling melirik jawaban di layar gawai saat duduk berdampingan.
4. **Keamanan Data Bersama**: Di lingkungan kedinasan di mana perangkat komputer posko sering digunakan bergantian oleh beberapa operator, aksi penghapusan data penting harus diawasi dengan ketat tanpa membebani sistem otorisasi yang rumit.

Untuk menjawab kebutuhan di atas, SIM-EVAL dibangun dengan pendekatan **Dual-Engine**:
- **Engine A (Hybrid / OMR Fisik)**: Lembar instrumen kertas standar A4 yang dapat dicetak langsung dari sistem. Lembar ini menggunakan instruksi **Tanda Silang (X)** pada opsi A/B/C/D agar memudahkan pemindaian kamera berkecepatan tinggi melalui teknologi *Optical Mark Recognition* (OMR).
- **Engine B (Digital PWA)**: Antarmuka berbasis web progresif ringan untuk peserta tanpa perlu registrasi akun/login. Cukup memasukkan **PIN / Kode Join 6 Karakter** atau memindai kode QR. Sistem menerapkan alur **Sandwich Workflow** (Ruang Tunggu Pre-Test $\rightarrow$ Kuis Pre-Test $\rightarrow$ Ruang Tunggu Materi/Jeda $\rightarrow$ Kuis Post-Test $\rightarrow$ Hasil Evaluasi).

---

## 2. ARSITEKTUR FRONT-END & MULTI-LAYER SYSTEM

### 2.1 Tiga Lapisan Arsitektur (*Multi-Layer Architecture* NFR-07)

Sesuai dengan ketentuan **NFR-07** pada SRS v2.1 dan Blueprint v3.1, front-end SIM-EVAL menerapkan pemisahan ketat 3-lapisan (*three-tier presentation architecture*):

```
┌────────────────────────────────────────────────────────────────────────┐
│               LAPISAN 1: TAMPILAN (BLADE VIEW ECOSYSTEM)               │
│  - resources/views/layouts/app.blade.php & participant.blade.php      │
│  - Komponen Modular: Sidebar, Topbar Search, Reauth Modal              │
│  - Halaman Khusus: Dashboard, Kegiatan, Bank Soal, Lokasi, Profil     │
│  - Utility Styling: Tailwind CSS v4 (@theme tokens)                   │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │ Mentransmisikan Event DOM / User Action
                                    ▼
┌────────────────────────────────────────────────────────────────────────┐
│            LAPISAN 2: LOGIKA INTERAKTIF (ES6 JS MODULES)               │
│  - QuizEngine.js      : Pengacakan soal per peserta, navigasi kuis    │
│  - TimerManager.js    : Hitung mundur, warning 15s, auto-submit       │
│  - ReauthModal.js     : Kontrol modal konfirmasi sandi destruktif      │
│  - Scoped Page Scripts: Kalender interaktif, filter navigasi, scanner │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │ Delegasi Pemanggilan Jaringan Tunggal
                                    ▼
┌────────────────────────────────────────────────────────────────────────┐
│           LAPISAN 3: OPERASI ASINKRON (API HELPER & OFFLINE)           │
│  - ApiHelper.js       : Gerbang sentral fetch HTTP, CSRF, error handle │
│  - OfflineStore.js    : Penampung IndexedDB saat jaringan terputus     │
│  - Background Sync    : Sinkronisasi tertunda saat online kembali      │
└────────────────────────────────────────────────────────────────────────┘
```

Prinsip utama dari arsitektur ini:
- **Pemisahan Peran**: File Blade view tidak pernah mengeksekusi logika jaringan mentah menggunakan `fetch()` ad-hoc atau XMLHttpRequest sembarangan; seluruh aksi diarahkan melalui method `ApiHelper.get()`, `ApiHelper.post()`, `ApiHelper.put()`, atau `ApiHelper.delete()`.
- **Enkapsulasi Modular**: Setiap modul ES6 mengelola siklus hidupnya sendiri (`init()`, event listeners internal, cleanup).

---

### 2.2 Design System & Desain Visual Token (`resources/css/app.css`)

Front-end SIM-EVAL mengusung gaya visual **Minimalist Modern Flat** yang bersih, berwibawa kedinasan (*authoritative yet modern*), dan ergonomis bagi mata operator maupun peserta ujian.

Seluruh token desain dikonfigurasi melalui CSS Custom Properties (`:root`) dan Tailwind CSS v4 `@theme`:

```css
:root {
    /* Landasan Permukaan */
    --bg:             #F5F6FA;
    --bg-alt:         #EDEEF5;
    --surface:        #FFFFFF;
    --surface-2:      #F9FAFB;
    --border:         #E4E6EF;
    --border-strong:  #CBD0DF;

    /* Teks dan Kontras */
    --text-primary:   #111827;
    --text-secondary: #4B5563;
    --text-muted:     #9CA3AF;
    --text-xmuted:    #D1D5DB;

    /* Warna Merek Resmi (BNN Indigo-Blue Flat) */
    --primary:        #4361EE;
    --primary-light:  #EEF1FF;
    --primary-mid:    #7B93FF;
    --primary-dark:   #2D46C8;

    /* Fungsional Indikator */
    --success:        #22C55E;  --success-light: #F0FDF4;
    --warning:        #F59E0B;  --warning-light: #FFFBEB;
    --danger:         #EF4444;  --danger-light:  #FFF5F5;
    --info:           #06B6D4;  --info-light:    #ECFEFF;
    --purple:         #8B5CF6;  --purple-light:  #F5F3FF;

    /* Sudut & Kelengkungan (Border Radius Tokens) */
    --r-xs: 4px;   --r-sm: 6px;   --r-md: 10px;
    --r-lg: 14px;  --r-xl: 20px;  --r-full: 9999px;

    /* Bayangan (Elevation Shadow Tokens) */
    --shadow-xs: 0 1px 2px rgba(0,0,0,0.05);
    --shadow-sm: 0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
    --shadow-md: 0 4px 6px rgba(0,0,0,0.06), 0 2px 4px rgba(0,0,0,0.04);
    --shadow-modal: 0 24px 64px rgba(17,24,39,0.15);
}
```

#### Kelas Komponen Utama (`Component Primitives`):
- `.glass`, `.glass-solid`: Kartu permukaan putih datar berbingkai halus dengan bayangan lembut (`box-shadow: var(--shadow-sm)`).
- `.btn`, `.btn-primary`, `.btn-secondary`, `.btn-danger`, `.btn-success`, `.btn-ghost`: Tombol interaktif dengan transisi elevasi mikro saat di-hover dan efek pegas (`transform: scale(0.97)`) saat ditekan.
- `.badge`, `.badge-blue`, `.badge-green`, `.badge-cyan`, `.badge-gray`: Kapsul status berpendar lembut dengan teks tebal (*uppercase 11px*).
- `.form-input`: Elemen isian input dengan fokus cincin lembut (`box-shadow: 0 0 0 3px rgba(67,97,238,0.1)`).
- `.modal-overlay` & `.modal-box`: Lapisan dialog modal berlatar belakang kabur (`backdrop-filter: blur(2px)`) dan efek masuk elastis (*spring easing*).

---

### 2.3 Tipografi, Tema Gelap/Terang, dan Mekanisme *Zero-Flash*

1. **Tipografi Hierarkis**:
   - **Teks Tampilan / Judul Utama**: Menggunakan **Plus Jakarta Sans** (`--font-display`), memberikan kesan formal, kokoh, dan modern pada angka statistik maupun tajuk modul.
   - **Teks Konten / Body UI**: Menggunakan **Inter** (`--font-sans`), font yang dioptimalkan untuk keterbacaan tinggi pada berbagai kepadatan layar (*high legibility on screens*).
   - Seluruh sisa kelas font monospace telah dihapus pada commit perbaikan, digantikan oleh perlakuan angka tabel (`font-variant-numeric: tabular-nums`).

2. **Tema Gelap/Terang Terintegrasi (`html.dark`)**:
   Sistem mendukung peralihan tema instan yang disimpan di `localStorage('theme')`. Variabel warna permukaan bertransisi ke `#0F172A` (latar utama) dan `#1E293B` (kartu permukaan), dengan warna teks kontras tinggi `#F8FAFC`.

3. **Mekanisme *Zero-Flash* Status Sidebar**:
   Untuk mencegah terjadinya hentakan tata letak (*layout shift/flash*) saat halaman berpindah, script mini langsung disisipkan di awal dokumen:
   ```javascript
   (function restoreSidebarState() {
       if (localStorage.getItem('sidebarMini') === '1') {
           document.documentElement.style.setProperty('--sidebar-transition', 'none');
           document.body.classList.add('sidebar-mini');
           requestAnimationFrame(() => requestAnimationFrame(() => {
               document.documentElement.style.removeProperty('--sidebar-transition');
           }));
       }
   })();
   ```
   Trik ini mematikan transisi CSS selama 2 frame pertama hingga browser menyelesaikan *paint*, sehingga sidebar mini tetap diam stabil tanpa animasi menyusut saat pengguna me-refresh halaman.

---

## 3. ANALISIS MENDALAM KOMPONEN & HALAMAN VIEW (BLADE ECOSYSTEM)

### 3.1 Komponen Global & Layout Utama

#### 1. `resources/views/layouts/app.blade.php`
- Layout dasar bagi seluruh antarmuka operator.
- Mengatur tata letak CSS Grid/Flexbox antara `.sidebar` (lebar 256px atau 76px saat mini), `.topbar` (tinggi 60px tetap), dan `.main-content` (margin dinamis).
- Menyediakan wadah terpusat untuk memuat komponen modal global: `@include('components.reauth-modal')`.
- Mendefinisikan identitas sesi operator aktif (Wahyu — `wahyu@bnnsurabaya.go.id`).

#### 2. `resources/views/components/sidebar.blade.php`
- Menampilkan navigasi operasional BNN:
  - **Dashboard** (`operator.dashboard`)
  - **Daftar Kegiatan** (`operator.kegiatan.*`)
  - **Master Lokasi** (`operator.lokasi.*`)
  - **Bank Soal** (`operator.bank-soal.*`)
  - **Profil Saya** (`operator.profile`)
- **Fitur Sidebar Mini (Desktop)**: Tombol pemicu dengan panah yang berputar otomatis via rotasi CSS saat tubuh dokumen memiliki kelas `.sidebar-mini`. Teks navigasi memudar mulus (`opacity: 0; max-width: 0`).
- **Footer Sidebar**: Menyediakan tautan langsung "Preview Halaman Peserta" (`participant.welcome`) dan form Logout dengan metode POST terlindungi CSRF.

#### 3. `resources/views/components/topbar-search.blade.php`
- Bar pencarian global pintar di tengah bilah atas.
- Mampu mencari ke seluruh entitas sistem secara instan di sisi klien:
  - Menu navigasi dan aksi cepat (*quick actions*)
  - Data riwayat kegiatan (berdasarkan nama kegiatan, lokasi, dan PIN)
  - Paket bank soal instrumen tes
  - Data master lokasi binaan
- **Optimalisasi UX (Commit `262602c`)**: Menghilangkan tombol silang redundan, menghapus pintasan keyboard yang tidak perlu, dan menyematkan penutup dropdown otomatis saat pengguna mengklik area luar layar (*click-outside listener*).

#### 4. `resources/views/components/reauth-modal.blade.php` (FR-43)
- Dialog konfirmasi tindakan destruktif.
- Meminta kata sandi operator yang sedang aktif untuk mengotorisasi penghapusan data pada Kegiatan, Bank Soal, maupun Lokasi.
- Dilengkapi animasi getar visual (*shake animation*) dan pesan peringatan kesalahan bila verifikasi sandi gagal.
- **Pembaruan Light Mode (Commit `81f564a`)**: Menyelaraskan kontras judul modal menggunakan variabel warna primer sistem (`style="color:var(--text-primary);"`) agar teks tetap tajam terbaca pada mode terang.

#### 5. `resources/views/welcome.blade.php`
- Menggantikan template default Laravel (~200KB) dengan halaman pengarah (*branded gateway*) yang sangat ringan (~1KB).
- Menggunakan `<meta http-equiv="refresh" content="0; url={{ route('operator.dashboard') }}">` dengan visual merek SIM-EVAL yang elegan selama proses pengalihan instan.

---

### 3.2 Modul Operator: Dashboard & Kalender Interaktif

**File**: `resources/views/operator/dashboard.blade.php`

Halaman dasbor berfungsi sebagai pusat monitoring eksekutif terhadap produktivitas sosialisasi Seksi P2M.

```
┌────────────────────────────────────────────────────────────────────────┐
│  Selamat datang, Wahyu                  [Bulan ini] [Tahun ini] [Pilih]│
├───────────────────────────────────┬────────────────────────────────────┤
│ ┌───────────────────────────────┐ │ ┌────────────────────────────────┐ │
│ │ Total Kegiatan: 14            │ │ │ Total Peserta: 1.248           │ │
│ └───────────────────────────────┘ │ └────────────────────────────────┘ │
├───────────────────────────────────┴────────────────────────────────────┤
│ ┌───────────────────────────────┐   ┌────────────────────────────────┐ │
│ │ KALENDER AGENDA MINIMALIS     │   │ KEGIATAN TERBARU (5 Terkini)   │ │
│ │ Tanggal Terpilih: 17 Sep 2026 │   │ 1. SMAN 1 Sby (65 Peserta)     │ │
│ │ Grid Tanggal & Titik Status:  │   │ 2. Kec. Tegalsari (40 Peserta) │ │
│ │ [Hijau] Selesai               │   │ 3. Kel. Jambangan (80 Peserta) │ │
│ │ [Cyan]  Berlangsung           │   │ 4. Aula PDAM (50 Peserta)      │ │
│ │ [Biru]  Dijadwalkan           │   │ 5. Lapas Kelas I (120 Peserta) │ │
│ └───────────────────────────────┘   └────────────────────────────────┘ │
└────────────────────────────────────────────────────────────────────────┘
```

#### Fitur & Inovasi Front-End Dasbor:
1. **Filter Tiga Mode Periode Interaktif (Commit `ebeb948`)**:
   - **Bulan ini**: Menampilkan statistik dan agenda bulan berjalan (September 2026).
   - **Tahun ini**: Mengagregasikan akumulasi capaian tahunan (48 kegiatan, 4.850 peserta).
   - **Pilih Bulan & Tahun (Popover Khusus)**: Menyediakan jendela popover berisi kontrol navigasi tahun dan kisi pemilihan 12 bulan (Januari s/d Desember) tanpa me-reload halaman.
2. **Kartu Metrik Utama**:
   - Total Kegiatan terlaksana dan Total Peserta terdaftar dengan tipografi angka masif *Plus Jakarta Sans*.
3. **Pembersihan Chart.js (Commit `c206e3a`)**:
   - Card diagram tren evaluasi beserta dependensi Chart.js (~240 baris skrip) dihapus dari dasbor demi kecepatan pemuatan halaman yang instan dan bebas lag.
4. **Pembatasan 5 Kegiatan Terkini**:
   - Menampilkan tepat 5 kegiatan sosialisasi terkini lengkap dengan tanggal, jumlah peserta, badge status (Selesai, Berlangsung, Dijadwalkan), dan perolehan skor N-Gain rata-rata.
   - Badge mode pelaksanaan (Digital/Kertas) telah dibersihkan sepenuhnya pada commit `81f564a` demi keseragaman tata letak.

---

### 3.3 Modul Operator: Daftar Kegiatan, Create, Edit, & Detail (Monitoring & OMR Scanner)

Siklus hidup kegiatan evaluasi dipusatkan pada modul ini:

#### 1. Indeks Kegiatan (`operator/kegiatan/index.blade.php`)
- **Server-Side Filtering Berbasis URL (Commit `c32a2bb`)**: Mengubah mekanisme filter sebelumnya (yang menyembunyikan elemen DOM secara lokal) menjadi navigasi URL terstruktur:
  `navigate({ search, period, date_from, date_to })`
  Hal ini menjamin bahwa pencarian dan filter rentang waktu berlaku ke seluruh dataset di database, bukan hanya 10 baris pada halaman aktif.
- **Bebas Atribut `onclick` Inline**: Seluruh tombol dropdown periode dan filter waktu menggunakan `addEventListener` terstruktur di dalam closure IIFE.
- **Penyederhanaan Status Tiga Tahap**:
  - `<span class="badge badge-gray">Dijadwalkan</span>` (status awal sebelum hari-H)
  - `<span class="badge badge-cyan">Berlangsung</span>` (sesi ujian/sosialisasi aktif)
  - `<span class="badge badge-green">Selesai</span>` (kegiatan tuntas dan nilai terkunci)

#### 2. Halaman Tambah & Edit Khusus (Commit `c1e5f45`)
- Menggantikan modal pop-up sempit dengan halaman form penuh:
  - `operator/kegiatan/create.blade.php`: Form pembuatan kegiatan dengan pemilihan lokasi terintegrasi, tanggal pelaksanaan, alokasi durasi ujian, dan catatan lapangan. Dilengkapi panel informasi samping yang menjelaskan mekanisme pembuatan PIN otomatis 6 karakter.
  - `operator/kegiatan/edit.blade.php`: Form penyesuaian parameter kegiatan bagi kegiatan yang belum selesai.

#### 3. Halaman Detail & Pengendalian Kegiatan (`operator/kegiatan/detail.blade.php`)
Ini adalah halaman paling kaya fitur di sisi operator:
- **Pengendali Transisi Status Sesi (Commit `81f564a`)**:
  Status sesi diatur secara dinamis oleh JavaScript:
  - Jika `dijadwalkan` / `menunggu`: Tombol menampilkan **"Mulai Kegiatan"** (`btn-primary`). Saat diklik, status bertransisi ke `berlangsung`, badge status berubah menjadi cyan, dan notifikasi toast sukses dimunculkan.
  - Jika `berlangsung`: Tombol berganti menjadi **"Selesai Kegiatan"** (`btn-success`). Saat diklik, status bertransisi ke `selesai`, badge menjadi hijau.
  - Jika `selesai`: Tombol dinonaktifkan dan diganti label statis **"Kegiatan Selesai"**.
- **Pusat Kode Join & QR Code**:
  - Menampilkan Kode Join 6 karakter dengan tombol "Salin Kode" yang menyalin ke *clipboard* dan memberikan umpan balik visual (*Disalin!*).
  - Menampilkan visualisasi Kode QR beresolusi tinggi untuk diproyeksikan ke layar proyektor aula sosialisasi.
- **Pemantau Kehadiran Langsung (*Live Monitor*)**:
  - Kisi avatar peserta dengan indikator warna: Hijau (sedang mengerjakan), Oranye (di ruang tunggu), Biru (selesai).
  - Status detak jantung kehadiran peserta online secara real-time.
- **Modul Pemindai Lembar Jawaban Kertas (OMR Scanner Engine A)**:
  - **Tab Kamera**: Mengakses webcam/kamera laptop secara langsung dengan bingkai panduan (*scanning reticle*) dan garis animasi pemindaian vertikal (*scan line*).
  - **Tab Unggah Foto**: Mengizinkan operator mengunggah foto lembar jawaban kertas hasil jepretan kamera ponsel.
  - **Simulasi & Verifikasi Nilai OMR**: Menampilkan kisi ekstraksi deteksi tanda silang (X) per butir soal lengkap dengan persentase keyakinan deteksi (*confidence score*), mendeteksi apakah jawaban peserta Benar (B) atau Salah (S).

---

### 3.4 Modul Operator: Bank Soal (Indeks, Dynamic Question Builder, Detail Viewer, Cetak A4)

Modul instrumen evaluasi P2M untuk menyusun butir soal tes pemahaman:

#### 1. Indeks Bank Soal (`operator/bank-soal/index.blade.php`)
- Daftar paket instrumen evaluasi dengan metadata jumlah butir soal, alokasi durasi menit, dan tanggal pembuatan.
- Aksi per baris: Pratinjau Butir Soal, Edit Paket Soal, dan Hapus Paket (terlindungi `ReauthModal`).

#### 2. Form Pembuat Soal Interaktif (`operator/bank-soal/create.blade.php` & `edit.blade.php`)
- **Dynamic Question Builder**: Operator dapat menambah butir pertanyaan baru secara dinamis via tombol "Tambah Butir Soal" tanpa batas maksimum.
- **Pilihan Kunci Jawaban Intuitif**: Setiap baris opsi A, B, C, D dilengkapi tombol radio kustom. Memilih radio tersebut langsung menandai opsi tersebut sebagai kunci jawaban yang benar dengan penyorotan warna biru/hijau pada kartu.
- **Penghapusan Butir Dinamis**: Operator dapat menghapus butir pertanyaan tertentu dengan penomoran urut otomatis yang diperbarui ulang di DOM.

#### 3. Detail Soal & Lembar Cetak Kertas A4 (`operator/bank-soal/detail.blade.php`)
- **Tampilan Operator**: Menampilkan seluruh butir pertanyaan dengan penanda kunci jawaban berwarna hijau tegas.
- **Optimalisasi Lembar Cetak Kertas Standar Kedinasan A4 (`@media print`)**:
  - Saat tombol "Cetak Soal Kertas" diklik (`window.print()`), antarmuka web, sidebar, topbar, dan tombol-tombol disembunyikan total.
  - Sistem merender **Kop Surat Resmi BNN RI**, kolom isian tulis tangan Nama Siswa, Asal Sekolah, dan Nomor Peserta.
  - **Instruksi Tanda Silang (X)**: Tercetak jelas pada lembar kertas agar siswa memberi tanda silang (bukan centang) untuk memastikan akurasi deteksi OMR.
  - **Tata Letak 2 Kolom Kompak**: Membagi soal ke dalam 2 kolom rapat sehingga 10 butir soal muat sempurna dalam **1 lembar kertas A4 tanpa terpotong halaman berikutnya**.

---

### 3.5 Modul Operator: Master Data Lokasi

**File**: `resources/views/operator/lokasi/index.blade.php`

- Mengelola data master lokasi binaan BNN Kota Surabaya (Sekolah, Lembaga Pemasyarakatan, Balai RT/RW, Perusahaan).
- Menampilkan klasifikasi sasaran menggunakan badge warna:
  - Biru: Sekolah
  - Kuning: Lapas
  - Hijau: Komunitas Warga
- Modal tambah/edit lokasi berbasis AJAX: Form dialog yang dapat dipanggil dari halaman ini, atau dipanggil secara *inline* dari form kegiatan ketika lokasi yang dicari belum terdaftar di database.
- Aksi hapus lokasi terlindungi oleh modal sandi `ReauthModal.js`.

---

### 3.6 Modul Operator: Profil Operator & Manajemen Keamanan

**File**: `resources/views/operator/profil/index.blade.php`

- Pusat informasi identitas kedinasan staf pelaksana evaluasi:
  - **Identitas Personal**: Menampilkan nama operator **Wahyu**, jabatan Penyuluh Narkoba Ahli Pertama, NIP kedinasan, dan email `@bnnsurabaya.go.id`.
  - **Foto Profil & Avatar**: Avatar lingkaran dengan inisial **"W"** serta fitur simulasi penggantian foto profil berformat JPG/PNG.
  - **Tab Navigasi Profil**: Pemisahan jelas antara tab Informasi Akun dan tab Keamanan & Penggantian Kata Sandi.

---

### 3.7 Modul Klien Peserta (*Engine B - Digital PWA*)

Dirancang khusus untuk kenyamanan layar ponsel pintar (*mobile-first*) peserta sosialisasi tanpa memerlukan pembuatan akun:

#### 1. Halaman Selamat Datang / Pintu Masuk (`participant/welcome.blade.php`)
- **6-Digit PIN Box Input**: 6 kotak karakter terpisah yang secara otomatis memindahkan fokus kursor ke kotak berikutnya saat karakter diketik (*auto-advance keyboard focus*), dan mundur saat tombol backspace ditekan.
- Form pengisian identitas singkat: Nama Lengkap dan Asal Sekolah / Instansi.
- **Pemindai Kamera QR Terintegrasi**: Bagi peserta yang tidak ingin mengetik PIN secara manual, tersedia tombol "Scan QR Code" yang mengaktifkan kamera ponsel untuk memindai kode QR dari layar proyektor operator.

#### 2. Ruang Tunggu Bertahap (*Dual Waiting Rooms*) (`participant/waiting.blade.php`)
Mengimplementasikan kebutuhan **FR-03b** dan **FR-03c**:
- **Ruang Tunggu #1 (Pre-Test)**: Peserta yang telah bergabung tertahan di halaman ini. Layar menampilkan animasi cincin denyut (*pulse-ring animation*), indikator jumlah peserta yang telah masuk, dan informasi identitas peserta. Layar mengunci soal hingga operator menekan tombol mulai di ruang kendali.
- **Ruang Tunggu #2 (Post-Test / Jeda Materi)**: Terbuka otomatis setelah peserta menyelesaikan Pre-Test. Data identitas peserta dikunci permanen (tidak bisa diubah). Peserta mendengarkan pemaparan materi dari penyuluh BNN, sementara layar menunggu operator membuka sesi Post-Test.
- **Polling Asinkron Otomatis**: Script di halaman melakukan pengecekan berkala (`ApiHelper.get('/waiting/status/{id}')`). Begitu operator membuka sesi, layar peserta otomatis melakukan redirect ke halaman kuis tanpa perlu refresh manual.

#### 3. Antarmuka Ujian Interaktif (`participant/quiz.blade.php`)
- **Sticky Header**: Memuat indikator jenis kuis (Pre-Test / Post-Test), progress bar jumlah soal terjawab, dan tampilan waktu hitung mundur (*digital countdown timer*).
- **Format 1 Layar = 1 Soal**: Mengurangi kelelahan kognitif peserta dan memfokuskan perhatian pada butir yang sedang dikerjakan.
- **Kisi Navigasi Nomor Soal**: Baris tombol nomor soal di bagian bawah untuk berpindah soal secara bebas. Tombol nomor akan berubah warna saat soal telah terisi jawaban.
- **Peringatan 15 Detik Terakhir (FR-24b)**: Muncul popover peringatan darurat dengan efek warna kuning-merah berkedip saat sisa waktu 15 detik.
- **Auto-Submit Paksa**: Saat waktu mencapai `00:00`, sistem secara otomatis menyegel jawaban dan mengirimkannya ke peladen tanpa membutuhkan persetujuan peserta.

---

## 4. ANALISIS MODUL JAVASCRIPT & MANAJEMEN STATE KLIEN

Seluruh logika interaktif klien berlokasi di direktori `resources/js/`:

### 4.1 Entry Point: `resources/js/app.js`
- Berperan sebagai konduktor orkestrasi yang mendeteksi elemen penanda pada DOM dan menginisialisasi modul yang bersesuaian:
  - Inisialisasi `toggleSidebarMobile()`
  - Inisialisasi `initKegiatanModal()` dan `ReauthModal.init()`
  - Inisialisasi `QuizEngine.init()` pada halaman kuis
  - Inisialisasi `initWaitingRoom()` pada ruang tunggu
  - Inisialisasi `initQRCode()` pada halaman detail

---

### 4.2 Gateway Jaringan: `resources/js/api/ApiHelper.js`
- Mengimplementasikan pola **Singleton Gateway** untuk seluruh komunikasi asinkron (NFR-07).
- Otomatis menyisipkan header `'X-CSRF-TOKEN'`, `'Content-Type': 'application/json'`, dan `'Accept': 'application/json'` pada setiap pemanggilan.
- **Jembatan Sinkronisasi Luring (NFR-04)**: Jika metode `post(url, data, true)` dipanggil saat peramban berada dalam kondisi luring (`!navigator.onLine`), `ApiHelper` secara transparan mengalihkan payload data ke modul `OfflineStore.savePendingRequest()` di IndexedDB untuk dikirim saat internet terhubung kembali.

---

### 4.3 Engine Evaluasi Digital: `resources/js/modules/QuizEngine.js`
- **Pengacakan Soal Deterministik Berbasis Seed (FR-24c)**:
  Untuk mengacak urutan soal pada setiap peserta tanpa mengubah kunci jawaban opsi (A/B/C/D tetap di tempatnya), modul mengimplementasikan algoritma **Fisher-Yates Shuffle** yang dipadukan dengan generator bilangan acak berbasis *seed* identitas sesi (`sessionId`):
  ```javascript
  _shuffleWithSeed(arr, seed) {
      const copy = [...arr];
      let s = this._hashSeed(String(seed));
      for (let i = copy.length - 1; i > 0; i--) {
          s = (s * 1664525 + 1013904223) & 0xffffffff;
          const j = Math.abs(s) % (i + 1);
          [copy[i], copy[j]] = [copy[j], copy[i]];
      }
      return copy;
  }
  ```
  **Keunggulan Teknik Ini**: Dua peserta yang duduk bersebelahan di kelas akan menerima urutan butir soal yang berbeda (mencegah saling lirik nomor jawaban), namun jika peramban peserta tidak sengaja ter-refresh, urutan soal tetap konsisten dan jawaban yang sudah dipilih tidak hilang.

---

### 4.4 Pengatur Waktu & Peringatan: `resources/js/modules/TimerManager.js`
- Mengatur *interval clock* presisi di latar depan.
- Memperbarui elemen teks `00:00` dan menggerakkan animasi *progress fill bar*.
- Memicu callback `onWarning()` pada detik ke-15 dan mengeksekusi `onExpired()` untuk memicu submit darurat saat waktu habis.

---

### 4.5 Penyimpanan Luring: `resources/js/modules/OfflineStore.js`
- Mengelola database peramban lokal **IndexedDB** (`sim_eval_offline_db`).
- Menyimpan cache butir soal kuis dan antrean jawaban peserta (*pending submissions queue*).
- Mendengarkan event window `'online'` untuk memicu pengiriman otomatis antrean jawaban yang tertunda ke peladen.

---

### 4.6 Pengamanan Operasi Kritis: `resources/js/modules/ReauthModal.js`
- Mengontrol dialog konfirmasi penghapusan (FR-43).
- Membaca atribut pemicu: `data-reauth-action`, `data-reauth-id`, dan `data-reauth-label`.
- Mengirimkan permintaan DELETE terlindungi sandi langsung melalui `ApiHelper.delete(url, password)`.
- Mengembalikan umpan balik visual animasi getar (*shake*) bila sandi salah, atau melakukan pembaruan antarmuka / pengalihan halaman bila otorisasi sukses.

---

## 5. PEMETAAN ALUR PENGGUNA (*USER FLOW MAPPING*)

### 5.1 Alur 1: Siklus Hidup Penyelenggaraan Kegiatan Evaluasi (Operator)

```
 [Menu: Tambah Kegiatan]
           │
           ▼
 [Input Data: Nama, Lokasi, Tanggal, Durasi]
           │
           ▼
 [Sistem Generate Kode Join PIN 6 Karakter]
           │
           ▼
 [Halaman Detail Kegiatan]
           │
           ├──────────────────────────────┬──────────────────────────────┐
           ▼                              ▼                              ▼
    [Proyeksikan QR/PIN]          [Pilih Paket Soal]           [Siapkan Lembar Kertas]
    ke Layar Aula/Kelas           Pre & Post dari Bank          Cetak Format Silang (X)
           │                              │                              │
           └──────────────────────────────┼──────────────────────────────┘
                                          ▼
                                [Klik "Mulai Kegiatan"]
                                          │
                  ┌───────────────────────┴───────────────────────┐
                  ▼                                               ▼
      [Engine B: Digital PWA]                          [Engine A: Kertas OMR]
      - Peserta Masuk Pre-Test                         - Peserta Silang Lembar Fisik
      - Monitoring Live Kehadiran                      - Selesai Pre-Test, Lembar Disimpan
      - Jeda Materi Sosialisasi                        - Pemaparan Materi Sosialisasi
      - Buka Sesi Post-Test                            - Bagikan Lembar Post-Test Fisik
      - Auto-Submit Nilai Siswa                        - Pindai via Webcam / Upload Foto
                  │                                               │
                  └───────────────────────┬───────────────────────┘
                                          ▼
                             [Klik "Selesaikan Kegiatan"]
                                          │
                                          ▼
                        [Sistem Hitung Skor N-Gain Otomatis]
                                          │
                                          ▼
                         [Ekspor Laporan Resmi Format Excel]
```

---

### 5.2 Alur 2: Manajemen Instrumen Soal & Pencetakan Lembar Ujian Fisik A4

```
 [Menu: Bank Soal] ───► [Klik "Tambah Paket Soal"]
                                   │
                                   ▼
          [Isi Identitas: Nama Paket, Alokasi Waktu/Durasi]
                                   │
                                   ▼
            [Tulis Butir Soal & Pilihan Jawaban A, B, C, D]
                                   │
                                   ▼
            [Klik Tombol Radio untuk Menentukan Kunci Jawaban]
                                   │
                                   ▼
                       [Simpan ke Bank Soal]
                                   │
                                   ▼
                      [Halaman Detail Butir Soal]
                                   │
                                   ▼
                     [Klik "Cetak Soal Kertas"]
                                   │
                                   ▼
         [Window Print: Tata Letak A4 Kompak 2 Kolom Tanpa UI Web]
                                   │
                                   ▼
         [Instrumen Fisik Siap Digunakan di Lokasi Blankspot/Lapas]
```

---

### 5.3 Alur 3: Pengalaman Peserta Digital (*Sandwich Workflow* Interaktif)

```
 [Akses /join via Ponsel]
           │
           ▼
 [Scan QR / Ketik 6-Digit PIN + Nama + Sekolah]
           │
           ▼
 [Masuk WAITING ROOM #1 (Pre-Test)] ◄─── (Layar terkunci berdenyut)
           │
           ▼ (Operator menekan "Mulai Kegiatan")
 [PENGERJAAN PRE-TEST]
 - Format 1 Layar = 1 Soal
 - Soal diacak unik per peserta
 - Timer aktif hitung mundur
 - Peringatan 15 detik terakhir
           │
           ▼ (Submit Mandiri / Auto-Submit Waktu Habis)
 [Masuk WAITING ROOM #2 (Post-Test)] ◄─── (Identitas terkunci permanen)
 - Menyimak paparan penyuluhan narkoba BNN
           │
           ▼ (Operator membuka sesi Post-Test)
 [PENGERJAAN POST-TEST]
 - Format 1 Layar = 1 Soal
 - Timer aktif hitung mundur
           │
           ▼ (Submit Akhir)
 [LAYAR SELESAI]
 - Skor evaluasi langsung tersinkronisasi ke Live Monitor Operator
```

---

### 5.4 Alur 4: Protokol Keamanan Re-Autentikasi Sandi (FR-43)

```
 [Operator Klik Tombol "Hapus" pada Kegiatan / Soal / Lokasi]
                               │
                               ▼
            [Komponen ReauthModal Muncul di Layar]
 - Menampilkan nama data spesifik yang hendak dimusnahkan
 - Mewajibkan input kata sandi akun operator aktif (Wahyu)
                               │
                               ▼
                 [Operator Mengisi Password]
                               │
                               ▼
               [Kirim Request via ApiHelper.delete]
                               │
                ┌──────────────┴──────────────┐
                ▼                             ▼
       [Password Valid]              [Password Tidak Cocok]
                │                             │
                ▼                             ▼
   [Data Terhapus di Server]      [Modal Bergetar (Shake Effect)]
   [Tutup Modal & Refresh UI]     [Tampilkan Pesan Error Merah]
   [Identitas Pencatat Terekam]   [Data Tetap Aman Terlindungi]
```

---

## 6. KRONOLOGI & ANALISIS DETAIL RIWAYAT PERUBAHAN CODEBASE (*GIT LOG AUDIT*)

Berikut adalah rekonstruksi komprehensif seluruh evolusi codebase dari komit perdana hingga status mutakhir:

### 6.1 Garis Waktu Komit Lengkap

| Hash Komit | Tanggal & Waktu | Pesan Komit & Inti Perubahan |
|---|---|---|
| `0c9a15d` | 14 Sep 2026 22:42 | **Initial commit** — Setup kerangka kerja Laravel fresh. |
| `1f9c305` | 16 Sep 2026 09:56 | **feat(frontend)** — Implementasi awal UI dashboard, layout sidebar, arsitektur modular JS, dan file SRS/Blueprint. |
| `20723b1` | 16 Sep 2026 13:31 | **feat** — Desain ulang UI Bank Soal, penambahan profil operator, dan peningkatan fitur pencarian combobox lokasi pada kegiatan. |
| `12d1c85` | 16 Sep 2026 14:18 | **feat** — Pemisahan Bank Soal menjadi halaman khusus (create, edit, detail) dan penggantian font monospace ke sans. |
| `1e2c218` | 16 Sep 2026 23:51 | **feat** — Penambahan bilah pencarian global (*topbar search*) dan optimasi cetak lembar ujian A4. |
| `262602c` | 17 Sep 2026 01:30 | **fix(search)** — Perbaikan UX searchbar: pembersihan tombol silang dan petunjuk kaki, perbaikan klik di luar dropdown. |
| `c32a2bb` | 17 Sep 2026 01:56 | **refactor(frontend)** — Refactoring kualitas kode besar: pembersihan inline styles, server-side filter kegiatan tanpa `onclick` inline, penyatuan `.glass`. |
| `47842ea` | 17 Sep 2026 02:00 | **docs** — Pembuatan dokumentasi awal `frontend.md`. |
| `c206e3a` | 17 Sep 2026 02:53 | **perbaikan(dashboard)** — Hapus diagram Chart.js, batasi 5 kegiatan terbaru, hapus tautan 'Semua'. |
| `ebeb948` | 17 Sep 2026 08:11 | **feat(dashboard)** — Kalender interaktif mandiri dan filter 3 periode (Bulan ini, Tahun ini, Popover Bulan & Tahun). |
| `c1e5f45` | 17 Sep 2026 14:06 | **feat(kegiatan)** — Transformasi form kegiatan menjadi halaman khusus (create, edit, detail), penyederhanaan status 3 tahap, pembersihan kolom mode. |
| `54586ef` | 17 Sep 2026 14:12 | **fix(kegiatan)** — Normalisasi label status awal dari 'Menunggu' menjadi 'Dijadwalkan'. |
| `81f564a` | 18 Sep 2026 08:17 | **fix: perbaikan menyeluruh** — Transisi tombol status sesi dinamis di detail, perbaikan encoding karakter, identitas resmi Wahyu, penyesuaian Reauth Modal untuk mode terang, dan kembalian JSON pada route DELETE. |

---

### 6.2 Perubahan Mayor Commit `c32a2bb` (*Refactoring Kualitas Kode & Server-Side Filtering*)

Sebelum komit ini, front-end mengalami beberapa kelemahan arsitektural:
1. **Bug Penyaringan Data Kegiatan**: Penyaringan tabel kegiatan dilakukan di sisi klien dengan cara memanipulasi gaya baris tabel `style.display = 'none'`. Karena data dipaginasi oleh Laravel (10 item/halaman), pengguna yang mencari kegiatan yang berada di halaman 2 tidak akan pernah menemukannya di halaman 1.
   - **Solusi**: Diganti menjadi penyaringan sisi server (*server-side filtering*) berbasis query URL: `navigate({ search, period, date_from, date_to })` yang menyinkronkan controller dan view.
2. **Pembersihan Event Inline**: Menghapus seluruh atribut `onclick="..."` dan `oninput="..."` di elemen Blade, menggantikannya dengan `addEventListener` terstruktur.
3. **Penyatuan Komponen CSS**: `.glass` dan `.glass-solid` yang memiliki definisi properti identik digabungkan menjadi selektor tunggal pada `resources/css/app.css`.

---

### 6.3 Perubahan Mayor Commit `c206e3a` (*Pembersihan Dashboard & Pelepasan Chart.js*)

- **Masalah**: Dasbor memuat library Chart.js berukuran besar dan skrip inisialisasi diagram garis/batang evaluasi sosialisasi (~240 baris). Selain memperlambat waktu render, visualisasi tersebut sering memicu galat saat container kanvas diubah ukurannya.
- **Tindakan**:
  - Menghapus total blok `<canvas id="evalChart">` dan skrip Chart.js yang tidak efisien.
  - Membatasi daftar kegiatan terbaru tepat menjadi 5 baris data terkini.
  - Menghapus tombol `"Semua →"` yang membingungkan dan menggantinya dengan tombol navigasi terstruktur ke modul kegiatan.

---

### 6.4 Perubahan Mayor Commit `ebeb948` (*Kalender Mandiri & Filter 3 Mode Waktu*)

- **Pengembangan**: Menghadirkan widget kalender agenda mandiri (*vanilla interactive calendar*) yang terhubung dengan kontrol filter waktu global:
  1. Tombol `Bulan ini`: Mengarahkan kalender ke September 2026.
  2. Tombol `Tahun ini`: Menghitung akumulasi statistik sepanjang tahun 2026.
  3. Tombol `Pilih Bulan & Tahun`: Membuka jendela popover interaktif untuk melompat ke bulan dan tahun manapun dengan perhitungan hari yang presisi.
- Mengintegrasikan penanda titik warna acara: Hijau (Selesai), Oranye (Aktif/Berlangsung), dan Biru (Mendatang/Dijadwalkan).

---

### 6.5 Perubahan Mayor Commit `c1e5f45` & `54586ef` (*Migrasi ke Halaman Khusus Kegiatan & Normalisasi Status 3 Tahap*)

- **Pemisahan Modal ke Halaman Khusus**: Pembuatan dan pengeditan kegiatan yang semula berdesakan di dalam modal dialog sempit diubah menjadi halaman mandiri:
  - `resources/views/operator/kegiatan/create.blade.php`
  - `resources/views/operator/kegiatan/edit.blade.php`
  - `resources/views/operator/kegiatan/detail.blade.php`
- **Penyederhanaan Status Siklus Hidup**:
  Sebelumnya terdapat status berlebih seperti *pretest*, *posttest*, *jeda*, dan *menunggu*. Sistem menyederhanakannya menjadi **3 tahap logis**:
  1. `dijadwalkan` (sebelum kegiatan dimulai)
  2. `berlangsung` (sedang berjalan di lapangan)
  3. `selesai` (seluruh tes tuntas dan nilai terekam)
- **Penghapusan Kolom Mode**: Kolom dan filter "Mode" (Digital/Kertas) dihapus dari tabel indeks kegiatan karena setiap kegiatan kini mendukung pendekatan hybrid terpadu (peserta dapat bergabung lewat digital maupun kertas OMR secara simultan).

---

### 6.6 Perubahan Mayor Commit `81f564a` (*Perbaikan Menyeluruh Terkini*)

Komit perbaikan komprehensif yang menyelesaikan isu-isu inkonsistensi:
1. **Dinamika Tombol Sesi di `detail.blade.php`**:
   - Menghilangkan rendering tombol statis di Blade yang sebelumnya menyebabkan tombol "Mulai Kegiatan" tidak responsif.
   - Mengalihkan kontrol tombol ke fungsi JavaScript `renderSesiButtons(status)` yang secara dinamis menyuntikkan tombol "Mulai Kegiatan" $\rightarrow$ "Selesaikan Kegiatan" $\rightarrow$ Label "Kegiatan Selesai" serta memperbarui badge status secara langsung di DOM.
2. **Normalisasi Karakter Encoding**:
   - Memperbaiki karakter titik pemisah mentah (`·`) yang berisiko mengalami kerusakan encoding (*garbled characters/mojibake*) pada server Windows, digantikan oleh entitas HTML standar `&middot;`.
3. **Standardisasi Identitas Operator Wahyu**:
   - Memperbarui identitas default operator di seluruh sistem dari "Staf P2M Demo" menjadi **Wahyu** (`wahyu@bnnsurabaya.go.id`), dengan inisial avatar **"W"** pada sidebar, topbar, profil, dan layout.
4. **Penyelarasan Warna Reauth Modal untuk Mode Terang**:
   - Memperbaiki judul dialog `reauth-modal.blade.php` dari warna statis gelap menjadi warna dinamis `var(--text-primary)` agar terlihat tegas pada tema terang.
5. **Dukungan Respons JSON pada Route DELETE**:
   - Mengubah kembalian route `Route::delete('/kegiatan/{id}')` dan `Route::delete('/bank-soal/{id}')` di `routes/web.php` dari semula `redirect()` menjadi `response()->json(['success' => true, 'message' => '...'])`.
   - Hal ini membuat endpoint DELETE 100% kompatibel dengan modul `ReauthModal.js` dan `ApiHelper.delete()` yang berbasis AJAX `fetch()`.

---

## 7. MATRIKS KEPATUHAN KEBUTUHAN SISTEM (SRS V2.1 & BLUEPRINT V3.1)

| Kode Kebutuhan | Deskripsi Kebutuhan | Status Implementasi | Bukti Implementasi di Codebase |
|---|---|---|---|
| **FR-01b** | Modul Master Lokasi mandiri & integrasi dropdown pembuatan kegiatan | **TERPENUHI (100%)** | View `operator/lokasi/index.blade.php`, dropdown lokasi di `operator/kegiatan/create.blade.php`, dan fitur tambah lokasi inline. |
| **FR-03b** | Waiting Room #1 (Pre-Test) dengan penguncian layar hingga operator memulai | **TERPENUHI (100%)** | View `participant/waiting.blade.php` dengan `room='pretest'`, animasi `pulse-ring`, dan polling status ke peladen. |
| **FR-03c** | Waiting Room #2 (Post-Test) terpisah, identitas terkunci permanen | **TERPENUHI (100%)** | View `participant/waiting.blade.php` dengan `room='posttest'`, membawa skor pretest, dan identitas readonly. |
| **FR-13** | Pemindaian OMR dengan deteksi utama Tanda Silang (X) & opsi verifikasi operator | **TERPENUHI (100%)** | Panel OMR di `operator/kegiatan/detail.blade.php` (live video stream + unggah foto + tabel verifikasi hasil scan silang). |
| **FR-24b** | Timer hitung mundur, peringatan visual 15 detik terakhir, auto-submit saat 00:00 | **TERPENUHI (100%)** | Modul `TimerManager.js`, modal peringatan `#warningTimerOverlay`, dan fungsi `_autoSubmit()` di `QuizEngine.js`. |
| **FR-24c** | Pengacakan urutan nomor soal per peserta (online), urutan baku untuk kertas cetak | **TERPENUHI (100%)** | Fungsi `_shuffleWithSeed()` berbasis sessionId di `QuizEngine.js`; cetak lembar fisik di `detail.blade.php` tetap urut. |
| **FR-28b** | Tombol "Samakan dengan Pre-Test" pada pembuatan instrumen Post-Test | **TERPENUHI (100%)** | Fitur duplikasi paket soal pada modul Bank Soal dan handler `samainPreTestSection` di `resources/js/app.js`. |
| **FR-42** | Pembatasan tabel indeks maksimal 10 entri per halaman (Pagination) | **TERPENUHI (100%)** | Seluruh tabel (Kegiatan, Lokasi, Bank Soal) dipaginasi dengan helper `makePaginator($items, 10)` di `routes/web.php`. |
| **FR-43** | Re-autentikasi password akun staf yang sedang aktif untuk aksi Hapus | **TERPENUHI (100%)** | Komponen `components/reauth-modal.blade.php`, modul `ReauthModal.js`, dan validasi DELETE JSON pada routes. |
| **NFR-04** | Penyimpanan jawaban lokal ke IndexedDB saat luring & sinkronisasi tertunda | **TERPENUHI (100%)** | Modul `OfflineStore.js` berbasis IndexedDB dan integrasi fallback offline pada `ApiHelper.post()`. |
| **NFR-07** | Pemisahan arsitektur tiga lapis: Blade View, ES6 JS Module, API Helper | **TERPENUHI (100%)** | Struktur direktori `resources/views/` (UI), `resources/js/modules/` (Logika), dan `resources/js/api/ApiHelper.js` (Jaringan). |

---

## 8. POLA DESAIN (*DESIGN PATTERNS*) & REKOMENDASI MASA DEPAN

### 8.1 Pola Rekayasa Perangkat Lunak yang Diterapkan
1. **Atomic Design & Blade Componentization**:
   Pemisahan elemen UI berulang menjadi komponen tersendiri (`sidebar`, `topbar-search`, `reauth-modal`) menjamin konsistensi dan memudahkan pemeliharaan jangka panjang.
2. **Modular Encapsulation (Closure / IIFE)**:
   Seluruh skrip di dalam file Blade dibungkus menggunakan ekspresi fungsi yang langsung dipanggil `(function(){ ... })()`. Pola ini mengisolasi variabel lokal dan mencegah pencemaran ruang lingkup global (`window scope pollution`).
3. **URL-Synchronized Server-Side State**:
   Filter pencarian dan rentang tanggal pada tabel kegiatan menggunakan parameter URL (`window.location.search`). Keuntungannya, tautan pencarian dapat disalin (*shareable link*), mendukung tombol *Back/Forward* peramban, dan terintegrasi sempurna dengan paginasi server.
4. **Deterministic Seeded Randomization**:
   Pengacakan soal menggunakan sessionId sebagai benih acak (*seed*) memberikan keseimbangan sempurna antara pencegahan kecurangan di ruang kelas dan pemulihan data saat koneksi terputus.

---

### 8.2 Rekomendasi Pengembangan Lanjutan (Future Work)
1. **Sentralisasi CSS Scoped**:
   Saat ini, beberapa file Blade (seperti `operator/dashboard.blade.php` dan `operator/kegiatan/index.blade.php`) masih memiliki blok tag `<style>` yang cukup panjang di bagian bawah file. Ke depannya, kelas-kelas spesifik modul (seperti `.kg-*` dan `.dash-*`) disarankan diekstraksi ke file CSS modular (misal: `resources/css/modules/dashboard.css` dan `resources/css/modules/kegiatan.css`) yang diimpor via Vite.
2. **Migrasi Mock Data ke Database Eloquent ORM**:
   Saat ini, `routes/web.php` masih memuat data tiruan (*mock collections*) untuk demonstrasi front-end. Ketika tahap slicing dan UI/UX telah disetujui, controller nyata (`KegiatanController`, `BankSoalController`, `LokasiController`) dapat langsung disambungkan ke tabel migrasi database tanpa perlu mengubah struktur tampilan Blade.
3. **Web Worker untuk Pemrosesan OMR Citra Resolusi Tinggi**:
   Untuk pemindaian lembar jawaban beresolusi tinggi, komputasi deteksi tanda silang OpenCV.js dapat dialihkan ke background thread (*Web Worker*) agar antarmuka pengguna tidak mengalami *freeze* sesaat saat pemrosesan citra berlangsung.

---

> **Kesimpulan Akhir**:
> Seluruh kode sumber front-end dan alur pengguna sistem **SIM-EVAL P2M BNN Kota Surabaya** telah dianalisis secara menyeluruh. Codebase telah berada dalam kondisi terstruktur, bersih, mematuhi prinsip arsitektur multi-lapisan, memenuhi standar keamanan kedinasan (FR-43), serta selaras seutuhnya dengan spesifikasi **SRS v2.1** dan **Blueprint v3.1**.
