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
    Route::get('/admin/departments', [AdminController::class, 'departmentsIndex'])->name('admin.departments.index');
    Route::prefix('admin/users')
        ->name('admin.users.')
        ->group(function () {
            Route::get('/', [AdminController::class, 'usersIndex'])->name('index');
            Route::get('/{user}', [AdminController::class, 'usersShow'])->name('show');
            Route::post('/', [AdminController::class, 'usersStore'])->name('store');
            Route::put('/{user}', [AdminController::class, 'usersUpdate'])->name('update');
            Route::delete('/{user}', [AdminController::class, 'usersDestroy'])->name('destroy');
        });
    Route::prefix('admin/departments')
        ->name('admin.departments.')
        ->group(function () {
            Route::get('/manage', [AdminController::class, 'departmentsIndex'])->name('manage'); // same endpoint with manage=1
            Route::get('/manage/{department}', [AdminController::class, 'departmentsShow'])->name('show');
            Route::post('/manage', [AdminController::class, 'departmentsStore'])->name('store');
            Route::put('/manage/{department}', [AdminController::class, 'departmentsUpdate'])->name('update');
            Route::delete('/manage/{department}', [AdminController::class, 'departmentsDestroy'])->name('destroy');
        });
    Route::prefix('admin/letter-types')
        ->name('admin.letter_types.')
        ->group(function () {
            Route::get('/', [AdminController::class, 'letterTypesIndex'])->name('index');
            Route::get('/{letterType}', [AdminController::class, 'letterTypesShow'])->name('show');
            Route::post('/', [AdminController::class, 'letterTypesStore'])->name('store');
            Route::put('/{letterType}', [AdminController::class, 'letterTypesUpdate'])->name('update');
            Route::delete('/{letterType}', [AdminController::class, 'letterTypesDestroy'])->name('destroy');
        });
});

// role rektorat
Route::middleware(['auth:sanctum', 'verified', 'role:rektorat'])->group(function () {
    Route::get('/dashboard/rektor', [DashboardController::class, 'index'])->name('dashboard.rektorat');
    Route::get('rektor/surat-masuk', [RektoratController::class, 'suratMasuk'])->name('surat.masuk');
    Route::get('rektor/surat-tugas', [RektoratController::class, 'suratTugas'])->name('surat.tugas');
    Route::get('rektor/inbox-surat-tugas', [RektoratController::class, 'inboxSuratTugas'])->name('inbox.surat.tugas');
    Route::get('rektor/history-disposisi', [RektoratController::class, 'historyDisposisi'])->name('history.disposisi');
    Route::get('rektor/tindak-lanjut-surat-tugas', [RektoratController::class, 'tindakLanjutSuratTugas'])->name('tindaklanjut.surat.tugas');
    Route::get('rektor/arsip-surat-tugas', [RektoratController::class, 'arsipSuratTugas'])->name('arsip.surat.tugas');

    Route::prefix('rektor/api')
        ->name('rektor.api.')
        ->group(function () {
            Route::get('/incoming-letters', [RektoratController::class, 'incomingIndex'])->name('incoming.index');
            Route::get('/incoming-letters/{letter}', [RektoratController::class, 'incomingShow'])->name('incoming.show');
            Route::post('/incoming-letters/{letter}/mark-received', [RektoratController::class, 'incomingMarkReceived'])->name('incoming.mark_received');
            Route::get('/incoming-letters/{letter}/dispositions', [RektoratController::class, 'incomingDispositionsIndex'])->name('incoming.dispositions.index');
            Route::post('/incoming-letters/{letter}/dispositions', [RektoratController::class, 'incomingDispositionsStore'])->name('incoming.dispositions.store');
            Route::get('/incoming-letters/{letter}/attachments', [RektoratController::class, 'incomingAttachmentsIndex'])->name('incoming.attachments.index');
            Route::post('/incoming-letters/{letter}/attachments', [RektoratController::class, 'incomingAttachmentsStore'])->name('incoming.attachments.store');
            Route::get('/incoming-letters/{letter}/history', [RektoratController::class, 'incomingHistory'])->name('incoming.history');
            Route::get('/incoming-letters/{letter}/signatures', [RektoratController::class, 'incomingSignaturesIndex'])->name('incoming.signatures.index');
            Route::post('/incoming-letters/{letter}/signatures', [RektoratController::class, 'incomingSignaturesStore'])->name('incoming.signatures.store');
            Route::get('/incoming-letters/recipients/dispositions', [RektoratController::class, 'incomingDispositionRecipients'])->name('incoming.recipients');
        });
});

// role unit_kerja
Route::middleware(['auth:sanctum', 'verified', 'role:unit_kerja'])->group(function () {
    Route::get('/dashboard/unit-kerja', [DashboardController::class, 'index'])->name('dashboard.unit_kerja');
    Route::get('unit-kerja/arsip-surat-tugas', [UnitKerjaController::class, 'arsipSuratTugas'])->name('unit_kerja.arsip.surat.tugas');
    Route::get('unit-kerja/arsip-surat-tugas/export', [UnitKerjaController::class, 'exportArchives'])->name('unit_kerja.archives.export');
    Route::get('unit-kerja/buat-surat', [UnitKerjaController::class, 'buatSurat'])->name('unit_kerja.buat.surat');
    Route::get('unit-kerja/surat-masuk', [UnitKerjaController::class, 'suratMasuk'])->name('unit_kerja.surat.masuk');
    Route::get('unit-kerja/surat-masuk/export', [UnitKerjaController::class, 'exportIncoming'])->name('unit_kerja.surat_masuk.export');

    Route::prefix('unit-kerja/api')
        ->name('unit_kerja.api.')
        ->group(function () {
            Route::get('/letter-types', [UnitKerjaController::class, 'letterTypes'])->name('letter_types');
            Route::get('/signers', [UnitKerjaController::class, 'signers'])->name('signers');
            Route::get('/penandatangan', [UnitKerjaController::class, 'penandatangan'])->name('penandatangan');
            Route::post('/attachments/temp', [UnitKerjaController::class, 'uploadTempAttachment'])->name('attachments.temp.upload');
            Route::post('/letters/draft', [UnitKerjaController::class, 'storeDraft'])->name('letters.draft.store');
            Route::put('/letters/draft/{letter}', [UnitKerjaController::class, 'updateDraft'])->name('letters.draft.update');
            Route::post('/letters/{letter}/attachments', [UnitKerjaController::class, 'uploadAttachment'])->name('letters.attachments.upload');
            Route::delete('/letters/{letter}/attachments/{attachment}', [UnitKerjaController::class, 'deleteAttachment'])->name('letters.attachments.delete');
            Route::get('/letters/number/preview', [UnitKerjaController::class, 'previewNextNumber'])->name('letters.number.preview');
            Route::post('/letters/{letter}/submit', [UnitKerjaController::class, 'submitForSignature'])->name('letters.submit');
            Route::post('/letters/submit', [UnitKerjaController::class, 'submitDirect'])->name('letters.submit.direct');
            Route::post('/letters/{letter}/archive', [UnitKerjaController::class, 'archiveLetter'])->name('letters.archive');
            Route::get('/letters/outgoing/search', [UnitKerjaController::class, 'outgoingSearch'])->name('letters.outgoing.search');
        });
});
