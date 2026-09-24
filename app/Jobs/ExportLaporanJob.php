<?php

namespace App\Jobs;

use App\Http\Controllers\KegiatanController;
use App\Services\AuditService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ExportLaporanJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $eventId, public int $userId) {}

    public function handle(): void
    {
        Log::info('Export laporan dimulai', ['event_id' => $this->eventId, 'by' => $this->userId]);
        try {
            $kegiatan = KegiatanController::findEventDetailed($this->eventId);
            if (! $kegiatan) {
                Log::warning('Export laporan: kegiatan tidak ditemukan', ['event_id' => $this->eventId]);

                return;
            }
            $participants = KegiatanController::eventParticipants($this->eventId);
            $stats = KegiatanController::eventStats($this->eventId);

            $lines = [];
            $lines[] = 'BADAN NARKOTIKA NASIONAL KOTA SURABAYA';
            $lines[] = 'LAPORAN REKAPITULASI EVALUASI PRE-TEST & POST-TEST';
            $lines[] = 'Nama Kegiatan,'.($kegiatan->nama_kegiatan ?? '-');
            $lines[] = 'Total Peserta,'.($stats['total'] ?? count($participants));
            $lines[] = 'Rata-rata N-Gain,'.($stats['avg_gain'] ?? 0);
            $lines[] = '';
            $lines[] = 'No,Nama,Kelas,Pre,Post,Gain,N-Gain,Kategori,Metode';
            foreach ($participants->values() as $i => $p) {
                $gain = ($p->posttest_score ?? 0) - ($p->pretest_score ?? 0);
                $lines[] = implode(',', [$i + 1, '"'.str_replace('"', '""', $p->name).'"', $p->class_grade ?? '-', $p->pretest_score ?? 0, $p->posttest_score ?? 0, $gain, $p->n_gain ?? 0, $p->kategori ?? '-', $p->input_method ?? 'manual']);
            }
            $content = "\xEF\xBB\xBF".implode("\n", $lines);
            Storage::disk('local')->put("laporan/event-{$this->eventId}-".date('YmdHis').'.csv', $content);
            AuditService::record('unduh_rekap', 'event', $this->eventId, 'Export laporan via queue selesai.');
            Log::info('Export laporan selesai', ['event_id' => $this->eventId]);
        } catch (\Throwable $e) {
            Log::error('Export laporan gagal', ['event_id' => $this->eventId, 'err' => $e->getMessage()]);
            throw $e;
        }
    }
}
