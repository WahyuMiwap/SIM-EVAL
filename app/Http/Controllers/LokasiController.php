<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLokasiRequest;
use App\Models\Event;
use App\Models\Location;
use App\Services\AuditService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LokasiController extends Controller
{
    /**
     * Daftar jenis sasaran kanonis.
     * 'komunitas' dipertahankan sebagai alias lama dari 'masyarakat'.
     */
    public const JENIS_SASARAN = ['sekolah', 'kampus', 'masyarakat', 'komunitas', 'lapas', 'instansi'];

    /**
     * Apakah tabel locations ada isinya (untuk guard hapus protektif)?
     */
    public static function useDatabase(): bool
    {
        try {
            return Location::query()->exists();
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Ambil semua lokasi (DB simeval_db).
     */
    public static function allLocations(): array
    {
        try {
            return Location::withCount('events')->orderBy('nama_lokasi')->get()->map(fn ($l) => (object) [
                'id' => $l->id,
                'nama_lokasi' => $l->nama_lokasi,
                'alamat' => $l->alamat,
                'kecamatan' => $l->kecamatan,
                'jenis_sasaran' => $l->jenis_sasaran ?? 'sekolah',
                'events_count' => $l->events_count ?? 0,
                'created_at' => $l->created_at,
            ])->all();
        } catch (\Throwable $e) {
            Log::warning('allLocations gagal', ['err' => $e->getMessage()]);

            return [];
        }
    }

    /**
     * Cari satu lokasi berdasarkan id (DB dulu, fallback mock).
     */
    public static function findLocation($id): ?object
    {
        if (empty($id)) {
            return null;
        }
        foreach (self::allLocations() as $l) {
            if ((int) $l->id === (int) $id) {
                return $l;
            }
        }

        return null;
    }

    /**
     * Normalisasi nama lokasi untuk anti-duplikat:
     * trim + rapikan spasi ganda.
     */
    public static function normalizeName(string $nama): string
    {
        return trim(preg_replace('/\s+/', ' ', $nama));
    }

    /**
     * Filter lokasi berdasarkan kata kunci (nama/kecamatan/alamat).
     */
    public static function filterLocations(array $locations, string $q, int $limit = 20): array
    {
        $q = strtolower(trim($q));
        if ($q === '') {
            return array_slice($locations, 0, $limit);
        }

        $out = [];
        foreach ($locations as $loc) {
            $hay = strtolower(($loc->nama_lokasi ?? '').' '.($loc->kecamatan ?? '').' '.($loc->alamat ?? '').' '.($loc->jenis_sasaran ?? ''));
            if (str_contains($hay, $q)) {
                $out[] = $loc;
                if (count($out) >= $limit) {
                    break;
                }
            }
        }

        return $out;
    }

    /**
     * Daftar Lokasi Binaan (Master — disederhanakan jadi tabel CRUD ringan).
     */
    public function index(Request $request)
    {
        $search = trim($request->get('search', ''));
        $jenis = $request->get('jenis_sasaran', '');

        $all = self::allLocations();

        if ($jenis) {
            $all = array_values(array_filter($all, function ($l) use ($jenis) {
                $j = $l->jenis_sasaran ?? '';
                if ($jenis === 'masyarakat') {
                    return $j === 'masyarakat' || $j === 'komunitas';
                }

                return $j === $jenis;
            }));
        }

        $filtered = $search
            ? self::filterLocations($all, $search, 1000)
            : $all;

        $page = max(1, (int) $request->get('page', 1));
        $lokasi = new LengthAwarePaginator(array_slice($filtered, ($page - 1) * 10, 10), count($filtered), 10, $page, ['path' => $request->url(), 'query' => $request->query()]);

        // Cabang JSON untuk tabel master AJAX (tanpa reload):
        // GET /operator/lokasi?ajax=1&q=...&jenis_sasaran=...&page=N
        if ($request->get('ajax') == 1 || $request->wantsJson()) {
            $items = $lokasi->getCollection()->values()->map(fn ($l) => [
                'id' => $l->id,
                'nama_lokasi' => $l->nama_lokasi,
                'alamat' => $l->alamat,
                'kecamatan' => $l->kecamatan,
                'jenis_sasaran' => $l->jenis_sasaran ?? 'sekolah',
                'events_count' => $l->events_count ?? $l->kegiatan_count ?? 0,
            ])->all();

            return response()->json([
                'data' => $items,
                'meta' => [
                    'current_page' => $lokasi->currentPage(),
                    'last_page' => $lokasi->lastPage(),
                    'per_page' => $lokasi->perPage(),
                    'from' => $lokasi->firstItem(),
                    'to' => $lokasi->lastItem(),
                    'total' => $lokasi->total(),
                ],
            ]);
        }

        return view('operator.lokasi.index', compact('lokasi', 'search', 'jenis'));
    }

    /**
     * Endpoint JSON untuk combobox searchable.
     * GET /operator/lokasi/search?q=tegal&limit=20
     */
    public function search(Request $request)
    {
        $q = trim($request->get('q', ''));
        $limit = max(1, min(50, (int) $request->get('limit', 20)));

        $items = self::filterLocations(self::allLocations(), $q, $limit);

        return response()->json([
            'data' => array_map(fn ($l) => [
                'id' => $l->id,
                'nama_lokasi' => $l->nama_lokasi,
                'kecamatan' => $l->kecamatan,
                'alamat' => $l->alamat,
                'jenis_sasaran' => $l->jenis_sasaran ?? 'sekolah',
                'label' => $l->nama_lokasi.' ('.ucfirst($l->jenis_sasaran ?? 'sekolah').')',
            ], $items),
        ]);
    }

    /**
     * Tambah Lokasi Baru.
     * - Form biasa (Master): redirect seperti dulu.
     * - Quick-Add dari form Kegiatan (fetch JSON): kembalikan 201 + objek.
     * - Duplikat (case-insensitive): 422 + kandidat mirip agar user
     *   bisa pilih yang sudah ada ("Maksudmu ...?").
     */
    public function store(StoreLokasiRequest $request)
    {
        $validated = $request->validated();

        $nama = self::normalizeName($validated['nama_lokasi']);
        $jenis = $validated['jenis_sasaran'] ?? 'sekolah';

        // ── Cek duplikat (case-insensitive) ──
        $existing = null;
        foreach (self::allLocations() as $l) {
            if (strtolower(self::normalizeName($l->nama_lokasi ?? '')) === strtolower($nama)) {
                $existing = $l;
                break;
            }
        }
        if ($existing) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lokasi sudah terdaftar.',
                    'existing' => [
                        'id' => $existing->id,
                        'nama_lokasi' => $existing->nama_lokasi,
                        'jenis_sasaran' => $existing->jenis_sasaran ?? 'sekolah',
                        'label' => $existing->nama_lokasi.' ('.ucfirst($existing->jenis_sasaran ?? 'sekolah').')',
                    ],
                ], 422);
            }

            return redirect()->route('operator.lokasi.index')
                ->with('warning', "Lokasi \"{$existing->nama_lokasi}\" sudah terdaftar.");
        }

        // Kandidat mirip (untuk saran, dikirim saat JSON juga bila diminta)
        $similar = [];
        foreach (self::allLocations() as $l) {
            similar_text(strtolower($nama), strtolower($l->nama_lokasi ?? ''), $pct);
            if ($pct >= 60) {
                $similar[] = [
                    'id' => $l->id,
                    'nama_lokasi' => $l->nama_lokasi,
                    'label' => $l->nama_lokasi.' ('.ucfirst($l->jenis_sasaran ?? 'sekolah').')',
                    'mirip' => round($pct).'%',
                ];
                if (count($similar) >= 3) {
                    break;
                }
            }
        }

        // ── Simpan: DB bila tersedia, fallback session mock ──
        $created = null;
        try {
            $row = DB::transaction(fn () => Location::create([
                'nama_lokasi' => $nama,
                'alamat' => $validated['alamat'] ?? null,
                'kecamatan' => $validated['kecamatan'] ?? null,
                'jenis_sasaran' => $jenis,
            ]));
            $created = (object) [
                'id' => $row->id,
                'nama_lokasi' => $row->nama_lokasi,
                'alamat' => $row->alamat,
                'kecamatan' => $row->kecamatan,
                'jenis_sasaran' => $row->jenis_sasaran,
                'label' => $row->nama_lokasi.' ('.ucfirst($row->jenis_sasaran).')',
            ];
            Log::info('Lokasi ditambahkan', ['id' => $row->id, 'by' => auth()->id()]);
        } catch (\Throwable $e) {
            Log::error('Gagal tambah lokasi', ['err' => $e->getMessage()]);
            if ($request->wantsJson() || $request->ajax()) {
                return ApiResponse::fail('Gagal menambahkan lokasi.', null, 500);
            }

            return back()->withInput()->withErrors(['nama_lokasi' => 'Gagal menambahkan lokasi.']);
        }

        AuditService::record('tambah_lokasi', 'location', $created->id ?? null, "Lokasi ditambahkan: {$created->nama_lokasi}");

        if ($request->wantsJson() || $request->ajax()) {
            return ApiResponse::ok(['data' => $created, 'similar' => $similar], 'Lokasi baru berhasil ditambahkan.');
        }

        return redirect()->route('operator.lokasi.index')
            ->with('success', "Lokasi \"{$created->nama_lokasi}\" berhasil ditambahkan.");
    }

    /**
     * Edit Lokasi (AJAX JSON)
     */
    public function edit($id)
    {
        $target = self::findLocation($id);
        if (! $target) {
            return response()->json(['message' => 'Lokasi tidak ditemukan.'], 404);
        }

        return response()->json($target);
    }

    /**
     * Update Lokasi
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_lokasi' => 'required|string|max:255',
            'alamat' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:100',
            'jenis_sasaran' => 'nullable|in:'.implode(',', self::JENIS_SASARAN),
        ]);

        $row = Location::findOrFail($id);
        DB::transaction(fn () => $row->update([
            'nama_lokasi' => self::normalizeName($validated['nama_lokasi']),
            'alamat' => $validated['alamat'] ?? null,
            'kecamatan' => $validated['kecamatan'] ?? null,
            'jenis_sasaran' => $validated['jenis_sasaran'] ?? $row->jenis_sasaran,
        ]));
        Log::info('Lokasi diubah', ['id' => $id, 'by' => auth()->id()]);

        AuditService::record('ubah_lokasi', 'location', $id, "Lokasi #{$id} diperbarui.");

        return redirect()->route('operator.lokasi.index')
            ->with('success', 'Data lokasi berhasil diperbarui.');
    }

    /**
     * Hapus Lokasi — ditolak bila masih dipakai kegiatan.
     */
    public function destroy($id)
    {
        // Cek pemakaian — tolak bila masih dipakai kegiatan
        $used = Event::where('lokasi_id', $id)->count();
        if ($used > 0) {
            $msg = "Lokasi tidak dapat dihapus karena masih dipakai {$used} kegiatan.";
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }

            return redirect()->route('operator.lokasi.index')->with('warning', $msg);
        }
        $this->authorize('delete', Location::findOrFail($id));
        DB::transaction(fn () => Location::where('id', $id)->delete());
        Log::info('Lokasi dihapus', ['id' => $id, 'by' => auth()->id()]);

        AuditService::record('hapus_lokasi', 'location', $id, "Lokasi #{$id} dihapus.");

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Lokasi sasaran berhasil dihapus.',
            ]);
        }

        return redirect()->route('operator.lokasi.index')
            ->with('success', 'Lokasi berhasil dihapus.');
    }
}
