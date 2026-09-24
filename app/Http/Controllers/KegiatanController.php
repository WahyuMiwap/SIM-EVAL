<?php

namespace App\Http\Controllers;

use App\Actions\CreateEventAction;
use App\Http\Requests\StoreKegiatanRequest;
use App\Http\Requests\UpdateKegiatanRequest;
use App\Jobs\ExportLaporanJob;
use App\Models\Event;
use App\Models\Location;
use App\Models\Participant;
use App\Models\ParticipantAnswer;
use App\Models\Scan;
use App\Services\AuditService;
use App\Services\BankSoalService;
use App\Services\LokasiService;
use App\Services\OmrService;
use App\Support\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

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
            if ($event instanceof Event && $event->exists) {
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
        $status = strtolower($event->status ?? 'dijadwalkan');
        $count = self::participantCount($event);
        $pristine = $status === 'dijadwalkan' && $count === 0;

        return [
            'pristine' => $pristine,
            'boleh_ubah_paket' => $pristine,
            'jadwal_terkunci' => ! $pristine,
            'peserta_count' => $count,
            'status' => $status,
            'alasan' => $pristine ? null
                : ($status !== 'dijadwalkan'
                    ? "Kegiatan berstatus '{$status}' — data lapangan tidak boleh ditulis ulang."
                    : "Sudah ada {$count} peserta — tanggal, lokasi, durasi, dan paket soal dikunci."),
        ];
    }

    /**
     * Daftar kegiatan untuk tampilan — DB-first (MySQL simeval_db).
     * Objek hasil dipetakan ke bentuk yang dipakai view blade.
     */
    public static function allEventsDetailed(): array
    {
        try {
            $rows = Event::with(['lokasi', 'participants'])->withCount('participants')->orderByDesc('tanggal')->orderByDesc('id')->get();

            return $rows->map(fn ($e) => self::mapEvent($e))->all();
        } catch (\Throwable $e) {
            Log::error('allEventsDetailed gagal', ['err' => $e->getMessage()]);

            return [];
        }
    }

    /**
     * Satu kegiatan untuk tampilan — DB-first (MySQL simeval_db).
     */
    public static function findEventDetailed($id): ?object
    {
        try {
            if (! is_numeric($id)) {
                return null;
            }
            $e = Event::with(['lokasi', 'participants', 'pretestPackage', 'posttestPackage'])->withCount('participants')->find((int) $id);
            if ($e) {
                return self::mapEvent($e);
            }
        } catch (\Throwable $e) {
            Log::error('findEventDetailed gagal', ['id' => $id, 'err' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Petakan Eloquent Event → objek view (kompatibel struktur mock).
     */
    public static function mapEvent(Event $e): object
    {
        $parts = $e->relationLoaded('participants') ? $e->participants : collect();
        $count = $parts->count();
        $gains = $parts->whereNotNull('n_gain')->map(fn ($p) => (float) $p->n_gain)->values();
        $avg = $gains->count() ? round($gains->avg(), 2) : null;
        $kat = $avg === null ? '—' : ($avg >= 0.7 ? 'Tinggi' : ($avg >= 0.3 ? 'Sedang' : 'Rendah'));

        $lok = $e->relationLoaded('lokasi') ? $e->lokasi : null;
        try {
            if (! $lok && $e->lokasi_id) {
                $lok = $e->lokasi;
            }
        } catch (\Throwable $ex) {
        }

        return (object) [
            'id' => $e->id,
            'nama_kegiatan' => $e->nama_kegiatan,
            'kode_join' => $e->kode_join,
            'kode_event' => $e->kode_event,
            'status' => $e->status ?? 'dijadwalkan',
            'status_fase' => $e->status_fase ?? 'DRAFT',
            'tanggal' => $e->tanggal ? Carbon::parse($e->tanggal)->format('Y-m-d') : null,
            'durasi_menit' => (int) ($e->durasi_menit ?? 30),
            'catatan' => $e->catatan ?? '',
            'lokasi_id' => $e->lokasi_id,
            'lokasi' => $lok ? (object) [
                'id' => $lok->id,
                'nama_lokasi' => $lok->nama_lokasi,
                'jenis_sasaran' => $lok->jenis_sasaran ?? 'sekolah',
                'kecamatan' => $lok->kecamatan ?? null,
                'alamat' => $lok->alamat ?? null,
            ] : null,
            'pretest_package_id' => $e->pretest_package_id,
            'posttest_package_id' => $e->posttest_package_id,
            'peserta_count' => $count,
            'participants_count' => $count,
            'avg_gain' => $avg,
            'kategori_gain' => $kat,
            'created_at' => $e->created_at,
        ];
    }

    /**
     * Peserta kegiatan untuk Meja Kerja — DB-first (MySQL).
     */
    public static function eventParticipants($eventId)
    {
        try {
            if (! is_numeric($eventId)) {
                return collect();
            }
            $rows = Participant::where('event_id', (int) $eventId)->orderBy('id')->get();

            return $rows->map(function ($p) {
                $cat = strtolower($p->category ?? 'kurang');
                $st = strtolower($p->status ?? 'menunggu');

                return (object) [
                    'id' => $p->id,
                    'event_id' => $p->event_id,
                    'name' => $p->name,
                    'class_grade' => $p->class_grade ?? '-',
                    'pretest_score' => $p->pretest_score !== null ? (float) $p->pretest_score : null,
                    'posttest_score' => $p->posttest_score !== null ? (float) $p->posttest_score : null,
                    'n_gain' => $p->n_gain !== null ? (float) $p->n_gain : null,
                    'category' => $cat,
                    'kategori' => ucfirst($cat),
                    'status' => $st === 'selesai' ? 'selesai'
                        : (in_array($st, ['pretest', 'posttest', 'jeda', 'mengerjakan']) ? 'mengerjakan' : 'menunggu'),
                    'input_method' => $p->input_method ?? 'manual',
                ];
            });
        } catch (\Throwable $e) {
            Log::error('eventParticipants gagal', ['event_id' => $eventId, 'err' => $e->getMessage()]);

            return collect();
        }
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
            'total' => $total,
            'avg_pre' => $pre !== null ? round($pre, 1) : 0,
            'avg_post' => $post !== null ? round($post, 1) : 0,
            'avg_gain' => $gain !== null ? round($gain, 2) : 0,
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
        $pkgById = collect($packages)->keyBy('id');

        if ($request->boolean('paket_sama', true)) {
            $request->validate(
                ['paket_utama' => 'required'],
                ['paket_utama.required' => 'Pilih paket soal untuk Pre & Post-Test.']
            );
            $pretestPkgId = (int) $request->input('paket_utama');
            $posttestPkgId = $pretestPkgId;
        } else {
            $request->validate(
                [
                    'pretest_package_id' => 'required',
                    'posttest_package_id' => 'required',
                ],
                [
                    'pretest_package_id.required' => 'Pilih paket soal Pre-Test.',
                    'posttest_package_id.required' => 'Pilih paket soal Post-Test.',
                ]
            );
            $pretestPkgId = (int) $request->input('pretest_package_id');
            $posttestPkgId = (int) $request->input('posttest_package_id');
        }

        $pretestPkg = $pkgById->get($pretestPkgId);
        $posttestPkg = $pkgById->get($posttestPkgId);
        if (! $pretestPkg || ! $posttestPkg) {
            throw ValidationException::withMessages([
                'paket_utama' => 'Paket soal tidak dikenal. Pilih dari daftar yang tersedia.',
            ]);
        }

        $preCount = $pretestPkg->soal_count ?? $pretestPkg->questions_count ?? 0;
        $postCount = $posttestPkg->soal_count ?? $posttestPkg->questions_count ?? 0;
        if ($pretestPkgId !== $posttestPkgId && $preCount !== $postCount
            && ! $request->boolean('konfirmasi_beda')) {
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
        $search = strtolower(trim($request->get('search', '')));
        $period = $request->get('period', 'all');
        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');

        $allEvents = self::allEventsDetailed();

        $filtered = collect($allEvents)->filter(function ($k) use ($search, $period, $dateFrom, $dateTo) {
            // Filter Search Nama Kegiatan / Lokasi
            if ($search) {
                $matchNama = str_contains(strtolower($k->nama_kegiatan), $search);
                $matchLokasi = str_contains(strtolower($k->lokasi->nama_lokasi ?? ''), $search);
                if (! $matchNama && ! $matchLokasi) {
                    return false;
                }
            }

            // Filter Periode
            if ($period !== 'all' && ! empty($k->tanggal)) {
                $d = Carbon::parse($k->tanggal);
                if ($period === 'custom') {
                    if ($dateFrom && $d->lt(Carbon::parse($dateFrom)->startOfDay())) {
                        return false;
                    }
                    if ($dateTo && $d->gt(Carbon::parse($dateTo)->endOfDay())) {
                        return false;
                    }
                } else {
                    $days = (int) $period;
                    $cutoff = now()->subDays($days)->startOfDay();
                    if ($d->lt($cutoff) || $d->gt(now()->endOfDay())) {
                        return false;
                    }
                }
            }

            return true;
        })->values()->all();

        $page = max(1, (int) $request->get('page', 1));
        $kegiatan = new LengthAwarePaginator(
            array_slice($filtered, ($page - 1) * 10, 10),
            count($filtered), 10, $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        $lokasiList = (new LokasiService)->allLocations();
        $bankSoalList = (new BankSoalService)->allPackages();
        $filters = compact('search', 'period', 'dateFrom', 'dateTo');

        $gains = array_values(array_filter(array_map(
            fn ($e) => $e->avg_gain ?? null, $allEvents
        ), fn ($g) => $g !== null));
        $stats = [
            'total' => count($allEvents),
            'aktif' => count(array_filter($allEvents, fn ($e) => ($e->status ?? '') === 'berlangsung')),
            'totalPeserta' => array_sum(array_map(fn ($e) => (int) ($e->peserta_count ?? 0), $allEvents)),
            'avgNGain' => count($gains) ? number_format(round(array_sum($gains) / count($gains), 2), 2) : '0.00',
        ];

        return view('operator.kegiatan.index', compact('kegiatan', 'lokasiList', 'bankSoalList', 'filters', 'stats'));
    }

    /**
     * Halaman Tambah Kegiatan
     */
    public function create()
    {
        $lokasiList = LokasiController::allLocations();
        $paketList = BankSoalController::allPackages();

        return view('operator.kegiatan.create', compact('lokasiList', 'paketList'));
    }

    /**
     * Simpan Kegiatan Baru — DB-first via CreateEventAction (transaksional).
     */
    public function store(StoreKegiatanRequest $request, CreateEventAction $action)
    {
        $data = $request->validated();
        $lokasiObj = Location::find($data['lokasi_id']);
        if (! $lokasiObj) {
            return back()->withInput()
                ->withErrors(['lokasi_id' => 'Lokasi tidak dikenal. Pilih dari daftar yang muncul saat mengetik, atau tambah baru.']);
        }

        $pkg = $this->resolvePackages($request);
        $data['pretest_package_id'] = $pkg['pretestPkgId'];
        $data['posttest_package_id'] = $pkg['posttestPkgId'];
        $data['kategori_audiens'] = self::audiensDariLokasi($lokasiObj);

        try {
            $event = $action->handle($data, auth()->id());
        } catch (\Throwable $e) {
            Log::error('Gagal buat kegiatan', ['err' => $e->getMessage(), 'by' => auth()->id()]);

            return back()->withInput()->withErrors(['nama_kegiatan' => 'Gagal menyimpan kegiatan. Coba lagi.']);
        }

        return redirect()->route('operator.kegiatan.detail', $event->id)
            ->with('success', "Kegiatan '{$event->nama_kegiatan}' berhasil dibuat! Kode PIN Sesi: {$event->kode_join}");
    }

    /**
     * Kode join unik 6 karakter (cek tabrakan di DB).
     */
    protected static function uniqueKodeJoin(): string
    {
        for ($i = 0; $i < 10; $i++) {
            $kode = strtoupper(Str::random(6));
            if (Event::where('kode_join', $kode)->exists()) {
                continue;
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
        if (! $kegiatan) {
            return redirect()->route('operator.kegiatan.index')
                ->with('warning', 'Kegiatan tidak ditemukan.');
        }
        $participants = self::eventParticipants($kegiatan->id);
        $stats = self::eventStats($kegiatan->id);

        return view('operator.kegiatan.detail', compact('kegiatan', 'participants', 'stats'));
    }

    /**
     * Halaman / Data Edit Kegiatan
     */
    public function edit(Request $request, $id)
    {
        $kegiatan = self::findEventDetailed($id);
        if (! $kegiatan) {
            return redirect()->route('operator.kegiatan.index')
                ->with('warning', 'Kegiatan tidak ditemukan.');
        }
        $lokasiList = LokasiController::allLocations();
        $paketList = BankSoalController::allPackages();
        $policy = self::editPolicy($kegiatan);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'id' => $kegiatan->id,
                'nama_kegiatan' => $kegiatan->nama_kegiatan,
                'lokasi_id' => $kegiatan->lokasi_id,
                'lokasi' => $kegiatan->lokasi,
                'tanggal' => $kegiatan->tanggal,
                'durasi_menit' => $kegiatan->durasi_menit,
                'catatan' => $kegiatan->catatan,
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
    public function update(UpdateKegiatanRequest $request, $id)
    {
        $event = Event::find($id);
        if (! $event) {
            return redirect()->route('operator.kegiatan.index')
                ->with('warning', 'Kegiatan tidak ditemukan.');
        }
        $this->authorize('update', $event);
        $kegiatan = self::mapEvent($event->load(['lokasi', 'participants']));
        $policy = self::editPolicy($kegiatan);

        $attrs = [
            'nama_kegiatan' => $request->input('nama_kegiatan'),
            'catatan' => $request->input('catatan', ''),
        ];

        if ($policy['jadwal_terkunci']) {
            $this->rejectSmuggledSchedule($request, $kegiatan);
        } else {
            $request->validate([
                'lokasi_id' => 'nullable',
                'tanggal' => 'nullable|date',
                'durasi_menit' => 'nullable|integer|min:5|max:180',
            ]);
            if ($request->filled('lokasi_id') && ! LokasiController::findLocation($request->input('lokasi_id'))) {
                throw ValidationException::withMessages([
                    'lokasi_id' => 'Lokasi tidak dikenal. Pilih dari daftar yang muncul saat mengetik, atau tambah baru.',
                ]);
            }
            if ($request->filled('lokasi_id')) {
                $attrs['lokasi_id'] = (int) $request->input('lokasi_id');
            }
            if ($request->filled('tanggal')) {
                $attrs['tanggal'] = $request->input('tanggal');
            }
            if ($request->filled('durasi_menit')) {
                $attrs['durasi_menit'] = (int) $request->input('durasi_menit');
            }
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
                'paket_utama' => 'Paket soal terkunci karena '.($policy['peserta_count'] > 0
                    ? "sudah ada {$policy['peserta_count']} peserta."
                    : "status kegiatan '{$policy['status']}'."),
            ]);
        }

        try {
            DB::transaction(function () use ($event, $attrs) {
                $event->update($attrs);
            });
        } catch (\Throwable $e) {
            Log::error('Gagal ubah kegiatan', ['id' => $id, 'err' => $e->getMessage()]);

            return back()->withInput()->withErrors(['nama_kegiatan' => 'Gagal menyimpan perubahan.']);
        }

        AuditService::record('ubah_kegiatan', 'event', $id, "Kegiatan #{$id} diperbarui.");
        Log::info('Kegiatan diubah', ['id' => $id, 'by' => auth()->id()]);

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
                if ($old !== $new) {
                    $errors['tanggal'] = 'Tanggal terkunci dan tidak dapat diubah pada kondisi ini.';
                }
            } catch (\Throwable $e) {
                $errors['tanggal'] = 'Tanggal terkunci dan tidak dapat diubah pada kondisi ini.';
            }
        }

        if ($request->filled('durasi_menit')
            && (int) $request->input('durasi_menit') !== (int) ($event->durasi_menit ?? 30)) {
            $errors['durasi_menit'] = 'Durasi terkunci dan tidak dapat diubah pada kondisi ini.';
        }

        if (! empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }

    /**
     * Ubah Status Kegiatan (dijadwalkan / berlangsung / selesai)
     */
    public function updateStatus(Request $request, $id)
    {
        $newStatus = strtolower($request->get('status', 'berlangsung'));
        if (! in_array($newStatus, ['dijadwalkan', 'berlangsung', 'selesai'], true)) {
            return ApiResponse::fail('Status tidak dikenal.', null, 422);
        }
        $event = Event::find($id);
        if (! $event) {
            return ApiResponse::notFound('Kegiatan tidak ditemukan.');
        }
        try {
            DB::transaction(fn () => $event->update(['status' => $newStatus]));
        } catch (\Throwable $e) {
            Log::error('Gagal ubah status', ['id' => $id, 'err' => $e->getMessage()]);

            return ApiResponse::fail('Gagal mengubah status.', null, 500);
        }
        AuditService::record('ubah_status', 'event', $id, "Status kegiatan #{$id} → {$newStatus}");
        Log::info('Status kegiatan diubah', ['id' => $id, 'status' => $newStatus, 'by' => auth()->id()]);

        return ApiResponse::ok(['status' => $newStatus], 'Status kegiatan berhasil diubah.');
    }

    /**
     * Ubah Fase Sesi (DRAFT / PRE_ACTIVE / MATERIAL_PAUSED / POST_ACTIVE / COMPLETED).
     * Mengendalikan pintu pre-test & post-test yang dipolling siswa.
     */
    public function updateFase(Request $request, $id)
    {
        $fase = strtoupper($request->get('fase', ''));
        $allowed = ['DRAFT', 'PRE_ACTIVE', 'MATERIAL_PAUSED', 'POST_ACTIVE', 'COMPLETED'];
        if (! in_array($fase, $allowed, true)) {
            return ApiResponse::fail('Fase tidak dikenal.', null, 422);
        }
        $event = Event::find($id);
        if (! $event) {
            return ApiResponse::notFound('Kegiatan tidak ditemukan.');
        }
        try {
            DB::transaction(fn () => $event->update(['status_fase' => $fase]));
        } catch (\Throwable $e) {
            Log::error('Gagal ubah fase', ['id' => $id, 'err' => $e->getMessage()]);

            return ApiResponse::fail('Gagal mengubah fase.', null, 500);
        }
        AuditService::record('ubah_fase', 'event', $id, "Fase sesi kegiatan #{$id} → {$fase}");
        Log::info('Fase diubah', ['id' => $id, 'fase' => $fase, 'by' => auth()->id()]);

        return ApiResponse::ok(['fase' => $fase], 'Fase sesi berhasil diubah.');
    }

    /**
     * Hapus Kegiatan — Policy + transaksi (JSON envelope untuk ReauthModal).
     */
    public function destroy($id)
    {
        $ev = Event::find($id);
        if (! $ev) {
            if (request()->wantsJson() || request()->ajax()) {
                return ApiResponse::notFound('Kegiatan tidak ditemukan.');
            }

            return redirect()->route('operator.kegiatan.index')->with('warning', 'Kegiatan tidak ditemukan.');
        }
        $this->authorize('delete', $ev);
        $nama = $ev->nama_kegiatan;
        try {
            DB::transaction(fn () => $ev->delete());
        } catch (\Throwable $e) {
            Log::error('Gagal hapus kegiatan', ['id' => $id, 'err' => $e->getMessage()]);
            if (request()->wantsJson() || request()->ajax()) {
                return ApiResponse::fail('Gagal menghapus kegiatan.', null, 500);
            }

            return back()->withErrors(['general' => 'Gagal menghapus kegiatan.']);
        }
        AuditService::record('hapus_kegiatan', 'event', $id, "Kegiatan dihapus: {$nama}");
        Log::info('Kegiatan dihapus', ['id' => $id, 'by' => auth()->id()]);
        if (request()->wantsJson() || request()->ajax()) {
            return ApiResponse::ok(null, 'Kegiatan berhasil dihapus.');
        }

        return redirect()->route('operator.kegiatan.index')->with('success', 'Kegiatan berhasil dihapus.');
    }

    /**
     * Ekspor Laporan Rekapitulasi Evaluasi BNN (CSV Kompatibel Excel dengan UTF-8 BOM)
     */
    public function exportLaporan($id)
    {
        if (request()->get('queue') == 1) {
            ExportLaporanJob::dispatch((int) $id, auth()->id());
            Log::info('Export laporan diantre', ['event_id' => $id, 'by' => auth()->id()]);
            if (request()->wantsJson() || request()->ajax()) {
                return ApiResponse::ok(['queued' => true], 'Export sedang diproses di antrean.');
            }

            return back()->with('success', 'Export sedang diproses di antrean. File akan tersedia di penyimpanan.');
        }
        $kegiatan = self::findEventDetailed($id);
        if (! $kegiatan) {
            return redirect()->route('operator.kegiatan.index')
                ->with('warning', 'Kegiatan tidak ditemukan.');
        }
        AuditService::record('unduh_rekap', 'event', $kegiatan->id, 'Unduh rekap: '.($kegiatan->nama_kegiatan ?? "kegiatan #{$id}"));
        Log::info('Export laporan langsung', ['event_id' => $id, 'by' => auth()->id()]);
        $participants = self::eventParticipants($kegiatan->id);
        $stats = self::eventStats($kegiatan->id);

        $filename = 'Rekap_Evaluasi_BNN_'.Str::slug($kegiatan->nama_kegiatan ?? 'Sosialisasi').'_'.date('Ymd').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($kegiatan, $participants, $stats) {
            $file = fopen('php://output', 'w');
            // Tambahkan UTF-8 BOM agar Microsoft Excel di Windows membuka huruf latin/Indonesia dengan rapi
            fwrite($file, "\xEF\xBB\xBF");

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
                'Metode Input',
            ]);

            // Isi Data Peserta
            foreach ($participants as $index => $p) {
                $pre = (float) ($p->pretest_score ?? 0);
                $post = (float) ($p->posttest_score ?? 0);
                $delta = round($post - $pre, 2);
                $gain = $p->n_gain !== null ? number_format((float) $p->n_gain, 2) : '-';
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
                    strtoupper($p->input_method ?? 'MANUAL'),
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
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'class_grade' => ['nullable', 'string', 'max:50'],
            'pretest_score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'posttest_score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'input_method' => ['nullable', 'in:manual,omr,online'],
        ]);
        if (! Event::where('id', (int) $id)->exists()) {
            return ApiResponse::notFound('Kegiatan tidak ditemukan.');
        }
        $rowId = $request->get('id');
        try {
            $saved = DB::transaction(function () use ($validated, $id, $rowId) {
                $data = [
                    'name' => trim($validated['name']) ?: 'Peserta Baru',
                    'class_grade' => trim($validated['class_grade'] ?? '-') ?: '-',
                    'pretest_score' => $validated['pretest_score'] ?? 0,
                    'posttest_score' => $validated['posttest_score'] ?? 0,
                    'input_method' => $validated['input_method'] ?? 'manual',
                ];
                if ($rowId && Participant::where('id', (int) $rowId)->where('event_id', (int) $id)->exists()) {
                    Participant::where('id', (int) $rowId)->update($data);

                    return Participant::find((int) $rowId)->refresh();
                }

                return Participant::create(array_merge(['event_id' => (int) $id], $data))->refresh();
            });
            Log::info('Baris peserta disimpan', ['event_id' => $id, 'participant_id' => $saved->id, 'by' => auth()->id()]);
            $gain = $saved->n_gain !== null ? (float) $saved->n_gain : 0;
            $cat = strtolower($saved->category ?? 'kurang');

            return ApiResponse::ok([
                'participant' => [
                    'id' => $saved->id, 'name' => $saved->name, 'class_grade' => $saved->class_grade,
                    'pretest_score' => (float) $saved->pretest_score, 'posttest_score' => (float) $saved->posttest_score,
                    'n_gain' => $gain, 'kategori' => $cat,
                ],
            ], 'Data nilai peserta berhasil disimpan.');
        } catch (\Throwable $e) {
            Log::error('Gagal simpan baris peserta', ['event_id' => $id, 'err' => $e->getMessage()]);

            return ApiResponse::fail('Gagal menyimpan baris peserta.', null, 500);
        }
    }

    /**
     * Hapus Baris Peserta (persist bila baris riil di DB).
     */
    public function deleteParticipantRow(Request $request, $id, $pesertaId)
    {
        try {
            DB::transaction(fn () => Participant::where('id', (int) $pesertaId)->where('event_id', (int) $id)->delete());
            Log::info('Baris peserta dihapus', ['event_id' => $id, 'participant_id' => $pesertaId, 'by' => auth()->id()]);
        } catch (\Throwable $e) {
            Log::error('Gagal hapus baris peserta', ['event_id' => $id, 'err' => $e->getMessage()]);

            return ApiResponse::fail('Gagal menghapus baris peserta.', null, 500);
        }

        return ApiResponse::ok(null, 'Baris peserta berhasil dihapus.');
    }

    /**
     * Daftar peserta jalur digital (online) untuk Live Monitoring.
     * Mock mode: [] (join digital hanya persist di DB).
     */
    public function pesertaDigital($id)
    {
        $list = Participant::where('event_id', $id)
            ->where('input_method', 'online')
            ->orderByDesc('updated_at')->limit(50)->get()
            ->map(fn ($p) => [
                'id' => $p->id, 'name' => $p->name, 'class_grade' => $p->class_grade,
                'pretest_score' => $p->pretest_score !== null ? (float) $p->pretest_score : null,
                'posttest_score' => $p->posttest_score !== null ? (float) $p->posttest_score : null,
                'n_gain' => $p->n_gain !== null ? (float) $p->n_gain : null, 'status' => $p->status,
            ])->all();

        return ApiResponse::ok(['data' => $list], 'Peserta digital.');
    }

    /**
     * Simpan hasil pindaian kamera LJK yang sudah DIVERIFIKASI operator.
     * Grading vs kunci paket event (server-side), persist Participant +
     * Answers + Scan. Idempoten per (peserta, stage): pindai ulang menimpa.
     */
    public function omrSubmit(Request $request, $id)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'kelas' => 'required|string|max:50',
            'stage' => 'required|in:pretest,posttest',
            'answers' => 'required|array|min:1',
            'answers.*.nomor' => 'required|integer|min:1',
            'answers.*.jawaban' => 'nullable|in:A,B,C,D,a,b,c,d',
            'serial' => 'nullable|string|max:50',
            'min_confidence' => 'nullable|numeric',
        ]);

        $event = self::findEventDetailed($id);
        if (! $event) {
            return response()->json(['success' => false, 'message' => 'Kegiatan tidak ditemukan.'], 404);
        }

        $pkgId = $validated['stage'] === 'posttest'
            ? ($event->posttest_package_id ?? null)
            : ($event->pretest_package_id ?? null);
        if (! $pkgId) {
            return response()->json(['success' => false, 'message' => 'Paket soal kegiatan belum ditentukan.'], 422);
        }

        $questions = ParticipantController::resolvePackageQuestions((int) $pkgId);
        usort($questions, fn ($a, $b) => ($a['nomor'] ?? $a['id']) <=> ($b['nomor'] ?? $b['id']));

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
            if ($ok) {
                $correct++;
            }
            $graded[] = ['question_id' => $q['id'], 'nomor' => $nomor, 'jawaban' => $given, 'is_correct' => $ok];
        }
        $total = count($questions);
        $score = $total > 0 ? round($correct / $total * 100, 2) : 0;

        $nama = trim(preg_replace('/\s+/', ' ', $validated['nama']));
        $kelas = trim($validated['kelas']);
        $normNama = strtolower($nama);
        $normKelas = strtolower($kelas);

        // Persist transaksional (DB-first, tanpa mock).
        try {
            $participant = DB::transaction(function () use ($event, $normNama, $normKelas, $nama, $kelas, $validated, $score, $graded, $correct) {
                $p = Participant::where('event_id', (int) $event->id)->get()
                    ->first(fn ($row) => strtolower(trim(preg_replace('/\s+/', ' ', $row->name ?? ''))) === $normNama
                        && strtolower(trim($row->class_grade ?? '')) === $normKelas);
                if (! $p) {
                    $p = Participant::create([
                        'event_id' => (int) $event->id,
                        'name' => $nama,
                        'class_grade' => $kelas,
                        'school_origin' => $event->lokasi->nama_lokasi ?? null,
                        'input_method' => 'omr',
                        'status' => $validated['stage'] === 'pretest' ? 'jeda' : 'selesai',
                    ]);
                }
                if ($validated['stage'] === 'pretest') {
                    $p->pretest_score = $score;
                    if ($p->posttest_score === null) {
                        $p->status = 'jeda';
                    }
                } else {
                    $p->posttest_score = $score;
                }
                $p->save();

                ParticipantAnswer::where('participant_id', $p->id)
                    ->where('stage', $validated['stage'])->delete();
                foreach ($graded as $g) {
                    ParticipantAnswer::create([
                        'participant_id' => $p->id,
                        'question_id' => $g['question_id'],
                        'stage' => $validated['stage'],
                        'jawaban' => $g['jawaban'],
                        'is_correct' => $g['is_correct'],
                    ]);
                }

                Scan::create([
                    'event_id' => (int) $event->id,
                    'participant_id' => $p->id,
                    'phase_type' => $validated['stage'] === 'pretest' ? 'PRE' : 'POST',
                    'raw_answers' => collect($graded)->mapWithKeys(fn ($g) => [$g['nomor'] => $g['jawaban']])->all(),
                    'score_raw' => $correct,
                    'score_percent' => $score,
                    'omr_confidence' => 'HIGH',
                    'captured_by' => auth()->id(),
                    'captured_at' => now(),
                    'capture_method' => 'CAMERA_LIVE',
                    'serial_number' => $validated['serial'] ?? null,
                ]);

                return $p->refresh();
            });
            AuditService::record('omr_scan', 'event', (int) $event->id, "LJK {$validated['stage']} {$nama} ({$kelas}): skor {$score}");
            Log::info('OMR submit tersimpan', ['event_id' => $event->id, 'participant_id' => $participant->id, 'score' => $score, 'by' => auth()->id()]);

            return ApiResponse::ok([
                'score' => $score, 'correct' => $correct, 'total' => $total,
                'n_gain' => $participant->n_gain !== null ? (float) $participant->n_gain : null,
                'category' => $participant->category,
                'participant' => ['id' => $participant->id, 'name' => $participant->name],
            ], 'Hasil pindaian tersimpan.');
        } catch (\Throwable $e) {
            Log::error('OMR submit gagal', ['event_id' => $event->id ?? $id, 'err' => $e->getMessage()]);

            return ApiResponse::fail('Gagal menyimpan hasil pindaian.', null, 500);
        }
    }

    /**
     * Hasil Scan OMR Kamera — DB-first via OmrService (tanpa angka acak).
     * Membutuhkan nama + kelas + stage + answers terverifikasi operator.
     */
    public function omrScanRecord(Request $request, $id, OmrService $omr)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'class_grade' => ['nullable', 'string', 'max:50'],
            'phase_type' => ['nullable', 'in:PRE,POST'],
            'raw_answers' => ['nullable', 'array'],
            'score_raw' => ['nullable', 'integer', 'min:0'],
            'score_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'omr_confidence' => ['nullable', 'in:HIGH,LOW'],
            'participant_id' => ['nullable', 'integer', 'exists:participants,id'],
        ]);
        $event = Event::find($id);
        if (! $event) {
            return ApiResponse::notFound('Kegiatan tidak ditemukan.');
        }
        try {
            $scan = DB::transaction(fn () => $omr->persistScan((int) $id, [
                'participant_id' => $validated['participant_id'] ?? null,
                'phase_type' => $validated['phase_type'] ?? 'PRE',
                'raw_answers' => $validated['raw_answers'] ?? [],
                'score_raw' => $validated['score_raw'] ?? 0,
                'score_percent' => $validated['score_percent'] ?? 0,
                'omr_confidence' => $validated['omr_confidence'] ?? 'HIGH',
                'capture_method' => 'CAMERA_LIVE',
            ], auth()->id()));
            AuditService::record('omr_scan_record', 'event', (int) $id, "Scan OMR direkam: {$validated['nama']}");

            return ApiResponse::ok(['scan_id' => $scan->id], 'Lembar jawaban berhasil dipindai.');
        } catch (\Throwable $e) {
            Log::error('omrScanRecord gagal', ['event_id' => $id, 'err' => $e->getMessage()]);

            return ApiResponse::fail('Gagal merekam hasil pindaian.', null, 500);
        }
    }
}
