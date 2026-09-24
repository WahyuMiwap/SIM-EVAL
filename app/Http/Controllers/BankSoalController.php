<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaketSoalRequest;
use App\Models\Event;
use App\Models\Question;
use App\Models\QuestionPackage;
use App\Services\AuditService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            $rows = QuestionPackage::withCount(['questions', 'eventsPre', 'eventsPost'])
                ->orderBy('nama_paket')->get();

            return $rows->map(function ($p) {
                $soal = (int) ($p->questions_count ?? 0);

                return (object) [
                    'id' => $p->id,
                    'nama_paket' => $p->nama_paket,
                    'tema' => $p->tema ?? null,
                    'tipe' => $p->tipe ?? 'umum',
                    'durasi' => $p->durasi ?? 30,
                    'deskripsi' => null,
                    'acak_urutan' => (bool) ($p->acak_urutan ?? true),
                    'soal_count' => $soal,
                    'questions_count' => $soal,
                    'events_count' => (int) ($p->events_pre_count ?? 0) + (int) ($p->events_post_count ?? 0),
                    'questions' => $p->questions,
                    'created_at' => $p->created_at,
                ];
            })->all();
        } catch (\Throwable $e) {
            Log::warning('allPackages gagal', ['err' => $e->getMessage()]);

            return [];
        }
    }

    public static function findPackage($id): ?object
    {
        if (empty($id)) {
            return null;
        }
        foreach (self::allPackages() as $p) {
            if ((int) $p->id === (int) $id) {
                return $p;
            }
        }

        return null;
    }

    /**
     * Hitung pemakaian paket oleh kegiatan (DB bila tersedia).
     */
    public static function usageCount($id): int
    {
        $pkg = self::findPackage($id);
        if ($pkg && isset($pkg->events_count)) {
            return (int) $pkg->events_count;
        }

        return Event::where('pretest_package_id', $id)
            ->orWhere('posttest_package_id', $id)->count();
    }

    /**
     * Daftar Paket Bank Soal (filter search + tema).
     */
    public function index(Request $request)
    {
        $search = strtolower(trim($request->get('search', '')));
        $tema = trim($request->get('tema', ''));

        $all = self::allPackages();

        $temas = collect($all)->map(fn ($p) => $p->tema ?? null)
            ->filter()->unique()->sort()->values()->all();

        $filtered = collect($all)->filter(function ($pkg) use ($search, $tema) {
            if ($search && ! str_contains(strtolower($pkg->nama_paket ?? ''), $search)) {
                return false;
            }
            if ($tema && ($pkg->tema ?? '') !== $tema) {
                return false;
            }

            return true;
        })->values()->all();

        $page = max(1, (int) $request->get('page', 1));
        $bankSoal = new LengthAwarePaginator(array_slice($filtered, ($page - 1) * 10, 10), count($filtered), 10, $page, ['path' => $request->url(), 'query' => $request->query()]);

        return view('operator.bank-soal.index', compact('bankSoal', 'search', 'tema', 'temas'));
    }

    /**
     * Form Buat Paket Baru
     */
    public function create()
    {
        $temas = collect(self::allPackages())->map(fn ($p) => $p->tema ?? null)
            ->filter()->unique()->sort()->values()->all();

        return view('operator.bank-soal.create', compact('temas'));
    }

    /**
     * Simpan Paket Soal Baru (DB bila tersedia, fallback session mock).
     */
    public function store(StorePaketSoalRequest $request)
    {
        $validated = $request->validated();

        $soalInput = $request->get('soal', []);
        $tema = trim($validated['tema'] ?? '') ?: null;
        $tipe = $validated['tipe'] ?? 'umum';
        $durasi = (int) ($validated['durasi'] ?? 30);

        try {
            $newId = DB::transaction(function () use ($validated, $tema, $tipe, $durasi, $soalInput) {
                $row = QuestionPackage::create([
                    'nama_paket' => trim($validated['nama_paket']),
                    'tema' => $tema,
                    'kategori_audiens' => $validated['kategori_audiens'] ?? 'UMUM',
                    'jumlah_opsi' => $validated['jumlah_opsi'] ?? 4,
                    'tipe' => $tipe,
                    'durasi' => $durasi,
                    'acak_urutan' => $validated['acak_urutan'] ?? true,
                    'created_by' => auth()->id(),
                ]);
                foreach (array_values(is_array($soalInput) ? $soalInput : []) as $i => $s) {
                    if (empty($s['pertanyaan'] ?? null)) {
                        continue;
                    }
                    Question::create([
                        'question_package_id' => $row->id,
                        'pertanyaan' => $s['pertanyaan'],
                        'opsi_a' => $s['opsi']['A'] ?? $s['opsi_a'] ?? '',
                        'opsi_b' => $s['opsi']['B'] ?? $s['opsi_b'] ?? '',
                        'opsi_c' => $s['opsi']['C'] ?? $s['opsi_c'] ?? '',
                        'opsi_d' => $s['opsi']['D'] ?? $s['opsi_d'] ?? '',
                        'kunci' => strtoupper($s['kunci'] ?? 'A'),
                        'urutan' => $i + 1,
                    ]);
                }

                return $row->id;
            });
            Log::info('Paket soal dibuat', ['id' => $newId, 'by' => auth()->id()]);
        } catch (\Throwable $e) {
            Log::error('Gagal buat paket', ['err' => $e->getMessage()]);

            return back()->withInput()->withErrors(['nama_paket' => 'Gagal menyimpan paket soal.']);
        }

        AuditService::record('buat_paket', 'package', $newId, 'Paket soal dibuat: '.trim($validated['nama_paket']));

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
                'id' => $paket->id,
                'nama_paket' => $paket->nama_paket,
                'tema' => $paket->tema ?? null,
                'tipe' => $paket->tipe,
                'durasi' => $paket->durasi,
                'acak_urutan' => $paket->acak_urutan,
            ]);
        }

        $soalList = $paket->questions ?? collect([]);
        $dipakai = self::usageCount($paket->id);
        $temas = collect($all)->map(fn ($p) => $p->tema ?? null)
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
            'tema' => 'nullable|string|max:100',
            'tipe' => 'nullable|in:'.implode(',', self::TIPE_LIST),
            'durasi' => 'nullable|integer|min:5|max:180',
            'soal' => 'nullable|array',
            'soal.*.pertanyaan' => 'nullable|string',
            'soal.*.kunci' => 'nullable|in:A,B,C,D,a,b,c,d',
        ]);

        $soalInput = $request->get('soal', null);

        $row = QuestionPackage::findOrFail($id);
        DB::transaction(function () use ($row, $validated, $soalInput) {
            $row->update([
                'nama_paket' => trim($validated['nama_paket']),
                'tema' => trim($validated['tema'] ?? '') ?: null,
                'tipe' => $validated['tipe'] ?? $row->tipe,
                'durasi' => (int) ($validated['durasi'] ?? $row->durasi),
            ]);

            // Sinkron butir soal bila builder mengirimkannya.
            // Aman: tidak ada FK peserta → soal (skor tersimpan sebagai angka).
            if (is_array($soalInput)) {
                Question::where('question_package_id', $row->id)->delete();
                $urutan = 0;
                foreach (array_values($soalInput) as $s) {
                    if (empty(trim($s['pertanyaan'] ?? ''))) {
                        continue;
                    }
                    $urutan++;
                    Question::create([
                        'question_package_id' => $row->id,
                        'pertanyaan' => trim($s['pertanyaan']),
                        'opsi_a' => trim($s['opsi']['A'] ?? ''),
                        'opsi_b' => trim($s['opsi']['B'] ?? ''),
                        'opsi_c' => trim($s['opsi']['C'] ?? ''),
                        'opsi_d' => trim($s['opsi']['D'] ?? ''),
                        'kunci' => strtoupper($s['kunci'] ?? 'A'),
                        'urutan' => $urutan,
                    ]);
                }
            }
        });
        Log::info('Paket soal diubah', ['id' => $id, 'by' => auth()->id()]);

        $dipakai = self::usageCount($id);
        $msg = 'Paket soal berhasil diperbarui.';
        if ($dipakai > 0) {
            $msg .= " Perhatian: paket ini dipakai {$dipakai} kegiatan — perubahan memengaruhi interpretasi historis N-Gain.";
        }

        AuditService::record(
            'ubah_paket', 'package', $id,
            'Paket diperbarui: '.trim($validated['nama_paket'])
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

        $this->authorize('delete', QuestionPackage::findOrFail($id));
        DB::transaction(fn () => QuestionPackage::where('id', $id)->delete());
        Log::info('Paket soal dihapus', ['id' => $id, 'by' => auth()->id()]);

        AuditService::record('hapus_paket', 'package', $id, "Paket soal #{$id} dihapus.");

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
        $pkg = QuestionPackage::find($id);
        if (! $pkg) {
            return ApiResponse::notFound('Paket soal tidak ditemukan.');
        }
        $questions = Question::where('question_package_id', (int) $id)->orderBy('urutan')->get();

        return ApiResponse::ok(['soal' => $questions], 'Butir soal.');
    }

    /**
     * Tambah Butir Soal Baru (cabang DB tersimpan; mock jujur belum persist).
     */
    public function storeQuestion(Request $request, $id)
    {
        $validated = $request->validate([
            'pertanyaan' => 'required|string',
            'opsi_a' => 'nullable|string|max:255',
            'opsi_b' => 'nullable|string|max:255',
            'opsi_c' => 'nullable|string|max:255',
            'opsi_d' => 'nullable|string|max:255',
            'kunci' => 'nullable|in:A,B,C,D,a,b,c,d',
            // Format builder: soal[0][pertanyaan], soal[0][opsi][A..D], soal[0][kunci]
            'soal' => 'nullable|array',
        ]);

        $saved = false;
        try {
            DB::transaction(function () use ($id, $request, $validated, &$saved) {
                $row = QuestionPackage::findOrFail($id);
                if (is_array($request->get('soal'))) {
                    foreach (array_values($request->get('soal')) as $s) {
                        if (empty(trim($s['pertanyaan'] ?? ''))) {
                            continue;
                        }
                        Question::create([
                            'question_package_id' => $row->id,
                            'pertanyaan' => trim($s['pertanyaan']),
                            'opsi_a' => trim($s['opsi']['A'] ?? ''),
                            'opsi_b' => trim($s['opsi']['B'] ?? ''),
                            'opsi_c' => trim($s['opsi']['C'] ?? ''),
                            'opsi_d' => trim($s['opsi']['D'] ?? ''),
                            'kunci' => strtoupper($s['kunci'] ?? 'A'),
                            'urutan' => (int) (Question::where('question_package_id', $row->id)->max('urutan') ?? 0) + 1,
                        ]);
                        $saved = true;
                    }
                } else {
                    Question::create([
                        'question_package_id' => $row->id,
                        'pertanyaan' => trim($validated['pertanyaan']),
                        'opsi_a' => trim($validated['opsi_a'] ?? ''),
                        'opsi_b' => trim($validated['opsi_b'] ?? ''),
                        'opsi_c' => trim($validated['opsi_c'] ?? ''),
                        'opsi_d' => trim($validated['opsi_d'] ?? ''),
                        'kunci' => strtoupper($validated['kunci'] ?? 'A'),
                        'urutan' => (int) (Question::where('question_package_id', $row->id)->max('urutan') ?? 0) + 1,
                    ]);
                    $saved = true;
                }
            });
            Log::info('Butir soal ditambahkan', ['package_id' => $id, 'by' => auth()->id()]);
        } catch (\Throwable $e) {
            Log::error('Gagal tambah butir soal', ['package_id' => $id, 'err' => $e->getMessage()]);

            return back()->withInput()->withErrors(['pertanyaan' => 'Gagal menyimpan butir soal. Coba lagi.']);
        }

        return redirect()->route('operator.bank-soal.detail', $id)
            ->with('success', 'Butir soal berhasil ditambahkan.');
    }

    /**
     * Hapus Butir Soal (DB + urutan dirapikan).
     */
    public function destroyQuestion($id, $soalId)
    {
        try {
            $deleted = (bool) DB::transaction(function () use ($id, $soalId) {
                $n = Question::where('question_package_id', $id)->where('id', $soalId)->delete();
                if ($n) {
                    $i = 0;
                    foreach (Question::where('question_package_id', $id)->orderBy('urutan')->get() as $q) {
                        $q->update(['urutan' => ++$i]);
                    }
                }

                return $n;
            });
        } catch (\Throwable $e) {
            Log::error('Gagal hapus butir soal', ['package_id' => $id, 'err' => $e->getMessage()]);

            return back()->withErrors(['general' => 'Gagal menghapus butir soal.']);
        }

        return redirect()->route('operator.bank-soal.detail', $id)
            ->with('success', 'Butir soal berhasil dihapus.');
    }
}
