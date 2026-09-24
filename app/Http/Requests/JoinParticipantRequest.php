<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JoinParticipantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kode_join' => ['required', 'string', 'max:10'],
            'nama' => ['required', 'string', 'max:100'],
            'kelas' => ['required', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'kode_join.required' => 'Masukkan 6 karakter kode join.',
            'nama.required' => 'Masukkan nama lengkap.',
            'kelas.required' => 'Masukkan kelas / kelompok.',
        ];
    }
}
