<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKegiatanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && in_array(auth()->user()->role, ['superadmin', 'operator'], true);
    }

    public function rules(): array
    {
        return [
            'nama_kegiatan' => ['required', 'string', 'max:255'],
            'lokasi_id' => ['required', 'integer', 'exists:locations,id'],
            'tanggal' => ['nullable', 'date'],
            'durasi_menit' => ['nullable', 'integer', 'min:5', 'max:180'],
            'catatan' => ['nullable', 'string', 'max:2000'],
            'pretest_package_id' => ['nullable', 'integer', 'exists:question_packages,id'],
            'posttest_package_id' => ['nullable', 'integer', 'exists:question_packages,id'],
            'status' => ['nullable', 'in:dijadwalkan,berlangsung,selesai'],
        ];
    }

    public function messages(): array
    {
        return [
            'lokasi_id.required' => 'Pilih lokasi binaan dari daftar atau tambah baru lewat tombol + Tambah.',
            'lokasi_id.exists' => 'Lokasi tidak dikenal. Pilih dari daftar yang muncul saat mengetik.',
        ];
    }
}
