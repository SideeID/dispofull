<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataFeedController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\NasabahController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\KehadiranController;

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

    Route::get('/json-data-feed', [DataFeedController::class, 'getDataFeed'])->name('json_data_feed');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/data-nasabah', [AdminController::class, 'dataNasabah'])->name('data-nasabah');
    Route::get('/dashboard/data-petugas', [AdminController::class, 'dataPetugas'])->name('data-petugas');
    Route::get('/dashboard/data-sampah', [AdminController::class, 'dataSampah'])->name('data-sampah');
    Route::get('/dashboard/setoran', [AdminController::class, 'setoran'])->name('setoran');
    Route::get('/dashboard/tarik-saldo', [AdminController::class, 'tarikSaldo'])->name('tarik-saldo');
    Route::get('/dashboard/iuran', [AdminController::class, 'iuran'])->name('iuran');
    Route::get('/manajemen-jadwal', [JadwalController::class, 'index'])->name('manajemen-jadwal');
    Route::get('/upload-materi', [MateriController::class, 'index'])->name('upload-materi');
    Route::get('/forum-diskusi', [ForumController::class, 'index'])->name('forum-diskusi');
});
