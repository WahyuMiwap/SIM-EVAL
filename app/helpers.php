<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

if (! function_exists('setting')) {
    /**
     * Ambil nilai Kustomisasi Tampilan (cache 1 jam).
     * Fallback ke default bila tabel/kunci belum ada.
     */
    function setting(string $key, $default = null)
    {
        $defaults = [
            'app.logo' => null,
            'app.nama' => 'SIM-EVAL',
            'app.subnama' => 'P2M BNN Kota Surabaya',
            'app.warna_primer' => '#4361EE',
            'app.bg_login' => null,
        ];
        try {
            if (! Schema::hasTable('settings')) {
                return $defaults[$key] ?? $default;
            }

            return Cache::remember("setting.{$key}", 3600, function () use ($key, $defaults, $default) {
                $row = Setting::find($key);
                if (! $row || $row->value === null || $row->value === '') {
                    return $defaults[$key] ?? $default;
                }

                return $row->value;
            });
        } catch (Throwable $e) {
            return $defaults[$key] ?? $default;
        }
    }
}
