<?php

namespace App\Http\Controllers;

use App\Actions\SubmitQuizAction;
use App\Http\Requests\JoinParticipantRequest;
use App\Models\Event;
use App\Models\Participant;
use App\Models\ParticipantAnswer;
use App\Models\Question;
use App\Services\QuizService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ParticipantController extends Controller
{
    /**
     * Halaman Welcome / Masukkan Kode Join
     */
    public function welcome(Request $request)
    {
        $prefilledKode = strtoupper(trim((string) $request->query('kode', '')));

        return view('participant.welcome', compact('prefilledKode'));
    }

    /**
     * Label field kelompok identitas mengikuti jenis sasaran lokasi event.
     * Sekolah → Kelas (wajib, untuk disambiguasi nama sama);
     * non-sekolah → RW/Blok/Divisi sesuai konteks.
     */
    public static function kelompokLabel(?object $event): string
    {
        $jenis = null;
        try {
            $lok = $event->lokasi ?? null;
            if ($lok && isset($lok->jenis_sasaran)) {
                $jenis = strtolower($lok->jenis_sasaran);
            } elseif ($lok && method_exists($lok, 'getAttribute')) {
                $jenis = strtolower($lok->getAttribute('jenis_sasaran') ?? '');
            }
        } catch (\Throwable $e) {
        }

        return match ($jenis) {
            'kampus' => 'Kelas / Angkatan',
            'masyarakat', 'komunitas' => 'RW / Kelompok',
            'lapas' => 'Blok / Kamar',
            'instansi' => 'Unit / Divisi',
            default => 'Kelas',
        };
    }

    /**
     * Cari event berdasarkan kode join — DB dulu, fallback mock
     * (termasuk event custom dari session).
     */
    public static function findEventByKode(string $kode): ?object
    {
        $kode = strtoupper(trim($kode));
        if ($kode === '') {
            return null;
        }
        try {
            return Event::with('lokasi')->where('kode_join', $kode)->first();
        } catch (\Throwable $e) {
            Log::warning('findEventByKode gagal', ['kode' => $kode]);
        }

        return null;
    }

    /**
     * Info event untuk form join (dipakai JS: label kelompok dinamis +
     * konfirmasi nama kegiatan setelah PIN lengkap). Tanpa auth.
     * GET /join/info?kode=XXXXXX
     */
    public function joinInfo(Request $request)
    {
        $event = self::findEventByKode($request->get('kode', ''));
        if (! $event) {
            return response()->json(['found' => false]);
        }
        $lokasiNama = null;
        try {
            $lokasiNama = $event->lokasi->nama_lokasi ?? null;
        } catch (\Throwable $e) {
        }

        return response()->json([
            'found' => true,
            'nama_kegiatan' => $event->nama_kegiatan ?? 'Sesi Evaluasi',
            'lokasi' => $lokasiNama ?? 'Lokasi kegiatan',
            'kelompok_label' => self::kelompokLabel($event),
            'status' => strtolower($event->status ?? 'dijadwalkan'),
        ]);
    }

    /**
     * Bergabung ke Sesi Ujian — PIN divalidasi ke event, identitas
     * disimpan sebagai peserta (DB) atau session (mock).
     */
    public function join(JoinParticipantRequest $request, QuizService $quiz)
    {
        $validated = $request->validated();

        $event = $quiz->findEventByKode($validated['kode_join']);
        if (! $event) {
            Log::warning('Join gagal: kode tidak dikenal', ['kode' => $validated['kode_join']]);

            return back()->withInput()
                ->withErrors(['kode_join' => 'Kode join tidak dikenal. Tanyakan kode yang benar kepada petugas.']);
        }

        $nama = trim(preg_replace('/\s+/', ' ', $validated['nama']));
        $kelas = trim($validated['kelas']);
        $lokasiNama = null;
        try {
            $lokasiNama = $event->lokasi->nama_lokasi ?? null;
        } catch (\Throwable $e) {
        }

        $participantId = null;
        $token = Str::random(32);

        try {
            $row = DB::transaction(fn () => Participant::create([
                'event_id' => $event->id,
                'name' => $nama,
                'class_grade' => $kelas,
                'school_origin' => $lokasiNama,
                'input_method' => 'online',
                'status' => 'menunggu',
                'session_token' => $token,
            ]));
            $participantId = $row->id;
            Log::info('Peserta join', ['event_id' => $event->id, 'participant_id' => $participantId]);
        } catch (\Throwable $e) {
            Log::error('Join gagal persist', ['event_id' => $event->id ?? null, 'err' => $e->getMessage()]);

            return back()->withInput()->withErrors(['nama' => 'Gagal bergabung. Coba lagi.']);
        }

        session([
            'participant_id' => $participantId,
            'participant_name' => $nama,
            'participant_kelas' => $kelas,
            'event_id' => $event->id,
            'session_token' => $token,
        ]);

        return redirect()->route('participant.waiting', [
            'room' => 'pretest',
            'id' => $participantId,
        ]);
    }

    /**
     * Apakah room boleh dimasuki sekarang.
     * Bila operator sudah memakai kontrol fase (status_fase), fase yang
     * menentukan. Bila belum (DRAFT/null), turun ke status kegiatan:
     * pretest saat berlangsung; posttest saat berlangsung + pre selesai.
     */
    public static function roomOpen(?object $event, string $room, ?object $participant): bool
    {
        if (! $event) {
            return false;
        }
        $fase = null;
        try {
            $fase = $event->status_fase ?? null;
            if ($fase) {
                $fase = strtoupper($fase);
            }
        } catch (\Throwable $e) {
        }
        if (in_array($fase, ['PRE_ACTIVE', 'MATERIAL_PAUSED', 'POST_ACTIVE', 'COMPLETED'], true)) {
            return $room === 'pretest' ? $fase === 'PRE_ACTIVE' : $fase === 'POST_ACTIVE';
        }
        $status = strtolower($event->status ?? 'dijadwalkan');
        if ($status !== 'berlangsung') {
            return false;
        }
        if ($room === 'pretest') {
            return true;
        }
        try {
            return $participant && $participant->pretest_score !== null;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Cari peserta (DB simeval_db).
     */
    protected function resolveParticipant($id): ?object
    {
        if (! is_numeric($id)) {
            return null;
        }

        return Participant::find((int) $id);
    }

    protected function countEventParticipants($eventId): int
    {
        if (! $eventId) {
            return 0;
        }

        return Participant::where('event_id', $eventId)->count();
    }

    /**
     * Ruang Tunggu — data identitas & skor riil (bukan hardcode).
     */
    public function waiting($room, $id = null)
    {
        $id = $id ?? session('participant_id') ?? 1;
        $room = $room === 'posttest' ? 'posttest' : 'pretest';
        $participant = $this->resolveParticipant($id);

        $eventId = $participant->event_id ?? session('event_id');
        $event = $this->resolveEvent($eventId);
        abort_if(! $event, 404, 'Kegiatan tidak ditemukan.');

        $nama = $participant->name ?? session('participant_name', 'Peserta Ujian');
        $kelas = $participant->class_grade ?? session('participant_kelas', '-');
        $sekolah = $participant->school_origin ?? null;
        if (! $sekolah) {
            try {
                $sekolah = $event->lokasi->nama_lokasi ?? 'SMAN 1 Surabaya';
            } catch (\Throwable $e) {
                $sekolah = 'SMAN 1 Surabaya';
            }
        }

        $session = (object) [
            'id' => $participant->id ?? $id,
            'kegiatan_id' => $event->id,
            'kegiatan' => $event,
            'kode_join' => $event->kode_join,
            'nama' => $nama,
            'name' => $nama,
            'nama_peserta' => $nama,
            'sekolah' => $sekolah.($kelas && $kelas !== '-' ? " • {$kelas}" : ''),
            'kelas' => $kelas,
            'skor_pretest' => $participant->pretest_score ?? null,
            'status' => 'menunggu',
        ];

        $kegiatan = $event;
        $waitingCount = $this->countEventParticipants($event->id);
        $triggerRedirect = true;
        $redirectUrl = route('participant.quiz', ['type' => $room, 'session' => $session->id]);

        return view('participant.waiting', compact('room', 'session', 'kegiatan', 'waitingCount', 'triggerRedirect', 'redirectUrl'));
    }

    /**
     * Polling Status Sesi — dinamis mengikuti status/fase kegiatan.
     */
    public function status(Request $request, $id = null)
    {
        $id = $id ?? session('participant_id') ?? 1;
        $participant = $this->resolveParticipant($id);
        // Room dari halaman yang sedang polling; fallback inferensi via skor.
        $room = $request->get('room') === 'posttest' ? 'posttest'
            : ($request->get('room') === 'pretest' ? 'pretest'
            : (($participant && $participant->pretest_score !== null) ? 'posttest' : 'pretest'));
        $event = $this->resolveEvent($participant->event_id ?? session('event_id'));

        return response()->json([
            'status_changed' => self::roomOpen($event, $room, $participant),
            'waiting_count' => $this->countEventParticipants($event->id ?? null),
            'status' => strtolower($event->status ?? 'menunggu'),
            'room' => $room,
        ]);
    }

    /**
     * Ambil butir soal paket beserta kunci (SERVER ONLY — kunci tidak
     * pernah dikirim ke browser). DB dulu, fallback mock.
     * Return: array of ['id','nomor','pertanyaan','opsi_a'..'opsi_d','kunci'].
     */
    public static function resolvePackageQuestions(int $packageId): array
    {
        $rows = Question::where('question_package_id', $packageId)->orderBy('urutan')->get();

        return $rows->map(fn ($q) => [
            'id' => $q->id,
            'nomor' => $q->urutan,
            'pertanyaan' => $q->pertanyaan,
            'opsi_a' => $q->opsi_a,
            'opsi_b' => $q->opsi_b,
            'opsi_c' => $q->opsi_c,
            'opsi_d' => $q->opsi_d,
            'kunci' => strtoupper($q->kunci ?? 'A'),
        ])->all();
    }

    /**
     * Cari event peserta — DB-first (MySQL simeval_db).
     */
    protected function resolveEvent($eventId): ?object
    {
        if (empty($eventId) || ! is_numeric($eventId)) {
            return null;
        }

        return Event::with('lokasi')->find((int) $eventId);
    }

    /**
     * Halaman Pengerjaan Soal (Pre-Test / Post-Test).
     * KUNCI JAWABAN DICABUT dari payload klien (anti bocoran via DevTools).
     */
    public function quiz($type, $session = null)
    {
        $session = $session ?? session('participant_id') ?? 1;
        $type = $type === 'posttest' ? 'posttest' : 'pretest';

        $packageId = $type === 'posttest' ? 2 : 1;
        $durasi = 30;
        try {
            $eventId = session('event_id');
            if ($eventId) {
                $event = $this->resolveEvent($eventId);
                if ($event) {
                    $pid = $type === 'posttest'
                        ? ($event->posttest_package_id ?? null)
                        : ($event->pretest_package_id ?? null);
                    if ($pid) {
                        $packageId = (int) $pid;
                    }
                }
            }
            foreach (BankSoalController::allPackages() as $p) {
                if ((int) $p->id === (int) $packageId) {
                    $durasi = (int) ($p->durasi ?? 30);
                    break;
                }
            }
        } catch (\Throwable $e) {
            // abaikan — pakai fallback di atas
        }

        $full = self::resolvePackageQuestions((int) $packageId);
        // STRIP kunci & bobot sebelum ke browser
        $soal = array_map(fn ($s) => [
            'id' => $s['id'],
            'nomor' => $s['nomor'],
            'pertanyaan' => $s['pertanyaan'],
            'opsi_a' => $s['opsi_a'],
            'opsi_b' => $s['opsi_b'],
            'opsi_c' => $s['opsi_c'],
            'opsi_d' => $s['opsi_d'],
        ], $full);

        $sessionObj = (object) [
            'id' => $session,
        ];

        return view('participant.quiz', [
            'quizType' => $type,
            'session' => $sessionObj,
            'soal' => $soal,
            'durasi' => $durasi,
        ]);
    }

    /**
     * Submit Jawaban Ujian — API JSON (dipakai QuizEngine via fetch).
     * Menilai server-side (jawaban vs kunci), menyimpan Participant +
     * ParticipantAnswer (cabang DB), mengembalikan skor + N-Gain.
     *
     * Kontrak respons: {success, score, n_gain, category, next_url}.
     */
    public function submit(Request $request, SubmitQuizAction $submitter)
    {
        // Kompatibilitas: POST form biasa (non-JSON) → alur redirect lama.
        if (! $request->wantsJson() && ! $request->ajax() && ! $request->isJson()) {
            $type = $request->get('type', 'pretest');
            $nextRoom = $type === 'pretest' ? 'posttest' : 'finish';

            if ($nextRoom === 'finish') {
                return redirect()->route('participant.welcome')
                    ->with('success', 'Evaluasi telah selesai. Terima kasih telah berpartisipasi!');
            }

            $id = $request->get('session_id', session('participant_id'));
            if (! $id) {
                return redirect()->route('participant.welcome')->withErrors(['kode_join' => 'Sesi tidak valid. Masukkan kode join ulang.']);
            }

            return redirect()->route('participant.waiting', [
                'room' => $nextRoom,
                'id' => $id,
            ])->with('success', 'Pre-Test berhasil dikirim! Menunggu sesi Post-Test.');
        }

        $type = $request->input('quiz_type', 'pretest');
        $type = $type === 'posttest' ? 'posttest' : 'pretest';
        $sessionId = $request->input('session_id');
        $answers = $request->input('answers', []);
        if (! is_array($answers)) {
            $answers = [];
        }

        // Event + paket (agar kunci yang dipakai = paket event saat ini)
        $event = $this->resolveEvent(session('event_id'));
        $packageId = $type === 'posttest'
            ? (int) ($event->posttest_package_id ?? 2)
            : (int) ($event->pretest_package_id ?? 1);

        $questions = self::resolvePackageQuestions($packageId);
        $total = count($questions);
        $correct = 0;
        $graded = [];
        foreach ($questions as $q) {
            $given = strtoupper(trim($answers[$q['id']] ?? $answers[(string) $q['id']] ?? ''));
            $ok = $given !== '' && $given === strtoupper($q['kunci']);
            if ($ok) {
                $correct++;
            }
            $graded[] = [
                'question_id' => $q['id'],
                'jawaban' => $given !== '' ? $given : null,
                'is_correct' => $ok,
            ];
        }
        $score = $total > 0 ? round($correct / $total * 100, 2) : 0;

        $nGain = null;
        $category = null;

        // Persist transaksional: peserta + jawaban per butir.
        try {
            $participant = is_numeric($sessionId) ? Participant::find((int) $sessionId) : Participant::where('session_token', (string) $sessionId)->first();
            if (! $participant) {
                Log::warning('Submit quiz: peserta tidak ditemukan', ['session' => $sessionId]);

                return ApiResponse::notFound('Sesi peserta tidak ditemukan.');
            }
            DB::transaction(function () use ($participant, $type, $score, $graded) {
                if ($type === 'pretest') {
                    $participant->pretest_score = $score;
                    $participant->status = 'jeda';
                } else {
                    $participant->posttest_score = $score;
                }
                $participant->save();
                ParticipantAnswer::where('participant_id', $participant->id)->where('stage', $type)->delete();
                foreach ($graded as $g) {
                    ParticipantAnswer::create([
                        'participant_id' => $participant->id,
                        'question_id' => $g['question_id'],
                        'stage' => $type,
                        'jawaban' => $g['jawaban'],
                        'is_correct' => $g['is_correct'],
                    ]);
                }
            });
            $participant->refresh();
            $nGain = $participant->n_gain !== null ? (float) $participant->n_gain : null;
            $category = $participant->category;
            Log::info('Quiz submit persist', ['participant_id' => $participant->id, 'type' => $type, 'score' => $score]);
        } catch (\Throwable $e) {
            Log::error('Submit quiz gagal', ['session' => $sessionId, 'err' => $e->getMessage()]);

            return ApiResponse::fail('Gagal menyimpan jawaban. Coba lagi.', null, 500);
        }

        $nextUrl = $type === 'pretest'
            ? route('participant.waiting', ['room' => 'posttest', 'id' => $sessionId])
            : route('participant.welcome');

        return ApiResponse::ok([
            'score' => $score,
            'correct' => $correct,
            'total' => $total,
            'n_gain' => $nGain,
            'category' => $category,
            'next_url' => $nextUrl,
        ], $type === 'pretest' ? 'Pre-Test berhasil dinilai.' : 'Post-Test berhasil dinilai.');
    }
}
