<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Location;
use App\Models\Participant;
use App\Models\Question;
use App\Models\QuestionPackage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParticipantFlowTest extends TestCase
{
    use RefreshDatabase;

    private function seedEvent(): Event
    {
        $operator = User::factory()->operator()->create();

        $lokasi = Location::create([
            'nama_lokasi' => 'SMA N 5 Surabaya',
            'alamat' => 'Jl. Pemuda No. 5',
            'kecamatan' => 'Genteng',
            'jenis_sasaran' => 'sekolah',
        ]);

        $paket = QuestionPackage::create([
            'nama_paket' => 'Pre-Test Umum',
            'tema' => 'P4GN Pelajar',
            'kategori_audiens' => 'SMA',
            'jumlah_opsi' => 4,
            'tipe' => 'pretest',
            'durasi' => 30,
            'acak_urutan' => false,
            'created_by' => $operator->id,
        ]);

        foreach ([
            ['pertanyaan' => 'Kepanjangan Narkoba adalah?', 'kunci' => 'C'],
            ['pertanyaan' => 'Jenis narkoba?', 'kunci' => 'B'],
        ] as $i => $s) {
            Question::create([
                'question_package_id' => $paket->id,
                'pertanyaan' => $s['pertanyaan'],
                'opsi_a' => 'Opsi A',
                'opsi_b' => 'Opsi B',
                'opsi_c' => 'Opsi C',
                'opsi_d' => 'Opsi D',
                'kunci' => $s['kunci'],
                'urutan' => $i + 1,
            ]);
        }

        return Event::create([
            'nama_kegiatan' => 'Sosialisasi SMA 5',
            'kode_join' => 'AB1C2D',
            'status' => 'berlangsung',
            'tanggal' => '2026-09-10',
            'durasi_menit' => 30,
            'kategori_audiens' => 'SMA',
            'lokasi_id' => $lokasi->id,
            'pretest_package_id' => $paket->id,
            'posttest_package_id' => $paket->id,
            'created_by' => $operator->id,
        ]);
    }

    public function test_join_info_ditemukan_dan_tidak_ditemukan(): void
    {
        $this->seedEvent();

        $this->getJson('/join/info?kode=AB1C2D')
            ->assertOk()
            ->assertJson(['found' => true]);

        $this->getJson('/join/info?kode=XXXXXX')
            ->assertOk()
            ->assertJson(['found' => false]);
    }

    public function test_join_kode_salah_gagal_validasi(): void
    {
        $this->seedEvent();

        $response = $this->post(route('participant.join'), [
            'kode_join' => 'SALAH1',
            'nama' => 'Budi',
            'kelas' => 'XI-1',
        ]);

        $response->assertSessionHasErrors('kode_join');
    }

    public function test_join_berhasil_redirect_ruang_tunggu(): void
    {
        $event = $this->seedEvent();

        $response = $this->post(route('participant.join'), [
            'kode_join' => strtolower('AB1C2D'), // case-insensitive
            'nama' => 'Budi Santoso',
            'kelas' => 'XI-MIPA 1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('participants', [
            'event_id' => $event->id,
            'name' => 'Budi Santoso',
        ]);
    }

    public function test_submit_pretest_json_menilai_dengan_kunci(): void
    {
        $event = $this->seedEvent();

        $peserta = Participant::create([
            'event_id' => $event->id,
            'name' => 'Citra',
            'class_grade' => 'XI-1',
            'input_method' => 'online',
            'status' => 'menunggu',
        ]);

        $questions = Question::where('question_package_id', $event->pretest_package_id)
            ->orderBy('urutan')->get();
        $answers = [
            $questions[0]->id => 'C', // benar
            $questions[1]->id => 'A', // salah (kunci B)
        ];

        $response = $this->withSession(['event_id' => $event->id])
            ->postJson(route('participant.submit'), [
                'quiz_type' => 'pretest',
                'session_id' => $peserta->id,
                'answers' => $answers,
            ]);

        $response->assertOk()
            ->assertJson(['success' => true, 'score' => 50.0, 'correct' => 1, 'total' => 2]);

        $this->assertDatabaseHas('participants', [
            'id' => $peserta->id,
            'pretest_score' => 50,
        ]);
    }

    public function test_halaman_quiz_tidak_membocorkan_kunci(): void
    {
        $event = $this->seedEvent();

        $response = $this->withSession(['event_id' => $event->id])
            ->get(route('participant.quiz', ['type' => 'pretest', 'session' => 1]));

        $response->assertOk();
        $soal = $response->viewData('soal');
        $this->assertNotEmpty($soal);
        foreach ($soal as $butir) {
            $this->assertArrayNotHasKey('kunci', $butir);
        }
    }
}
