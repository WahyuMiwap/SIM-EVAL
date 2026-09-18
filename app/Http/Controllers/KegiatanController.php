<?php

namespace App\Http\Controllers;

use App\Services\MockDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class KegiatanController extends Controller
{
    /**
     * Tampilan Daftar Kegiatan (Server-side Filter & Pagination via Mock Data)
     */
    public function index(Request $request)
    {
        $search   = strtolower(trim($request->get('search', '')));
        $period   = $request->get('period', 'all');
        $dateFrom = $request->get('date_from', '');
        $dateTo   = $request->get('date_to', '');

        $allEvents = MockDataService::getEvents();

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
        $lokasiList   = MockDataService::getLocations();
        $bankSoalList = MockDataService::getQuestionPackages();
        $filters      = compact('search', 'period', 'dateFrom', 'dateTo');

        $stats = [
            'total'        => count($allEvents),
            'aktif'        => count(array_filter($allEvents, fn($e) => $e->status === 'berlangsung')),
            'totalPeserta' => array_sum(array_column($allEvents, 'peserta_count')),
            'avgNGain'     => '0.72',
        ];

        return view('operator.kegiatan.index', compact('kegiatan', 'lokasiList', 'bankSoalList', 'filters', 'stats'));
    }

    /**
     * Halaman Tambah Kegiatan
     */
    public function create()
    {
        $lokasiList = MockDataService::getLocations();
        $paketList  = MockDataService::getQuestionPackages();
        return view('operator.kegiatan.create', compact('lokasiList', 'paketList'));
    }

    /**
     * Simpan Kegiatan Baru (Mock Redirect)
     */
    public function store(Request $request)
    {
        $kodeJoin = strtoupper(Str::random(6));
        return redirect()->route('operator.kegiatan.detail', 1)
            ->with('success', "Kegiatan baru berhasil dibuat (Mock Mode). Kode Join: {$kodeJoin}");
    }

    /**
     * Detail Kegiatan & Meja Kerja Rekap
     */
    public function detail($id)
    {
        $kegiatan     = MockDataService::getEventById($id);
        $participants = MockDataService::getParticipantsForEvent($id);
        $stats        = MockDataService::getEventStats($id);

        return view('operator.kegiatan.detail', compact('kegiatan', 'participants', 'stats'));
    }

    /**
     * Halaman / Data Edit Kegiatan
     */
    public function edit(Request $request, $id)
    {
        $kegiatan   = MockDataService::getEventById($id);
        $lokasiList = MockDataService::getLocations();
        $paketList  = MockDataService::getQuestionPackages();

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

        return view('operator.kegiatan.edit', compact('kegiatan', 'lokasiList', 'paketList'));
    }

    /**
     * Perbarui Kegiatan
     */
    public function update(Request $request, $id)
    {
        return redirect()->route('operator.kegiatan.detail', $id)
            ->with('success', 'Data kegiatan berhasil diperbarui.');
    }

    /**
     * Ubah Status Kegiatan (dijadwalkan / berlangsung / selesai)
     */
    public function updateStatus(Request $request, $id)
    {
        $newStatus = $request->get('status', 'berlangsung');
        return response()->json([
            'success'   => true,
            'status'    => $newStatus,
            'message'   => 'Status kegiatan berhasil diubah.',
        ]);
    }

    /**
     * Hapus Kegiatan (JSON untuk ReauthModal)
     */
    public function destroy($id)
    {
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Kegiatan berhasil dihapus (Mock Mode).',
            ]);
        }
        return redirect()->route('operator.kegiatan.index')
            ->with('success', 'Kegiatan berhasil dihapus.');
    }

    /**
     * Ekspor Format Diktari (Mock)
     */
    public function exportDiktari($id)
    {
        return back()->with('success', 'File Rekap Evaluasi Diktari BNN (Excel) berhasil diunduh.');
    }

    /**
     * Simpan / Tambah Baris Peserta Meja Kerja
     */
    public function saveParticipantRow(Request $request, $id)
    {
        $pre  = (float) $request->get('pretest_score', 0);
        $post = (float) $request->get('posttest_score', 0);
        $gain = ($post > $pre && $pre < 100) ? round(($post - $pre) / (100 - $pre), 2) : 0;
        $cat  = $gain >= 0.70 ? 'Paham' : ($gain >= 0.30 ? 'Cukup' : 'Kurang');

        return response()->json([
            'success'     => true,
            'message'     => 'Data nilai peserta berhasil disimpan.',
            'participant' => [
                'id'             => rand(200, 999),
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
     * Hapus Baris Peserta
     */
    public function deleteParticipantRow(Request $request, $id, $pesertaId)
    {
        return response()->json([
            'success' => true,
            'message' => 'Baris peserta berhasil dihapus.',
        ]);
    }

    /**
     * Hasil Scan OMR Kamera
     */
    public function omrScanRecord(Request $request, $id)
    {
        $score = rand(70, 95);
        return response()->json([
            'success' => true,
            'message' => 'Lembar jawaban berhasil dipindai.',
            'data'    => [
                'nama'   => 'Peserta Scan #' . rand(1, 99),
                'score'  => $score,
                'status' => 'valid',
            ],
        ]);
    }
}
