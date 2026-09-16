# SOFTWARE REQUIREMENTS SPECIFICATION (SRS)
## SIM-EVAL P2M - Sistem Informasi Monitoring & Evaluasi Pre-Test/Post-Test v2.1

*Dokumen ini merupakan turunan teknis (testable requirements) dari Blueprint v3.1, mengadaptasi kerangka IEEE 830.*

### Riwayat Versi
- **v2.0**: Rilis awal turunan teknis dari Blueprint v3.0.
- **v2.1 (Dokumen ini)**: Pemisahan FR-03b menjadi dua status Waiting Room, klarifikasi mekanisme konfirmasi OMR (FR-13), pembeda aturan pengacakan soal online vs offline (FR-24c), dan penjelasan mekanisme re-autentikasi password pada aksi hapus (FR-43).

---

### 1. Kebutuhan Fungsional (Modul Baru & Pembaruan)

| ID | Kebutuhan Fungsional (FR) | Prioritas |
|---|---|---|
| **FR-01b** | Sistem harus menyediakan modul Master Lokasi yang memungkinkan penambahan data lokasi secara mandiri, maupun dari dalam *form dropdown* pembuatan Kegiatan (Event). | MVP |
| **FR-03b** | Sistem harus mengimplementasikan status *Waiting Room #1 (Pre-Test)* pada antarmuka klien peserta (Engine B), di mana layar tidak menampilkan soal Pre-Test hingga transisi status dipicu secara manual oleh operator. | F2 |
| **FR-03c** | *(Baru)* Sistem harus mengimplementasikan status *Waiting Room #2 (Post-Test)*, terpisah dari FR-03b, yang otomatis diaktifkan setelah peserta menyelesaikan/waktu Pre-Test habis. Identitas peserta (nama, sekolah) dari sesi Pre-Test dikunci secara permanen dan dibawa ke sesi ini. Layar tidak menampilkan soal Post-Test hingga operator memicu transisi status secara terpisah dari FR-03b. | F2 |
| **FR-13** | *(Diperbarui)* Sistem pemindaian kamera dan OMR pada Engine A harus mendeteksi dan mengekstraksi **Tanda Silang (X)** pada koordinat kotak jawaban sebagai input utama. Hasil dengan tingkat keyakinan (*confidence*) tinggi diterima secara otomatis; hasil dengan *confidence* rendah (misal: tanda ganda, tanda tidak jelas/tumpang tindih, atau di luar area kotak) harus ditampilkan sebagai usulan yang memerlukan konfirmasi manual operator sebelum disimpan. Operator tetap harus diberi opsi untuk meninjau ulang seluruh hasil scan (termasuk yang *confidence* tinggi) sebagai jaring pengaman tambahan. | MVP |
| **FR-24b** | Sistem harus mengimplementasikan fitur penghitung waktu mundur otomatis (timer) pada mode digital, yang memberikan peringatan visual pada 15 detik terakhir, dan mengeksekusi pengiriman data paksa (auto-submit) saat waktu mencapai `00:00`. | F2 |
| **FR-24c** | *(Diperbarui)* Sistem harus mendukung pengacakan urutan nomor soal (bukan pengacakan opsi jawaban A, B, C, D) dengan aturan sebagai berikut: **(a) Mode Online/Digital** — urutan soal diacak secara independen **per peserta**, sehingga dua peserta dalam sesi/kegiatan yang sama menerima isi soal identik namun urutan tampil berbeda, apabila fitur "Acak Urutan Soal" diaktifkan pada Bank Soal terkait; **(b) Mode Offline/Kertas** — urutan soal **tidak diacak** dan tetap mengikuti urutan baku pada template cetak, terlepas dari status *toggle* pengacakan pada Bank Soal. | F2 |
| **FR-28b** | Sistem harus menyediakan tombol aksi "Samakan dengan Pre-Test" pada antarmuka pembuatan Post-Test untuk menyalin struktur dan isi soal secara identik. | MVP |
| **FR-42** | Sistem harus membatasi tampilan tabel indeks (Lokasi dan Kegiatan) maksimal 10 entri per halaman (pagination). | MVP |
| **FR-43** | *(Diperbarui)* Sistem harus mewajibkan re-autentikasi menggunakan **password akun staf/operator yang sedang aktif (login)** — bukan password bersama/administratif terpisah — untuk setiap aksi penghapusan (Delete) pada data Kegiatan, Lokasi, dan Bank Soal. Tujuannya mencegah aksi hapus oleh pengguna lain pada sesi perangkat yang masih terbuka (misal: laptop yang ditinggal dalam keadaan login), sekaligus mencatat identitas akun yang melakukan penghapusan untuk keperluan jejak audit (*audit log*). | MVP |

---

### 2. Kebutuhan Non-Fungsional (Pembaruan Ketersediaan Luring)

| ID | Kebutuhan Non-Fungsional (NFR) |
|---|---|
| **NFR-04** | *(Diperbarui)* Antarmuka peserta digital (PWA) harus mampu menampung simpanan jawaban secara luring ke dalam *IndexedDB/LocalStorage* ketika koneksi jaringan terputus di tengah pengerjaan kuis, dan memiliki mekanisme sinkronisasi tertunda (background sync) yang dijalankan melalui Lapisan API Helper (lihat NFR-07) begitu koneksi pulih. |
| **NFR-07** | *(Diperbarui)* Pengelolaan antarmuka aplikasi harus mematuhi pemisahan berlapis (*Multi-layer Architecture*) tiga lapis: **(1)** elemen *Blade view* (tampilan), **(2)** logika komponen interaktif Vanilla JS (modul ES6, per domain fungsional), dan **(3)** operasi asinkron (API Helper) yang menjadi satu-satunya titik komunikasi ke peladen/API, termasuk penanganan sinkronisasi data luring. |

---

### 3. Kebutuhan Antarmuka Eksternal

| Jenis | Deskripsi |
|---|---|
| **Klien Peserta** | Peramban harus mendukung *Service Worker* untuk penembolokan (caching) aset UI agar tampilan kuis, *timer*, dan peringatan *SweetAlert2* tetap berfungsi tanpa akses peladen. |
| **Keamanan UI** | Modal peringatan konfirmasi destruktif (*delete*) diimplementasikan menggunakan lapisan peringatan interaktif yang terisolasi dari *DOM event* utama demi mencegah klik tidak disengaja. Modal ini menyertakan input password sesuai FR-43. |

---

### 4. Catatan Keluar Cakupan (Out of Scope untuk Revisi Ini)

- **Kode Join & QR Generation**: Sudah didefinisikan pada iterasi FR sebelumnya (v1.0/2.0) dan tidak diulang di dokumen ini — dibahas terpisah bila diperlukan pembaruan.
- **Pembedaan role "Admin" vs "Operator"**: Berdasarkan konfirmasi, sistem saat ini hanya mengenal tiga level akses — Super Admin, Staf/Operator (hak akses setara), dan Peserta (tanpa akun). Tidak ada pemisahan hak akses lebih lanjut antara "Admin" dan "Operator" pada revisi ini.
