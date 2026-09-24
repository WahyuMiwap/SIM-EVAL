<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Location;
use App\Models\Participant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DashboardService
{
    public function computeStats(?Carbon $now = null): array
    {
        $now ??= Carbon::now();
        try {
            $totalEvents = Event::count();
            $totalParticipants = Participant::count();
            $sekolahBinaan = Location::whereIn('jenis_sasaran', ['sekolah', 'kampus'])->count();
            $avgGain = round((float) (Participant::where('status_data', 'COMPLETE')->avg('n_gain') ?? 0), 2);

            $bulanIni = [
                'total_kegiatan' => Event::whereMonth('tanggal', $now->month)->whereYear('tanggal', $now->year)->count(),
                'total_peserta' => Participant::whereHas('event', fn ($q) => $q->whereMonth('tanggal', $now->month)->whereYear('tanggal', $now->year))->count(),
                'sekolah_binaan' => $sekolahBinaan,
                'avg_gain' => $avgGain,
            ];

            return ['bulan_ini' => $bulanIni, 'tahun_ini' => $bulanIni, 'total_events' => $totalEvents, 'total_participants' => $totalParticipants];
        } catch (\Throwable $e) {
            Log::error('Dashboard computeStats gagal', ['err' => $e->getMessage()]);
            $empty = ['total_kegiatan' => 0, 'total_peserta' => 0, 'sekolah_binaan' => 0, 'avg_gain' => 0];

            return ['bulan_ini' => $empty, 'tahun_ini' => $empty, 'total_events' => 0, 'total_participants' => 0];
        }
    }

    public function calendarEvents(): array
    {
        return Event::with('lokasi')->orderBy('tanggal')->limit(100)->get()
            ->map(fn ($e) => ['id' => $e->id, 'title' => $e->nama_kegiatan, 'date' => $e->tanggal?->format('Y-m-d'), 'lokasi' => $e->lokasi->nama_lokasi ?? null])
            ->all();
    }

    public function recentActivities(int $limit = 5): array
    {
        return Event::with('lokasi')->orderByDesc('tanggal')->limit($limit)->get()
            ->map(fn ($e) => ['id' => $e->id, 'nama' => $e->nama_kegiatan, 'tanggal' => $e->tanggal?->format('Y-m-d'), 'status' => $e->status])
            ->all();
    }
}
