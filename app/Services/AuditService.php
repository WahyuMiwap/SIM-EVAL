<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class AuditService
{
    /**
     * Catat satu baris jejak audit. Tidak pernah melempar exception
     * (audit tak boleh menggagalkan aksi utama) dan diam bila tabel
     * belum tersedia (mis. pra-migrasi).
     */
    public static function record(string $aksi, ?string $targetType = null, $targetId = null, ?string $detail = null): void
    {
        try {
            if (! Schema::hasTable('activity_logs')) {
                return;
            }

            $user = Auth::user();
            ActivityLog::create([
                'user_id' => $user?->id,
                'nama_aktor' => $user?->name ?? 'Sistem',
                'role_aktor' => $user?->role ?? null,
                'aksi' => $aksi,
                'target_type' => $targetType,
                'target_id' => is_numeric($targetId) ? (int) $targetId : null,
                'detail' => $detail ? mb_substr($detail, 0, 1000) : null,
                'ip' => request()->ip(),
            ]);
        } catch (\Throwable $e) {
            // abaikan — audit tidak boleh menggagalkan aksi
        }
    }
}
