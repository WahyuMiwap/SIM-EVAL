<?php

namespace App\Http\Controllers;

use App\Services\MockDataService;
use Illuminate\Http\Request;

class LokasiController extends Controller
{
    /**
     * Daftar Lokasi Binaan
     */
    public function index(Request $request)
    {
        $search = strtolower(trim($request->get('search', '')));
        $jenis  = $request->get('jenis_sasaran', '');

        $allLocations = MockDataService::getLocations();

        $filtered = collect($allLocations)->filter(function ($loc) use ($search, $jenis) {
            if ($search) {
                $matchNama = str_contains(strtolower($loc->nama_lokasi), $search);
                $matchKec  = str_contains(strtolower($loc->kecamatan), $search);
                $matchAlmt = str_contains(strtolower($loc->alamat), $search);
                if (!$matchNama && !$matchKec && !$matchAlmt) return false;
            }
            if ($jenis && $loc->jenis_sasaran !== $jenis) {
                return false;
            }
            return true;
        })->values()->all();

        $lokasi = MockDataService::paginate($filtered, 10);

        return view('operator.lokasi.index', compact('lokasi', 'search', 'jenis'));
    }

    /**
     * Tambah Lokasi Baru
     */
    public function store(Request $request)
    {
        return redirect()->route('operator.lokasi.index')
            ->with('success', 'Lokasi sasaran baru berhasil ditambahkan (Mock Mode).');
    }

    /**
     * Edit Lokasi (AJAX JSON)
     */
    public function edit($id)
    {
        $locations = MockDataService::getLocations();
        $target = $locations[0];
        foreach ($locations as $l) {
            if ($l->id == $id) { $target = $l; break; }
        }

        return response()->json($target);
    }

    /**
     * Update Lokasi
     */
    public function update(Request $request, $id)
    {
        return redirect()->route('operator.lokasi.index')
            ->with('success', 'Data lokasi berhasil diperbarui.');
    }

    /**
     * Hapus Lokasi
     */
    public function destroy($id)
    {
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Lokasi sasaran berhasil dihapus.',
            ]);
        }
        return redirect()->route('operator.lokasi.index')
            ->with('success', 'Lokasi berhasil dihapus.');
    }
}
