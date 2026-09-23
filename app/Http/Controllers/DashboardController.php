<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Participant;
use App\Services\MockDataService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    /**
     * Centroid kasar kecamatan Surabaya untuk peta sebaran.
     * Tanpa migrasi: agregat per kecamatan, titik = centroid.
     */
    public const KECAMATAN_COORDS = [
        'genteng' => [-7.2637, 112.7517], 'tegalsari' => [-7.2656, 112.7347],
        'bubutan' => [-7.2567, 112.7355], 'simokerto' => [-7.2450, 112.7500],
        'pabean cantian' => [-7.2317, 112.7372], 'semampir' => [-7.2250, 112.7550],
        'krembangan' => [-7.2344, 112.7230], 'kenjeran' => [-7.2350, 112.7800],
        'bulak' => [-7.2400, 112.7950], 'tambaksari' => [-7.2505, 112.7632],
        'gubeng' => [-7.2752, 112.7824], 'gunung anyar' => [-7.3200, 112.8000],
        'sukolilo' => [-7.2900, 112.7950], 'mulyorejo' => [-7.2700, 112.7900],
        'tenggilis mejoyo' => [-7.3150, 112.7600], 'tenggilis' => [-7.3150, 112.7600],
        'gayungan' => [-7.3200, 112.7350], 'wonocolo' => [-7.3050, 112.7400],
        'wonokromo' => [-7.2950, 112.7400], 'sawahan' => [-7.2681, 112.7190],
        'dukuh pakis' => [-7.2850, 112.6900], 'wiyung' => [-7.3050, 112.6800],
        'karang pilang' => [-7.3200, 112.6900], 'jambangan' => [-7.3123, 112.7161],
        'tandes' => [-7.2650, 112.6900], 'sukomanunggal' => [-7.2700, 112.6700],
        'asemrowo' => [-7.2500, 112.7000], 'benowo' => [-7.2400, 112.6600],
        'pakal' => [-7.2350, 112.6400], 'lakarsantri' => [-7.2900, 112.6500],
        'sambikerep' => [-7.2750, 112.6600], 'rungkut' => [-7.3200, 112.7800],
        'porong' => [-7.5427, 112.6877], 'waru' => [-7.3600, 112.7200],
    ];

    public const SURABAYA_CENTER = [-7.2575, 112.7521];

    public function index(Request $request)
    {
        $stats = self::computeStats();
        return view('operator.dashboard', compact('stats'));
    }

    /**
     * Data dashboard per periode untuk filter JS (tanpa reload).
     * GET /operator/dashboard/data?mode=bulan_ini|tahun_ini|custom&month=8&year=2026
     * Selalu dari server (DB bila ada, mock bila kosong); tanpa mock angka hiasan.
     */
    public function data(Request $request)
    {
        $mode = $request->get('mode', 'bulan_ini');
        $now = Carbon::now();
        if ($mode === 'custom') {
            $m = max(1, min(12, (int) $request->get('month', $now->month)));
            $y = max(2000, min(2099, (int) $request->get('year', $now->year)));
            $now = Carbon::create($y, $m, 1);
        }

        $stats = self::computeStats($now);
        $metrics = $mode === 'tahun_ini' ? $stats['tahun_ini'] : $stats['bulan_ini'];

        return response()->json([
            'metrics'  => $metrics,
            'calendar' => self::calendarEvents(),
            'recent'   => self::recentActivities(),
        ]);
    }

    /**
     * Jumlah sekolah binaan (jenis sekolah/kampus di master lokasi).
     */
    public static function sekolahBinaanCount(): int
    {
        try {
            $all = \App\Http\Controllers\LokasiController::allLocations();
            return count(array_filter($all, fn($l) =>
                in_array(strtolower($l->jenis_sasaran ?? ''), ['sekolah', 'kampus'], true)));
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * Daftar event untuk kalender — DB dulu, fallback mock.
     */
    public static function calendarEvents(): array
    {
        $statusMeta = [
            'selesai'    => ['badge' => 'badge-green', 'color' => '#22c55e', 'label' => 'Selesai'],
            'berlangsung'=> ['badge' => 'badge-cyan',  'color' => '#06b6d4', 'label' => 'Berlangsung'],
            'dijadwalkan'=> ['badge' => 'badge-blue',   'color' => '#4361ee', 'label' => 'Dijadwalkan'],
        ];
        $map = function ($id, $date, $title, $status, $loc, $peserta, $gain) use ($statusMeta) {
            $st = strtolower($status ?? 'dijadwalkan');
            $meta = $statusMeta[$st] ?? $statusMeta['dijadwalkan'];
            $kat = $gain !== null ? ($gain >= 0.7 ? 'Tinggi' : ($gain >= 0.3 ? 'Sedang' : 'Rendah')) : '—';
            return [
                'id' => $id, 'date' => $date, 'title' => $title,
                'status' => $meta['label'], 'badgeClass' => $meta['badge'], 'color' => $meta['color'],
                'category' => 'Kegiatan P2M', 'time' => '—', 'location' => $loc,
                'participants' => $peserta . ' Peserta',
                'nGain' => $gain, 'kategoriGain' => $kat,
            ];
        };

        try {
            if (Schema::hasTable('events')) {
                $rows = Event::with(['lokasi', 'participants'])->orderBy('tanggal')->get();
                if ($rows->isNotEmpty()) {
                    return $rows->map(function ($e) use ($map) {
                        $g = $e->participants->whereNotNull('n_gain')->avg('n_gain');
                        return $map(
                            $e->id,
                            $e->tanggal ? Carbon::parse($e->tanggal)->format('Y-m-d') : null,
                            $e->nama_kegiatan,
                            $e->status,
                            $e->lokasi->nama_lokasi ?? 'Lokasi Binaan',
                            $e->participants->count(),
                            $g !== null ? round($g, 2) : null
                        );
                    })->all();
                }
            }
        } catch (\Throwable $e) {
        }

        return array_map(function ($e) use ($map) {
            $g = null;
            try {
                $parts = MockDataService::getParticipantsForEvent($e->id);
                $avg = $parts->whereNotNull('n_gain')->avg('n_gain');
                $g = $avg !== null ? round($avg, 2) : ($e->avg_gain ?? null);
            } catch (\Throwable $ex) {
                $g = $e->avg_gain ?? null;
            }
            return $map(
                $e->id, $e->tanggal ?? null, $e->nama_kegiatan ?? 'Kegiatan',
                $e->status ?? 'dijadwalkan',
                $e->lokasi->nama_lokasi ?? 'Lokasi Binaan',
                (int) ($e->peserta_count ?? 0), $g
            );
        }, MockDataService::getEvents());
    }

    /**
     * 5 kegiatan terbaru untuk panel sisi kanan.
     */
    public static function recentActivities(): array
    {
        $badge = ['selesai' => 'badge-green', 'berlangsung' => 'badge-cyan', 'dijadwalkan' => 'badge-gray'];
        $list = array_map(function ($e) use ($badge) {
            $g = $e['gain'];
            return [
                'id' => $e['id'], 'nama' => $e['title'],
                'tanggal_short' => $e['date'] ? Carbon::parse($e['date'])->isoFormat('DD MMM') : '—',
                'peserta' => $e['peserta'], 'date' => $e['date'] ?? '',
                'status' => ucfirst($e['status']), 'status_badge' => $badge[strtolower($e['status'])] ?? 'badge-gray',
                'n_gain' => $g, 'n_gain_kategori' => $g !== null ? ($g >= 0.7 ? 'Tinggi' : ($g >= 0.3 ? 'Sedang' : 'Rendah')) : '—',
            ];
        }, array_map(fn($c) => [
            'id' => $c['id'], 'title' => $c['title'], 'date' => $c['date'],
            'status' => strtolower($c['status']), 'peserta' => (int) filter_var($c['participants'], FILTER_SANITIZE_NUMBER_INT),
            'gain' => $c['nGain'],
        ], self::calendarEvents()));
        usort($list, fn($a, $b) => strcmp($b['date'], $a['date']));
        return array_slice($list, 0, 5);
    }

    /**
     * Agregat dashboard — DB dulu, fallback mock.
     * Bentuk output disamakan dengan mock frontend (bulan_ini/tahun_ini)
     * agar JS filter existing langsung bisa memakainya.
     */
    public static function computeStats(?Carbon $ref = null): array
    {
        $now = $ref ?? Carbon::now();
        try {
            if (Schema::hasTable('events')) {
                $events = Event::with(['participants', 'lokasi'])->get();
                if ($events->isNotEmpty()) {
                    return self::fromEventCollection(
                        $events,
                        fn($e) => $e->tanggal ? Carbon::parse($e->tanggal) : null,
                        fn($e) => (int) $e->participants->count(),
                        // Cara B (pooled): kumpulkan gain tiap SISWA, bukan rata-rata event.
                        fn($e) => $e->participants->whereNotNull('n_gain')->pluck('n_gain')->map(fn($g) => round((float) $g, 2))->all(),
                        fn($e) => $e->lokasi->kecamatan ?? null,
                        $now
                    );
                }
            }
        } catch (\Throwable $e) {
        }

        $events = MockDataService::getEvents();
        return self::fromEventCollection(
            $events,
            fn($e) => isset($e->tanggal) ? Carbon::parse($e->tanggal) : null,
            fn($e) => (int) ($e->peserta_count ?? $e->participants_count ?? 0),
            // Cara B (pooled): kumpulkan gain tiap SISWA dari sampel peserta event.
            function ($e) {
                try {
                    return MockDataService::getParticipantsForEvent($e->id)
                        ->whereNotNull('n_gain')->pluck('n_gain')
                        ->map(fn($g) => round((float) $g, 2))->all();
                } catch (\Throwable $ex) {
                    return [];
                }
            },
            function ($e) {
                try {
                    return $e->lokasi->nama_lokasi
                        ? ($e->lokasi->kecamatan ?? self::kecamatanDariNama($e->lokasi->nama_lokasi))
                        : null;
                } catch (\Throwable $ex) {
                    return null;
                }
            },
            $now
        );
    }

    /**
     * Hitung metrik bulan_ini + tahun_ini + tren 6 bulan + sebaran + distribusi.
     *
     * Cara B (pooled, sesuai definisi Hake): rata-rata N-Gain dihitung dari
     * kumpulan gain tiap SISWA pada periode itu — BUKAN rata-rata dari
     * rata-rata event. Setiap manusia bobotnya sama.
     *
     * @param iterable $events
     * @param callable $getDate   (event) => ?Carbon
     * @param callable $getCount  (event) => int peserta
     * @param callable $getGains  (event) => float[] gain tiap siswa event itu
     * @param callable $getKec    (event) => ?string kecamatan
     */
    protected static function fromEventCollection(iterable $events, callable $getDate, callable $getCount, callable $getGains, callable $getKec, Carbon $now): array
    {
        $rows = [];
        foreach ($events as $e) {
            try {
                $gains = array_values(array_filter(
                    array_map(fn($g) => is_numeric($g) ? round((float) $g, 2) : null, (array) $getGains($e)),
                    fn($g) => $g !== null
                ));
                $rows[] = [
                    'id'      => is_object($e) ? ($e->id ?? null) : ($e['id'] ?? null),
                    'date'    => $getDate($e),
                    'count'   => $getCount($e),
                    'gains'   => $gains,
                    'kec'     => $getKec($e),
                ];
            } catch (\Throwable $ex) {
            }
        }
        $rows = array_values(array_filter($rows, fn($r) => $r['date'] instanceof Carbon));

        // Rata-rata pooled: gabungkan gain semua siswa dalam himpunan.
        $poolAvg = function (array $set): ?float {
            $all = [];
            foreach ($set as $r) foreach ($r['gains'] as $g) $all[] = $g;
            return count($all) ? round(array_sum($all) / count($all), 2) : null;
        };

        $inMonth = fn($r, $y, $m) => $r['date']->year === $y && $r['date']->month === $m;
        $inYear  = fn($r, $y) => $r['date']->year === $y;

        $mk = function (array $set, array $prev, string $label) use ($poolAvg) {
            $keg = count($set);
            $pes = array_sum(array_column($set, 'count'));
            $avg = $poolAvg($set);

            $pkeg = count($prev);
            $ppes = array_sum(array_column($prev, 'count'));
            $pavg = $poolAvg($prev);

            $dist = self::distribusi($set);

            return [
                'periodLabel' => $label,
                'sekolah' => [
                    'value' => self::sekolahBinaanCount(),
                    'sub' => 'sekolah & kampus binaan',
                ],
                'kegiatan' => [
                    'value' => $keg,
                    'growth' => self::pct($pkeg, $keg),
                    'sub' => 'kegiatan terlaksana',
                    'growthLabel' => 'vs periode lalu',
                ],
                'peserta' => [
                    'value' => number_format($pes, 0, ',', '.'),
                    'growth' => self::pct($ppes, $pes),
                    'sub' => 'siswa tersosialisasi',
                    'growthLabel' => 'vs periode lalu',
                ],
                'nGain' => [
                    'value' => $avg !== null ? number_format($avg, 2) : '—',
                    'growth' => ($pavg !== null && $avg !== null) ? sprintf('%+.2f', round($avg - $pavg, 2)) : '—',
                    'badge' => $avg !== null && $avg >= 0.7 ? 'Efektif' : ($avg !== null && $avg >= 0.3 ? 'Cukup' : 'Rendah'),
                    'badgeColor' => $avg !== null && $avg >= 0.7 ? 'badge-green' : ($avg !== null && $avg >= 0.3 ? 'badge-yellow' : 'badge-gray'),
                    'sub' => 'rata-rata N-Gain se-Surabaya',
                ],
                'efektivitas' => [
                    'value' => $dist['efektif_pct'] . '%',
                    'growth' => '—',
                    'sub' => 'kategori paham/cukup',
                ],
                'tinggi' => [
                    'value' => $dist['tinggi_pct'] . '%',
                    'sub' => 'kategori tinggi (paham)',
                ],
                'distribusi' => $dist['items'],
            ];
        };

        $y = $now->year;
        $m = $now->month;
        $prev = $m === 1 ? [$y - 1, 12] : [$y, $m - 1];

        $setBulan = array_values(array_filter($rows, fn($r) => $inMonth($r, $y, $m)));
        $setPrev  = array_values(array_filter($rows, fn($r) => $inMonth($r, $prev[0], $prev[1])));
        $setTahun = array_values(array_filter($rows, fn($r) => $inYear($r, $y)));
        $setTahunLalu = array_values(array_filter($rows, fn($r) => $inYear($r, $y - 1)));

        // Tren 6 bulan terakhir (berakhir bulan berjalan)
        $trendLabels = [];
        $trendGain = [];
        $trendKeg = [];
        for ($i = 5; $i >= 0; $i--) {
            $d = (clone $now)->subMonthsNoOverflow($i);
            $set = array_values(array_filter($rows, fn($r) => $inMonth($r, $d->year, $d->month)));
            $trendLabels[] = $d->isoFormat('MMM');
            $trendGain[] = $poolAvg($set);
            $trendKeg[] = count($set);
        }

        // Sebaran per kecamatan
        $kecAgg = [];
        foreach ($rows as $r) {
            $k = trim($r['kec'] ?? '');
            if ($k === '') $k = 'Lainnya';
            if (!isset($kecAgg[$k])) $kecAgg[$k] = ['kecamatan' => $k, 'kegiatan' => 0, 'peserta' => 0];
            $kecAgg[$k]['kegiatan']++;
            $kecAgg[$k]['peserta'] += $r['count'];
        }
        $sebaran = array_values(array_map(function ($a) {
            $c = self::KECAMATAN_COORDS[strtolower($a['kecamatan'])] ?? self::SURABAYA_CENTER;
            $a['lat'] = $c[0];
            $a['lng'] = $c[1];
            return $a;
        }, $kecAgg));
        usort($sebaran, fn($a, $b) => $b['kegiatan'] <=> $a['kegiatan']);

        return [
            'bulan_ini' => $mk($setBulan, $setPrev, $now->isoFormat('MMMM Y')),
            'tahun_ini' => $mk($setTahun, $setTahunLalu, 'Tahun ' . $y),
            // Dormant by design (keputusan produk): 'trend' dipakai bila grafik
            // dihidupkan kembali (butuh ≥3 bulan data); lat/lng 'sebaran' dipakai
            // bila peta dihidupkan kembali. Biaya runtime praktis nol.
            'trend'     => ['labels' => $trendLabels, 'gain' => $trendGain, 'kegiatan' => $trendKeg],
            'sebaran'   => $sebaran,
            'center'    => self::SURABAYA_CENTER,
        ];
    }

    /**
     * Distribusi paham/cukup/kurang + % efektif (paham+cukup)
     * untuk himpunan event periode berjalan.
     */
    protected static function distribusi(array $set): array
    {
        $paham = $cukup = $kurang = 0;
        $ids = array_values(array_filter(array_column($set, 'id')));
        try {
            if (Schema::hasTable('participants') && !empty($ids)) {
                $base = Participant::whereIn('event_id', $ids)->whereNotNull('n_gain');
                $paham = (clone $base)->where('n_gain', '>=', 0.7)->count();
                $cukup = (clone $base)->where('n_gain', '>=', 0.3)->where('n_gain', '<', 0.7)->count();
                $kurang = (clone $base)->where('n_gain', '<', 0.3)->count();
                if (($paham + $cukup + $kurang) === 0) throw new \RuntimeException('empty');
            } else {
                throw new \RuntimeException('mock');
            }
        } catch (\Throwable $e) {
            $byId = [];
            foreach (MockDataService::getEvents() as $ev) $byId[$ev->id] = $ev;
            foreach ($ids as $eid) {
                if (!isset($byId[$eid])) continue;
                try {
                    foreach (MockDataService::getParticipantsForEvent($eid) as $p) {
                        $cat = strtolower($p->category ?? $p->kategori ?? '');
                        if (str_contains($cat, 'paham') || $cat === 'tinggi') $paham++;
                        elseif (str_contains($cat, 'cukup') || str_contains($cat, 'sedang')) $cukup++;
                        else $kurang++;
                    }
                } catch (\Throwable $ex) {
                }
            }
        }
        $total = max(1, $paham + $cukup + $kurang);
        $pct = fn($n) => round($n / $total * 100, 1);
        return [
            'efektif_pct' => str_replace('.', ',', (string) $pct($paham + $cukup)),
            'tinggi_pct' => str_replace('.', ',', (string) $pct($paham)),
            'items' => [
                ['label' => 'Paham / Tinggi (g ≥ 0.70)', 'count' => $paham, 'percent' => $pct($paham), 'color' => 'var(--success, #22c55e)'],
                ['label' => 'Cukup / Sedang (0.30 ≤ g < 0.70)', 'count' => $cukup, 'percent' => $pct($cukup), 'color' => 'var(--warning, #f59e0b)'],
                ['label' => 'Kurang / Rendah (g < 0.30)', 'count' => $kurang, 'percent' => $pct($kurang), 'color' => 'var(--danger, #ef4444)'],
            ],
        ];
    }

    protected static function pct($old, $new): string
    {
        if ($old <= 0) return $new > 0 ? 'baru' : '—';
        $v = round(($new - $old) / $old * 100, 1);
        return ($v >= 0 ? '+' : '') . str_replace('.', ',', (string) $v) . '%';
    }

    protected static function kecamatanDariNama(string $nama): ?string
    {
        foreach (array_keys(self::KECAMATAN_COORDS) as $kec) {
            if (stripos($nama, $kec) !== false) return ucwords($kec);
        }
        return null;
    }
}
