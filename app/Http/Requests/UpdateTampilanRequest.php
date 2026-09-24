<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTampilanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'superadmin';
    }

    public function rules(): array
    {
        return [
            'nama' => ['nullable', 'string', 'max:100'],
            'subnama' => ['nullable', 'string', 'max:255'],
            'warna_primer' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'bg_login' => ['nullable', 'image', 'max:4096'],
        ];
    }

    public function messages(): array
    {
        return [
            'warna_primer.regex' => 'Warna primer harus format hex seperti #4361EE.',
        ];
    }
}
