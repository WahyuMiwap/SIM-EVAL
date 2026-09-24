<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLokasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && in_array(auth()->user()->role, ['superadmin', 'operator'], true);
    }

    public function rules(): array
    {
        return [
            'nama_lokasi' => ['required', 'string', 'max:255'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'kecamatan' => ['nullable', 'string', 'max:100'],
            'jenis_sasaran' => ['nullable', 'in:sekolah,kampus,lapas,komunitas,instansi,masyarakat'],
        ];
    }
}
