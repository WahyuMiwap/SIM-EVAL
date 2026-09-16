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
    Route::get('/profil', fn() => view('operator.kegiatan.index', [
        'kegiatan'     => makePaginator([]),
        'stats'        => ['total'=>0,'aktif'=>0,'totalPeserta'=>0,'avgNGain'=>'—'],
        'lokasiList'   => [],
        'bankSoalList' => [],
    ]))->name('profile');

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

        $mockSoal = [
            (object)['id' => 1, 'nama_paket' => 'Pre-Test Anti Narkoba Umum',   'tipe' => 'pretest',  'durasi' => 30, 'acak_urutan' => true,  'soal_count' => 10, 'kegiatan_count' => 5,  'created_at' => '2026-08-01'],
            (object)['id' => 2, 'nama_paket' => 'Post-Test Anti Narkoba Umum',  'tipe' => 'posttest', 'durasi' => 30, 'acak_urutan' => true,  'soal_count' => 10, 'kegiatan_count' => 5,  'created_at' => '2026-08-01'],
            (object)['id' => 3, 'nama_paket' => 'Pre-Test P4GN Pelajar',        'tipe' => 'pretest',  'durasi' => 20, 'acak_urutan' => false, 'soal_count' => 8,  'kegiatan_count' => 2,  'created_at' => '2026-08-15'],
            (object)['id' => 4, 'nama_paket' => 'Pre-Test Lapas — Khusus',      'tipe' => 'pretest',  'durasi' => 25, 'acak_urutan' => false, 'soal_count' => 12, 'kegiatan_count' => 1,  'created_at' => '2026-09-01'],
        ];

        $mockPretest = array_filter($mockSoal, fn($s) => $s->tipe === 'pretest');

        Route::get('/', function () use ($mockSoal, $mockPretest) {
            return view('operator.bank-soal.index', [
                'bankSoal'    => makePaginator($mockSoal),
                'pretestList' => array_values($mockPretest),
            ]);
        })->name('index');

        Route::post('/',          fn() => back())->name('store');
        Route::get('/{id}',       fn($id) => back())->name('detail');
        Route::get('/{id}/edit',  fn($id) => response()->json(['id' => $id, 'nama_paket' => 'Demo Soal', 'tipe' => 'pretest', 'durasi' => 30, 'acak_urutan' => false]))->name('edit');
        Route::get('/{id}/soal',  fn($id) => response()->json(['soal' => []]))->name('soal');
        Route::put('/{id}',       fn($id) => back())->name('update');
        Route::delete('/{id}',    fn($id) => response()->json(['success' => true]))->name('destroy');
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
