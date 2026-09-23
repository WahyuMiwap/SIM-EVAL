<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Location;
use App\Services\MockDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class LokasiController extends Controller
{
    /**
     * Daftar jenis sasaran kanonis.
     * 'komunitas' dipertahankan sebagai alias lama dari 'masyarakat'.
     */
    public const JENIS_SASARAN = ['sekolah', 'kampus', 'masyarakat', 'komunitas', 'lapas', 'instansi'];

    /**
     * Apakah tabel locations DB bisa dipakai (migrasi sudah jalan & ada data)?
     */
    public static function useDatabase(): bool
    {
        try {
            return Schema::hasTable('locations') && Location::query()->exists();
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Ambil semua lokasi — DB dulu, fallback ke Mock (+ session custom).
     * Hasil selalu array of object dengan field:
     * id, nama_lokasi, alamat, kecamatan, jenis_sasaran, events_count.
     */
    public static function allLocations(): array
    {
        try {
            if (Schema::hasTable('locations')) {
                $rows = Location::withCount('events')->orderBy('nama_lokasi')->get();
                if ($rows->isNotEmpty()) {
                    return $rows->map(fn($l) => (object)[
                        'id'            => $l->id,
                        'nama_lokasi'   => $l->nama_lokasi,
                        'alamat'        => $l->alamat,
                        'kecamatan'     => $l->kecamatan,
                        'jenis_sasaran' => $l->jenis_sasaran ?? 'sekolah',
                        'events_count'  => $l->events_count ?? 0,
                        'created_at'    => $l->created_at,
                    ])->all();
                }
            }
        } catch (\Throwable $e) {
            // abaikan — fallback ke mock
        }

        return MockDataService::getLocations();
    }

    /**
     * Cari satu lokasi berdasarkan id (DB dulu, fallback mock).
     */
    public static function findLocation($id): ?object
    {
        if (empty($id)) return null;
        foreach (self::allLocations() as $l) {
            if ((int) $l->id === (int) $id) return $l;
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
        if ($q === '') return array_slice($locations, 0, $limit);

        $out = [];
        foreach ($locations as $loc) {
            $hay = strtolower(($loc->nama_lokasi ?? '') . ' ' . ($loc->kecamatan ?? '') . ' ' . ($loc->alamat ?? '') . ' ' . ($loc->jenis_sasaran ?? ''));
            if (str_contains($hay, $q)) {
                $out[] = $loc;
                if (count($out) >= $limit) break;
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
        $jenis  = $request->get('jenis_sasaran', '');

        $all = self::allLocations();

        if ($jenis) {
            $all = array_values(array_filter($all, function ($l) use ($jenis) {
                $j = $l->jenis_sasaran ?? '';
                if ($jenis === 'masyarakat') return $j === 'masyarakat' || $j === 'komunitas';
                return $j === $jenis;
            }));
        }

        $filtered = $search
            ? self::filterLocations($all, $search, 1000)
            : $all;

        $lokasi = MockDataService::paginate($filtered, 10);

        // Cabang JSON untuk tabel master AJAX (tanpa reload):
        // GET /operator/lokasi?ajax=1&q=...&jenis_sasaran=...&page=N
        if ($request->get('ajax') == 1 || $request->wantsJson()) {
            $items = $lokasi->getCollection()->values()->map(fn($l) => [
                'id'            => $l->id,
                'nama_lokasi'   => $l->nama_lokasi,
                'alamat'        => $l->alamat,
                'kecamatan'     => $l->kecamatan,
                'jenis_sasaran' => $l->jenis_sasaran ?? 'sekolah',
                'events_count'  => $l->events_count ?? $l->kegiatan_count ?? 0,
            ])->all();

            return response()->json([
                'data' => $items,
                'meta' => [
                    'current_page' => $lokasi->currentPage(),
                    'last_page'    => $lokasi->lastPage(),
                    'per_page'     => $lokasi->perPage(),
                    'from'         => $lokasi->firstItem(),
                    'to'           => $lokasi->lastItem(),
                    'total'        => $lokasi->total(),
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
        $q     = trim($request->get('q', ''));
        $limit = max(1, min(50, (int) $request->get('limit', 20)));

        $items = self::filterLocations(self::allLocations(), $q, $limit);

        return response()->json([
            'data' => array_map(fn($l) => [
                'id'            => $l->id,
                'nama_lokasi'   => $l->nama_lokasi,
                'kecamatan'     => $l->kecamatan,
                'alamat'        => $l->alamat,
                'jenis_sasaran' => $l->jenis_sasaran ?? 'sekolah',
                'label'         => $l->nama_lokasi . ' (' . ucfirst($l->jenis_sasaran ?? 'sekolah') . ')',
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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lokasi'   => 'required|string|max:255',
            'alamat'        => 'nullable|string|max:255',
            'kecamatan'     => 'nullable|string|max:100',
            'jenis_sasaran' => 'nullable|in:' . implode(',', self::JENIS_SASARAN),
        ]);

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
                        'id'            => $existing->id,
                        'nama_lokasi'   => $existing->nama_lokasi,
                        'jenis_sasaran' => $existing->jenis_sasaran ?? 'sekolah',
                        'label'         => $existing->nama_lokasi . ' (' . ucfirst($existing->jenis_sasaran ?? 'sekolah') . ')',
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
                    'label' => $l->nama_lokasi . ' (' . ucfirst($l->jenis_sasaran ?? 'sekolah') . ')',
                    'mirip' => round($pct) . '%',
                ];
                if (count($similar) >= 3) break;
            }
        }

        // ── Simpan: DB bila tersedia, fallback session mock ──
        $created = null;
        try {
            if (Schema::hasTable('locations')) {
                $row = Location::create([
                    'nama_lokasi'   => $nama,
                    'alamat'        => $validated['alamat'] ?? null,
                    'kecamatan'     => $validated['kecamatan'] ?? null,
                    'jenis_sasaran' => $jenis,
                ]);
                $created = (object)[
                    'id'            => $row->id,
                    'nama_lokasi'   => $row->nama_lokasi,
                    'alamat'        => $row->alamat,
                    'kecamatan'     => $row->kecamatan,
                    'jenis_sasaran' => $row->jenis_sasaran,
                    'label'         => $row->nama_lokasi . ' (' . ucfirst($row->jenis_sasaran) . ')',
                ];
            }
        } catch (\Throwable $e) {
            $created = null;
        }
        if (!$created) {
            $obj = MockDataService::addCustomLocation([
                'nama_lokasi'   => $nama,
                'alamat'        => $validated['alamat'] ?? null,
                'kecamatan'     => $validated['kecamatan'] ?? null,
                'jenis_sasaran' => $jenis,
            ]);
            $created = (object)[
                'id'            => $obj->id,
                'nama_lokasi'   => $obj->nama_lokasi,
                'alamat'        => $obj->alamat,
                'kecamatan'     => $obj->kecamatan,
                'jenis_sasaran' => $obj->jenis_sasaran,
                'label'         => $obj->nama_lokasi . ' (' . ucfirst($obj->jenis_sasaran) . ')',
            ];
        }

        \App\Services\AuditService::record(
            'tambah_lokasi', 'location', $created->id ?? null,
            "Lokasi ditambahkan: {$created->nama_lokasi}"
        );

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Lokasi baru berhasil ditambahkan.',
                'data'    => $created,
                'similar' => $similar,
            ], 201);
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
        if (!$target) return response()->json(['message' => 'Lokasi tidak ditemukan.'], 404);

        return response()->json($target);
    }

    /**
     * Update Lokasi
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_lokasi'   => 'required|string|max:255',
            'alamat'        => 'nullable|string|max:255',
            'kecamatan'     => 'nullable|string|max:100',
            'jenis_sasaran' => 'nullable|in:' . implode(',', self::JENIS_SASARAN),
        ]);

        try {
            if (Schema::hasTable('locations')) {
                $row = Location::findOrFail($id);
                $row->update([
                    'nama_lokasi'   => self::normalizeName($validated['nama_lokasi']),
                    'alamat'        => $validated['alamat'] ?? null,
                    'kecamatan'     => $validated['kecamatan'] ?? null,
                    'jenis_sasaran' => $validated['jenis_sasaran'] ?? $row->jenis_sasaran,
                ]);
            }
        } catch (\Throwable $e) {
            // Mock mode: update session custom bila ada
            $custom = session('custom_mock_locations', []);
            foreach ($custom as &$c) {
                $c = (object) $c;
                if ((int) $c->id === (int) $id) {
                    $c->nama_lokasi = self::normalizeName($validated['nama_lokasi']);
                    $c->alamat = $validated['alamat'] ?? $c->alamat;
                    $c->kecamatan = $validated['kecamatan'] ?? $c->kecamatan;
                    $c->jenis_sasaran = $validated['jenis_sasaran'] ?? $c->jenis_sasaran;
                }
            }
            session(['custom_mock_locations' => array_map(fn($c) => (object) $c, $custom)]);
        }

        \App\Services\AuditService::record('ubah_lokasi', 'location', $id, "Lokasi #{$id} diperbarui.");

        return redirect()->route('operator.lokasi.index')
            ->with('success', 'Data lokasi berhasil diperbarui.');
    }

    /**
     * Hapus Lokasi — ditolak bila masih dipakai kegiatan.
     */
    public function destroy($id)
    {
        // Cek pemakaian (DB)
        try {
            if (Schema::hasTable('events') && Schema::hasTable('locations')) {
                $used = Event::where('lokasi_id', $id)->count();
                if ($used > 0) {
                    $msg = "Lokasi tidak dapat dihapus karena masih dipakai {$used} kegiatan.";
                    if (request()->wantsJson() || request()->ajax()) {
                        return response()->json(['success' => false, 'message' => $msg], 422);
                    }
                    return redirect()->route('operator.lokasi.index')->with('warning', $msg);
                }
                Location::where('id', $id)->delete();
            }
        } catch (\Throwable $e) {
            // Mock mode: hapus dari session custom saja
            $custom = collect(session('custom_mock_locations', []))
                ->reject(fn($c) => (int) ((object) $c)->id === (int) $id)
                ->values()->all();
            session(['custom_mock_locations' => $custom]);
        }

        \App\Services\AuditService::record('hapus_lokasi', 'location', $id, "Lokasi #{$id} dihapus.");

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
