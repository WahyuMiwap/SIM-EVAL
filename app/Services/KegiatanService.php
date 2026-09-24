<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class KegiatanService
{
    public function allEventsDetailed(): Collection
    {
        return Event::with(['lokasi', 'participants', 'pretestPackage.questions', 'posttestPackage.questions'])
            ->withCount('participants')
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->get();
    }

    public function findEventDetailed(int $id): ?Event
    {
        return Event::with(['lokasi', 'participants.answers', 'pretestPackage.questions', 'posttestPackage.questions', 'scans', 'reconciliationQueue'])
            ->withCount('participants')
            ->find($id);
    }

    public function participantCount(Event $event): int
    {
        try {
            return (int) $event->participants()->count();
        } catch (\Throwable $e) {
            Log::warning('Gagal hitung peserta', ['event_id' => $event->id ?? null]);

            return 0;
        }
    }

    public function editPolicy(Event $event): array
    {
        $status = strtolower($event->status ?? 'dijadwalkan');
        $count = $this->participantCount($event);
        $pristine = $status === 'dijadwalkan' && $count === 0;

        return [
            'pristine' => $pristine,
            'boleh_ubah_paket' => $pristine,
            'jadwal_terkunci' => ! $pristine,
            'peserta_count' => $count,
            'status' => $status,
            'alasan' => $pristine ? null
                : ($status !== 'dijadwalkan'
                    ? "Kegiatan berstatus '{$status}' — data lapangan tidak boleh ditulis ulang."
                    : "Sudah ada {$count} peserta — tanggal, lokasi, durasi, dan paket soal dikunci."),
        ];
    }
}
