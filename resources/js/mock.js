/**
 * mock.js — Unified Mock Data System SIM-EVAL P2M BNN Kota Surabaya
 * 
 * Modul ini menyediakan data tiruan realistis untuk seluruh antarmuka sistem:
 * 1. Dashboard (Metrik KPI, Kalender Agenda, Kegiatan Terkini, Distribusi N-Gain)
 * 2. Kegiatan (Daftar Kegiatan, Meja Kerja Detail, Peserta Pre/Post-Test, Rekap OMR)
 * 3. Master Lokasi (Daftar Lokasi Binaan Surabaya)
 * 4. Master Bank Soal (Paket Soal & Butir Pertanyaan Pilihan Ganda A/B/C/D)
 * 5. Tata Kelola Staf & Hak Akses (Super Admin, Operator, Magang)
 * 6. Peserta / PWA Exam Engine
 */

export const MONTH_NAMES = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

export const MONTH_NAMES_SHORT = [
    'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
    'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
];

export const DAY_NAMES = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
export const DAY_FULL_NAMES = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

// ─────────────────────────────────────────────────────────────────────────────
// 1. MASTER LOKASI MOCK
// ─────────────────────────────────────────────────────────────────────────────
export const MOCK_LOKASI = [
    { id: 1, nama_lokasi: 'SMAN 1 Surabaya', alamat: 'Jl. Wijaya Kusuma No. 48', kecamatan: 'Genteng', jenis_sasaran: 'sekolah', events_count: 3 },
    { id: 2, nama_lokasi: 'SMAN 5 Surabaya', alamat: 'Jl. Kusuma Bangsa No. 21', kecamatan: 'Genteng', jenis_sasaran: 'sekolah', events_count: 2 },
    { id: 3, nama_lokasi: 'SMKN 2 Surabaya', alamat: 'Jl. Tentara Genie Pelajar No. 26', kecamatan: 'Sawahan', jenis_sasaran: 'sekolah', events_count: 2 },
    { id: 4, nama_lokasi: 'Kecamatan Tegalsari', alamat: 'Jl. Tanggulangin No. 12', kecamatan: 'Tegalsari', jenis_sasaran: 'masyarakat', events_count: 2 },
    { id: 5, nama_lokasi: 'Kelurahan Jambangan', alamat: 'Jl. Jambangan No. 80', kecamatan: 'Jambangan', jenis_sasaran: 'masyarakat', events_count: 1 },
    { id: 6, nama_lokasi: 'Aula PDAM Surya Sembada', alamat: 'Jl. Mayjen Prof. Dr. Moestopo No. 2', kecamatan: 'Tambaksari', jenis_sasaran: 'instansi', events_count: 2 },
    { id: 7, nama_lokasi: 'Lapas Kelas I Surabaya', alamat: 'Jl. Pemasyarakatan No. 1, Porong', kecamatan: 'Porong', jenis_sasaran: 'lapas', events_count: 1 },
    { id: 8, nama_lokasi: 'Universitas Airlangga (Kampus B)', alamat: 'Jl. Dharmawangsa Dalam', kecamatan: 'Gubeng', jenis_sasaran: 'kampus', events_count: 1 },
    { id: 9, nama_lokasi: 'Kawasan Industri SIER', alamat: 'Jl. Rungkut Industri Raya', kecamatan: 'Rungkut', jenis_sasaran: 'instansi', events_count: 1 }
];

