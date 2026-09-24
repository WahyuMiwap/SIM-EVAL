<?php

namespace App\Actions;

use App\Models\Scan;
use App\Services\OmrService;

class RecordOmrScanAction
{
    public function __construct(private OmrService $omr) {}

    public function handle(int $eventId, array $data, int $userId): Scan
    {
        return $this->omr->persistScan($eventId, $data, $userId);
    }
}
