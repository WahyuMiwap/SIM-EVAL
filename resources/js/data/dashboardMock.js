/**
 * dashboardMock.js
 * Mock Data Independen untuk UI Dashboard SIM-EVAL P2M BNN Kota Surabaya
 * 
 * Catatan: File ini murni data tiruan frontend tanpa dependensi database.
 * Saat backend siap diintegrasikan nanti, fungsi-fungsi di sini dapat
 * digantikan dengan endpoint API / Axios tanpa merombak struktur UI.
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

/**
 * Data Agenda Kegiatan Kalender (Multi-Bulan & Realistis BNN Surabaya)
 */
export const CALENDAR_EVENTS = [
    // September 2026
    {
        id: 1,
        date: '2026-09-05',
        color: '#22c55e',
        status: 'Selesai',
        badgeClass: 'badge-green',
        title: 'Sosialisasi P4GN SMAN 1 Surabaya',
        category: 'Pendidikan / Remaja',
        time: '08:30 - 11:30 WIB',
        location: 'Aula SMAN 1 Surabaya',
        participants: '65 Siswa',
        nGain: 0.78,
        kategoriGain: 'Tinggi',
        pretestAvg: 54.2,
        posttestAvg: 89.8
    },
    {
        id: 2,
        date: '2026-09-10',
        color: '#22c55e',
        status: 'Selesai',
        badgeClass: 'badge-green',
        title: 'Workshop Ketahanan Keluarga Anti Narkoba',
        category: 'Keluarga / Masyarakat',
        time: '09:00 - 12:00 WIB',
        location: 'Kec. Tegalsari Surabaya',
        participants: '40 Warga',
        nGain: 0.71,
        kategoriGain: 'Tinggi',
        pretestAvg: 48.5,
        posttestAvg: 85.1
    },
    {
        id: 3,
        date: '2026-09-14',
        color: '#06b6d4',
        status: 'Berlangsung',
        badgeClass: 'badge-cyan',
        title: 'Pembinaan & Edukasi Komunitas Pemuda Bersinar',
        category: 'Komunitas Pemuda',
        time: '13:00 - 15:30 WIB',
        location: 'Kel. Jambangan Surabaya',
        participants: '80 Pemuda',
        nGain: 0.68,
        kategoriGain: 'Sedang',
        pretestAvg: 52.0,
        posttestAvg: 84.6
    },
    {
        id: 4,
        date: '2026-09-17',
        color: '#f59e0b',
        status: 'Aktif',
        badgeClass: 'badge-yellow',
        title: 'Monitoring & Evaluasi Pelaksanaan P4GN Triwulan III',
        category: 'Internal BNN & Mitra',
        time: '09:30 - 12:00 WIB',
        location: 'Ruang Rapat BNN Kota Surabaya',
        participants: '25 Peserta',
        nGain: null,
        kategoriGain: '—',
        pretestAvg: null,
        posttestAvg: null
    },
    {
        id: 5,
        date: '2026-09-22',
        color: '#4361ee',
        status: 'Dijadwalkan',
        badgeClass: 'badge-blue',
        title: 'Sosialisasi Bahaya Narkoba Lingkungan Kerja PDAM Surya Sembada',
        category: 'Instansi BUMD',
        time: '08:00 - 12:00 WIB',
        location: 'Kantor Pusat PDAM Surabaya',
        participants: '50 Karyawan',
        nGain: null,
        kategoriGain: '—',
        pretestAvg: null,
        posttestAvg: null
    },
    {
        id: 6,
        date: '2026-09-28',
        color: '#4361ee',
        status: 'Dijadwalkan',
        badgeClass: 'badge-blue',
        title: 'Pemeriksaan & Deteksi Dini Tes Urin Berkala',
        category: 'Dunia Usaha / Swasta',
        time: '08:30 - 13:30 WIB',
        location: 'Kawasan Industri Rungkut Surabaya',
        participants: '120 Pekerja',
        nGain: null,
        kategoriGain: '—',
        pretestAvg: null,
        posttestAvg: null
    },

    // Oktober 2026
    {
        id: 7,
        date: '2026-10-08',
        color: '#4361ee',
        status: 'Dijadwalkan',
        badgeClass: 'badge-blue',
        title: 'Edukasi Bahaya Narkotika Kampus Bersinar — Unair Kampus B',
        category: 'Perguruan Tinggi',
        time: '09:00 - 12:00 WIB',
        location: 'Aula Fakultas Farmasi Unair',
        participants: '150 Mahasiswa',
        nGain: null,
        kategoriGain: '—'
    },
    {
        id: 8,
        date: '2026-10-19',
        color: '#4361ee',
        status: 'Dijadwalkan',
        badgeClass: 'badge-blue',
        title: 'Diseminasi Informasi P4GN Kelurahan Wonokromo',
        category: 'Masyarakat',
        time: '19:00 - 21:00 WIB',
        location: 'Balai RW 04 Kel. Wonokromo',
        participants: '60 Warga',
        nGain: null,
        kategoriGain: '—'
    },

    // November 2026
    {
        id: 9,
        date: '2026-11-10',
        color: '#4361ee',
        status: 'Dijadwalkan',
        badgeClass: 'badge-blue',
        title: 'Peringatan Hari Pahlawan: Deklarasi Remaja Surabaya Anti Narkoba',
        category: 'Pendidikan / Pemuda',
        time: '08:00 - 11:30 WIB',
        location: 'Taman Bungkul Surabaya',
        participants: '250 Siswa & Relawan',
        nGain: null,
        kategoriGain: '—'
    },

    // Desember 2026
    {
        id: 10,
        date: '2026-12-05',
        color: '#22c55e',
        status: 'Selesai',
        badgeClass: 'badge-green',
        title: 'Sosialisasi Bahaya Narkoba Akhir Semester — SMK N 2 Surabaya',
        category: 'Pendidikan / Remaja',
        time: '08:30 - 11:00 WIB',
        location: 'Auditorium SMK N 2 Surabaya',
        participants: '95 Siswa',
        nGain: 0.75,
        kategoriGain: 'Tinggi',
        pretestAvg: 50.0,
        posttestAvg: 87.5
    },
    {
        id: 11,
        date: '2026-12-12',
        color: '#f59e0b',
        status: 'Aktif',
        badgeClass: 'badge-yellow',
        title: 'Rapat Koordinasi Evaluasi Tahunan Relawan P4GN Surabaya',
        category: 'Masyarakat & Penggiat',
        time: '09:00 - 12:30 WIB',
        location: 'Gedung Graha Sawunggaling Surabaya',
        participants: '110 Peserta',
        nGain: 0.69,
        kategoriGain: 'Sedang',
        pretestAvg: 60.0,
        posttestAvg: 87.6
    },
    {
        id: 12,
        date: '2026-12-22',
        color: '#4361ee',
        status: 'Mendatang',
        badgeClass: 'badge-blue',
        title: 'Kampanye Terpadu Libur Nataru Bersinar (Bersih Narkoba)',
        category: 'Masyarakat Umum / Transportasi',
        time: '08:00 - 14:00 WIB',
        location: 'Terminal Purabaya & Stasiun Gubeng',
        participants: '350 Sasaran',
        nGain: null,
        kategoriGain: '—'
    }
];

