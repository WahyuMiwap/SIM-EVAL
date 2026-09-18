<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\BankSoalController;
use App\Http\Controllers\StafController;
use App\Http\Controllers\ParticipantController;

// ─────────────────────────────────────────────────────────────────────────────
//  ROOT — redirect ke halaman dashboard operator
// ─────────────────────────────────────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('operator.dashboard'));

// ─────────────────────────────────────────────────────────────────────────────
//  OPERATOR / ADMIN PANEL
// ─────────────────────────────────────────────────────────────────────────────
Route::prefix('operator')->name('operator.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profil Saya
    Route::get('/profil', fn() => view('operator.profil.index'))->name('profile');

    // ── Kegiatan & Rekap Evaluasi ─────────────────────────
    Route::prefix('kegiatan')->name('kegiatan.')->group(function () {
        Route::get('/', [KegiatanController::class, 'index'])->name('index');
        Route::get('/create', [KegiatanController::class, 'create'])->name('create');
        Route::post('/', [KegiatanController::class, 'store'])->name('store');
        Route::get('/{id}', [KegiatanController::class, 'detail'])->name('detail');
        Route::get('/{id}/edit', [KegiatanController::class, 'edit'])->name('edit');
        Route::put('/{id}', [KegiatanController::class, 'update'])->name('update');
        Route::patch('/{id}/status', [KegiatanController::class, 'updateStatus'])->name('status');
        Route::delete('/{id}', [KegiatanController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/export', [KegiatanController::class, 'exportDiktari'])->name('export');

        // Meja Kerja Rekap & OMR
        Route::post('/{id}/peserta', [KegiatanController::class, 'saveParticipantRow'])->name('peserta.save');
        Route::delete('/{id}/peserta/{pesertaId}', [KegiatanController::class, 'deleteParticipantRow'])->name('peserta.destroy');
        Route::post('/{id}/omr-scan', [KegiatanController::class, 'omrScanRecord'])->name('omr');
    });

    // ── Master Lokasi Binaan ──────────────────────────────
    Route::prefix('lokasi')->name('lokasi.')->group(function () {
        Route::get('/', [LokasiController::class, 'index'])->name('index');
        Route::post('/', [LokasiController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [LokasiController::class, 'edit'])->name('edit');
        Route::put('/{id}', [LokasiController::class, 'update'])->name('update');
        Route::delete('/{id}', [LokasiController::class, 'destroy'])->name('destroy');
    });

    // ── Master Bank Soal & Paket ─────────────────────────
    Route::prefix('bank-soal')->name('bank-soal.')->group(function () {
        Route::get('/', [BankSoalController::class, 'index'])->name('index');
        Route::get('/create', [BankSoalController::class, 'create'])->name('create');
        Route::post('/', [BankSoalController::class, 'store'])->name('store');
        Route::get('/{id}', [BankSoalController::class, 'detail'])->name('detail');
        Route::get('/{id}/edit', [BankSoalController::class, 'edit'])->name('edit');
        Route::put('/{id}', [BankSoalController::class, 'update'])->name('update');
        Route::delete('/{id}', [BankSoalController::class, 'destroy'])->name('destroy');

        // Butir Soal AJAX & Form
        Route::get('/{id}/soal', [BankSoalController::class, 'soal'])->name('soal');
        Route::post('/{id}/soal', [BankSoalController::class, 'storeQuestion'])->name('soal.store');
        Route::delete('/{id}/soal/{soalId}', [BankSoalController::class, 'destroyQuestion'])->name('soal.destroy');
    });

    // ── Tata Kelola Akun Staf & Magang (Superadmin / Staf) ──
    Route::prefix('staf')->name('staf.')->group(function () {
        Route::get('/', [StafController::class, 'index'])->name('index');
        Route::post('/', [StafController::class, 'store'])->name('store');
        Route::post('/{id}/reset-password', [StafController::class, 'resetPassword'])->name('reset-password');
        Route::patch('/{id}/toggle-active', [StafController::class, 'toggleActive'])->name('toggle-active');
        Route::delete('/{id}', [StafController::class, 'destroy'])->name('destroy');
        Route::post('/switch-role/{role}', [StafController::class, 'switchRole'])->name('switch-role');
    });
});

// ─────────────────────────────────────────────────────────────────────────────
//  PARTICIPANT — PWA Exam Engine
// ─────────────────────────────────────────────────────────────────────────────
Route::prefix('')->name('participant.')->group(function () {
    Route::get('/join', [ParticipantController::class, 'welcome'])->name('welcome');
    Route::post('/join', [ParticipantController::class, 'join'])->name('join');
    Route::get('/waiting/{room}/{id}', [ParticipantController::class, 'waiting'])->name('waiting');
    Route::get('/waiting/status/{id}', [ParticipantController::class, 'status'])->name('status');
    Route::get('/quiz/{type}/{session}', [ParticipantController::class, 'quiz'])->name('quiz');
    Route::post('/quiz/submit', [ParticipantController::class, 'submit'])->name('submit');
});

// ─────────────────────────────────────────────────────────────────────────────
//  LOGOUT
// ─────────────────────────────────────────────────────────────────────────────
Route::post('/logout', function () {
    session()->forget('current_role');
    return redirect()->route('participant.welcome');
})->name('logout');
