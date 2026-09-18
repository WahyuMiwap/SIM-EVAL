<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuestionPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_paket',
        'kategori_audiens', // SD, SMP, SMA, UMUM, LAPAS
        'jumlah_opsi',      // 3, 4
        'tipe',             // pretest, posttest, umum
        'durasi',
        'acak_urutan',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'durasi'       => 'integer',
            'jumlah_opsi'  => 'integer',
            'acak_urutan'  => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'question_package_id')->orderBy('urutan');
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'pretest_package_id');
    }
}
