<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Location;
use App\Models\Participant;
use App\Models\QuestionPackage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NGainTest extends TestCase
{
    use RefreshDatabase;

    private function buatPeserta(float $pre, float $post): Participant
    {
        $user = User::factory()->create();
        $lokasi = Location::create([
            'nama_lokasi' => 'SMA N 5',
            'jenis_sasaran' => 'sekolah',
        ]);
        $paket = QuestionPackage::create([
            'nama_paket' => 'Paket Uji',
            'kategori_audiens' => 'SMA',
            'jumlah_opsi' => 4,
            'tipe' => 'pretest',
            'durasi' => 30,
            'created_by' => $user->id,
        ]);
        $event = Event::create([
            'nama_kegiatan' => 'Uji N-Gain',
            'kode_join' => strtoupper(substr(md5(rand()), 0, 6)),
            'status' => 'selesai',
            'tanggal' => '2026-09-10',
            'lokasi_id' => $lokasi->id,
            'pretest_package_id' => $paket->id,
            'posttest_package_id' => $paket->id,
            'created_by' => $user->id,
        ]);

        return Participant::create([
            'event_id' => $event->id,
            'name' => 'Peserta Uji',
            'pretest_score' => $pre,
            'posttest_score' => $post,
            'input_method' => 'manual',
        ]);
    }

    public function test_n_gain_normal_dan_kategori_cukup(): void
    {
        // (85-60)/(100-60) = 0.625 -> 0.63 -> cukup
        $p = $this->buatPeserta(60, 85);

        $this->assertEquals(25.0, (float) $p->delta);
        $this->assertEquals(0.63, (float) $p->n_gain);
        $this->assertEquals('cukup', $p->category);
        $this->assertEquals('COMPLETE', $p->status_data);
    }

    public function test_n_gain_tinggi_batas_070(): void
    {
        // (70-0)/100 = 0.70 -> paham
        $p = $this->buatPeserta(0, 70);

        $this->assertEquals(0.70, (float) $p->n_gain);
        $this->assertEquals('paham', $p->category);
    }

    public function test_n_gain_rendah(): void
    {
        // (50-40)/60 = 0.1667 -> 0.17 -> kurang
        $p = $this->buatPeserta(40, 50);

        $this->assertEquals(0.17, (float) $p->n_gain);
        $this->assertEquals('kurang', $p->category);
    }

    public function test_pre_sempurna_post_sempurna_dapat_satu(): void
    {
        $p = $this->buatPeserta(100, 100);

        $this->assertEquals(1.00, (float) $p->n_gain);
    }

    public function test_pre_sempurna_post_turun_jadi_null(): void
    {
        $p = $this->buatPeserta(100, 80);

        $this->assertNull($p->n_gain);
    }

    public function test_skor_turun_tidak_di_clamp_saat_ini(): void
    {
        // Blueprint: skor turun idealnya g=0.00. Implementasi saat ini
        // membiarkan minus — test ini mendokumentasikan perilaku aktual.
        // (50-70)/30 = -0.6667 -> -0.67
        $p = $this->buatPeserta(70, 50);

        $this->assertEquals(-20.0, (float) $p->delta);
        $this->assertEquals(-0.67, (float) $p->n_gain);
        $this->assertEquals('kurang', $p->category);
    }

    public function test_belum_lengkap_tidak_hitung_gain(): void
    {
        $user = User::factory()->create();
        $lokasi = Location::create(['nama_lokasi' => 'X', 'jenis_sasaran' => 'sekolah']);
        $paket = QuestionPackage::create([
            'nama_paket' => 'P', 'kategori_audiens' => 'SMA',
            'jumlah_opsi' => 4, 'tipe' => 'pretest', 'durasi' => 30,
            'created_by' => $user->id,
        ]);
        $event = Event::create([
            'nama_kegiatan' => 'E', 'kode_join' => 'ZZ9Z9Z',
            'status' => 'berlangsung', 'tanggal' => '2026-09-10',
            'lokasi_id' => $lokasi->id,
            'pretest_package_id' => $paket->id, 'posttest_package_id' => $paket->id,
            'created_by' => $user->id,
        ]);

        $p = Participant::create([
            'event_id' => $event->id,
            'name' => 'Setengah Jalan',
            'pretest_score' => 60,
            'posttest_score' => null,
            'input_method' => 'manual',
        ]);

        $this->assertNull($p->n_gain);
        $this->assertNull($p->delta);
        $this->assertEquals('INCOMPLETE_RECORD', $p->status_data);
    }
}
