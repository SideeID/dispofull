<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RektoratController;
use App\Http\Controllers\RektorSuratTugasController;
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
    
    // Agenda Surat
    Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda.index');
    Route::prefix('agenda')
        ->name('agenda.')
        ->group(function () {
            Route::get('/list', [AgendaController::class, 'getAgendas'])->name('list');
            Route::get('/filter-options', [AgendaController::class, 'getFilterOptions'])->name('filter-options');
            Route::post('/', [AgendaController::class, 'store'])->name('store');
            Route::get('/{id}', [AgendaController::class, 'show'])->name('show');
            Route::put('/{id}', [AgendaController::class, 'update'])->name('update');
            Route::delete('/{id}', [AgendaController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/publish', [AgendaController::class, 'publish'])->name('publish');
            Route::post('/{id}/archive', [AgendaController::class, 'archive'])->name('archive');
            Route::get('/{id}/export-pdf', [AgendaController::class, 'exportPdf'])->name('export-pdf');
            Route::get('/{id}/preview-pdf', [AgendaController::class, 'previewPdf'])->name('preview-pdf');
            Route::post('/auto-generate', [AgendaController::class, 'autoGenerate'])->name('auto-generate');
        });
    
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
    Route::get('rektor/surat-tugas', [RektorSuratTugasController::class, 'index'])->name('surat.tugas');
    Route::get('rektor/inbox-surat-tugas', [RektoratController::class, 'inboxSuratTugas'])->name('inbox.surat.tugas');
    Route::get('rektor/history-disposisi', [RektoratController::class, 'historyDisposisi'])->name('history.disposisi');
    Route::get('rektor/tindak-lanjut-surat-tugas', [RektoratController::class, 'tindakLanjutSuratTugas'])->name('tindaklanjut.surat.tugas');
    Route::get('rektor/arsip-surat-tugas', [RektoratController::class, 'arsipSuratTugas'])->name('arsip.surat.tugas');

    Route::prefix('rektor/api')
        ->name('rektor.api.')
        ->group(function () {
            // Tindak Lanjut Surat Tugas
            Route::get('/tindak-lanjut', [RektoratController::class, 'tindakLanjutIndex'])->name('tindak_lanjut.index');
            Route::get('/tindak-lanjut/{id}', [RektoratController::class, 'tindakLanjutShow'])->name('tindak_lanjut.show');
            Route::get('/tindak-lanjut/{letterId}/responses/{recipientId}/{recipientType}', [RektoratController::class, 'tindakLanjutResponses'])->name('tindak_lanjut.responses');

            // Surat Tugas (ST)
            Route::get('/assignments', [RektorSuratTugasController::class, 'apiIndex'])->name('assignments.index');
            Route::post('/assignments', [RektorSuratTugasController::class, 'store'])->name('assignments.store');
            Route::get('/assignments/{letter}', [RektorSuratTugasController::class, 'show'])->name('assignments.show');
            Route::get('/assignments/{letter}/participants', [RektorSuratTugasController::class, 'participantsIndex'])->name('assignments.participants.index');
            Route::post('/assignments/{letter}/participants', [RektorSuratTugasController::class, 'participantsStore'])->name('assignments.participants.store');
            Route::delete('/assignments/{letter}/participants/{index}', [RektorSuratTugasController::class, 'participantsDestroy'])->name('assignments.participants.destroy');
            Route::get('/assignments/{letter}/history', [RektorSuratTugasController::class, 'history'])->name('assignments.history');
            Route::post('/assignments/{letter}/confirm', [RektorSuratTugasController::class, 'confirm'])->name('assignments.confirm');
            Route::patch('/assignments/{letter}/status', [RektorSuratTugasController::class, 'updateStatus'])->name('assignments.status.update');
            Route::post('/assignments/{letter}/followups', [RektorSuratTugasController::class, 'storeFollowup'])->name('assignments.followups.store');
            Route::get('/assignments/{letter}/followups', [RektorSuratTugasController::class, 'getFollowups'])->name('assignments.followups.index');
            Route::get('/incoming-letters', [RektoratController::class, 'incomingIndex'])->name('incoming.index');
            // Routes tanpa parameter harus di atas routes dengan {letter}
            Route::get('/incoming-letters/recipients/dispositions', [RektoratController::class, 'incomingDispositionRecipients'])->name('incoming.recipients');
            Route::get('/departments', [RektoratController::class, 'getDepartments'])->name('departments.index');
            // Routes dengan {letter} parameter
            Route::get('/incoming-letters/{letter}', [RektoratController::class, 'incomingShow'])->name('incoming.show');
            Route::post('/incoming-letters/{letter}/archive', [RektoratController::class, 'incomingArchive'])->name('incoming.archive');
            Route::post('/incoming-letters/{letter}/unarchive', [RektoratController::class, 'incomingUnarchive'])->name('incoming.unarchive');
            Route::post('/incoming-letters/{letter}/mark-received', [RektoratController::class, 'incomingMarkReceived'])->name('incoming.mark_received');
            Route::get('/incoming-letters/{letter}/dispositions', [RektoratController::class, 'incomingDispositionsIndex'])->name('incoming.dispositions.index');
            Route::post('/incoming-letters/{letter}/dispositions', [RektoratController::class, 'incomingDispositionsStore'])->name('incoming.dispositions.store');
            Route::get('/incoming-letters/{letter}/attachments', [RektoratController::class, 'incomingAttachmentsIndex'])->name('incoming.attachments.index');
            Route::post('/incoming-letters/{letter}/attachments', [RektoratController::class, 'incomingAttachmentsStore'])->name('incoming.attachments.store');
            Route::get('/incoming-letters/{letter}/history', [RektoratController::class, 'incomingHistory'])->name('incoming.history');
            Route::get('/incoming-letters/{letter}/signatures', [RektoratController::class, 'incomingSignaturesIndex'])->name('incoming.signatures.index');
            Route::post('/incoming-letters/{letter}/signatures', [RektoratController::class, 'incomingSignaturesStore'])->name('incoming.signatures.store');
            // History Disposisi (role rektorat)
            Route::get('/history/dispositions', [RektoratController::class, 'historyDispositionsIndex'])->name('history.dispositions.index');
            Route::get('/history/dispositions/{disposition}', [RektoratController::class, 'historyDispositionsShow'])->name('history.dispositions.show');
            Route::get('/history/dispositions/{disposition}/attachments', [RektoratController::class, 'historyDispositionsAttachments'])->name('history.dispositions.attachments');
            Route::get('/history/dispositions/{disposition}/route', [RektoratController::class, 'historyDispositionsRoute'])->name('history.dispositions.route');
            Route::get('/history/dispositions/{disposition}/notes', [RektoratController::class, 'historyDispositionsNotes'])->name('history.dispositions.notes');
            Route::get('/history/dispositions/{disposition}/history', [RektoratController::class, 'historyDispositionsTimeline'])->name('history.dispositions.history');
            // Arsip Surat Tugas (role rektorat)
            Route::get('/archives/assignments', [RektorSuratTugasController::class, 'archivesIndex'])->name('archives.assignments.index');
            Route::get('/archives/assignments/{letter}', [RektorSuratTugasController::class, 'archivesShow'])->name('archives.assignments.show');
            Route::get('/archives/assignments/{letter}/participants', [RektorSuratTugasController::class, 'archivesParticipantsIndex'])->name('archives.assignments.participants');
            Route::get('/archives/assignments/{letter}/attachments', [RektorSuratTugasController::class, 'archivesAttachmentsIndex'])->name('archives.assignments.attachments');
            Route::get('/archives/assignments/{letter}/history', [RektorSuratTugasController::class, 'archivesHistory'])->name('archives.assignments.history');
            Route::post('/archives/assignments/{letter}/restore', [RektorSuratTugasController::class, 'archivesRestore'])->name('archives.assignments.restore');
            // Departments - Shared endpoint
            Route::get('/departments', [RektoratController::class, 'departmentsIndex'])->name('departments');
            // Users - Shared endpoint
            Route::get('/users', [RektoratController::class, 'usersIndex'])->name('users');
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
    Route::get('unit-kerja/inbox-disposisi', [UnitKerjaController::class, 'inboxDisposisi'])->name('unit_kerja.inbox.disposisi');

    Route::prefix('unit-kerja/api')
        ->name('unit_kerja.api.')
        ->group(function () {
            // Dashboard APIs
            Route::get('/dashboard/stats', [UnitKerjaController::class, 'dashboardStats'])->name('dashboard.stats');
            Route::get('/dashboard/recent-incoming', [UnitKerjaController::class, 'dashboardRecentIncoming'])->name('dashboard.recent_incoming');
            Route::get('/dashboard/draft-outgoing', [UnitKerjaController::class, 'dashboardDraftOutgoing'])->name('dashboard.draft_outgoing');
            Route::get('/dashboard/archived-assignments', [UnitKerjaController::class, 'dashboardArchivedAssignments'])->name('dashboard.archived_assignments');
            Route::get('/dashboard/signature-queue', [UnitKerjaController::class, 'dashboardSignatureQueue'])->name('dashboard.signature_queue');
            Route::get('/dashboard/chart-monthly', [UnitKerjaController::class, 'dashboardChartMonthly'])->name('dashboard.chart_monthly');
            Route::get('/dashboard/chart-disposition-status', [UnitKerjaController::class, 'dashboardChartDispositionStatus'])->name('dashboard.chart_disposition_status');
            Route::get('/dashboard/pending-notifications', [UnitKerjaController::class, 'dashboardPendingNotifications'])->name('dashboard.pending_notifications');

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
            Route::post('/letters/{letter}/restore', [UnitKerjaController::class, 'restoreLetter'])->name('letters.restore');
            Route::get('/letters/outgoing/search', [UnitKerjaController::class, 'outgoingSearch'])->name('letters.outgoing.search');
            
            // Disposisi Routes untuk Unit Kerja (penerima)
            Route::get('/dispositions/inbox', [UnitKerjaController::class, 'dispositionsInbox'])->name('dispositions.inbox');
            Route::get('/dispositions/{disposition}', [UnitKerjaController::class, 'dispositionShow'])->name('dispositions.show');
            Route::post('/dispositions/{disposition}/read', [UnitKerjaController::class, 'dispositionMarkRead'])->name('dispositions.read');
            Route::post('/dispositions/{disposition}/response', [UnitKerjaController::class, 'dispositionUpdateResponse'])->name('dispositions.response');
            Route::post('/dispositions/{disposition}/complete', [UnitKerjaController::class, 'dispositionComplete'])->name('dispositions.complete');
            // Departments - Shared endpoint
            Route::get('/departments', [UnitKerjaController::class, 'departmentsIndex'])->name('departments');
            // Users - Shared endpoint
            Route::get('/users', [UnitKerjaController::class, 'usersIndex'])->name('users');
        });
});

// Template API Routes
Route::middleware(['auth'])->prefix('unit-kerja')->group(function() {
    Route::get('/api/templates', [UnitKerjaController::class, 'getTemplates']);
    Route::get('/api/templates/{id}', [UnitKerjaController::class, 'getTemplateContent']);
    Route::post('/api/templates/custom', [UnitKerjaController::class, 'saveCustomTemplate']);
});
