<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataFeedController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RektoratController;
use App\Http\Controllers\UnitKerjaController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::redirect('/', 'login');

Route::get('/landing', function () {
    return view('pages.landing.index');
})->name('landing');

// role admin
Route::middleware(['auth:sanctum', 'verified', 'role:admin'])->group(function () {
    Route::get('/dashboard/admin', [DashboardController::class, 'index'])->name('dashboard.admin');

    Route::get('/pengguna', [AdminController::class, 'pengguna'])->name('dashboard.pengguna');
    Route::get('/departemen', [AdminController::class, 'departemen'])->name('dashboard.departemen');
    Route::get('/jenis-surat', [AdminController::class, 'jenisSurat'])->name('dashboard.jenis-surat');
    Route::get('/monitoring', [AdminController::class, 'monitoring'])->name('dashboard.monitoring');
});

// role rektorat
Route::middleware(['auth:sanctum', 'verified', 'role:rektorat'])->group(function () {
    Route::get('/dashboard/rektor', [DashboardController::class, 'index'])->name('dashboard.rektorat');
    Route::get('/surat-masuk', [RektoratController::class, 'suratMasuk'])->name('surat.masuk');
    Route::get('/surat-tugas', [RektoratController::class, 'suratTugas'])->name('surat.tugas');
    Route::get('/inbox-surat-tugas', [RektoratController::class, 'inboxSuratTugas'])->name('inbox.surat.tugas');
    Route::get('/history-disposisi', [RektoratController::class, 'historyDisposisi'])->name('history.disposisi');
    Route::get('/tindak-lanjut-surat-tugas', [RektoratController::class, 'tindakLanjutSuratTugas'])->name('tindaklanjut.surat.tugas');
    Route::get('/arsip-surat-tugas', [RektoratController::class, 'arsipSuratTugas'])->name('arsip.surat.tugas');
});

// role unit_kerja
Route::middleware(['auth:sanctum', 'verified', 'role:unit_kerja'])->group(function () {
    Route::get('/dashboard/unit-kerja', [DashboardController::class, 'index'])->name('dashboard.unit_kerja');
    Route::get('/arsip-surat-tugas', [UnitKerjaController::class, 'arsipSuratTugas'])->name('unit_kerja.arsip.surat.tugas');
    Route::get('/buat-surat', [UnitKerjaController::class, 'buatSurat'])->name('unit_kerja.buat.surat');
    Route::get('/surat-masuk', [UnitKerjaController::class, 'suratMasuk'])->name('unit_kerja.surat.masuk');
});
