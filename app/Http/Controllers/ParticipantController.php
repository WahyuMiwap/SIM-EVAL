<?php

namespace App\Http\Controllers;

use App\Services\MockDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ParticipantController extends Controller
{
    /**
     * Halaman Welcome / Masukkan Kode Join
     */
    public function welcome()
    {
        return view('participant.welcome');
    }

    /**
     * Bergabung ke Sesi Ujian (Mock Mode)
     */
    public function join(Request $request)
    {
        $kode = strtoupper(trim($request->get('kode_join', 'SMAN01')));
        $nama = trim($request->get('nama', 'Peserta Demo'));

        $event = MockDataService::getEvents()[0]; // SMAN 1 Surabaya
        $participantId = rand(100, 999);

        session([
            'participant_id'   => $participantId,
            'participant_name' => $nama,
            'event_id'         => $event->id,
            'session_token'    => Str::random(32),
        ]);

        return redirect()->route('participant.waiting', [
            'room' => 'pretest',
            'id'   => $participantId,
        ]);
    }

    /**
     * Ruang Tunggu (Waiting Room)
     */
    public function waiting($room, $id)
    {
        $event = MockDataService::getEvents()[0];

        $session = (object)[
            'id'           => $id,
            'kegiatan_id'  => $event->id,
            'kegiatan'     => $event,
            'kode_join'    => $event->kode_join,
            'nama'         => session('participant_name', 'Peserta Ujian'),
            'name'         => session('participant_name', 'Peserta Ujian'),
            'nama_peserta' => session('participant_name', 'Peserta Ujian'),
            'sekolah'      => $event->lokasi->nama_lokasi ?? 'SMAN 1 Surabaya',
            'kelas'        => 'X MIPA 1',
            'skor_pretest' => 75,
            'status'       => 'menunggu',
        ];

        $kegiatan = $event;
        $waitingCount = rand(35, 60);
        $triggerRedirect = true;
        $redirectUrl = route('participant.quiz', ['type' => $room, 'session' => $id]);

        return view('participant.waiting', compact('room', 'session', 'kegiatan', 'waitingCount', 'triggerRedirect', 'redirectUrl'));
    }

    /**
     * Polling Status Sesi dari Waiting Room
     */
    public function status($id)
    {
        return response()->json([
            'status_changed' => false,
            'waiting_count'  => rand(40, 65),
            'status'         => 'aktif',
        ]);
    }

    /**
     * Halaman Pengerjaan Soal (Pre-Test / Post-Test)
     */
    public function quiz($type, $session)
    {
        $packageId = $type === 'posttest' ? 2 : 1;
        $soal = MockDataService::getQuestionsForPackage($packageId);
        $durasi = 30;

        $sessionObj = (object)[
            'id' => $session,
        ];

        return view('participant.quiz', [
            'quizType' => $type,
            'session'  => $sessionObj,
            'soal'     => $soal,
            'durasi'   => $durasi,
        ]);
    }

    /**
     * Submit Jawaban Ujian
     */
    public function submit(Request $request)
    {
        $type = $request->get('type', 'pretest');
        $nextRoom = $type === 'pretest' ? 'posttest' : 'finish';

        if ($nextRoom === 'finish') {
            return redirect()->route('participant.welcome')
                ->with('success', 'Evaluasi telah selesai. Terima kasih telah berpartisipasi!');
        }

        $id = $request->get('session_id', rand(100, 999));
        return redirect()->route('participant.waiting', [
            'room' => $nextRoom,
            'id'   => $id,
        ])->with('success', 'Pre-Test berhasil dikirim! Menunggu sesi Post-Test.');
    }
}