// ─────────────────────────────────────────────────────────────────────────────
// 2. MASTER BANK SOAL MOCK
// ─────────────────────────────────────────────────────────────────────────────
export const MOCK_BANK_SOAL = [
    {
        id: 1,
        nama_paket: 'Instrumen Pre-Test P4GN Remaja & Pelajar (SMA/SMK)',
        tipe: 'pretest',
        durasi: 30,
        deskripsi: 'Paket pengukuran awal tingkat pengetahuan bahaya narkoba, zat adiktif, dan sanksi hukum bagi pelajar.',
        acak_urutan: true,
        soal_count: 10,
        events_count: 4,
        questions: [
            { id: 1, nomor: 1, pertanyaan: 'Apa kepanjangan dari singkatan P4GN yang dicanangkan oleh BNN?', pilihan_a: 'Pencegahan, Pemberantasan, Penyalahgunaan dan Peredaran Gelap Narkotika', pilihan_b: 'Pengawasan, Penindakan, Pengobatan dan Rehabilitasi Narkotika', pilihan_c: 'Penyuluhan, Pemulihan, Penertiban dan Pengawasan Narkotika', pilihan_d: 'Pencegahan, Penuntutan, Pengadilan dan Pemasyarakatan Narkotika', kunci_jawaban: 'A', bobot: 10 },
            { id: 2, nomor: 2, pertanyaan: 'Zat atau obat yang dapat menurunkan kesadaran, menghilangkan rasa nyeri, dan menimbulkan ketergantungan disebut...', pilihan_a: 'Psikotropika', pilihan_b: 'Narkotika', pilihan_c: 'Zat Adiktif Non-Narkotika', pilihan_d: 'Minuman Keras', kunci_jawaban: 'B', bobot: 10 },
            { id: 3, nomor: 3, pertanyaan: 'Berikut ini yang BUKAN merupakan dampak buruk penyalahgunaan narkotika bagi kesehatan fisik adalah...', pilihan_a: 'Kerusakan organ hati dan ginjal', pilihan_b: 'Peningkatan konsentrasi dan stamina permanen', pilihan_c: 'Gangguan pada sistem saraf pusat', pilihan_d: 'Kerentanan tertular HIV/AIDS dan Hepatitis', kunci_jawaban: 'B', bobot: 10 },
            { id: 4, nomor: 4, pertanyaan: 'Berdasarkan Undang-Undang No. 35 Tahun 2009, Narkotika Golongan I hanya dapat digunakan untuk kepentingan...', pilihan_a: 'Pelayanan kesehatan rawat jalan', pilihan_b: 'Pengembangan ilmu pengetahuan dan teknologi serta reagensia diagnostik', pilihan_c: 'Pengobatan penyakit saraf stadium akhir', pilihan_d: 'Terapi psikologis dan penenang mandiri', kunci_jawaban: 'B', bobot: 10 },
            { id: 5, nomor: 5, pertanyaan: 'Tindakan awal yang paling tepat jika mengetahui teman sebaya menunjukkan gelagat penyalahgunaan narkoba adalah...', pilihan_a: 'Menjauhi dan menyebarkan rumor ke seluruh sekolah', pilihan_b: 'Melaporkan kepada guru BK atau petugas BNN untuk rehabilitasi', pilihan_c: 'Mencoba ikut mengonsumsi agar tahu rasanya', pilihan_d: 'Menghakimi secara sepihak di media sosial', kunci_jawaban: 'B', bobot: 10 },
            { id: 6, nomor: 6, pertanyaan: 'Layanan rehabilitasi BNN bagi korban penyalahguna narkotika bersifat...', pilihan_a: 'Dipidana dan dikenai denda kurungan', pilihan_b: 'Gratis, manusiawi, rahasia, dan tanpa hukuman pidana jika melapor sukarela', pilihan_c: 'Dipublikasikan ke khalayak ramai sebagai sanksi sosial', pilihan_d: 'Wajib membayar biaya rawat inap ratusan juta rupiah', kunci_jawaban: 'B', bobot: 10 },
            { id: 7, nomor: 7, pertanyaan: 'Narkotika jenis sabu-sabu (metamfetamin) termasuk dalam kategori stimulan yang bekerja dengan cara...', pilihan_a: 'Menekan kerja sistem saraf dan menimbulkan kantuk', pilihan_b: 'Mempercepat kerja sistem saraf pusat dan detak jantung', pilihan_c: 'Membuat pengguna melihat halusinasi yang tidak nyata', pilihan_d: 'Menstabilkan emosi secara alami', kunci_jawaban: 'B', bobot: 10 },
            { id: 8, nomor: 8, pertanyaan: 'Program Desa/Kelurahan Bersih Narkoba yang digalakkan BNN dikenal dengan istilah...', pilihan_a: 'Desa Berseri', pilihan_b: 'Kelurahan Bersinar', pilihan_c: 'Kampung Tangguh Sehat', pilihan_d: 'Lingkungan Madani', kunci_jawaban: 'B', bobot: 10 },
            { id: 9, nomor: 9, pertanyaan: 'Nomor call center darurat layanan pengaduan dan informasi BNN adalah...', pilihan_a: '110', pilihan_b: '112', pilihan_c: '184', pilihan_d: '119', kunci_jawaban: 'C', bobot: 10 },
            { id: 10, nomor: 10, pertanyaan: 'Faktor pemicu utama seorang remaja terjerumus dalam penyalahgunaan narkoba dari sisi psikososial adalah...', pilihan_a: 'Tekanan teman sebaya dan keinginan coba-coba', pilihan_b: 'Kecukupan gizi makanan keluarga', pilihan_c: 'Prestasi akademik yang terlalu tinggi', pilihan_d: 'Kegiatan ekstrakurikuler yang padat', kunci_jawaban: 'A', bobot: 10 }
        ]
    },
    {
        id: 2,
        nama_paket: 'Instrumen Post-Test P4GN Remaja & Pelajar (SMA/SMK)',
        tipe: 'posttest',
        durasi: 30,
        deskripsi: 'Paket evaluasi akhir setelah pemaparan materi sosialisasi bahaya narkoba untuk mengukur N-Gain efektivitas.',
        acak_urutan: true,
        soal_count: 10,
        events_count: 4,
        questions: [
            { id: 11, nomor: 1, pertanyaan: 'Apa kepanjangan dari singkatan P4GN yang dicanangkan oleh BNN?', pilihan_a: 'Pencegahan, Pemberantasan, Penyalahgunaan dan Peredaran Gelap Narkotika', pilihan_b: 'Pengawasan, Penindakan, Pengobatan dan Rehabilitasi Narkotika', pilihan_c: 'Penyuluhan, Pemulihan, Penertiban dan Pengawasan Narkotika', pilihan_d: 'Pencegahan, Penuntutan, Pengadilan dan Pemasyarakatan Narkotika', kunci_jawaban: 'A', bobot: 10 },
            { id: 12, nomor: 2, pertanyaan: 'Zat atau obat yang dapat menurunkan kesadaran, menghilangkan rasa nyeri, dan menimbulkan ketergantungan disebut...', pilihan_a: 'Psikotropika', pilihan_b: 'Narkotika', pilihan_c: 'Zat Adiktif Non-Narkotika', pilihan_d: 'Minuman Keras', kunci_jawaban: 'B', bobot: 10 },
            { id: 13, nomor: 3, pertanyaan: 'Berikut ini yang BUKAN merupakan dampak buruk penyalahgunaan narkotika bagi kesehatan fisik adalah...', pilihan_a: 'Kerusakan organ hati dan ginjal', pilihan_b: 'Peningkatan konsentrasi dan stamina permanen', pilihan_c: 'Gangguan pada sistem saraf pusat', pilihan_d: 'Kerentanan tertular HIV/AIDS dan Hepatitis', kunci_jawaban: 'B', bobot: 10 },
            { id: 14, nomor: 4, pertanyaan: 'Berdasarkan Undang-Undang No. 35 Tahun 2009, Narkotika Golongan I hanya dapat digunakan untuk kepentingan...', pilihan_a: 'Pelayanan kesehatan rawat jalan', pilihan_b: 'Pengembangan ilmu pengetahuan dan teknologi serta reagensia diagnostik', pilihan_c: 'Pengobatan penyakit saraf stadium akhir', pilihan_d: 'Terapi psikologis dan penenang mandiri', kunci_jawaban: 'B', bobot: 10 },
            { id: 15, nomor: 5, pertanyaan: 'Tindakan awal yang paling tepat jika mengetahui teman sebaya menunjukkan gelagat penyalahgunaan narkoba adalah...', pilihan_a: 'Menjauhi dan menyebarkan rumor ke seluruh sekolah', pilihan_b: 'Melaporkan kepada guru BK atau petugas BNN untuk rehabilitasi', pilihan_c: 'Mencoba ikut mengonsumsi agar tahu rasanya', pilihan_d: 'Menghakimi secara sepihak di media sosial', kunci_jawaban: 'B', bobot: 10 },
            { id: 16, nomor: 6, pertanyaan: 'Layanan rehabilitasi BNN bagi korban penyalahguna narkotika bersifat...', pilihan_a: 'Dipidana dan dikenai denda kurungan', pilihan_b: 'Gratis, manusiawi, rahasia, dan tanpa hukuman pidana jika melapor sukarela', pilihan_c: 'Dipublikasikan ke khalayak ramai sebagai sanksi sosial', pilihan_d: 'Wajib membayar biaya rawat inap ratusan juta rupiah', kunci_jawaban: 'B', bobot: 10 },
            { id: 17, nomor: 7, pertanyaan: 'Narkotika jenis sabu-sabu (metamfetamin) termasuk dalam kategori stimulan yang bekerja dengan cara...', pilihan_a: 'Menekan kerja sistem saraf dan menimbulkan kantuk', pilihan_b: 'Mempercepat kerja sistem saraf pusat dan detak jantung', pilihan_c: 'Membuat pengguna melihat halusinasi yang tidak nyata', pilihan_d: 'Menstabilkan emosi secara alami', kunci_jawaban: 'B', bobot: 10 },
            { id: 18, nomor: 8, pertanyaan: 'Program Desa/Kelurahan Bersih Narkoba yang digalakkan BNN dikenal dengan istilah...', pilihan_a: 'Desa Berseri', pilihan_b: 'Kelurahan Bersinar', pilihan_c: 'Kampung Tangguh Sehat', pilihan_d: 'Lingkungan Madani', kunci_jawaban: 'B', bobot: 10 },
            { id: 19, nomor: 9, pertanyaan: 'Nomor call center darurat layanan pengaduan dan informasi BNN adalah...', pilihan_a: '110', pilihan_b: '112', pilihan_c: '184', pilihan_d: '119', kunci_jawaban: 'C', bobot: 10 },
            { id: 20, nomor: 10, pertanyaan: 'Faktor pemicu utama seorang remaja terjerumus dalam penyalahgunaan narkoba dari sisi psikososial adalah...', pilihan_a: 'Tekanan teman sebaya dan keinginan coba-coba', pilihan_b: 'Kecukupan gizi makanan keluarga', pilihan_c: 'Prestasi akademik yang terlalu tinggi', pilihan_d: 'Kegiatan ekstrakurikuler yang padat', kunci_jawaban: 'A', bobot: 10 }
        ]
    },
    {
        id: 3,
        nama_paket: 'Evaluasi Ketahanan Keluarga Anti Narkoba',
        tipe: 'kombinasi',
        durasi: 25,
        deskripsi: 'Paket evaluasi komprehensif peran orang tua dan keluarga dalam mendeteksi dan mencegah narkoba sejak dini.',
        acak_urutan: false,
        soal_count: 8,
        events_count: 2,
        questions: []
    },
    {
        id: 4,
        nama_paket: 'Sosialisasi Lingkungan Kerja Bebas Narkoba (BUMD/Swasta)',
        tipe: 'kombinasi',
        durasi: 30,
        deskripsi: 'Evaluasi kesadaran K3 dan regulasi P4GN di lingkungan instansi dan korporasi.',
        acak_urutan: true,
        soal_count: 10,
        events_count: 2,
        questions: []
    }
];

