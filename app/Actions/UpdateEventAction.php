<?php

namespace App\Actions;

use App\Models\Event;
use App\Services\AuditService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateEventAction
{
    public function handle(Event $event, array $data, int $userId): Event
    {
        return DB::transaction(function () use ($event, $data, $userId) {
            $event->update($data);
            AuditService::record('ubah_kegiatan', 'event', $event->id, "Kegiatan diubah: {$event->nama_kegiatan}");
            Log::info('Kegiatan diubah', ['event_id' => $event->id, 'by' => $userId]);

            return $event->refresh();
        });
    }
}