/**
 * Data 5 Kegiatan Terkini
 */
export const RECENT_ACTIVITIES = [
    {
        id: 1,
        nama: 'SMAN 1 Surabaya',
        kategori_sasaran: 'Pelajar SMA',
        tanggal: '05 Sep 2026',
        tanggal_short: '05 Sep',
        peserta: 65,
        status: 'Selesai',
        status_badge: 'badge-green',
        n_gain: 0.78,
        n_gain_kategori: 'Tinggi',
        pretest_avg: 54.2,
        posttest_avg: 89.8
    },
    {
        id: 2,
        nama: 'Kec. Tegalsari',
        kategori_sasaran: 'Masyarakat Umum',
        tanggal: '10 Sep 2026',
        tanggal_short: '10 Sep',
        peserta: 40,
        status: 'Selesai',
        status_badge: 'badge-green',
        n_gain: 0.71,
        n_gain_kategori: 'Tinggi',
        pretest_avg: 48.5,
        posttest_avg: 85.1
    },
    {
        id: 3,
        nama: 'Kel. Jambangan',
        kategori_sasaran: 'Komunitas Pemuda',
        tanggal: '14 Sep 2026',
        tanggal_short: '14 Sep',
        peserta: 80,
        status: 'Berlangsung',
        status_badge: 'badge-cyan',
        n_gain: 0.68,
        n_gain_kategori: 'Sedang',
        pretest_avg: 52.0,
        posttest_avg: 84.6
    },
    {
        id: 4,
        nama: 'Aula PDAM Sby',
        kategori_sasaran: 'Pegawai BUMD',
        tanggal: '22 Sep 2026',
        tanggal_short: '22 Sep',
        peserta: 50,
        status: 'Dijadwalkan',
        status_badge: 'badge-gray',
        n_gain: null,
        n_gain_kategori: '—',
        pretest_avg: null,
        posttest_avg: null
    },
    {
        id: 5,
        nama: 'Lapas Kelas I Surabaya',
        kategori_sasaran: 'Warga Binaan',
        tanggal: '28 Sep 2026',
        tanggal_short: '28 Sep',
        peserta: 120,
        status: 'Dijadwalkan',
        status_badge: 'badge-gray',
        n_gain: null,
        n_gain_kategori: '—',
        pretest_avg: null,
        posttest_avg: null
    }
];

