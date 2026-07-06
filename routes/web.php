<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GolonganController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\KonversiPredikatKinerjaController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\ProjectionController;
use App\Http\Controllers\RiwayatPakController;
use App\Http\Controllers\KinerjaTahunanController;
use App\Http\Controllers\UnitKerjaController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.attempt');
});

// Protected routes - only authenticated admin users can access
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('unit-kerjas', UnitKerjaController::class)->except('show');
    Route::resource('golongans', GolonganController::class)->except('show');
    Route::resource('jabatans', JabatanController::class)->except('show');
    Route::resource('pegawais', PegawaiController::class)->except('show');
    Route::resource('riwayat-paks', RiwayatPakController::class)->except('show');
    Route::resource('kinerja-tahunans', KinerjaTahunanController::class)->except(['index', 'show']);

    // Konversi Predikat Kinerja management
    Route::resource('konversi-predikats', KonversiPredikatKinerjaController::class)->except('show');
    Route::post('konversi-predikats/generate', [KonversiPredikatKinerjaController::class, 'generate'])->name('konversi-predikats.generate');

    Route::get('/proyeksi-jabatan', [ProjectionController::class, 'index'])->name('projections.index');
    Route::get('/proyeksi-jabatan/{pegawai}', [ProjectionController::class, 'show'])->name('projections.show');

    // Usulan Kenaikan Pangkat routes
    Route::get('/usulan-pangkat', [App\Http\Controllers\UsulanKenaikanPangkatController::class, 'index'])->name('usulan-pangkat.index');
    Route::post('/usulan-pangkat/store', [App\Http\Controllers\UsulanKenaikanPangkatController::class, 'store'])->name('usulan-pangkat.store');
    Route::post('/usulan-pangkat/{usulan}/update', [App\Http\Controllers\UsulanKenaikanPangkatController::class, 'update'])->name('usulan-pangkat.update');
    Route::post('/usulan-pangkat/{usulan}/approve', [App\Http\Controllers\UsulanKenaikanPangkatController::class, 'approve'])->name('usulan-pangkat.approve');

    // API: Fetch konversi AK for a pegawai + predikat (used by Riwayat PAK form AJAX)
    Route::get('/api/konversi-ak/{pegawai}/{predikat}', [RiwayatPakController::class, 'getKonversiAk'])
        ->name('api.konversi-ak');

    // API: Generate Nomor PAK & SK automatically
    Route::get('/api/generate-no-pak', [RiwayatPakController::class, 'generateNoPak'])
        ->name('api.generate-no-pak');
        
    Route::get('/api/generate-no-sk', [App\Http\Controllers\UsulanKenaikanPangkatController::class, 'generateNoSk'])
        ->name('api.generate-no-sk');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
