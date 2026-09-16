<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

// ─────────────────────────────────────────────────────────────────────────────
//  ROOT — redirect ke halaman kegiatan
// ─────────────────────────────────────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('operator.dashboard'));

// ─────────────────────────────────────────────────────────────────────────────
//  HELPER: buat LengthAwarePaginator dari array data mock
// ─────────────────────────────────────────────────────────────────────────────
function makePaginator(array $items, int $perPage = 10): LengthAwarePaginator
{
    $page       = request()->get('page', 1);
    $collection = Collection::make($items);
    $sliced     = $collection->slice(($page - 1) * $perPage, $perPage)->values();
    return new LengthAwarePaginator(
        $sliced, $collection->count(), $perPage, $page,
        ['path' => request()->url(), 'query' => request()->query()]
    );
}

// ─────────────────────────────────────────────────────────────────────────────
//  OPERATOR PANEL
// ─────────────────────────────────────────────────────────────────────────────
Route::prefix('operator')->name('operator.')->group(function () {

    // Dashboard
    Route::get('/dashboard', fn() => view('operator.dashboard'))->name('dashboard');

    // Profil
    Route::get('/profil', fn() => view('operator.profil.index'))->name('profile');

    // ── Kegiatan ──────────────────────────────────────────
    Route::prefix('kegiatan')->name('kegiatan.')->group(function () {

        // MOCK DATA: 3 kegiatan contoh
        $mockKegiatan = [
            (object)[
                'id'            => 1,
                'nama_kegiatan' => 'Sosialisasi Anti Narkoba — SMA N 5 Surabaya',
                'kode_join'     => 'AB1C2D',
                'status'        => 'jeda',
                'mode'          => 'digital',
                'tanggal'       => '2026-09-10',
                'durasi_menit'  => 30,
                'jumlah_peserta'=> 42,
                'lokasi'        => (object)['nama_lokasi' => 'SMA N 5 Surabaya'],
            ],
            (object)[
                'id'            => 2,
                'nama_kegiatan' => 'Sosialisasi P4GN — SMAN 12 Surabaya',
                'kode_join'     => 'XY9Z8W',
                'status'        => 'selesai',
                'mode'          => 'kertas',
                'tanggal'       => '2026-09-08',
                'durasi_menit'  => 45,
                'jumlah_peserta'=> 35,
                'lokasi'        => (object)['nama_lokasi' => 'SMAN 12 Surabaya'],
            ],
            (object)[
                'id'            => 3,
                'nama_kegiatan' => 'Sosialisasi Narkoba — Lapas Kelas I Surabaya',
                'kode_join'     => 'LP3K4X',
                'status'        => 'menunggu',
                'mode'          => 'kertas',
                'tanggal'       => '2026-09-15',
                'durasi_menit'  => 30,
                'jumlah_peserta'=> 0,
                'lokasi'        => (object)['nama_lokasi' => 'Lapas Kelas I Surabaya'],
            ],
        ];

        $mockLokasi = [
            (object)['id' => 1, 'nama_lokasi' => 'SMA N 5 Surabaya'],
            (object)['id' => 2, 'nama_lokasi' => 'SMAN 12 Surabaya'],
            (object)['id' => 3, 'nama_lokasi' => 'Lapas Kelas I Surabaya'],
        ];

        $mockSoalList = [
            (object)['id' => 1, 'nama_paket' => 'Pre-Test Anti Narkoba Umum'],
            (object)['id' => 2, 'nama_paket' => 'Post-Test Anti Narkoba Umum'],
        ];

        Route::get('/', function () use ($mockKegiatan, $mockLokasi, $mockSoalList) {
            return view('operator.kegiatan.index', [
                'kegiatan'     => makePaginator($mockKegiatan),
                'stats'        => ['total' => 3, 'aktif' => 1, 'totalPeserta' => 77, 'avgNGain' => '0.62'],
                'lokasiList'   => $mockLokasi,
                'bankSoalList' => $mockSoalList,
            ]);
        })->name('index');

        Route::post('/', fn() => back()->with('success', 'Kegiatan berhasil ditambahkan.'))->name('store');

        Route::get('/{id}', function ($id) {
            return view('operator.kegiatan.detail', [
                'kegiatan' => (object)[
                    'id'            => $id,
                    'nama_kegiatan' => 'Sosialisasi Anti Narkoba — SMA N 5 Surabaya',
                    'kode_join'     => 'AB1C2D',
                    'status'        => 'jeda',
                    'mode'          => 'digital',
                    'tanggal'       => '2026-09-10',
                    'durasi_menit'  => 30,
                    'peserta_count' => 42,
                    'lokasi'        => (object)['nama_lokasi' => 'SMA N 5 Surabaya'],
                ],
            ]);
        })->name('detail');

        Route::get('/{id}/edit',    fn($id) => response()->json(['id' => $id, 'nama_kegiatan' => 'Sosialisasi Demo', 'lokasi_id' => 1, 'tanggal' => '2026-09-10', 'durasi_menit' => 30, 'catatan' => '']))->name('edit');
        Route::put('/{id}',         fn($id) => back())->name('update');
        Route::delete('/{id}',      fn($id) => response()->json(['success' => true]))->name('destroy');
        Route::get('/{id}/export',  fn($id) => back())->name('export');
    });

    // ── Lokasi ────────────────────────────────────────────
    Route::prefix('lokasi')->name('lokasi.')->group(function () {

        $mockLokasi = [
            (object)['id' => 1, 'nama_lokasi' => 'SMA N 5 Surabaya',       'alamat' => 'Jl. Pemuda No. 5',         'kecamatan' => 'Genteng',   'jenis_sasaran' => 'sekolah', 'kegiatan_count' => 5],
            (object)['id' => 2, 'nama_lokasi' => 'SMAN 12 Surabaya',       'alamat' => 'Jl. Semolowaru No. 45',    'kecamatan' => 'Sukolilo',  'jenis_sasaran' => 'sekolah', 'kegiatan_count' => 3],
            (object)['id' => 3, 'nama_lokasi' => 'Lapas Kelas I Surabaya', 'alamat' => 'Jl. Raya Medaeng No. 1',  'kecamatan' => 'Waru',      'jenis_sasaran' => 'lapas',   'kegiatan_count' => 2],
            (object)['id' => 4, 'nama_lokasi' => 'SMA Hang Tuah 1 Sby',    'alamat' => 'Jl. Balongsari Tama',     'kecamatan' => 'Asemrowo',  'jenis_sasaran' => 'sekolah', 'kegiatan_count' => 1],
        ];

        Route::get('/', fn() => view('operator.lokasi.index', ['lokasi' => makePaginator($mockLokasi)]))->name('index');
        Route::post('/', fn() => back())->name('store');
        Route::get('/{id}/edit', fn($id) => response()->json(['id' => $id, 'nama_lokasi' => 'Lokasi Demo', 'alamat' => 'Jl. Demo', 'kecamatan' => 'Kec. Demo', 'jenis_sasaran' => 'sekolah']))->name('edit');
        Route::put('/{id}',    fn($id) => back())->name('update');
        Route::delete('/{id}', fn($id) => response()->json(['success' => true]))->name('destroy');
    });

    // ── Bank Soal ─────────────────────────────────────────
    Route::prefix('bank-soal')->name('bank-soal.')->group(function () {

        $mockQuestions = [
            (object)[
                'id' => 1,
                'pertanyaan' => 'Apa yang dimaksud dengan Narkoba menurut Undang-Undang Republik Indonesia?',
                'opsi' => [
                    'A' => 'Zat atau obat alami/sintetis yang menyebabkan penurunan kesadaran, hilangnya rasa nyeri, dan ketergantungan',
                    'B' => 'Minuman berenergi yang aman dikonsumsi setiap hari tanpa resep dokter',
                    'C' => 'Suplemen makanan untuk meningkatkan stamina tubuh saat berolahraga',
                    'D' => 'Obat penenang yang dapat dibeli bebas di warung kelontong'
                ],
                'kunci' => 'A'
            ],
            (object)[
                'id' => 2,
                'pertanyaan' => 'Undang-Undang Republik Indonesia yang mengatur secara komprehensif tentang Narkotika adalah...',
                'opsi' => [
                    'A' => 'UU No. 35 Tahun 2009',
                    'B' => 'UU No. 22 Tahun 1997',
                    'C' => 'UU No. 5 Tahun 1997',
                    'D' => 'UU No. 36 Tahun 2009'
                ],
                'kunci' => 'A'
            ],
            (object)[
                'id' => 3,
                'pertanyaan' => 'Dampak negatif penyalahgunaan narkotika terhadap kesehatan mental dan psikologis seseorang meliputi...',
                'opsi' => [
                    'A' => 'Meningkatkan rasa percaya diri secara stabil dan permanen',
                    'B' => 'Halusinasi, gangguan kecemasan berat, depresi, dan hilangnya kendali emosi',
                    'C' => 'Meningkatkan konsentrasi belajar dan daya ingat jangka panjang',
                    'D' => 'Membuat pola tidur dan metabolisme tubuh menjadi lebih teratur'
                ],
                'kunci' => 'B'
            ],
            (object)[
                'id' => 4,
                'pertanyaan' => 'Program P4GN yang dicanangkan oleh BNN merupakan singkatan dari...',
                'opsi' => [
                    'A' => 'Pencegahan dan Pemberantasan Penyalahgunaan dan Peredaran Gelap Narkotika',
                    'B' => 'Pengawasan dan Pengendalian Penggunaan Narkoba Generasi Nasional',
                    'C' => 'Penyuluhan dan Pembinaan Pemuda Gerakan Nasional Anti Narkoba',
                    'D' => 'Pusat Pelayanan Pengaduan Narkoba dan Rehabilitasi Mandiri'
                ],
                'kunci' => 'A'
            ],
            (object)[
                'id' => 5,
                'pertanyaan' => 'Jika Anda menemukan indikasi penyalahgunaan narkoba pada anggota keluarga atau teman sebaya, tindakan awal yang paling tepat adalah...',
                'opsi' => [
                    'A' => 'Mengasingkannya dari lingkungan pergaulan agar tidak menulari orang lain',
                    'B' => 'Melaporkan ke Institusi Penerima Wajib Lapor (IPWL) atau BNN untuk mendapatkan layanan rehabilitasi',
                    'C' => 'Menghakiminya bersama masyarakat sekitar secara terbuka',
                    'D' => 'Mendiamkan masalah tersebut karena dianggap urusan pribadi masing-masing'
                ],
                'kunci' => 'B'
            ],
            (object)[
                'id' => 6,
                'pertanyaan' => 'Bagian tubuh yang paling rentan mengalami kerusakan permanen akibat konsumsi zat narkotika stimulan adalah...',
                'opsi' => [
                    'A' => 'Sistem saraf pusat dan jaringan otak',
                    'B' => 'Kuku dan rambut',
                    'C' => 'Jaringan kulit luar',
                    'D' => 'Tulang dan persendian kaki'
                ],
                'kunci' => 'A'
            ],
            (object)[
                'id' => 7,
                'pertanyaan' => 'Apakah seorang penyalahguna narkoba yang dengan sukarela melapor ke BNN / IPWL untuk rehabilitasi akan dijatuhi hukuman pidana penjara?',
                'opsi' => [
                    'A' => 'Ya, tetap langsung dipenjara minimal 4 tahun',
                    'B' => 'Tidak dipidana, melainkan mendapatkan hak rehabilitasi medis dan sosial sesuai UU No. 35 Tahun 2009',
                    'C' => 'Ya, tetapi hukumannya dipotong setengah',
                    'D' => 'Tergantung persetujuan dari pihak kepolisian setempat'
                ],
                'kunci' => 'B'
            ],
            (object)[
                'id' => 8,
                'pertanyaan' => 'Berikut yang merupakan salah satu faktor pencegahan protektif utama dalam membentengi remaja dari bahaya narkoba adalah...',
                'opsi' => [
                    'A' => 'Komunikasi keluarga yang terbuka, harmonis, serta keterlibatan aktif dalam kegiatan positif',
                    'B' => 'Memberikan kebebasan pergaulan tanpa batas waktu di luar rumah',
                    'C' => 'Mengikuti tren pergaulan tanpa menyaring pengaruh buruk kelompok teman',
                    'D' => 'Mencoba zat baru untuk membuktikan keberanian di hadapan teman'
                ],
                'kunci' => 'A'
            ],
            (object)[
                'id' => 9,
                'pertanyaan' => 'Zat adiktif yang terkandung dalam rokok dan tembakau yang dapat menyebabkan ketergantungan fisik dan psikologis adalah...',
                'opsi' => [
                    'A' => 'Nikotin',
                    'B' => 'Kafein',
                    'C' => 'Glukosa',
                    'D' => 'Kalsium'
                ],
                'kunci' => 'A'
            ],
            (object)[
                'id' => 10,
                'pertanyaan' => 'Peran aktif masyarakat dalam mendukung program P4GN di lingkungan tempat tinggal dapat diwujudkan melalui...',
                'opsi' => [
                    'A' => 'Membentuk Relawan/Penggiat Anti Narkoba dan menciptakan lingkungan Desa/Kelurahan Bersinar (Bersih Narkoba)',
                    'B' => 'Menolak kehadiran petugas sosialisasi BNN di wilayah pemukiman',
                    'C' => 'Menyembunyikan informasi jika ada pengedar narkoba di lingkungannya',
                    'D' => 'Memasang tarif retribusi bagi setiap kegiatan penyuluhan narkoba'
                ],
                'kunci' => 'A'
            ]
        ];

        $mockSoal = [
            (object)['id' => 1, 'nama_paket' => 'Pre-Test Anti Narkoba Umum',   'tipe' => 'pretest',  'durasi' => 30, 'acak_urutan' => true,  'soal_count' => 10, 'kegiatan_count' => 5,  'created_at' => '2026-08-01'],
            (object)['id' => 2, 'nama_paket' => 'Post-Test Anti Narkoba Umum',  'tipe' => 'posttest', 'durasi' => 30, 'acak_urutan' => true,  'soal_count' => 10, 'kegiatan_count' => 5,  'created_at' => '2026-08-01'],
            (object)['id' => 3, 'nama_paket' => 'Pre-Test P4GN Pelajar',        'tipe' => 'pretest',  'durasi' => 20, 'acak_urutan' => false, 'soal_count' => 8,  'kegiatan_count' => 2,  'created_at' => '2026-08-15'],
            (object)['id' => 4, 'nama_paket' => 'Pre-Test Lapas — Khusus',      'tipe' => 'pretest',  'durasi' => 25, 'acak_urutan' => false, 'soal_count' => 12, 'kegiatan_count' => 1,  'created_at' => '2026-09-01'],
        ];

        $findPaket = function($id) use ($mockSoal) {
            foreach ($mockSoal as $item) {
                if ($item->id == $id) return $item;
            }
            return $mockSoal[0];
        };

        // Index
        Route::get('/', function () use ($mockSoal) {
            return view('operator.bank-soal.index', [
                'bankSoal' => makePaginator($mockSoal),
            ]);
        })->name('index');

        // Create (harus sebelum /{id})
        Route::get('/create', function () {
            return view('operator.bank-soal.create');
        })->name('create');

        // Store
        Route::post('/', function () {
            return redirect()->route('operator.bank-soal.index')->with('success', 'Paket soal baru berhasil ditambahkan!');
        })->name('store');

        // Detail
        Route::get('/{id}', function ($id) use ($findPaket, $mockQuestions) {
            $paket = $findPaket($id);
            $count = min($paket->soal_count ?? 10, count($mockQuestions));
            $soalList = array_slice($mockQuestions, 0, $count);
            return view('operator.bank-soal.detail', compact('paket', 'soalList'));
        })->name('detail');

        // Edit
        Route::get('/{id}/edit', function ($id) use ($findPaket, $mockQuestions) {
            $paket = $findPaket($id);
            $count = min($paket->soal_count ?? 10, count($mockQuestions));
            $soalList = array_slice($mockQuestions, 0, $count);
            return view('operator.bank-soal.edit', compact('paket', 'soalList'));
        })->name('edit');

        // Update
        Route::put('/{id}', function ($id) {
            return redirect()->route('operator.bank-soal.detail', $id)->with('success', 'Paket soal berhasil diperbarui!');
        })->name('update');

        // AJAX Soal List
        Route::get('/{id}/soal', function ($id) use ($mockQuestions) {
            return response()->json(['soal' => $mockQuestions]);
        })->name('soal');

        // Delete
        Route::delete('/{id}', fn($id) => response()->json(['success' => true]))->name('destroy');
    });
});