/**
 * Data Preset Metrik per Periode
 */
export const DASHBOARD_METRICS_DATA = {
    // Mode default: September 2026
    bulan_ini: {
        periodLabel: 'September 2026',
        kegiatan: {
            value: 14,
            unit: 'kegiatan',
            sub: 'kegiatan terlaksana',
            growth: '+16.7%',
            growthDir: 'up',
            growthLabel: 'vs bulan lalu'
        },
        peserta: {
            value: '1.248',
            unit: 'peserta',
            sub: 'peserta terdaftar',
            growth: '+23.4%',
            growthDir: 'up',
            growthLabel: 'vs bulan lalu'
        },
        nGain: {
            value: '0.72',
            unit: '',
            sub: 'skor efektivitas',
            kategori: 'Tinggi',
            badge: 'Efektif',
            badgeColor: 'badge-green',
            growth: '+0.06',
            growthDir: 'up',
            growthLabel: 'peningkatan pemahaman'
        },
        efektivitas: {
            value: '84.6%',
            unit: '',
            sub: 'tingkat kelulusan',
            kategori: 'Paham',
            growth: '+5.2%',
            growthDir: 'up',
            growthLabel: 'kategori paham/cukup'
        },
        distribusi: [
            { label: 'Paham / Tinggi (g ≥ 0.70)', count: 874, percent: 70.0, color: 'var(--success, #22c55e)' },
            { label: 'Cukup / Sedang (0.30 ≤ g < 0.70)', count: 288, percent: 23.1, color: 'var(--warning, #f59e0b)' },
            { label: 'Kurang / Rendah (g < 0.30)', count: 86, percent: 6.9, color: 'var(--danger, #ef4444)' }
        ]
    },

    // Mode: Akumulasi Sepanjang 2026
    tahun_ini: {
        periodLabel: 'Tahun 2026',
        kegiatan: {
            value: 48,
            unit: 'kegiatan',
            sub: 'kegiatan terlaksana tahun ini',
            growth: '+32.4%',
            growthDir: 'up',
            growthLabel: 'vs tahun 2025'
        },
        peserta: {
            value: '4.850',
            unit: 'peserta',
            sub: 'total penerima sosialisasi',
            growth: '+28.1%',
            growthDir: 'up',
            growthLabel: 'vs tahun 2025'
        },
        nGain: {
            value: '0.74',
            unit: '',
            sub: 'rata-rata N-Gain tahunan',
            kategori: 'Tinggi',
            badge: 'Efektif Tinggi',
            badgeColor: 'badge-green',
            growth: '+0.09',
            growthDir: 'up',
            growthLabel: 'konsistensi metode'
        },
        efektivitas: {
            value: '86.2%',
            unit: '',
            sub: 'tingkat efektivitas rata-rata',
            kategori: 'Paham',
            growth: '+6.4%',
            growthDir: 'up',
            growthLabel: 'pencapaian target IKU'
        },
        distribusi: [
            { label: 'Paham / Tinggi (g ≥ 0.70)', count: 3492, percent: 72.0, color: 'var(--success, #22c55e)' },
            { label: 'Cukup / Sedang (0.30 ≤ g < 0.70)', count: 1067, percent: 22.0, color: 'var(--warning, #f59e0b)' },
            { label: 'Kurang / Rendah (g < 0.30)', count: 291, percent: 6.0, color: 'var(--danger, #ef4444)' }
        ]
    },

    // Helper kalkulator dinamis saat memilih Bulan & Tahun tertentu
    getCustomData(monthIdx, year) {
        if (year === 2026 && monthIdx === 11) { // Desember 2026
            return {
                periodLabel: `Desember ${year}`,
                kegiatan: {
                    value: 19,
                    unit: 'kegiatan',
                    sub: 'kegiatan penutup tahun',
                    growth: '+21.0%',
                    growthDir: 'up',
                    growthLabel: 'vs Nov 2026'
                },
                peserta: {
                    value: '1.680',
                    unit: 'peserta',
                    sub: 'peserta terdaftar',
                    growth: '+34.6%',
                    growthDir: 'up',
                    growthLabel: 'evaluasi akhir tahun'
                },
                nGain: {
                    value: '0.75',
                    unit: '',
                    sub: 'skor efektivitas puncak',
                    kategori: 'Tinggi',
                    badge: 'Sangat Efektif',
                    badgeColor: 'badge-green',
                    growth: '+0.03',
                    growthDir: 'up',
                    growthLabel: 'pencapaian optimal'
                },
                efektivitas: {
                    value: '88.4%',
                    unit: '',
                    sub: 'pemahaman materi P4GN',
                    kategori: 'Paham',
                    growth: '+3.8%',
                    growthDir: 'up',
                    growthLabel: 'kelulusan peserta'
                },
                distribusi: [
                    { label: 'Paham / Tinggi (g ≥ 0.70)', count: 1260, percent: 75.0, color: 'var(--success, #22c55e)' },
                    { label: 'Cukup / Sedang (0.30 ≤ g < 0.70)', count: 336, percent: 20.0, color: 'var(--warning, #f59e0b)' },
                    { label: 'Kurang / Rendah (g < 0.30)', count: 84, percent: 5.0, color: 'var(--danger, #ef4444)' }
                ]
            };
        }

        // Formula mock realistis untuk bulan lainnya
        const baseKeg = 10 + (monthIdx % 6) + (year >= 2026 ? 2 : 0);
        const basePes = 820 + (monthIdx * 65) + (year >= 2026 ? 120 : 0);
        const rawGain = 0.65 + ((monthIdx % 5) * 0.025);
        const gainVal = rawGain.toFixed(2);
        const efektivitasVal = (78 + (monthIdx % 10)).toFixed(1) + '%';
        const pPaham = 68 + (monthIdx % 8);
        const pCukup = 24 - Math.floor((monthIdx % 8) / 2);
        const pKurang = 100 - pPaham - pCukup;

        return {
            periodLabel: `${MONTH_NAMES[monthIdx]} ${year}`,
            kegiatan: {
                value: baseKeg,
                unit: 'kegiatan',
                sub: 'kegiatan terlaksana',
                growth: '+12.5%',
                growthDir: 'up',
                growthLabel: 'periode terpilih'
            },
            peserta: {
                value: basePes.toLocaleString('id-ID'),
                unit: 'peserta',
                sub: 'peserta terevaluasi',
                growth: '+18.0%',
                growthDir: 'up',
                growthLabel: 'periode terpilih'
            },
            nGain: {
                value: gainVal,
                unit: '',
                sub: 'skor N-Gain rata-rata',
                kategori: parseFloat(gainVal) >= 0.7 ? 'Tinggi' : 'Sedang',
                badge: parseFloat(gainVal) >= 0.7 ? 'Efektif' : 'Cukup Efektif',
                badgeColor: parseFloat(gainVal) >= 0.7 ? 'badge-green' : 'badge-yellow',
                growth: '+0.04',
                growthDir: 'up',
                growthLabel: 'vs bulan sebelumnya'
            },
            efektivitas: {
                value: efektivitasVal,
                unit: '',
                sub: 'tingkat pemahaman',
                kategori: 'Paham',
                growth: '+3.1%',
                growthDir: 'up',
                growthLabel: 'evaluasi materi'
            },
            distribusi: [
                { label: 'Paham / Tinggi (g ≥ 0.70)', count: Math.round(basePes * (pPaham / 100)), percent: pPaham, color: 'var(--success, #22c55e)' },
                { label: 'Cukup / Sedang (0.30 ≤ g < 0.70)', count: Math.round(basePes * (pCukup / 100)), percent: pCukup, color: 'var(--warning, #f59e0b)' },
                { label: 'Kurang / Rendah (g < 0.30)', count: Math.round(basePes * (pKurang / 100)), percent: pKurang, color: 'var(--danger, #ef4444)' }
            ]
        };
    }
};

/**
 * Filter Events Kalender berdasarkan Tahun dan Bulan (0-indexed)
 */
export function getCalendarEventsForMonth(year, monthIndex) {
    return CALENDAR_EVENTS.filter(evt => {
        const parts = evt.date.split('-');
        const evtY = parseInt(parts[0], 10);
        const evtM = parseInt(parts[1], 10) - 1;
        return evtY === year && evtM === monthIndex;
    });
}

/**
 * Filter Events Kalender untuk Tanggal Spesifik (YYYY-MM-DD)
 */
export function getCalendarEventsForDate(year, monthIndex, day) {
    const key = `${year}-${String(monthIndex + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
    return CALENDAR_EVENTS.filter(evt => evt.date === key);
}
