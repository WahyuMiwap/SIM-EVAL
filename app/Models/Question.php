<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_package_id',
        'pertanyaan',
        'opsi_a',
        'opsi_b',
        'opsi_c',
        'opsi_d',
        'kunci',
        'urutan',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(QuestionPackage::class, 'question_package_id');
    }

    /**
     * Akses $question->opsi => ['A'..'D'].
     * (Nama diawali getOpsi agar dikenali Eloquent sebagai mutator atribut `opsi`.)
     */
    public function getOpsiAttribute(): array
    {
        return [
            'A' => $this->opsi_a,
            'B' => $this->opsi_b,
            'C' => $this->opsi_c,
            'D' => $this->opsi_d,
        ];
    }

    /** Alias kompatibilitas lama. */
    public function getOptionsAttribute(): array
    {
        return $this->getOpsiAttribute();
    }
}
