# DOKUMEN SPESIFIKASI & PANDUAN PENGEMBANGAN SISTEM INFORMASI SIM-EVAL P2M
**Badan Narkotika Nasional (BNN) Kota Surabaya**

---

## 1. Tahap Pengembangan Konseptual (Tahap 1 s.d. 4)

### Tahap 1: Discovery / Problem Understanding (Penemuan Masalah)
*   **Kondisi Lapangan (As-Is):** Evaluasi sosialisasi pencegahan narkoba (P4GN) dilakukan dengan membagikan kertas sampling pilihan ganda (10–20 lembar per kegiatan) kepada audiens (pelajar SD, SMP, SMA, atau warga binaan).
*   **Akar Permasalahan (Pain Points):**
    *   Rekapitulasi nilai lembar pre-test dan post-test dilakukan secara manual oleh staf atau anak magang di kantor BNN menggunakan Microsoft Excel.
    *   Penghitungan skor efektivitas sosialisasi (N-Gain) memakan waktu karena harus dihitung dan dirumuskan satu per satu per baris siswa.
    *   Risiko salah ketik nama peserta (typo) tinggi saat mencocokkan kertas pre-test dengan kertas post-test.
*   **Tujuan Sistem (To-Be):** Membangun sistem web terintegrasi untuk mendigitalisasi input data evaluasi, mengotomatisasi kalkulasi nilai dan N-Gain, serta menghasilkan dokumen ekspor Excel yang susunan sel dan kolomnya 100% kompatibel dengan format pelaporan Diktari BNN.

### Tahap 2: Requirement Analysis (Spesifikasi Kebutuhan)
*   **Modul Bank Soal:** Fasilitas untuk menyusun instrumen evaluasi pilihan ganda (A/B/C/D) tanpa pembobotan rumit yang dapat disimpan dan digunakan berulang kali di berbagai kegiatan berbeda.
*   **Modul Kegiatan (Event):** Manajemen kegiatan yang menautkan jadwal, lokasi sosialisasi, paket soal pre-test, dan paket soal post-test.
*   **Antarmuka Input Ganda (Hybrid Data Entry):**
    *   *Tabel Interaktif (Utama):* Grid pengetikan nilai mirip spreadsheet dengan navigasi tombol Enter untuk efisiensi pengetikan manual anak magang di kantor.
    *   *Scanner OMR Full-Screen (Pendukung):* Modal pemindai kamera berbasis peramban untuk membaca lembar kertas tanda silang (X) secara beruntun (continuous scan) saat lembar tes direkap di kantor.
*   **Dukungan PWA Dasbor Staf:** Antarmuka responsif ramah seluler dilengkapi hamburger menu agar operator dapat memantau jadwal dan statistik dari ponsel pintar di lapangan.
*   **Keamanan Destruktif (FR-43):** Mengharuskan verifikasi ulang kata sandi pengguna aktif saat melakukan tindakan penghapusan data penting (kegiatan, lokasi, atau paket soal).

### Tahap 3: Scope & Project Planning (Ruang Lingkup & Perencanaan)
*   **Fase 1 (MVP - Operasional Dasar):**
    *   Implementasi kerangka PWA dasbor staf dan tata letak responsif.
    *   Modul Manajemen Lokasi dan Bank Soal.
    *   Modul Kegiatan dengan antarmuka Tabel Input Manual interaktif.
    *   Kalkulasi otomatis N-Gain dan modul Ekspor Laporan Excel format Diktari.
*   **Fase 2 (Automasi Pemindaian Kantor):**
    *   Integrasi pustaka pemrosesan citra (OpenCV.js) dengan metode pemuatan tertunda (lazy loading).
    *   Implementasi modal Continuous Scan kamera dengan umpan balik audio beep dan tab cadangan unggah foto lembar kertas.
*   **Fase 3 (Engine Digital Peserta):**
    *   Pengembangan PWA Klien untuk ujian online serentak di lokasi sosialisasi perkotaan.
    *   Mekanisme Sandwich Workflow (Ruang Tunggu #1 -> Pre-Test -> Ruang Tunggu #2 -> Post-Test) dan pengacakan urutan nomor butir soal per peserta.

### Tahap 4: System Analysis & Pemodelan Basis Data (ERD)
*   **Skema Relasional Data:**
    *   USERS -> EVENTS: Relasi pembuat kegiatan (created_by).
    *   LOCATIONS -> EVENTS: Relasi lokasi penyelenggaraan sosialisasi.
    *   QUESTION_PACKAGES -> QUESTIONS: Satu paket menampung banyak butir soal pilihan ganda.
    *   QUESTION_PACKAGES -> EVENTS: Satu kegiatan memilih satu paket pre-test dan satu paket post-test.
    *   EVENTS -> PARTICIPANTS: Satu kegiatan memiliki banyak catatan peserta evaluasi.
    *   PARTICIPANTS -> PARTICIPANT_ANSWERS: Detail jawaban per butir soal (khusus mode OMR/Online).
*   **Struktur Tabel Inti PARTICIPANTS:**
    *   id (BigInt PK), event_id (FK), name (String), class_grade (String, nullable).
    *   pretest_score (Decimal), posttest_score (Decimal), n_gain (Decimal).
    *   input_method (Enum: 'manual', 'omr', 'online').
    *   *Kunci Efisiensi:* Nama peserta dan kedua nilai tersimpan dalam satu baris data, menghindari duplikasi pengetikan nama dan mencegah inkonsistensi pencocokan data.
    *   *Logika Kalkulasi:* Nilai N-Gain dihitung secara otomatis melalui Model Observer Laravel pada siklus penyimpanan (saving event).

```text
 [QUESTION_PACKAGES] 1───* [QUESTIONS]
         │ (1:N)
         ├──────────────────────────┐ (pretest_paket & posttest_paket)
         ▼                          ▼
     [LOCATIONS] 1─────* [EVENTS] *─────1 [USERS]
                            │ (1:N)
                            ▼
                    [PARTICIPANTS] 1─────* [PARTICIPANT_ANSWERS] *─────1 [QUESTIONS]