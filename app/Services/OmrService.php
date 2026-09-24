<?php

namespace App\Services;

use App\Models\ReconciliationQueue;
use App\Models\Scan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OmrService
{
    public function persistScan(int $eventId, array $data, int $userId): Scan
    {
        return DB::transaction(function () use ($eventId, $data, $userId) {
            $scan = Scan::create([
                'event_id' => $eventId,
                'participant_id' => $data['participant_id'] ?? null,
                'phase_type' => $data['phase_type'] ?? 'PRE',
                'raw_answers' => $data['raw_answers'] ?? [],
                'score_raw' => $data['score_raw'] ?? 0,
                'score_percent' => $data['score_percent'] ?? 0,
                'omr_confidence' => $data['omr_confidence'] ?? 'HIGH',
                'captured_by' => $userId,
                'captured_at' => now(),
                'capture_method' => $data['capture_method'] ?? 'CAMERA_LIVE',
                'serial_number' => $data['serial_number'] ?? null,
            ]);

            $confidence = (float) ($data['confidence_margin'] ?? 1);
            $lowThreshold = (float) env('OMR_CONF_LOW', 0.12);
            if ($confidence < $lowThreshold || empty($data['participant_id'])) {
                ReconciliationQueue::create([
                    'event_id' => $eventId,
                    'scan_id' => $scan->id,
                    'issue_type' => $confidence < $lowThreshold ? 'LOW_CONFIDENCE_OMR' : 'NO_MATCH_POST',
                    'detail' => ['confidence' => $confidence, 'raw' => $data['raw_answers'] ?? []],
                    'resolved' => false,
                ]);
            }

            Log::info('OMR scan direkam', ['event_id' => $eventId, 'scan_id' => $scan->id, 'by' => $userId]);

            return $scan;
        });
    }
}
