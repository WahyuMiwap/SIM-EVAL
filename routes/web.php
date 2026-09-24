<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankSoalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StafController;
use Illuminate\Support\Facades\Route;

// ─────────────────────────────────────────────────────────────────────────────
//  ROOT — redirect ke halaman dashboard operator
// ─────────────────────────────────────────────────────────────────────────────
Route::get('/', fn () => redirect()->route('operator.dashboard'));

// ─────────────────────────────────────────────────────────────────────────────
//  AUTH — login staf (email + password), daftar akun internal
// ─────────────────────────────────────────────────────────────────────────────
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ─────────────────────────────────────────────────────────────────────────────
//  OPERATOR / ADMIN PANEL (wajib login; peran dibatasi per rute)
// ─────────────────────────────────────────────────────────────────────────────
Route::prefix('operator')->name('operator.')->middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/data', [DashboardController::class, 'data'])->name('dashboard.data');

    // Profil Saya
    Route::get('/profil', fn () => view('operator.profil.index'))->name('profile');

    // ── Kegiatan & Rekap Evaluasi ─────────────────────────
    Route::prefix('kegiatan')->name('kegiatan.')->group(function () {
        Route::get('/', [KegiatanController::class, 'index'])->name('index');
        Route::get('/create', [KegiatanController::class, 'create'])->name('create');
        Route::post('/', [KegiatanController::class, 'store'])->name('store');
        Route::get('/{id}', [KegiatanController::class, 'detail'])->name('detail');
        Route::get('/{id}/edit', [KegiatanController::class, 'edit'])->name('edit');
        Route::put('/{id}', [KegiatanController::class, 'update'])->name('update');
        Route::patch('/{id}/status', [KegiatanController::class, 'updateStatus'])->name('status')->middleware('role:superadmin,operator');
        Route::patch('/{id}/fase', [KegiatanController::class, 'updateFase'])->name('fase')->middleware('role:superadmin,operator');
        Route::delete('/{id}', [KegiatanController::class, 'destroy'])->name('destroy')->middleware('role:superadmin,operator');
        Route::get('/{id}/export', [KegiatanController::class, 'exportLaporan'])->name('export');

        // Meja Kerja Rekap & OMR
        Route::post('/{id}/peserta', [KegiatanController::class, 'saveParticipantRow'])->name('peserta.save');
        Route::get('/{id}/peserta-digital', [KegiatanController::class, 'pesertaDigital'])->name('peserta.digital');
        Route::delete('/{id}/peserta/{pesertaId}', [KegiatanController::class, 'deleteParticipantRow'])->name('peserta.destroy');
        Route::post('/{id}/omr-scan', [KegiatanController::class, 'omrScanRecord'])->name('omr');
        Route::post('/{id}/omr-submit', [KegiatanController::class, 'omrSubmit'])->name('omr.submit');
    });

    // ── Master Lokasi Binaan ──────────────────────────────
    Route::prefix('lokasi')->name('lokasi.')->group(function () {
        Route::get('/', [LokasiController::class, 'index'])->name('index');
        Route::get('/search', [LokasiController::class, 'search'])->name('search');
        Route::post('/', [LokasiController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [LokasiController::class, 'edit'])->name('edit');
        Route::put('/{id}', [LokasiController::class, 'update'])->name('update');
        Route::delete('/{id}', [LokasiController::class, 'destroy'])->name('destroy')->middleware('role:superadmin,operator');
    });

    // ── Master Bank Soal & Paket ─────────────────────────
    Route::prefix('bank-soal')->name('bank-soal.')->group(function () {
        Route::get('/', [BankSoalController::class, 'index'])->name('index');
        Route::get('/create', [BankSoalController::class, 'create'])->name('create');
        Route::post('/', [BankSoalController::class, 'store'])->name('store');
        Route::get('/{id}', [BankSoalController::class, 'detail'])->name('detail');
        Route::get('/{id}/edit', [BankSoalController::class, 'edit'])->name('edit');
        Route::put('/{id}', [BankSoalController::class, 'update'])->name('update');
        Route::delete('/{id}', [BankSoalController::class, 'destroy'])->name('destroy')->middleware('role:superadmin,operator');

        // Butir Soal AJAX & Form
        Route::get('/{id}/soal', [BankSoalController::class, 'soal'])->name('soal');
        Route::post('/{id}/soal', [BankSoalController::class, 'storeQuestion'])->name('soal.store');
        Route::delete('/{id}/soal/{soalId}', [BankSoalController::class, 'destroyQuestion'])->name('soal.destroy')->middleware('role:superadmin,operator');
    });

    // ── Tata Kelola Akun (Superadmin saja — disembunyikan dari staf/magang) ──
    Route::prefix('staf')->name('staf.')->middleware('role:superadmin')->group(function () {
        Route::get('/', [StafController::class, 'index'])->name('index');
        Route::post('/', [StafController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [StafController::class, 'edit'])->name('edit');
        Route::put('/{id}', [StafController::class, 'update'])->name('update');
        Route::post('/{id}/reset-password', [StafController::class, 'resetPassword'])->name('reset-password');
        Route::patch('/{id}/toggle-active', [StafController::class, 'toggleActive'])->name('toggle-active');
        Route::delete('/{id}', [StafController::class, 'destroy'])->name('destroy');
        Route::get('/aktivitas', [StafController::class, 'activity'])->name('activity');
    });

    // ── Kustomisasi Tampilan (Superadmin saja) ──
    Route::prefix('pengaturan')->name('setting.')->middleware('role:superadmin')->group(function () {
        Route::get('/tampilan', [SettingController::class, 'edit'])->name('edit');
        Route::put('/tampilan', [SettingController::class, 'update'])->name('update');
    });
});

// ─────────────────────────────────────────────────────────────────────────────
//  PARTICIPANT — PWA Exam Engine
// ─────────────────────────────────────────────────────────────────────────────
Route::prefix('')->name('participant.')->group(function () {
    Route::get('/join', [ParticipantController::class, 'welcome'])->name('welcome');
    Route::get('/join/info', [ParticipantController::class, 'joinInfo'])->name('join-info');
    Route::post('/join', [ParticipantController::class, 'join'])->name('join');
    Route::get('/waiting/status/{id?}', [ParticipantController::class, 'status'])->name('status');
    Route::get('/waiting/{room}/{id?}', [ParticipantController::class, 'waiting'])
        ->where('room', 'pretest|posttest')->name('waiting');
    Route::get('/quiz/{type}/{session?}', [ParticipantController::class, 'quiz'])->name('quiz');
    Route::post('/quiz/submit', [ParticipantController::class, 'submit'])->name('submit');
});
