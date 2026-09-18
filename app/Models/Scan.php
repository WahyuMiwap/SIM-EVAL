<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Scan extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'participant_id',
        'phase_type',
        'raw_answers',
        'score_raw',
        'score_percent',
        'photo_path',
        'omr_confidence',
        'captured_by',
        'captured_at',
        'capture_method',
        'serial_number',
    ];

    protected function casts(): array
    {
        return [
            'raw_answers'   => 'array',
            'score_percent' => 'decimal:2',
            'captured_at'   => 'datetime',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'participant_id');
    }

    public function officer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'captured_by');
    }
}
