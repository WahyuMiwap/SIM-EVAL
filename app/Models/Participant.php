<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Participant extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'nomor_absen',
        'name',
        'nama_normalized',
        'class_grade',
        'school_origin',
        'keterangan',
        'pretest_score',
        'posttest_score',
        'delta',
        'n_gain',
        'category',
        'input_method', // 'manual', 'omr', 'online'
        'status',       // 'menunggu', 'pretest', 'jeda', 'posttest', 'selesai'
        'status_data',   // 'COMPLETE', 'INCOMPLETE_RECORD'
        'serial_pre',
        'serial_post',
        'session_token',
    ];

    protected function casts(): array
    {
        return [
            'pretest_score' => 'decimal:2',
            'posttest_score' => 'decimal:2',
            'delta' => 'decimal:2',
            'n_gain' => 'decimal:2',
            'nomor_absen' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Participant $participant) {
            // Normalisasi nama (lower + trim + collapse spasi berganda)
            if (! empty($participant->name)) {
                $participant->nama_normalized = strtolower(trim(preg_replace('/\s+/', ' ', $participant->name)));
            }

            // Hitung delta dan N-Gain bila nilai PRE dan POST tersedia
            if ($participant->pretest_score !== null && $participant->posttest_score !== null) {
                $pre = (float) $participant->pretest_score;
                $post = (float) $participant->posttest_score;

                // 1. Rumus Utama: Delta (Post - Pre)
                $participant->delta = round($post - $pre, 2);

                // 2. Rumus Akademis: N-Gain Hake
                if ($pre >= 100) {
                    $participant->n_gain = ($post >= 100) ? 1.00 : null; // INVALID_DIVISION bila pre=100 & post<100
                } else {
                    $denom = 100.0 - $pre;
                    $nGain = ($post - $pre) / $denom;
                    $participant->n_gain = round($nGain, 2);
                }

                // Klasifikasi Paham / Cukup / Kurang
                if ($participant->n_gain !== null) {
                    if ($participant->n_gain >= 0.70) {
                        $participant->category = 'paham';
                    } elseif ($participant->n_gain >= 0.30) {
                        $participant->category = 'cukup';
                    } else {
                        $participant->category = 'kurang';
                    }
                }

                $participant->status_data = 'COMPLETE';
                $participant->status = 'selesai';
            } else {
                $participant->status_data = 'INCOMPLETE_RECORD';
                $participant->delta = null;
                $participant->n_gain = null;
            }
        });
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ParticipantAnswer::class, 'participant_id');
    }

    public function scans(): HasMany
    {
        return $this->hasMany(Scan::class, 'participant_id');
    }
}
