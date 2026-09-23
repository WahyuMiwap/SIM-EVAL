<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Participant;
use App\Services\MockDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class KegiatanController extends Controller
{
    /**
     * Jumlah peserta aktual sebuah kegiatan.
     * Acuan: counter milik event (DB: participants()->count()),
     * BUKAN daftar contoh mock (selalu 20 baris untuk semua event).
     */
    public static function participantCount($event): int
    {
        try {
            if ($event instanceof \App\Models\Event && $event->exists) {
                return (int) $event->participants()->count();
            }
        } catch (\Throwable $e) {
        }
        return (int) ($event->peserta_count ?? $event->participants_count ?? 0);
    }

    /**
     * Kebijakan kunci form edit — satu sumber kebenaran untuk view + update().
     * pristine = dijadwalkan & 0 peserta → semua field struktural editable.
     * Kondisi lain apa pun → paket + tanggal/lokasi/durasi terkunci,
     * hanya nama & catatan yang boleh berubah.
     */
    public static function editPolicy($event): array
    {
        $status  = strtolower($event->status ?? 'dijadwalkan');
        $count   = self::participantCount($event);
        $pristine = $status === 'dijadwalkan' && $count === 0;

        return [
            'pristine'         => $pristine,
            'boleh_ubah_paket' => $pristine,
            'jadwal_terkunci'  => !$pristine,
            'peserta_count'    => $count,
            'status'           => $status,
            'alasan'           => $pristine ? null
                : ($status !== 'dijadwalkan'
                    ? "Kegiatan berstatus '{$status}' — data lapangan tidak boleh ditulis ulang."
                    : "Sudah ada {$count} peserta — tanggal, lokasi, durasi, dan paket soal dikunci."),
        ];
    }

    /**
     * Daftar kegiatan untuk tampilan — DB dulu, fallback mock.
     * Objek hasil dipetakan ke bentuk yang dipakai view blade.
     */
    public static function allEventsDetailed(): array
    {
        $custom = [];
        try {
            foreach ((array) session('custom_mock_events', []) as $c) {
                $custom[] = (object) $c;
            }
        } catch (\Throwable $e) {
        }
        try {
            if (Schema::hasTable('events')) {
                $rows = Event::with(['lokasi', 'participants'])->orderByDesc('tanggal')->orderByDesc('id')->get();
                if ($rows->isNotEmpty()) {
                    $ids = $rows->pluck('id')->map(fn($i) => (int) $i)->all();
                    $extra = array_values(array_filter(
                        $custom,
                        fn($c) => !in_array((int) ($c->id ?? 0), $ids, true)
                    ));
                    return array_merge(
                        $extra,
                        $rows->map(fn($e) => self::mapEvent($e))->all()
                    );
                }
            }
        } catch (\Throwable $e) {
        }
        return MockDataService::getEvents();
    }

    /**
     * Satu kegiatan untuk tampilan — session custom dulu (milik user),
     * lalu DB, lalu fallback mock.
     */
    public static function findEventDetailed($id): ?object
    {
        try {
            foreach ((array) session('custom_mock_events', []) as $c) {
                $c = (object) $c;
                if ((int) ($c->id ?? 0) === (int) $id) return $c;
            }
        } catch (\Throwable $e) {
        }
        try {
            if (Schema::hasTable('events') && is_numeric($id)) {
                $e = Event::with(['lokasi', 'participants'])->find((int) $id);
                if ($e) return self::mapEvent($e);
            }
        } catch (\Throwable $e) {
        }
        try {
            return MockDataService::getEventById($id);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Petakan Eloquent Event → objek view (kompatibel struktur mock).
     */
    public static function mapEvent(Event $e): object
    {
        $parts = $e->relationLoaded('participants') ? $e->participants : collect();
        $count = $parts->count();
        $gains = $parts->whereNotNull('n_gain')->map(fn($p) => (float) $p->n_gain)->values();
        $avg = $gains->count() ? round($gains->avg(), 2) : null;
        $kat = $avg === null ? '—' : ($avg >= 0.7 ? 'Tinggi' : ($avg >= 0.3 ? 'Sedang' : 'Rendah'));

        $lok = $e->relationLoaded('lokasi') ? $e->lokasi : null;
        try {
            if (!$lok && $e->lokasi_id) $lok = $e->lokasi;
        } catch (\Throwable $ex) {
        }

        return (object)[
            'id'                  => $e->id,
            'nama_kegiatan'       => $e->nama_kegiatan,
            'kode_join'           => $e->kode_join,
            'kode_event'          => $e->kode_event,
            'status'              => $e->status ?? 'dijadwalkan',
            'status_fase'         => $e->status_fase ?? 'DRAFT',
            'tanggal'             => $e->tanggal ? Carbon::parse($e->tanggal)->format('Y-m-d') : null,
            'durasi_menit'        => (int) ($e->durasi_menit ?? 30),
            'catatan'             => $e->catatan ?? '',
            'lokasi_id'           => $e->lokasi_id,
            'lokasi'              => $lok ? (object)[
                'id'            => $lok->id,
                'nama_lokasi'   => $lok->nama_lokasi,
                'jenis_sasaran' => $lok->jenis_sasaran ?? 'sekolah',
                'kecamatan'     => $lok->kecamatan ?? null,
                'alamat'        => $lok->alamat ?? null,
            ] : null,
            'pretest_package_id'  => $e->pretest_package_id,
            'posttest_package_id' => $e->posttest_package_id,
            'peserta_count'       => $count,
            'participants_count'  => $count,
            'avg_gain'            => $avg,
            'kategori_gain'       => $kat,
            'created_at'          => $e->created_at,
        ];
    }

    /**
     * Peserta kegiatan untuk Meja Kerja — DB dulu, fallback mock.
     * Bentuk: collect of object (id, name, class_grade, pretest_score,
     * posttest_score, n_gain, category, kategori, status, input_method).
     */
    public static function eventParticipants($eventId)
    {
        try {
            if (Schema::hasTable('participants') && is_numeric($eventId)) {
                $rows = Participant::where('event_id', (int) $eventId)->orderBy('id')->get();
                // Hanya pakai DB bila ada baris riil; mock contoh bukan data.
                $real = $rows->filter(fn($p) => in_array($p->input_method, ['online', 'manual'])
                    && ($p->pretest_score !== null || $p->posttest_score !== null));
                if ($real->isNotEmpty() || $rows->isNotEmpty()) {
                    return $rows->map(function ($p) {
                        $cat = strtolower($p->category ?? 'kurang');
                        $st = strtolower($p->status ?? 'menunggu');
                        return (object)[
                            'id'             => $p->id,
                            'event_id'       => $p->event_id,
                            'name'           => $p->name,
                            'class_grade'    => $p->class_grade ?? '-',
                            'pretest_score'  => $p->pretest_score !== null ? (float) $p->pretest_score : null,
                            'posttest_score' => $p->posttest_score !== null ? (float) $p->posttest_score : null,
                            'n_gain'         => $p->n_gain !== null ? (float) $p->n_gain : null,
                            'category'       => $cat,
                            'kategori'       => ucfirst($cat),
                            'status'         => $st === 'selesai' ? 'selesai'
                                : (in_array($st, ['pretest', 'posttest', 'jeda', 'mengerjakan']) ? 'mengerjakan' : 'menunggu'),
                            'input_method'   => $p->input_method ?? 'manual',
                        ];
                    });
                }
            }
        } catch (\Throwable $e) {
        }
        return MockDataService::getParticipantsForEvent($eventId);
    }

    /**
     * Statistik kegiatan — dihitung dari baris peserta aktual.
     */
    public static function eventStats($eventId): array
    {
        $parts = collect(self::eventParticipants($eventId));
        $total = $parts->count();
        $pre = $parts->whereNotNull('pretest_score')->avg('pretest_score');
        $post = $parts->whereNotNull('posttest_score')->avg('posttest_score');
        $gain = $parts->whereNotNull('n_gain')->avg('n_gain');
        return [
            'total'         => $total,
            'avg_pre'       => $pre !== null ? round($pre, 1) : 0,
            'avg_post'      => $post !== null ? round($post, 1) : 0,
            'avg_gain'      => $gain !== null ? round($gain, 2) : 0,
            'selesai_count' => $total,
        ];
    }

    /**
     * Resolusi paket soal dari request (dipakai store & update).
     * Melempar ValidationException (redirect back otomatis) bila tidak valid.
     * Mengembalikan [pretestPkgId, posttestPkgId, pretestPkg, posttestPkg].
     */
    protected function resolvePackages(Request $request): array
    {
        $packages = BankSoalController::allPackages();
        $pkgById  = collect($packages)->keyBy('id');

        if ($request->boolean('paket_sama', true)) {
            $request->validate(
                ['paket_utama' => 'required'],
                ['paket_utama.required' => 'Pilih paket soal untuk Pre & Post-Test.']
            );
            $pretestPkgId  = (int) $request->input('paket_utama');
            $posttestPkgId = $pretestPkgId;
        } else {
            $request->validate(
                [
                    'pretest_package_id'  => 'required',
                    'posttest_package_id' => 'required',
                ],
                [
                    'pretest_package_id.required'   => 'Pilih paket soal Pre-Test.',
                    'posttest_package_id.required'  => 'Pilih paket soal Post-Test.',
                ]
            );
            $pretestPkgId  = (int) $request->input('pretest_package_id');
            $posttestPkgId = (int) $request->input('posttest_package_id');
        }

        $pretestPkg  = $pkgById->get($pretestPkgId);
        $posttestPkg = $pkgById->get($posttestPkgId);
        if (!$pretestPkg || !$posttestPkg) {
            throw ValidationException::withMessages([
                'paket_utama' => 'Paket soal tidak dikenal. Pilih dari daftar yang tersedia.',
            ]);
        }

        $preCount  = $pretestPkg->soal_count ?? $pretestPkg->questions_count ?? 0;
        $postCount = $posttestPkg->soal_count ?? $posttestPkg->questions_count ?? 0;
        if ($pretestPkgId !== $posttestPkgId && $preCount !== $postCount
            && !$request->boolean('konfirmasi_beda')) {
            session()->flash('butuh_konfirmasi_beda', true);
            throw ValidationException::withMessages([
                'posttest_package_id' => "Jumlah soal berbeda (Pre {$preCount} vs Post {$postCount}) sehingga N-Gain kurang valid.",
            ]);
        }

        return compact('pretestPkgId', 'posttestPkgId', 'pretestPkg', 'posttestPkg');
    }
    /**
     * Tampilan Daftar Kegiatan (Server-side Filter & Pagination via Mock Data)
     */
    public function index(Request $request)
    {
        $search   = strtolower(trim($request->get('search', '')));
        $period   = $request->get('period', 'all');
        $dateFrom = $request->get('date_from', '');
        $dateTo   = $request->get('date_to', '');

        $allEvents = self::allEventsDetailed();

        $filtered = collect($allEvents)->filter(function ($k) use ($search, $period, $dateFrom, $dateTo) {
            // Filter Search Nama Kegiatan / Lokasi
            if ($search) {
                $matchNama   = str_contains(strtolower($k->nama_kegiatan), $search);
                $matchLokasi = str_contains(strtolower($k->lokasi->nama_lokasi ?? ''), $search);
                if (!$matchNama && !$matchLokasi) return false;
            }

            // Filter Periode
            if ($period !== 'all' && !empty($k->tanggal)) {
                $d = Carbon::parse($k->tanggal);
                if ($period === 'custom') {
                    if ($dateFrom && $d->lt(Carbon::parse($dateFrom)->startOfDay())) return false;
                    if ($dateTo   && $d->gt(Carbon::parse($dateTo)->endOfDay()))   return false;
                } else {
                    $days   = (int) $period;
                    $cutoff = now()->subDays($days)->startOfDay();
                    if ($d->lt($cutoff) || $d->gt(now()->endOfDay())) return false;
                }
            }

            return true;
        })->values()->all();

        $kegiatan     = MockDataService::paginate($filtered, 10);
        $lokasiList   = LokasiController::allLocations();
        $bankSoalList = BankSoalController::allPackages();
        $filters      = compact('search', 'period', 'dateFrom', 'dateTo');

        $gains = array_values(array_filter(array_map(
            fn($e) => $e->avg_gain ?? null, $allEvents
        ), fn($g) => $g !== null));
        $stats = [
            'total'        => count($allEvents),
            'aktif'        => count(array_filter($allEvents, fn($e) => ($e->status ?? '') === 'berlangsung')),
            'totalPeserta' => array_sum(array_map(fn($e) => (int) ($e->peserta_count ?? 0), $allEvents)),
            'avgNGain'     => count($gains) ? number_format(round(array_sum($gains) / count($gains), 2), 2) : '0.00',
        ];

        return view('operator.kegiatan.index', compact('kegiatan', 'lokasiList', 'bankSoalList', 'filters', 'stats'));
    }

    /**
     * Halaman Tambah Kegiatan
     */
    public function create()
    {
        $lokasiList = LokasiController::allLocations();
        $paketList  = BankSoalController::allPackages();
        return view('operator.kegiatan.create', compact('lokasiList', 'paketList'));
    }

    /**
     * Simpan Kegiatan Baru (Mock Service & Session Persistence)
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'lokasi_id'     => 'required',
            'tanggal'       => 'nullable|date',
            'durasi_menit'  => 'nullable|integer|min:5|max:180',
        ], [
            'lokasi_id.required' => 'Pilih lokasi binaan dari daftar atau tambah baru lewat tombol + Tambah.',
        ]);

        // Cari Objek Lokasi Terpilih (DB dulu, fallback mock) — combobox
        // menjamin yang dikirim adalah id valid, bukan teks bebas.
        $lokasiId  = $request->input('lokasi_id');
        $lokasiObj = LokasiController::findLocation($lokasiId);
        if (!$lokasiObj) {
            return back()->withInput()
                ->withErrors(['lokasi_id' => 'Lokasi tidak dikenal. Pilih dari daftar yang muncul saat mengetik, atau tambah baru.']);
        }

        // Cari Objek Paket Soal: mode gabungan (toggle ON) atau terpisah
        $pkg = $this->resolvePackages($request);
        $pretestPkgId  = $pkg['pretestPkgId'];
        $posttestPkgId = $pkg['posttestPkgId'];
        $pretestPkg    = $pkg['pretestPkg'];
        $posttestPkg   = $pkg['posttestPkg'];

        $allEvents = self::allEventsDetailed();
        $nextId    = count($allEvents) + 1;
        $kodeJoin  = self::uniqueKodeJoin();

        // ── Tulis ke DB bila tersedia (agar PIN bisa dipakai join,
        // dashboard ikut menghitung, dan fase terkendali) ──
        $dbEvent = null;
        try {
            if (Schema::hasTable('events')) {
                $dbEvent = Event::create([
                    'nama_kegiatan'       => $request->input('nama_kegiatan'),
                    'kode_join'           => $kodeJoin,
                    'status'              => $request->input('status', 'dijadwalkan'),
                    'tanggal'             => $request->input('tanggal') ?: Carbon::now()->format('Y-m-d'),
                    'durasi_menit'        => (int) $request->input('durasi_menit', 30),
                    'catatan'             => $request->input('catatan', ''),
                    'kategori_audiens'    => self::audiensDariLokasi($lokasiObj),
                    'lokasi_id'           => $lokasiObj ? $lokasiObj->id : 1,
                    'pretest_package_id'  => $pretestPkgId,
                    'posttest_package_id' => $posttestPkgId,
                    'created_by'          => auth()->id(),
                ]);
            }
        } catch (\Throwable $e) {
            $dbEvent = null;
        }

        if ($dbEvent) {
            \App\Services\AuditService::record(
                'buat_kegiatan', 'event', $dbEvent->id,
                "Kegiatan dibuat: {$dbEvent->nama_kegiatan} (PIN {$kodeJoin})"
            );
            return redirect()->route('operator.kegiatan.detail', $dbEvent->id)
                ->with('success', "Kegiatan '{$dbEvent->nama_kegiatan}' berhasil dibuat! Kode PIN Sesi: {$kodeJoin}");
        }

        $newEvent = (object)[
            'id'                  => $nextId,
            'nama_kegiatan'       => $request->input('nama_kegiatan'),
            'kode_join'           => $kodeJoin,
            'status'              => $request->input('status', 'dijadwalkan'),
            'tanggal'             => $request->input('tanggal') ?: Carbon::now()->format('Y-m-d'),
            'durasi_menit'        => (int) $request->input('durasi_menit', 30),
            'catatan'             => $request->input('catatan', ''),
            'lokasi_id'           => $lokasiObj ? $lokasiObj->id : 1,
            'lokasi'              => $lokasiObj,
            'pretest_package_id'  => $pretestPkgId,
            'posttest_package_id' => $posttestPkgId,
            'pretestPackage'      => $pretestPkg,
            'posttestPackage'     => $posttestPkg,
            'peserta_count'       => 0,
            'participants_count'  => 0,
            'avg_gain'            => null,
            'kategori_gain'       => '—',
            'created_at'          => Carbon::now(),
        ];

        $existingCustom = session('custom_mock_events', []);
        $existingCustom[] = $newEvent;
        session(['custom_mock_events' => $existingCustom]);

        \App\Services\AuditService::record(
            'buat_kegiatan', 'event', $nextId,
            "Kegiatan dibuat: {$newEvent->nama_kegiatan} (PIN {$kodeJoin})"
        );

        return redirect()->route('operator.kegiatan.detail', $nextId)
            ->with('success', "Kegiatan '{$newEvent->nama_kegiatan}' berhasil dibuat! Kode PIN Sesi: {$kodeJoin}");
    }

    /**
     * Kode join unik 6 karakter (cek tabrakan di DB bila tersedia).
     */
    protected static function uniqueKodeJoin(): string
    {
        for ($i = 0; $i < 10; $i++) {
            $kode = strtoupper(Str::random(6));
            try {
                if (Schema::hasTable('events') && Event::where('kode_join', $kode)->exists()) {
                    continue;
                }
            } catch (\Throwable $e) {
            }
            return $kode;
        }
        return strtoupper(Str::random(6));
    }

    /**
     * Petakan jenis sasaran lokasi → kategori audiens event.
     */
    protected static function audiensDariLokasi($lokasiObj): ?string
    {
        $jenis = strtolower($lokasiObj->jenis_sasaran ?? '');
        return match ($jenis) {
            'sekolah' => 'SMA',
            'kampus' => 'UMUM',
            'lapas' => 'LAPAS',
            'instansi' => 'INSTANSI',
            default => null,
        };
    }

    /**
     * Detail Kegiatan & Meja Kerja Rekap
     */
    public function detail($id)
    {
        $kegiatan = self::findEventDetailed($id);
        if (!$kegiatan) {
            return redirect()->route('operator.kegiatan.index')
                ->with('warning', 'Kegiatan tidak ditemukan.');
        }
        $participants = self::eventParticipants($kegiatan->id);
        $stats        = self::eventStats($kegiatan->id);

        return view('operator.kegiatan.detail', compact('kegiatan', 'participants', 'stats'));
    }

    /**
     * Halaman / Data Edit Kegiatan
     */
    public function edit(Request $request, $id)
    {
        $kegiatan   = self::findEventDetailed($id);
        if (!$kegiatan) {
            return redirect()->route('operator.kegiatan.index')
                ->with('warning', 'Kegiatan tidak ditemukan.');
        }
        $lokasiList = LokasiController::allLocations();
        $paketList  = BankSoalController::allPackages();
        $policy     = self::editPolicy($kegiatan);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'id'            => $kegiatan->id,
                'nama_kegiatan' => $kegiatan->nama_kegiatan,
                'lokasi_id'     => $kegiatan->lokasi_id,
                'lokasi'        => $kegiatan->lokasi,
                'tanggal'       => $kegiatan->tanggal,
                'durasi_menit'  => $kegiatan->durasi_menit,
                'catatan'       => $kegiatan->catatan,
            ]);
        }

        return view('operator.kegiatan.edit', compact('kegiatan', 'lokasiList', 'paketList', 'policy'));
    }

    /**
     * Perbarui Kegiatan — menegakkan matriks kunci editPolicy().
     * pristine (dijadwalkan + 0 peserta): semua boleh berubah.
     * Selain itu: hanya nama & catatan; paket + tanggal/lokasi/durasi
     * yang diselundupkan akan ditolak.
     */
    public function update(Request $request, $id)
    {
        $kegiatan = self::findEventDetailed($id);
        if (!$kegiatan) {
            return redirect()->route('operator.kegiatan.index')
                ->with('warning', 'Kegiatan tidak ditemukan.');
        }
        $policy   = self::editPolicy($kegiatan);

        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'catatan'       => 'nullable|string',
        ]);

        $attrs = [
            'nama_kegiatan' => $request->input('nama_kegiatan'),
            'catatan'       => $request->input('catatan', ''),
        ];

        if ($policy['jadwal_terkunci']) {
            $this->rejectSmuggledSchedule($request, $kegiatan);
        } else {
            $request->validate([
                'lokasi_id'    => 'nullable',
                'tanggal'      => 'nullable|date',
                'durasi_menit' => 'nullable|integer|min:5|max:180',
            ]);
            if ($request->filled('lokasi_id') && !LokasiController::findLocation($request->input('lokasi_id'))) {
                throw ValidationException::withMessages([
                    'lokasi_id' => 'Lokasi tidak dikenal. Pilih dari daftar yang muncul saat mengetik, atau tambah baru.',
                ]);
            }
            if ($request->filled('lokasi_id')) $attrs['lokasi_id'] = (int) $request->input('lokasi_id');
            if ($request->filled('tanggal')) $attrs['tanggal'] = $request->input('tanggal');
            if ($request->filled('durasi_menit')) $attrs['durasi_menit'] = (int) $request->input('durasi_menit');
        }

        if ($policy['boleh_ubah_paket']) {
            // Validasi penuh (termasuk konfirmasi beda-jumlah) — sama dengan store.
            // Form selalu mengirim field paket; bila tak ada sama sekali (API),
            // paket dibiarkan apa adanya.
            if ($request->filled('paket_utama') || $request->filled('pretest_package_id') || $request->filled('posttest_package_id')) {
                $pkg = $this->resolvePackages($request);
                $attrs['pretest_package_id'] = $pkg['pretestPkgId'];
                $attrs['posttest_package_id'] = $pkg['posttestPkgId'];
            }
        } elseif ($request->filled('paket_utama') || $request->filled('pretest_package_id') || $request->filled('posttest_package_id')) {
            throw ValidationException::withMessages([
                'paket_utama' => 'Paket soal terkunci karena ' . ($policy['peserta_count'] > 0
                    ? "sudah ada {$policy['peserta_count']} peserta."
                    : "status kegiatan '{$policy['status']}'."),
            ]);
        }

        // Persist: DB bila event riil, session bila custom mock.
        $savedDb = false;
        try {
            if (Schema::hasTable('events') && is_numeric($id)
                && Event::where('id', (int) $id)->exists()) {
                Event::where('id', (int) $id)->update($attrs);
                $savedDb = true;
            }
        } catch (\Throwable $e) {
        }
        if (!$savedDb) {
            try {
                $custom = [];
                foreach ((array) session('custom_mock_events', []) as $c) {
                    $c = (object) $c;
                    if ((int) ($c->id ?? 0) === (int) $id) {
                        foreach ($attrs as $k => $v) $c->$k = $v;
                    }
                    $custom[] = $c;
                }
                session(['custom_mock_events' => $custom]);
            } catch (\Throwable $e) {
            }
        }

        \App\Services\AuditService::record('ubah_kegiatan', 'event', $id, "Kegiatan #{$id} diperbarui.");

        return redirect()->route('operator.kegiatan.detail', $id)
            ->with('success', 'Data kegiatan berhasil diperbarui.');
    }

    /**
     * Tolak perubahan tanggal/lokasi/durasi yang diselundupkan
     * saat jadwal terkunci (nilai harus sama dengan data tersimpan).
     */
    protected function rejectSmuggledSchedule(Request $request, $event): void
    {
        $errors = [];

        if ($request->filled('lokasi_id')
            && (int) $request->input('lokasi_id') !== (int) ($event->lokasi_id ?? null)) {
            $errors['lokasi_id'] = 'Lokasi terkunci dan tidak dapat diubah pada kondisi ini.';
        }

        if ($request->filled('tanggal')) {
            try {
                $old = Carbon::parse($event->tanggal ?? null)->format('Y-m-d');
                $new = Carbon::parse($request->input('tanggal'))->format('Y-m-d');
                if ($old !== $new) $errors['tanggal'] = 'Tanggal terkunci dan tidak dapat diubah pada kondisi ini.';
            } catch (\Throwable $e) {
                $errors['tanggal'] = 'Tanggal terkunci dan tidak dapat diubah pada kondisi ini.';
            }
        }

        if ($request->filled('durasi_menit')
            && (int) $request->input('durasi_menit') !== (int) ($event->durasi_menit ?? 30)) {
            $errors['durasi_menit'] = 'Durasi terkunci dan tidak dapat diubah pada kondisi ini.';
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }

    /**
     * Ubah Status Kegiatan (dijadwalkan / berlangsung / selesai)
     */
    public function updateStatus(Request $request, $id)
    {
        $newStatus = strtolower($request->get('status', 'berlangsung'));
        if (!in_array($newStatus, ['dijadwalkan', 'berlangsung', 'selesai'], true)) {
            return response()->json(['success' => false, 'message' => 'Status tidak dikenal.'], 422);
        }
        $this->persistMockEventField((int) $id, 'status', $newStatus);
        try {
            if (LokasiController::useDatabase() && Schema::hasTable('events')) {
                \App\Models\Event::where('id', $id)->update(['status' => $newStatus]);
            }
        } catch (\Throwable $e) {
        }
        \App\Services\AuditService::record('ubah_status', 'event', $id, "Status kegiatan #{$id} → {$newStatus}");
        return response()->json([
            'success'   => true,
            'status'    => $newStatus,
            'message'   => 'Status kegiatan berhasil diubah.',
        ]);
    }

    /**
     * Ubah Fase Sesi (DRAFT / PRE_ACTIVE / MATERIAL_PAUSED / POST_ACTIVE / COMPLETED).
     * Mengendalikan pintu pre-test & post-test yang dipolling siswa.
     */
    public function updateFase(Request $request, $id)
    {
        $fase = strtoupper($request->get('fase', ''));
        $allowed = ['DRAFT', 'PRE_ACTIVE', 'MATERIAL_PAUSED', 'POST_ACTIVE', 'COMPLETED'];
        if (!in_array($fase, $allowed, true)) {
            return response()->json(['success' => false, 'message' => 'Fase tidak dikenal.'], 422);
        }
        $this->persistMockEventField((int) $id, 'status_fase', $fase);
        try {
            if (LokasiController::useDatabase() && Schema::hasTable('events')) {
                \App\Models\Event::where('id', $id)->update(['status_fase' => $fase]);
            }
        } catch (\Throwable $e) {
        }
        \App\Services\AuditService::record('ubah_fase', 'event', $id, "Fase sesi kegiatan #{$id} → {$fase}");
        return response()->json([
            'success' => true,
            'fase'    => $fase,
            'message' => 'Fase sesi berhasil diubah.',
        ]);
    }

    /**
     * Simpan override field event mock ke session (agar tombol Meja Kerja
     * berfungsi selama Mock Mode).
     */
    protected function persistMockEventField(int $id, string $field, $value): void
    {
        try {
            $overrides = session('mock_event_overrides', []);
            $overrides[$id] = array_merge($overrides[$id] ?? [], [$field => $value]);
            session(['mock_event_overrides' => $overrides]);
        } catch (\Throwable $e) {
        }
    }

    /**
     * Hapus Kegiatan (JSON untuk ReauthModal)
     */
    public function destroy($id)
    {
        $nama = null;
        try {
            if (Schema::hasTable('events') && is_numeric($id)) {
                $ev = Event::find((int) $id);
                if ($ev) {
                    $nama = $ev->nama_kegiatan;
                    $ev->delete();
                }
            }
        } catch (\Throwable $e) {
        }
        // Bersihkan juga custom mock session ber-id sama (bila ada)
        try {
            $custom = collect((array) session('custom_mock_events', []))
                ->reject(fn($c) => (int) ((object) $c)->id === (int) $id)
                ->values()->all();
            session(['custom_mock_events' => $custom]);
        } catch (\Throwable $e) {
        }
        \App\Services\AuditService::record('hapus_kegiatan', 'event', $id, "Kegiatan dihapus: " . ($nama ?? "#{$id}"));
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Kegiatan berhasil dihapus.',
            ]);
        }
        return redirect()->route('operator.kegiatan.index')
            ->with('success', 'Kegiatan berhasil dihapus.');
    }

    /**
     * Ekspor Laporan Rekapitulasi Evaluasi BNN (CSV Kompatibel Excel dengan UTF-8 BOM)
     */
    public function exportLaporan($id)
    {
        $kegiatan = self::findEventDetailed($id);
        if (!$kegiatan) {
            return redirect()->route('operator.kegiatan.index')
                ->with('warning', 'Kegiatan tidak ditemukan.');
        }
        \App\Services\AuditService::record(
            'unduh_rekap', 'event', $kegiatan->id,
            'Unduh rekap: ' . ($kegiatan->nama_kegiatan ?? "kegiatan #{$id}")
        );
        $participants = self::eventParticipants($kegiatan->id);
        $stats = self::eventStats($kegiatan->id);

        $filename = 'Rekap_Evaluasi_BNN_' . Str::slug($kegiatan->nama_kegiatan ?? 'Sosialisasi') . '_' . date('Ymd') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($kegiatan, $participants, $stats) {
            $file = fopen('php://output', 'w');
            // Tambahkan UTF-8 BOM agar Microsoft Excel di Windows membuka huruf latin/Indonesia dengan rapi
            fputs($file, "\xEF\xBB\xBF");

            // Kop Dokumen Resmi BNN
            fputcsv($file, ['BADAN NARKOTIKA NASIONAL KOTA SURABAYA']);
            fputcsv($file, ['SEKSI PENCEGAHAN DAN PEMBERDAYAAN MASYARAKAT (P2M)']);
            fputcsv($file, ['LAPORAN REKAPITULASI EVALUASI PRE-TEST & POST-TEST']);
            fputcsv($file, []);
            fputcsv($file, ['Nama Kegiatan', $kegiatan->nama_kegiatan ?? 'Sosialisasi P4GN']);
            fputcsv($file, ['Lokasi Sasaran', $kegiatan->lokasi->nama_lokasi ?? '-']);
            fputcsv($file, ['Tanggal', $kegiatan->tanggal ?? date('Y-m-d')]);
            fputcsv($file, ['Kode PIN Sesi', $kegiatan->kode_join ?? '-']);
            fputcsv($file, ['Total Peserta', $stats['total'] ?? count($participants)]);
            fputcsv($file, ['Rata-rata Pre-Test', $stats['avg_pre'] ?? 0]);
            fputcsv($file, ['Rata-rata Post-Test', $stats['avg_post'] ?? 0]);
            fputcsv($file, ['Rata-rata N-Gain', $stats['avg_gain'] ?? 0]);
            fputcsv($file, []);

            // Header Tabel
            fputcsv($file, [
                'No',
                'Nama Lengkap Peserta',
                'Kelas / Kelompok',
                'Nilai Pre-Test',
                'Nilai Post-Test',
                'Gain Skor (Post - Pre)',
                'Indeks N-Gain',
                'Kategori Pemahaman',
                'Metode Input'
            ]);

            // Isi Data Peserta
            foreach ($participants as $index => $p) {
                $pre = (float) ($p->pretest_score ?? 0);
                $post = (float) ($p->posttest_score ?? 0);
                $delta = round($post - $pre, 2);
                $gain = $p->n_gain !== null ? number_format((float)$p->n_gain, 2) : '-';
                $kat = ucfirst($p->category ?? ($gain >= 0.7 ? 'Paham' : ($gain >= 0.3 ? 'Cukup' : 'Kurang')));

                fputcsv($file, [
                    $index + 1,
                    $p->name,
                    $p->class_grade ?? '-',
                    $pre,
                    $post,
                    $delta,
                    $gain,
                    $kat,
                    strtoupper($p->input_method ?? 'MANUAL')
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['', '', '', 'Mengetahui,', '', '', 'Petugas Operator Evaluasi,']);
            fputcsv($file, []);
            fputcsv($file, []);
            fputcsv($file, ['', '', '', 'Kepala Sub Bagian P2M BNN', '', '', 'Tim Evaluasi P2M BNN']);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Simpan / Tambah Baris Peserta Meja Kerja.
     * Persist ke DB bila tersedia (observer menghitung delta/N-Gain/kategori);
     * fallback respons terhitung seperti sebelumnya.
     */
    public function saveParticipantRow(Request $request, $id)
    {
        $pre  = (float) $request->get('pretest_score', 0);
        $post = (float) $request->get('posttest_score', 0);

        $rowId = $request->get('id');
        try {
            if (Schema::hasTable('participants') && Schema::hasTable('events')
                && Event::where('id', (int) $id)->exists()) {
                $data = [
                    'name'          => trim($request->get('name', 'Peserta Baru')) ?: 'Peserta Baru',
                    'class_grade'   => trim($request->get('class_grade', '-')) ?: '-',
                    'pretest_score' => $pre,
                    'posttest_score'=> $post,
                    'input_method'  => $request->get('input_method', 'manual'),
                ];
                if ($rowId && Participant::where('id', (int) $rowId)->where('event_id', (int) $id)->exists()) {
                    Participant::where('id', (int) $rowId)->update($data);
                    $saved = Participant::find((int) $rowId);
                } else {
                    $saved = Participant::create(array_merge(['event_id' => (int) $id], $data));
                }
                $saved->refresh();
                $gain = $saved->n_gain !== null ? (float) $saved->n_gain : 0;
                $cat = strtolower($saved->category ?? 'kurang');
                return response()->json([
                    'success'     => true,
                    'message'     => 'Data nilai peserta berhasil disimpan.',
                    'participant' => [
                        'id'             => $saved->id,
                        'name'           => $saved->name,
                        'class_grade'    => $saved->class_grade,
                        'pretest_score'  => (float) $saved->pretest_score,
                        'posttest_score' => (float) $saved->posttest_score,
                        'n_gain'         => $gain,
                        'kategori'       => $cat,
                    ],
                ]);
            }
        } catch (\Throwable $e) {
        }

        $gain = ($post > $pre && $pre < 100) ? round(($post - $pre) / (100 - $pre), 2) : 0;
        $cat  = $gain >= 0.70 ? 'paham' : ($gain >= 0.30 ? 'cukup' : 'kurang');

        return response()->json([
            'success'     => true,
            'message'     => 'Data nilai peserta berhasil disimpan.',
            'participant' => [
                'id'             => $rowId ?: rand(200, 999),
                'name'           => $request->get('name', 'Peserta Baru'),
                'class_grade'    => $request->get('class_grade', '-'),
                'pretest_score'  => $pre,
                'posttest_score' => $post,
                'n_gain'         => $gain,
                'kategori'       => $cat,
            ],
        ]);
    }

    /**
     * Hapus Baris Peserta (persist bila baris riil di DB).
     */
    public function deleteParticipantRow(Request $request, $id, $pesertaId)
    {
        try {
            if (Schema::hasTable('participants') && is_numeric($pesertaId)) {
                Participant::where('id', (int) $pesertaId)->where('event_id', (int) $id)->delete();
            }
        } catch (\Throwable $e) {
        }
        return response()->json([
            'success' => true,
            'message' => 'Baris peserta berhasil dihapus.',
        ]);
    }

    /**
     * Daftar peserta jalur digital (online) untuk Live Monitoring.
     * Mock mode: [] (join digital hanya persist di DB).
     */
    public function pesertaDigital($id)
    {
        $list = [];
        try {
            if (Schema::hasTable('participants')) {
                $list = \App\Models\Participant::where('event_id', $id)
                    ->where('input_method', 'online')
                    ->orderByDesc('updated_at')
                    ->limit(50)
                    ->get()
                    ->map(fn($p) => [
                        'id'             => $p->id,
                        'name'           => $p->name,
                        'class_grade'    => $p->class_grade,
                        'pretest_score'  => $p->pretest_score !== null ? (float) $p->pretest_score : null,
                        'posttest_score' => $p->posttest_score !== null ? (float) $p->posttest_score : null,
                        'n_gain'         => $p->n_gain !== null ? (float) $p->n_gain : null,
                        'status'         => $p->status,
                    ])->all();
            }
        } catch (\Throwable $e) {
        }
        return response()->json(['data' => $list]);
    }

    /**
     * Simpan hasil pindaian kamera LJK yang sudah DIVERIFIKASI operator.
     * Grading vs kunci paket event (server-side), persist Participant +
     * Answers + Scan. Idempoten per (peserta, stage): pindai ulang menimpa.
     */
    public function omrSubmit(Request $request, $id)
    {
        $validated = $request->validate([
            'nama'    => 'required|string|max:100',
            'kelas'   => 'required|string|max:50',
            'stage'   => 'required|in:pretest,posttest',
            'answers' => 'required|array|min:1',
            'answers.*.nomor'   => 'required|integer|min:1',
            'answers.*.jawaban' => 'nullable|in:A,B,C,D,a,b,c,d',
            'serial'  => 'nullable|string|max:50',
            'min_confidence' => 'nullable|numeric',
        ]);

        $event = self::findEventDetailed($id);
        if (!$event) {
            return response()->json(['success' => false, 'message' => 'Kegiatan tidak ditemukan.'], 404);
        }

        $pkgId = $validated['stage'] === 'posttest'
            ? ($event->posttest_package_id ?? null)
            : ($event->pretest_package_id ?? null);
        if (!$pkgId) {
            return response()->json(['success' => false, 'message' => 'Paket soal kegiatan belum ditentukan.'], 422);
        }

        $questions = \App\Http\Controllers\ParticipantController::resolvePackageQuestions((int) $pkgId);
        usort($questions, fn($a, $b) => ($a['nomor'] ?? $a['id']) <=> ($b['nomor'] ?? $b['id']));

        $byNomor = [];
        foreach ($validated['answers'] as $a) {
            $byNomor[(int) $a['nomor']] = isset($a['jawaban']) ? strtoupper($a['jawaban']) : null;
        }

        $correct = 0;
        $graded = [];
        foreach ($questions as $i => $q) {
            $nomor = $i + 1;
            $given = $byNomor[$nomor] ?? null;
            $ok = $given !== null && $given === strtoupper($q['kunci']);
            if ($ok) $correct++;
            $graded[] = ['question_id' => $q['id'], 'nomor' => $nomor, 'jawaban' => $given, 'is_correct' => $ok];
        }
        $total = count($questions);
        $score = $total > 0 ? round($correct / $total * 100, 2) : 0;

        $nama  = trim(preg_replace('/\s+/', ' ', $validated['nama']));
        $kelas = trim($validated['kelas']);
        $normNama  = strtolower($nama);
        $normKelas = strtolower($kelas);

        // Persist DB bila event riil; mock: kembalikan skor terhitung (jujur, tanpa simpan).
        try {
            if (Schema::hasTable('participants') && Schema::hasTable('events')
                && \App\Models\Event::where('id', (int) $event->id)->exists()) {
                $participant = \App\Models\Participant::where('event_id', (int) $event->id)->get()
                    ->first(fn($p) => strtolower(trim(preg_replace('/\s+/', ' ', $p->name ?? ''))) === $normNama
                        && strtolower(trim($p->class_grade ?? '')) === $normKelas);
                if (!$participant) {
                    $participant = \App\Models\Participant::create([
                        'event_id'      => (int) $event->id,
                        'name'          => $nama,
                        'class_grade'   => $kelas,
                        'school_origin' => $event->lokasi->nama_lokasi ?? null,
                        'input_method'  => 'omr',
                        'status'        => $validated['stage'] === 'pretest' ? 'jeda' : 'selesai',
                    ]);
                }
                if ($validated['stage'] === 'pretest') {
                    $participant->pretest_score = $score;
                    if ($participant->posttest_score === null) $participant->status = 'jeda';
                } else {
                    $participant->posttest_score = $score;
                }
                $participant->save();

                if (Schema::hasTable('participant_answers')) {
                    \App\Models\ParticipantAnswer::where('participant_id', $participant->id)
                        ->where('stage', $validated['stage'])->delete();
                    foreach ($graded as $g) {
                        \App\Models\ParticipantAnswer::create([
                            'participant_id' => $participant->id,
                            'question_id'    => $g['question_id'],
                            'stage'          => $validated['stage'],
                            'jawaban'        => $g['jawaban'],
                            'is_correct'     => $g['is_correct'],
                        ]);
                    }
                }

                if (Schema::hasTable('scans')) {
                    \App\Models\Scan::create([
                        'event_id'       => (int) $event->id,
                        'participant_id' => $participant->id,
                        'phase_type'     => $validated['stage'] === 'pretest' ? 'PRE' : 'POST',
                        'raw_answers'    => collect($graded)->mapWithKeys(fn($g) => [$g['nomor'] => $g['jawaban']])->all(),
                        'score_raw'      => $correct,
                        'score_percent'  => $score,
                        'omr_confidence' => 'HIGH',
                        'captured_by'    => auth()->id(),
                        'captured_at'    => now(),
                        'capture_method' => 'CAMERA_LIVE',
                        'serial_number'  => $validated['serial'] ?? null,
                    ]);
                }

                $participant->refresh();
                \App\Services\AuditService::record(
                    'omr_scan', 'event', (int) $event->id,
                    "LJK {$validated['stage']} {$nama} ({$kelas}): skor {$score}"
                );

                return response()->json([
                    'success'  => true,
                    'message'  => 'Hasil pindaian tersimpan.',
                    'score'    => $score,
                    'correct'  => $correct,
                    'total'    => $total,
                    'n_gain'   => $participant->n_gain !== null ? (float) $participant->n_gain : null,
                    'category' => $participant->category,
                    'participant' => ['id' => $participant->id, 'name' => $participant->name],
                ]);
            }
        } catch (\Throwable $e) {
        }

        return response()->json([
            'success'  => true,
            'message'  => 'Hasil dinilai (mode mock — tersimpan permanen setelah event tersimpan di database).',
            'score'    => $score,
            'correct'  => $correct,
            'total'    => $total,
            'n_gain'   => null,
            'category' => null,
            'participant' => ['id' => null, 'name' => $nama],
        ]);
    }

    /**
     * Hasil Scan OMR Kamera (simulator). Bila DB tersedia, baris hasil
     * ikut tersimpan agar tidak hilang saat reload.
     */
    public function omrScanRecord(Request $request, $id)
    {
        $pre   = rand(30, 60);
        $post  = rand(75, 100);
        $gain  = round(($post - $pre) / (100 - $pre), 2);
        $cat   = $gain >= 0.70 ? 'paham' : ($gain >= 0.30 ? 'cukup' : 'kurang');
        $name  = 'Peserta Scan #' . rand(10, 99);
        $pid   = rand(300, 999);

        try {
            if (Schema::hasTable('participants') && Schema::hasTable('events')
                && Event::where('id', (int) $id)->exists()) {
                $saved = Participant::create([
                    'event_id'       => (int) $id,
                    'name'           => $name,
                    'class_grade'    => $request->get('class_grade', 'Reguler'),
                    'pretest_score'  => $pre,
                    'posttest_score' => $post,
                    'input_method'   => 'omr',
                ]);
                $saved->refresh();
                $pid  = $saved->id;
                $gain = $saved->n_gain !== null ? (float) $saved->n_gain : $gain;
                $cat  = strtolower($saved->category ?? $cat);
            }
        } catch (\Throwable $e) {
        }

        return response()->json([
            'success'     => true,
            'message'     => 'Lembar jawaban berhasil dipindai.',
            'participant' => [
                'id'             => $pid,
                'name'           => $name,
                'class_grade'    => $request->get('class_grade', 'Reguler'),
                'pretest_score'  => $pre,
                'posttest_score' => $post,
                'n_gain'         => $gain,
                'kategori'       => $cat,
            ],
        ]);
    }
}
