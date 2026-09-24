<?php

namespace App\Services;

use App\Models\Location;

class LokasiService
{
    public function allLocations()
    {
        return Location::withCount('events')->orderBy('nama_lokasi')->get();
    }

    public function findLocation(int|string $id): ?Location
    {
        if (! is_numeric($id)) {
            return null;
        }

        return Location::withCount('events')->find((int) $id);
    }

    public function search(string $q, int $limit = 20)
    {
        return Location::withCount('events')
            ->where('nama_lokasi', 'like', "%{$q}%")
            ->orWhere('kecamatan', 'like', "%{$q}%")
            ->orderBy('nama_lokasi')->limit($limit)->get();
    }
}
