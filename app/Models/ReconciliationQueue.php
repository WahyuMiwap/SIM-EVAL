<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReconciliationQueue extends Model
{
    use HasFactory;

    protected $table = 'reconciliation_queue';

    protected $fillable = [
        'event_id',
        'scan_id',
        'issue_type', // DUPLICATE_NAME, NO_MATCH_POST, MISSING_SIDE, LOW_CONFIDENCE_OMR
        'detail',
        'resolved',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'detail'      => 'array',
            'resolved'    => 'boolean',
            'resolved_at' => 'datetime',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function scan(): BelongsTo
    {
        return $this->belongsTo(Scan::class, 'scan_id');
    }
}