// ─────────────────────────────────────────────────────────────────────────────
//  PARTICIPANT — Engine B
// ─────────────────────────────────────────────────────────────────────────────
Route::prefix('')->name('participant.')->group(function () {

    // Welcome
    Route::get('/join', fn() => view('participant.welcome'))->name('welcome');
    Route::post('/join', fn() => redirect()->route('participant.waiting', ['room' => 'pretest', 'id' => 1]))->name('join');

    // Waiting Room
    Route::get('/waiting/{room}/{id}', function ($room, $id) {
        $session = (object)[
            'id'          => $id,
            'nama'        => 'Budi Santoso',
            'sekolah'     => 'SMA N 5 Surabaya',
            'kode_join'   => 'AB1C2D',
            'skor_pretest'=> $room === 'posttest' ? 70 : null,
            'kegiatan_id' => 1,
            'kegiatan'    => (object)[
                'status' => 'jeda',
                'lokasi' => (object)['nama_lokasi' => 'SMA N 5 Surabaya'],
            ],
        ];
        return view('participant.waiting', [
            'session'      => $session,
            'room'         => $room,
            'waitingCount' => 12,
        ]);
    })->name('waiting');

    Route::get('/waiting/status/{id}', fn($id) => response()->json([
        'status_changed' => false,
        'waiting_count'  => 12,
    ]))->name('status');

    // Quiz
    Route::get('/quiz/{type}/{session}', function ($type, $session) {
        $mockSoal = [
            ['id' => 1, 'pertanyaan' => 'Apa kepanjangan dari BNN?',
             'opsi_a' => 'Badan Narkotika Nasional', 'opsi_b' => 'Badan Negara Narkoba',
             'opsi_c' => 'Biro Narkotika Nasional',  'opsi_d' => 'Badan Nasional Narkoba'],
            ['id' => 2, 'pertanyaan' => 'Apa bahaya penyalahgunaan narkoba bagi kesehatan?',
             'opsi_a' => 'Meningkatkan daya ingat', 'opsi_b' => 'Merusak organ tubuh dan otak',
             'opsi_c' => 'Tidak ada efek samping',  'opsi_d' => 'Membuat tubuh lebih sehat'],
            ['id' => 3, 'pertanyaan' => 'P2M singkatan dari?',
             'opsi_a' => 'Pencegahan dan Penindakan Masyarakat', 'opsi_b' => 'Pencegahan dan Pemberdayaan Masyarakat',
             'opsi_c' => 'Perlindungan dan Pemberdayaan Masyarakat', 'opsi_d' => 'Pemulihan dan Penguatan Masyarakat'],
            ['id' => 4, 'pertanyaan' => 'Narkoba yang berasal dari tanaman ganja disebut?',
             'opsi_a' => 'Heroin', 'opsi_b' => 'Kokain', 'opsi_c' => 'Kanabis', 'opsi_d' => 'Amfetamin'],
            ['id' => 5, 'pertanyaan' => 'Berapa lama proses rehabilitasi pecandu narkoba umumnya?',
             'opsi_a' => '1 minggu', 'opsi_b' => '1 bulan', 'opsi_c' => '3-6 bulan', 'opsi_d' => '10 tahun'],
        ];
        return view('participant.quiz', [
            'session'   => (object)['id' => $session, 'nama' => 'Budi Santoso', 'sekolah' => 'SMA N 5 Surabaya'],
            'quizType'  => $type,
            'soal'      => $mockSoal,
            'durasi'    => 30,
        ]);
    })->name('quiz');

    Route::post('/quiz/submit', fn() => response()->json([
        'success'  => true,
        'score'    => 80,
        'next_url' => route('participant.waiting', ['room' => 'posttest', 'id' => 1]),
    ]))->name('submit');
});

// ─────────────────────────────────────────────────────────────────────────────
//  LOGOUT
// ─────────────────────────────────────────────────────────────────────────────
Route::post('/logout', fn() => redirect('/join'))->name('logout');
