<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && in_array(auth()->user()->role, ['superadmin', 'operator'], true);
    }

    public function rules(): array
    {
        return [
            'pertanyaan' => ['required', 'string'],
            'opsi_a' => ['required', 'string'],
            'opsi_b' => ['required', 'string'],
            'opsi_c' => ['required', 'string'],
            'opsi_d' => ['nullable', 'string'],
            'kunci' => ['required', 'in:A,B,C,D'],
            'urutan' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
