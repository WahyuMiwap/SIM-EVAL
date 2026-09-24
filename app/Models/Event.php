<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_event',
        'nama_kegiatan',
        'kode_join',
        'status', // 'dijadwalkan', 'berlangsung', 'selesai'
        'tanggal',
        'durasi_menit',
        'catatan',
        'kategori_audiens',       // SD, SMP, SMA, LAPAS, UMUM, INSTANSI
        'mode_input',             // MANUAL_KERTAS, DIGITAL_PWA, HYBRID
        'digital_submode',         // TERBUKA, TERDAFTAR
        'enable_custody_tracking',
        'status_fase',            // DRAFT, PRE_ACTIVE, MATERIAL_PAUSED, POST_ACTIVE, COMPLETED
        'lokasi_id',
        'pretest_package_id',
        'posttest_package_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'durasi_menit' => 'integer',
            'enable_custody_tracking' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Event $event) {
            // Auto generate kode_event jika belum diisi (format: EV-YYYY-MM-DD-XXXX)
            if (empty($event->kode_event)) {
                $tgl = $event->tanggal ? $event->tanggal->format('Y-m-d') : now()->format('Y-m-d');
                $random = strtoupper(Str::random(5));
                $event->kode_event = "EV-{$tgl}-{$random}";
            }

            // Lapas otomatis mengaktifkan custody tracking
            if (strtoupper($event->kategori_audiens ?? '') === 'LAPAS') {
                $event->enable_custody_tracking = true;
            }
        });
    }

    public function lokasi(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'lokasi_id');
    }

    public function pretestPackage(): BelongsTo
    {
        return $this->belongsTo(QuestionPackage::class, 'pretest_package_id');
    }

    public function posttestPackage(): BelongsTo
    {
        return $this->belongsTo(QuestionPackage::class, 'posttest_package_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class, 'event_id');
    }

    public function scans(): HasMany
    {
        return $this->hasMany(Scan::class, 'event_id');
    }

    public function reconciliationQueue(): HasMany
    {
        return $this->hasMany(ReconciliationQueue::class, 'event_id');
    }

    public function custodyLogs(): HasMany
    {
        return $this->hasMany(CustodyLog::class, 'event_id');
    }

    /**
     * Hitung rata-rata N-Gain kelas (hanya dari peserta dengan status COMPLETE / valid)
     */
    public function getAvgNGainAttribute(): ?float
    {
        $avg = $this->participants()
            ->where('status_data', 'COMPLETE')
            ->whereNotNull('n_gain')
            ->avg('n_gain');

        return $avg !== null ? round($avg, 2) : null;
    }

    /**
     * Hitung rata-rata Delta (Post - Pre) kelas (hanya dari peserta COMPLETE)
     */
    public function getAvgDeltaAttribute(): ?float
    {
        $avg = $this->participants()
            ->where('status_data', 'COMPLETE')
            ->whereNotNull('delta')
            ->avg('delta');

        return $avg !== null ? round($avg, 2) : null;
    }
}
