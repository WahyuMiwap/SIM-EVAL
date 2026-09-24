<?php

namespace App\Jobs;

use App\Services\OmrService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessOmrBatchJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $eventId, public array $scans, public int $userId) {}

    public function handle(OmrService $omr): void
    {
        Log::info('OMR batch dimulai', ['event_id' => $this->eventId, 'count' => count($this->scans)]);
        foreach ($this->scans as $scan) {
            try {
                $omr->persistScan($this->eventId, $scan, $this->userId);
            } catch (\Throwable $e) {
                Log::error('OMR batch item gagal', ['event_id' => $this->eventId, 'err' => $e->getMessage()]);
            }
        }
        Log::info('OMR batch selesai', ['event_id' => $this->eventId]);
    }
}