// ─────────────────────────────────────────────────────────────────────────────
// 3. DAFTAR KEGIATAN & REKAP EVALUASI MOCK
// ─────────────────────────────────────────────────────────────────────────────
export const MOCK_KEGIATAN = [
    {
        id: 1,
        nama_kegiatan: 'Sosialisasi P4GN SMAN 1 Surabaya',
        kode_join: 'SMAN01',
        status: 'selesai',
        tanggal: '2026-09-05',
        durasi_menit: 30,
        catatan: 'Sosialisasi tatap muka interaktif diikuti 65 peserta siswa kelas X.',
        lokasi_id: 1,
        lokasi: MOCK_LOKASI[0],
        pretest_package_id: 1,
        posttest_package_id: 2,
        pretestPackage: MOCK_BANK_SOAL[0],
        posttestPackage: MOCK_BANK_SOAL[1],
        peserta_count: 65,
        avg_gain: 0.78,
        kategori_gain: 'Tinggi',
        stats: { total: 65, avg_pre: 54.2, avg_post: 89.8, avg_gain: 0.78, selesai_count: 65 }
    },
    {
        id: 2,
        nama_kegiatan: 'Workshop Ketahanan Keluarga Anti Narkoba Kec. Tegalsari',
        kode_join: 'TGLSRI',
        status: 'selesai',
        tanggal: '2026-09-10',
        durasi_menit: 30,
        catatan: 'Pemberdayaan masyarakat RW 01 s.d. RW 05 Kecamatan Tegalsari.',
        lokasi_id: 4,
        lokasi: MOCK_LOKASI[3],
        pretest_package_id: 3,
        posttest_package_id: 3,
        pretestPackage: MOCK_BANK_SOAL[2],
        posttestPackage: MOCK_BANK_SOAL[2],
        peserta_count: 40,
        avg_gain: 0.71,
        kategori_gain: 'Tinggi',
        stats: { total: 40, avg_pre: 48.5, avg_post: 85.1, avg_gain: 0.71, selesai_count: 40 }
    },
    {
        id: 3,
        nama_kegiatan: 'Pembinaan Komunitas Pemuda Bersinar Kel. Jambangan',
        kode_join: 'JMBG03',
        status: 'berlangsung',
        tanggal: '2026-09-14',
        durasi_menit: 35,
        catatan: 'Meja kerja sedang aktif untuk penginputan lembar OMR peserta.',
        lokasi_id: 5,
        lokasi: MOCK_LOKASI[4],
        pretest_package_id: 1,
        posttest_package_id: 2,
        pretestPackage: MOCK_BANK_SOAL[0],
        posttestPackage: MOCK_BANK_SOAL[1],
        peserta_count: 80,
        avg_gain: 0.68,
        kategori_gain: 'Sedang',
        stats: { total: 80, avg_pre: 52.0, avg_post: 84.6, avg_gain: 0.68, selesai_count: 45 }
    },
    {
        id: 4,
        nama_kegiatan: 'Sosialisasi Bahaya Narkoba Lingkungan Kerja PDAM Surya Sembada',
        kode_join: 'PDAM04',
        status: 'dijadwalkan',
        tanggal: '2026-09-22',
        durasi_menit: 30,
        catatan: 'Diselenggarakan di Aula Lantai 3 PDAM Surabaya.',
        lokasi_id: 6,
        lokasi: MOCK_LOKASI[5],
        pretest_package_id: 4,
        posttest_package_id: 4,
        pretestPackage: MOCK_BANK_SOAL[3],
        posttestPackage: MOCK_BANK_SOAL[3],
        peserta_count: 50,
        avg_gain: null,
        kategori_gain: '—',
        stats: { total: 50, avg_pre: 0, avg_post: 0, avg_gain: 0, selesai_count: 0 }
    },
    {
        id: 5,
        nama_kegiatan: 'Deteksi Dini & Pembinaan Warga Binaan Lapas Kelas I Surabaya',
        kode_join: 'LAPS05',
        status: 'dijadwalkan',
        tanggal: '2026-09-28',
        durasi_menit: 45,
        catatan: 'Penyuluhan dan skrining berkala P2M.',
        lokasi_id: 7,
        lokasi: MOCK_LOKASI[6],
        pretest_package_id: 1,
        posttest_package_id: 2,
        pretestPackage: MOCK_BANK_SOAL[0],
        posttestPackage: MOCK_BANK_SOAL[1],
        peserta_count: 120,
        avg_gain: null,
        kategori_gain: '—',
        stats: { total: 120, avg_pre: 0, avg_post: 0, avg_gain: 0, selesai_count: 0 }
    }
];

