<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaketSoalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && in_array(auth()->user()->role, ['superadmin', 'operator'], true);
    }

    public function rules(): array
    {
        return [
            'nama_paket' => ['required', 'string', 'max:255'],
            'tema' => ['nullable', 'string', 'max:255'],
            'kategori_audiens' => ['nullable', 'string', 'max:50'],
            'jumlah_opsi' => ['nullable', 'integer', 'in:3,4'],
            'tipe' => ['nullable', 'in:pretest,posttest,umum'],
            'durasi' => ['nullable', 'integer', 'min:5', 'max:180'],
            'acak_urutan' => ['nullable', 'boolean'],
        ];
    }
}
