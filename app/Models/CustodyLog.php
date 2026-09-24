<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustodyLog extends Model
{
    use HasFactory;

    protected $table = 'custody_log';

    protected $fillable = [
        'event_id',
        'handed_by',
        'handed_to',
        'location_note',
        'timestamp',
    ];

    protected function casts(): array
    {
        return [
            'timestamp' => 'datetime',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function handedByOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handed_by');
    }

    public function handedToOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handed_to');
    }
}