// ─────────────────────────────────────────────────────────────────────────────
// 4. PESERTA MEJA KERJA DETAIL MOCK (Realistis SMAN 1 Surabaya)
// ─────────────────────────────────────────────────────────────────────────────
export const MOCK_PARTICIPANTS = [
    { id: 101, event_id: 1, name: 'Aditya Pratama Putra', class_grade: 'X MIPA 1', pretest_score: 50, posttest_score: 90, n_gain: 0.80, status: 'selesai', input_method: 'omr' },
    { id: 102, event_id: 1, name: 'Aisyah Nur Rahmawati', class_grade: 'X MIPA 1', pretest_score: 60, posttest_score: 100, n_gain: 1.00, status: 'selesai', input_method: 'online' },
    { id: 103, event_id: 1, name: 'Bima Satria Wicaksana', class_grade: 'X MIPA 1', pretest_score: 40, posttest_score: 80, n_gain: 0.67, status: 'selesai', input_method: 'manual' },
    { id: 104, event_id: 1, name: 'Cantika Dewi Lestari', class_grade: 'X MIPA 2', pretest_score: 70, posttest_score: 100, n_gain: 1.00, status: 'selesai', input_method: 'omr' },
    { id: 105, event_id: 1, name: 'Dimas Bagus Pangestu', class_grade: 'X MIPA 2', pretest_score: 50, posttest_score: 80, n_gain: 0.60, status: 'selesai', input_method: 'manual' },
    { id: 106, event_id: 1, name: 'Eka Putri Handayani', class_grade: 'X IPS 1', pretest_score: 60, posttest_score: 90, n_gain: 0.75, status: 'selesai', input_method: 'omr' },
    { id: 107, event_id: 1, name: 'Fajar Nugraha Ramadhan', class_grade: 'X IPS 1', pretest_score: 30, posttest_score: 70, n_gain: 0.57, status: 'selesai', input_method: 'omr' },
    { id: 108, event_id: 1, name: 'Gita Permata Sari', class_grade: 'X IPS 2', pretest_score: 60, posttest_score: 90, n_gain: 0.75, status: 'selesai', input_method: 'online' },
    { id: 109, event_id: 1, name: 'Hafizh Al-Farizi', class_grade: 'X IPS 2', pretest_score: 50, posttest_score: 90, n_gain: 0.80, status: 'selesai', input_method: 'manual' },
    { id: 110, event_id: 1, name: 'Indah Kusuma Wardani', class_grade: 'X MIPA 3', pretest_score: 70, posttest_score: 100, n_gain: 1.00, status: 'selesai', input_method: 'omr' },
    { id: 111, event_id: 1, name: 'Jovan Ardiansyah', class_grade: 'X MIPA 3', pretest_score: 40, posttest_score: 80, n_gain: 0.67, status: 'selesai', input_method: 'omr' },
    { id: 112, event_id: 1, name: 'Keisha Amanda Putri', class_grade: 'X MIPA 4', pretest_score: 60, posttest_score: 90, n_gain: 0.75, status: 'selesai', input_method: 'manual' },
    { id: 113, event_id: 1, name: 'Lukman Hakim', class_grade: 'X MIPA 4', pretest_score: 50, posttest_score: 80, n_gain: 0.60, status: 'selesai', input_method: 'omr' },
    { id: 114, event_id: 1, name: 'Maya Anggraini', class_grade: 'X IPS 3', pretest_score: 60, posttest_score: 100, n_gain: 1.00, status: 'selesai', input_method: 'online' },
    { id: 115, event_id: 1, name: 'Naufal Rizky Fauzan', class_grade: 'X IPS 3', pretest_score: 50, posttest_score: 90, n_gain: 0.80, status: 'selesai', input_method: 'manual' }
];

