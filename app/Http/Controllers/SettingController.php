<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public const WARNA_PRESET = ['#4361EE', '#0EA5E9', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#0F766E'];

    /**
     * Halaman Kustomisasi Tampilan (superadmin).
     */
    public function edit()
    {
        $current = [
            'nama'     => setting('app.nama'),
            'subnama'  => setting('app.subnama'),
            'warna'    => setting('app.warna_primer'),
            'logo'     => setting('app.logo'),
            'bg_login' => setting('app.bg_login'),
        ];
        return view('operator.pengaturan.tampilan', [
            'current' => $current,
            'preset'  => self::WARNA_PRESET,
        ]);
    }

    /**
     * Simpan Kustomisasi Tampilan.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'nama'           => 'required|string|max:50',
            'subnama'        => 'nullable|string|max:100',
            'warna_primer'   => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'logo'           => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            'hapus_logo'     => 'nullable|boolean',
            'bg_login'       => 'nullable|image|mimes:png,jpg,jpeg,webp|max:5120',
            'hapus_bg_login' => 'nullable|boolean',
        ], [
            'nama.required' => 'Nama aplikasi wajib diisi.',
            'warna_primer.regex' => 'Warna primer tidak valid.',
            'logo.image' => 'Logo harus berupa file gambar.',
            'logo.max' => 'Ukuran logo maksimal 2 MB.',
            'bg_login.image' => 'Background login harus berupa file gambar.',
            'bg_login.max' => 'Ukuran background login maksimal 5 MB.',
        ]);

        $this->save('app.nama', trim($validated['nama']));
        $this->save('app.subnama', trim($validated['subnama'] ?? ''));
        $this->save('app.warna_primer', strtoupper($validated['warna_primer']));

        if ($request->boolean('hapus_logo')) {
            $this->deleteLogo();
        } elseif ($request->hasFile('logo')) {
            $this->deleteLogo();
            $path = $request->file('logo')->store('logo', 'public');
            $this->save('app.logo', '/storage/' . $path);
        }

        if ($request->boolean('hapus_bg_login')) {
            $this->deleteBgLogin();
        } elseif ($request->hasFile('bg_login')) {
            $this->deleteBgLogin();
            $path = $request->file('bg_login')->store('bg_login', 'public');
            $this->save('app.bg_login', '/storage/' . $path);
        }

        AuditService::record('ubah_tampilan', 'setting', null, 'Kustomisasi tampilan diperbarui.');

        return redirect()->route('operator.setting.edit')
            ->with('success', 'Kustomisasi tampilan berhasil disimpan dan langsung berlaku.');
    }

    protected function save(string $key, ?string $value): void
    {
        Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("setting.{$key}");
    }

    protected function deleteLogo(): void
    {
        $old = setting('app.logo');
        if ($old && str_starts_with($old, '/storage/logo/')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $old));
        }
        $this->save('app.logo', null);
    }

    protected function deleteBgLogin(): void
    {
        $old = setting('app.bg_login');
        if ($old && str_starts_with($old, '/storage/bg_login/')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $old));
        }
        $this->save('app.bg_login', null);
    }
}
