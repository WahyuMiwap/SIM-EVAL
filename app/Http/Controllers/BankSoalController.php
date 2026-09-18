<?php

namespace App\Http\Controllers;

use App\Services\MockDataService;
use Illuminate\Http\Request;

class BankSoalController extends Controller
{
    /**
     * Daftar Paket Bank Soal
     */
    public function index(Request $request)
    {
        $search = strtolower(trim($request->get('search', '')));
        $tipe   = $request->get('tipe', '');

        $allPackages = MockDataService::getQuestionPackages();

        $filtered = collect($allPackages)->filter(function ($pkg) use ($search, $tipe) {
            if ($search && !str_contains(strtolower($pkg->nama_paket), $search)) {
                return false;
            }
            if ($tipe && $pkg->tipe !== $tipe) {
                return false;
            }
            return true;
        })->values()->all();

        $bankSoal = MockDataService::paginate($filtered, 10);

        return view('operator.bank-soal.index', compact('bankSoal', 'search', 'tipe'));
    }

    /**
     * Form Buat Paket Baru
     */
    public function create()
    {
        return view('operator.bank-soal.create');
    }

    /**
     * Simpan Paket Soal Baru
     */
    public function store(Request $request)
    {
        return redirect()->route('operator.bank-soal.detail', 1)
            ->with('success', 'Paket soal baru berhasil dibuat (Mock Mode). Silakan tambahkan butir soal.');
    }

    /**
     * Detail Paket & Daftar Butir Soal
     */
    public function detail($id)
    {
        $packages = MockDataService::getQuestionPackages();
        $paket = $packages[0];
        foreach ($packages as $p) {
            if ($p->id == $id) { $paket = $p; break; }
        }

        $soalList = $paket->questions;

        return view('operator.bank-soal.detail', compact('paket', 'soalList'));
    }

    /**
     * Edit Paket Soal
     */
    public function edit(Request $request, $id)
    {
        $packages = MockDataService::getQuestionPackages();
        $paket = $packages[0];
        foreach ($packages as $p) {
            if ($p->id == $id) { $paket = $p; break; }
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'id'          => $paket->id,
                'nama_paket'  => $paket->nama_paket,
                'tipe'        => $paket->tipe,
                'durasi'      => $paket->durasi,
                'acak_urutan' => $paket->acak_urutan,
            ]);
        }

        $soalList = $paket->questions;
        return view('operator.bank-soal.edit', compact('paket', 'soalList'));
    }

    /**
     * Update Paket Soal
     */
    public function update(Request $request, $id)
    {
        return redirect()->route('operator.bank-soal.detail', $id)
            ->with('success', 'Paket soal berhasil diperbarui.');
    }

    /**
     * Hapus Paket Soal (JSON untuk ReauthModal)
     */
    public function destroy($id)
    {
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Paket soal berhasil dihapus.',
            ]);
        }
        return redirect()->route('operator.bank-soal.index')
            ->with('success', 'Paket soal berhasil dihapus.');
    }

    /**
     * Endpoint AJAX Butir Soal
     */
    public function soal($id)
    {
        $questions = MockDataService::getQuestionsForPackage((int)$id);
        return response()->json(['soal' => $questions]);
    }

    /**
     * Tambah Butir Soal Baru
     */
    public function storeQuestion(Request $request, $id)
    {
        return redirect()->route('operator.bank-soal.detail', $id)
            ->with('success', 'Butir soal berhasil ditambahkan.');
    }

    /**
     * Hapus Butir Soal
     */
    public function destroyQuestion($id, $soalId)
    {
        return redirect()->route('operator.bank-soal.detail', $id)
            ->with('success', 'Butir soal berhasil dihapus.');
    }
}
