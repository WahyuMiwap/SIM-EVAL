<?php

namespace App\Actions;

use App\Models\Event;
use App\Services\AuditService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CreateEventAction
{
    public function handle(array $data, int $userId): Event
    {
        return DB::transaction(function () use ($data, $userId) {
            $kodeJoin = $this->uniqueKodeJoin();
            $event = Event::create([
                'nama_kegiatan' => $data['nama_kegiatan'],
                'kode_join' => $kodeJoin,
                'status' => $data['status'] ?? 'dijadwalkan',
                'tanggal' => $data['tanggal'] ?? now()->format('Y-m-d'),
                'durasi_menit' => (int) ($data['durasi_menit'] ?? 30),
                'catatan' => $data['catatan'] ?? '',
                'lokasi_id' => $data['lokasi_id'],
                'pretest_package_id' => $data['pretest_package_id'] ?? null,
                'posttest_package_id' => $data['posttest_package_id'] ?? null,
                'created_by' => $userId,
            ]);
            AuditService::record('buat_kegiatan', 'event', $event->id, "Kegiatan dibuat: {$event->nama_kegiatan} (PIN {$kodeJoin})");
            Log::info('Kegiatan dibuat', ['event_id' => $event->id, 'kode_join' => $kodeJoin, 'by' => $userId]);

            return $event;
        });
    }

    private function uniqueKodeJoin(): string
    {
        do {
            $kode = strtoupper(Str::random(6));
        } while (Event::where('kode_join', $kode)->exists());

        return $kode;
    }
}
