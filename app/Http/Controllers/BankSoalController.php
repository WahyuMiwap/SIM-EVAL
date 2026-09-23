<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Question;
use App\Models\QuestionPackage;
use App\Services\MockDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class BankSoalController extends Controller
{
    public const TIPE_LIST = ['pretest', 'posttest', 'umum', 'kombinasi'];

    /**
     * Ambil semua paket — DB dulu, fallback ke Mock (+ session custom).
     * Setiap item: id, nama_paket, tema, tipe, durasi, soal_count,
     * events_count (dipakai berapa kegiatan), questions, deskripsi.
     */
    public static function allPackages(): array
    {
        try {
            if (Schema::hasTable('question_packages')) {
                $rows = QuestionPackage::withCount(['questions', 'eventsPre', 'eventsPost'])
                    ->orderBy('nama_paket')->get();
                if ($rows->isNotEmpty()) {
                    return $rows->map(function ($p) {
                        $soal = (int) ($p->questions_count ?? 0);
                        return (object)[
                            'id'              => $p->id,
                            'nama_paket'      => $p->nama_paket,
                            'tema'            => $p->tema ?? null,
                            'tipe'            => $p->tipe ?? 'umum',
                            'durasi'          => $p->durasi ?? 30,
                            'deskripsi'       => null,
                            'acak_urutan'     => (bool) ($p->acak_urutan ?? true),
                            'soal_count'      => $soal,
                            'questions_count' => $soal,
                            'events_count'    => (int) ($p->events_pre_count ?? 0) + (int) ($p->events_post_count ?? 0),
                            'questions'       => $p->questions,
                            'created_at'      => $p->created_at,
                        ];
                    })->all();
                }
            }
        } catch (\Throwable $e) {
            // abaikan — fallback ke mock
        }

        return MockDataService::getQuestionPackages();
    }

    public static function findPackage($id): ?object
    {
        if (empty($id)) return null;
        foreach (self::allPackages() as $p) {
            if ((int) $p->id === (int) $id) return $p;
        }
        return null;
    }

    /**
     * Hitung pemakaian paket oleh kegiatan (DB bila tersedia).
     */
    public static function usageCount($id): int
    {
        $pkg = self::findPackage($id);
        if ($pkg && isset($pkg->events_count)) return (int) $pkg->events_count;
        try {
            if (Schema::hasTable('events')) {
                return Event::where('pretest_package_id', $id)
                    ->orWhere('posttest_package_id', $id)->count();
            }
        } catch (\Throwable $e) {
        }
        return 0;
    }

    /**
     * Daftar Paket Bank Soal (filter search + tema).
     */
    public function index(Request $request)
    {
        $search = strtolower(trim($request->get('search', '')));
        $tema   = trim($request->get('tema', ''));

        $all = self::allPackages();

        $temas = collect($all)->map(fn($p) => $p->tema ?? null)
            ->filter()->unique()->sort()->values()->all();

        $filtered = collect($all)->filter(function ($pkg) use ($search, $tema) {
            if ($search && !str_contains(strtolower($pkg->nama_paket ?? ''), $search)) {
                return false;
            }
            if ($tema && ($pkg->tema ?? '') !== $tema) {
                return false;
            }
            return true;
        })->values()->all();

        $bankSoal = MockDataService::paginate($filtered, 10);

        return view('operator.bank-soal.index', compact('bankSoal', 'search', 'tema', 'temas'));
    }

    /**
     * Form Buat Paket Baru
     */
    public function create()
    {
        $temas = collect(self::allPackages())->map(fn($p) => $p->tema ?? null)
            ->filter()->unique()->sort()->values()->all();
        return view('operator.bank-soal.create', compact('temas'));
    }

    /**
     * Simpan Paket Soal Baru (DB bila tersedia, fallback session mock).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_paket' => 'required|string|max:255',
            'tema'       => 'nullable|string|max:100',
            'tipe'       => 'nullable|in:' . implode(',', self::TIPE_LIST),
            'durasi'     => 'nullable|integer|min:5|max:180',
        ]);

        $soalInput = $request->get('soal', []);
        $soalCount = is_array($soalInput) ? count(array_filter($soalInput, fn($s) => !empty($s['pertanyaan'] ?? null))) : 0;

        $tema = trim($validated['tema'] ?? '') ?: null;
        $tipe = $validated['tipe'] ?? 'umum';
        $durasi = (int) ($validated['durasi'] ?? 30);

        $newId = null;
        try {
            if (Schema::hasTable('question_packages')) {
                $row = QuestionPackage::create([
                    'nama_paket' => trim($validated['nama_paket']),
                    'tema'       => $tema,
                    'tipe'       => $tipe,
                    'durasi'     => $durasi,
                ]);
                foreach (array_values(is_array($soalInput) ? $soalInput : []) as $i => $s) {
                    if (empty($s['pertanyaan'] ?? null)) continue;
                    Question::create([
                        'question_package_id' => $row->id,
                        'pertanyaan'          => $s['pertanyaan'],
                        'opsi_a'              => $s['opsi']['A'] ?? '',
                        'opsi_b'              => $s['opsi']['B'] ?? '',
                        'opsi_c'              => $s['opsi']['C'] ?? '',
                        'opsi_d'              => $s['opsi']['D'] ?? '',
                        'kunci'               => strtoupper($s['kunci'] ?? 'A'),
                        'urutan'              => $i + 1,
                    ]);
                }
                $newId = $row->id;
            }
        } catch (\Throwable $e) {
            $newId = null;
        }
        if (!$newId) {
            $obj = MockDataService::addCustomPackage([
                'nama_paket' => trim($validated['nama_paket']),
                'tema'       => $tema,
                'tipe'       => $tipe,
                'durasi'     => $durasi,
                'soal_count' => $soalCount,
            ]);
            $newId = $obj->id;
        }

        \App\Services\AuditService::record(
            'buat_paket', 'package', $newId,
            'Paket soal dibuat: ' . trim($validated['nama_paket'])
        );

        return redirect(
            $request->get('lagi') == 1
                ? route('operator.bank-soal.create')
                : route('operator.bank-soal.detail', $newId)
        )->with('success', $request->get('lagi') == 1
            ? 'Paket soal berhasil dibuat. Silakan buat paket berikutnya.'
            : 'Paket soal baru berhasil dibuat. Silakan tambahkan butir soal.');
    }


    /**
     * Detail Paket & Daftar Butir Soal
     */
    public function detail($id)
    {
        $paket = self::findPackage($id) ?? self::allPackages()[0];
        $soalList = $paket->questions ?? collect([]);
        $dipakai = self::usageCount($paket->id);

        return view('operator.bank-soal.detail', compact('paket', 'soalList', 'dipakai'));
    }

    /**
     * Edit Paket Soal
     */
    public function edit(Request $request, $id)
    {
        $all = self::allPackages();
        $paket = self::findPackage($id) ?? $all[0];

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'id'          => $paket->id,
                'nama_paket'  => $paket->nama_paket,
                'tema'        => $paket->tema ?? null,
                'tipe'        => $paket->tipe,
                'durasi'      => $paket->durasi,
                'acak_urutan' => $paket->acak_urutan,
            ]);
        }

        $soalList = $paket->questions ?? collect([]);
        $dipakai = self::usageCount($paket->id);
        $temas = collect($all)->map(fn($p) => $p->tema ?? null)
            ->filter()->unique()->sort()->values()->all();

        return view('operator.bank-soal.edit', compact('paket', 'soalList', 'dipakai', 'temas'));
    }

    /**
     * Update Paket Soal — boleh, tapi beri peringatan bila sudah dipakai
     * kegiatan (perubahan memengaruhi makna historis N-Gain).
     * Butir soal dari builder ikut disinkronkan (cabang DB).
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_paket' => 'required|string|max:255',
            'tema'       => 'nullable|string|max:100',
            'tipe'       => 'nullable|in:' . implode(',', self::TIPE_LIST),
            'durasi'     => 'nullable|integer|min:5|max:180',
            'soal'       => 'nullable|array',
            'soal.*.pertanyaan' => 'nullable|string',
            'soal.*.kunci'      => 'nullable|in:A,B,C,D,a,b,c,d',
        ]);

        $soalInput = $request->get('soal', null);

        try {
            if (Schema::hasTable('question_packages')) {
                $row = QuestionPackage::findOrFail($id);
                $row->update([
                    'nama_paket' => trim($validated['nama_paket']),
                    'tema'       => trim($validated['tema'] ?? '') ?: null,
                    'tipe'       => $validated['tipe'] ?? $row->tipe,
                    'durasi'     => (int) ($validated['durasi'] ?? $row->durasi),
                ]);

                // Sinkron butir soal bila builder mengirimkannya.
                // Aman: tidak ada FK peserta → soal (skor tersimpan sebagai angka).
                if (is_array($soalInput)) {
                    Question::where('question_package_id', $row->id)->delete();
                    $urutan = 0;
                    foreach (array_values($soalInput) as $s) {
                        if (empty(trim($s['pertanyaan'] ?? ''))) continue;
                        $urutan++;
                        Question::create([
                            'question_package_id' => $row->id,
                            'pertanyaan'          => trim($s['pertanyaan']),
                            'opsi_a'              => trim($s['opsi']['A'] ?? ''),
                            'opsi_b'              => trim($s['opsi']['B'] ?? ''),
                            'opsi_c'              => trim($s['opsi']['C'] ?? ''),
                            'opsi_d'              => trim($s['opsi']['D'] ?? ''),
                            'kunci'               => strtoupper($s['kunci'] ?? 'A'),
                            'urutan'              => $urutan,
                        ]);
                    }
                }
            }
        } catch (\Throwable $e) {
            $custom = session('custom_mock_packages', []);
            foreach ($custom as &$c) {
                $c = (object) $c;
                if ((int) $c->id === (int) $id) {
                    $c->nama_paket = trim($validated['nama_paket']);
                    $c->tema = trim($validated['tema'] ?? '') ?: ($c->tema ?? null);
                    $c->tipe = $validated['tipe'] ?? $c->tipe;
                    $c->durasi = (int) ($validated['durasi'] ?? $c->durasi);
                }
            }
            session(['custom_mock_packages' => array_map(fn($c) => (object) $c, $custom)]);
        }

        $dipakai = self::usageCount($id);
        $msg = 'Paket soal berhasil diperbarui.';
        if ($dipakai > 0) {
            $msg .= " Perhatian: paket ini dipakai {$dipakai} kegiatan — perubahan memengaruhi interpretasi historis N-Gain.";
        }

        \App\Services\AuditService::record(
            'ubah_paket', 'package', $id,
            'Paket diperbarui: ' . trim($validated['nama_paket'])
        );

        return redirect()->route('operator.bank-soal.detail', $id)
            ->with('success', $msg);
    }

    /**
     * Hapus Paket Soal — ditolak bila masih dipakai kegiatan.
     */
    public function destroy($id)
    {
        $dipakai = self::usageCount($id);
        if ($dipakai > 0) {
            $msg = "Paket tidak dapat dihapus karena masih dipakai oleh {$dipakai} kegiatan sosialisasi.";
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->route('operator.bank-soal.index')->with('warning', $msg);
        }

        try {
            if (Schema::hasTable('question_packages')) {
                QuestionPackage::where('id', $id)->delete();
            }
        } catch (\Throwable $e) {
        }
        $custom = collect(session('custom_mock_packages', []))
            ->reject(fn($c) => (int) ((object) $c)->id === (int) $id)
            ->values()->all();
        session(['custom_mock_packages' => $custom]);

        \App\Services\AuditService::record('hapus_paket', 'package', $id, "Paket soal #{$id} dihapus.");

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Paket soal berhasil dihapus.',
            ]);
        }
        return redirect()->route('operator.bank-soal.index')
            ->with('success', 'Paket soal berhasil dihapus.');
    }

    /**
     * Endpoint AJAX Butir Soal
     */
    public function soal($id)
    {
        $questions = MockDataService::getQuestionsForPackage((int)$id);
        return response()->json(['soal' => $questions]);
    }

    /**
     * Tambah Butir Soal Baru (cabang DB tersimpan; mock jujur belum persist).
     */
    public function storeQuestion(Request $request, $id)
    {
        $validated = $request->validate([
            'pertanyaan' => 'required|string',
            'opsi_a'     => 'nullable|string|max:255',
            'opsi_b'     => 'nullable|string|max:255',
            'opsi_c'     => 'nullable|string|max:255',
            'opsi_d'     => 'nullable|string|max:255',
            'kunci'      => 'nullable|in:A,B,C,D,a,b,c,d',
            // Format builder: soal[0][pertanyaan], soal[0][opsi][A..D], soal[0][kunci]
            'soal'       => 'nullable|array',
        ]);

        $saved = false;
        try {
            if (Schema::hasTable('question_packages') && Schema::hasTable('questions')) {
                $row = QuestionPackage::findOrFail($id);
                if (is_array($request->get('soal'))) {
                    foreach (array_values($request->get('soal')) as $s) {
                        if (empty(trim($s['pertanyaan'] ?? ''))) continue;
                        Question::create([
                            'question_package_id' => $row->id,
                            'pertanyaan'          => trim($s['pertanyaan']),
                            'opsi_a'              => trim($s['opsi']['A'] ?? ''),
                            'opsi_b'              => trim($s['opsi']['B'] ?? ''),
                            'opsi_c'              => trim($s['opsi']['C'] ?? ''),
                            'opsi_d'              => trim($s['opsi']['D'] ?? ''),
                            'kunci'               => strtoupper($s['kunci'] ?? 'A'),
                            'urutan'              => (int) (Question::where('question_package_id', $row->id)->max('urutan') ?? 0) + 1,
                        ]);
                        $saved = true;
                    }
                } else {
                    Question::create([
                        'question_package_id' => $row->id,
                        'pertanyaan'          => trim($validated['pertanyaan']),
                        'opsi_a'              => trim($validated['opsi_a'] ?? ''),
                        'opsi_b'              => trim($validated['opsi_b'] ?? ''),
                        'opsi_c'              => trim($validated['opsi_c'] ?? ''),
                        'opsi_d'              => trim($validated['opsi_d'] ?? ''),
                        'kunci'               => strtoupper($validated['kunci'] ?? 'A'),
                        'urutan'              => (int) (Question::where('question_package_id', $row->id)->max('urutan') ?? 0) + 1,
                    ]);
                    $saved = true;
                }
            }
        } catch (\Throwable $e) {
            $saved = false;
        }

        if ($saved) {
            return redirect()->route('operator.bank-soal.detail', $id)
                ->with('success', 'Butir soal berhasil ditambahkan.');
        }
        return redirect()->route('operator.bank-soal.detail', $id)
            ->with('warning', 'Mode mock: butir soal belum tersimpan permanen (aktif penuh setelah database tersambung di Tahap 7).');
    }

    /**
     * Hapus Butir Soal (cabang DB terhapus + urutan dirapikan).
     */
    public function destroyQuestion($id, $soalId)
    {
        $deleted = false;
        try {
            if (Schema::hasTable('questions')) {
                $deleted = (bool) Question::where('question_package_id', $id)->where('id', $soalId)->delete();
                if ($deleted) {
                    $i = 0;
                    foreach (Question::where('question_package_id', $id)->orderBy('urutan')->get() as $q) {
                        $q->update(['urutan' => ++$i]);
                    }
                }
            }
        } catch (\Throwable $e) {
            $deleted = false;
        }

        if ($deleted) {
            return redirect()->route('operator.bank-soal.detail', $id)
                ->with('success', 'Butir soal berhasil dihapus.');
        }
        return redirect()->route('operator.bank-soal.detail', $id)
            ->with('warning', 'Mode mock: penghapusan butir soal belum tersimpan permanen (aktif penuh setelah database tersambung di Tahap 7).');
    }
}