// ─────────────────────────────────────────────────────────────────────────────
// 5. TATA KELOLA STAF & HAK AKSES MOCK
// ─────────────────────────────────────────────────────────────────────────────
export const MOCK_STAF = [
    {
        id: 1,
        name: 'Super Admin BNN',
        email: 'superadmin@bnn.go.id',
        role: 'superadmin',
        nip: '197905142005011002',
        jabatan: 'Kepala Seksi P2M BNN Kota Surabaya',
        is_active: true,
        last_login_at: '2026-09-18 14:20:15'
    },
    {
        id: 2,
        name: 'Wahyu Prasetyo',
        email: 'wahyu@bnnsurabaya.go.id',
        role: 'operator',
        nip: '199208212019031005',
        jabatan: 'Penyuluh Narkoba Ahli Pertama',
        is_active: true,
        last_login_at: '2026-09-18 15:10:00'
    },
    {
        id: 3,
        name: 'Rizky Maulana (Magang)',
        email: 'rizky.magang@mhs.unair.ac.id',
        role: 'magang',
        nip: 'NIM. 152111513042',
        jabatan: 'Mahasiswa Magang Sistem Informasi',
        is_active: true,
        last_login_at: '2026-09-18 13:45:00'
    },
    {
        id: 4,
        name: 'Siti Aminah, S.Sos.',
        email: 'siti.aminah@bnnsurabaya.go.id',
        role: 'operator',
        nip: '199411032020122008',
        jabatan: 'Pengelola Evaluasi Program P2M',
        is_active: true,
        last_login_at: '2026-09-17 16:30:20'
    },
    {
        id: 5,
        name: 'Bayu Irawan',
        email: 'bayu.magang@mhs.its.ac.id',
        role: 'magang',
        nip: 'NIM. 5025211024',
        jabatan: 'Mahasiswa Magang Teknik Informatika',
        is_active: false,
        last_login_at: '2026-09-12 11:00:00'
    }
];

export const MOCK_STAF_COUNTS = {
    total: 5,
    superadmin: 1,
    operator: 2,
    magang: 2
};

// Ekspor objek terpadu untuk penggunaan di window
const SIMEVAL_MOCK = {
    MOCK_LOKASI,
    MOCK_BANK_SOAL,
    MOCK_KEGIATAN,
    MOCK_PARTICIPANTS,
    MOCK_STAF,
    MOCK_STAF_COUNTS,
    MONTH_NAMES,
    MONTH_NAMES_SHORT,
    DAY_NAMES,
    DAY_FULL_NAMES
};

export default SIMEVAL_MOCK;
