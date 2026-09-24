<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Location;
use App\Models\Participant;
use App\Models\QuestionPackage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KegiatanTest extends TestCase
{
    use RefreshDatabase;

    private function seedDasar(): array
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
            'acak_urutan' => true,
            'created_by' => $operator->id,
        ]);

        return compact('operator', 'lokasi', 'paket');
    }

    public function test_index_butuh_login(): void
    {
        $this->get(route('operator.kegiatan.index'))->assertRedirect(route('login'));
    }

    public function test_store_validasi_nama_dan_lokasi_wajib(): void
    {
        ['operator' => $operator] = $this->seedDasar();

        $this->actingAs($operator)
            ->post(route('operator.kegiatan.store'), [])
            ->assertSessionHasErrors(['nama_kegiatan', 'lokasi_id']);
    }

    public function test_store_berhasil_buat_event_dan_redirect_detail(): void
    {
        ['operator' => $operator, 'lokasi' => $lokasi, 'paket' => $paket] = $this->seedDasar();

        $response = $this->actingAs($operator)->post(route('operator.kegiatan.store'), [
            'nama_kegiatan' => 'Sosialisasi Anti Narkoba — SMA N 5',
            'lokasi_id' => $lokasi->id,
            'tanggal' => '2026-09-10',
            'durasi_menit' => 30,
            'paket_sama' => true,
            'paket_utama' => $paket->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('events', [
            'nama_kegiatan' => 'Sosialisasi Anti Narkoba — SMA N 5',
            'lokasi_id' => $lokasi->id,
        ]);
    }

    public function test_export_csv_memuat_header_bnn(): void
    {
        ['operator' => $operator, 'lokasi' => $lokasi, 'paket' => $paket] = $this->seedDasar();

        $event = Event::create([
            'nama_kegiatan' => 'Sosialisasi P4GN',
            'kode_join' => 'AB1C2D',
            'status' => 'selesai',
            'tanggal' => '2026-09-10',
            'durasi_menit' => 30,
            'kategori_audiens' => 'SMA',
            'lokasi_id' => $lokasi->id,
            'pretest_package_id' => $paket->id,
            'posttest_package_id' => $paket->id,
            'created_by' => $operator->id,
        ]);

        Participant::create([
            'event_id' => $event->id,
            'name' => 'Ahmad Fauzi',
            'class_grade' => 'XI-MIPA 1',
            'pretest_score' => 60,
            'posttest_score' => 85,
            'input_method' => 'manual',
        ]);

        $response = $this->actingAs($operator)
            ->get(route('operator.kegiatan.export', $event->id));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        // StreamedResponse: baca via streamedContent(), bukan assertSee()
        $isi = $response->streamedContent();
        $this->assertStringContainsString('BADAN NARKOTIKA NASIONAL', $isi);
        $this->assertStringContainsString('Sosialisasi P4GN', $isi);
        $this->assertStringContainsString('Ahmad Fauzi', $isi);
    }
}
