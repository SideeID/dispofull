<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Letter;
use App\Models\LetterType;
use App\Models\LetterNumberSequence;
use App\Models\LetterAttachment;
use App\Models\LetterSignature;
use App\Models\LetterDisposition;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class UnitKerjaController extends Controller
{
    public function restoreLetter(Request $request, Letter $letter)
    {
        $user = Auth::user();
        
        // Validasi: hanya surat arsip yang bisa di-restore
        if ($letter->status !== 'archived') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya surat yang sudah diarsip yang dapat dipulihkan.'
            ], 422);
        }
        
        // Validasi: hanya department yang sama yang bisa restore
        if ($letter->from_department_id !== ($user?->department_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak memiliki izin untuk memulihkan surat ini.'
            ], 403);
        }
        
        // Ambil status sebelumnya dari notes (jika ada)
        $notes = $letter->notes ?: [];
        $previousStatus = $notes['previous_status'] ?? 'signed'; // default ke signed jika tidak ada
        
        // Update status kembali
        $letter->update([
            'status' => $previousStatus,
            'archived_at' => null,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Surat berhasil dipulihkan dari arsip.',
            'data' => [
                'id' => $letter->id,
                'status' => $letter->status,
                'letter_number' => $letter->letter_number,
            ]
        ]);
    }

    public function archiveLetter(Request $request, Letter $letter)
    {
        $user = Auth::user();
        if ($letter->direction !== 'outgoing') {
            return response()->json(['success'=>false,'message'=>'Hanya surat keluar yang dapat diarsipkan.'], 422);
        }
        if ($letter->from_department_id !== ($user?->department_id)) {
            return response()->json(['success'=>false,'message'=>'Tidak memiliki izin mengarsipkan surat ini.'], 403);
        }
        if ($letter->status === 'draft') {
            return response()->json(['success'=>false,'message'=>'Draft tidak dapat diarsipkan. Ajukan/selesaikan terlebih dahulu.'], 409);
        }

        $data = Validator::make($request->all(), [
            'start_date' => ['nullable','date'],
            'end_date' => ['nullable','date','after_or_equal:start_date'],
            'archive_reason' => ['nullable','string','max:500'],
        ])->validate();

        $notes = $letter->notes ?: [];
        
        // Simpan status sebelumnya sebelum diarsipkan
        if (!isset($notes['previous_status'])) {
            $notes['previous_status'] = $letter->status;
        }
        
        foreach (['start_date','end_date','archive_reason'] as $k) {
            if (array_key_exists($k, $data)) {
                $notes[$k] = $data[$k];
            }
        }

        $letter->update([
            'status' => 'archived',
            'archived_at' => now(),
            'notes' => $notes
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Surat telah diarsipkan.',
            'data' => [
                'id' => $letter->id,
                'status' => $letter->status,
                'archived_at' => optional($letter->archived_at)->format('Y-m-d H:i'),
            ]
        ]);
    }

    public function outgoingSearch(Request $request)
    {
        $user = Auth::user();
        $deptId = $user?->department_id;
        $q = trim((string) $request->get('q',''));

        $query = Letter::query()
            ->where('direction','outgoing')
            ->where('status','!=','archived')
            ->where('status','!=','draft')
            ->when($deptId, fn($qq)=>$qq->where('from_department_id',$deptId))
            ->when($deptId, fn($qq)=>$qq->where('from_department_id',$deptId));

        if ($q !== '') {
            $query->where(function($w) use ($q){
                $w->where('letter_number','like',"%$q%")
                  ->orWhere('subject','like',"%$q%");
            });
        }

        $rows = $query->orderByDesc('letter_date')->orderByDesc('id')->limit(20)->get([
            'id','letter_number','subject','letter_date','status'
        ]);

        $data = $rows->map(function(Letter $l){
            return [
                'id' => $l->id,
                'number' => $l->letter_number,
                'subject' => $l->subject,
                'date' => optional($l->letter_date)->format('Y-m-d'),
                'status' => $l->status,
            ];
        });

        return response()->json(['success'=>true,'data'=>$data]);
    }
    public function arsipSuratTugas(Request $request)
    {
        $user = Auth::user();
        $deptId = $user?->department_id;

        $query = Letter::query()
            ->withCount('attachments')
            ->where('direction', 'outgoing')
            ->where('status', 'archived')
            ->when($deptId, fn($q) => $q->where('from_department_id', $deptId));

        if ($q = trim((string)$request->get('q', ''))) {
            $query->where(function($w) use ($q){
                $w->where('letter_number','like',"%$q%")
                  ->orWhere('subject','like',"%$q%")
                  ->orWhere('recipient_name','like',"%$q%");
            });
        }
        if ($date = $request->get('date')) {
            $query->whereDate('letter_date', $date);
        }
        if ($priority = $request->get('priority')) {
            $query->where('priority', $priority);
        }
        if ($startFrom = $request->get('start_from')) {
            $query->whereDate('archived_at', '>=', $startFrom);
        }
        if ($endTo = $request->get('end_to')) {
            $query->whereDate('archived_at', '<=', $endTo);
        }

        $query->orderByDesc('archived_at')->orderByDesc('id');

    $paginator = $query->paginate(10);
    $mapped = collect($paginator->items())->map(function(Letter $l){
            $notes = $l->notes ?: [];
            $participants = $notes['participants'] ?? [];
            if (!is_array($participants)) { $participants = []; }
            $start = $notes['start_date'] ?? null;
            $end = $notes['end_date'] ?? null;
            $reason = $notes['archive_reason'] ?? null;

            $atts = $l->attachments()->limit(10)->get(['original_name','file_path']);
            $attachments = $atts->map(function($a){
                return [
                    'name' => $a->original_name,
                    'url' => Storage::url($a->file_path)
                ];
            })->all();

            return [
                'id' => $l->id,
                'number' => $l->letter_number,
                'subject' => $l->subject,
                'destination' => $l->recipient_name,
                'date' => optional($l->letter_date)->format('Y-m-d'),
                'start' => $start,
                'end' => $end,
                'priority' => $l->priority,
                'participants' => count($participants),
                'participants_list' => $participants,
                'files' => (int) $l->attachments_count,
                'archived_at' => optional($l->archived_at)->format('Y-m-d H:i'),
                'reason' => $reason,
                'attachments' => $attachments,
            ];
    });
    $archives = $mapped->all();

        $priorityColors = [
            'low' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
            'normal' => 'bg-slate-500/10 text-slate-600 dark:text-slate-300',
            'high' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400',
            'urgent' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400',
        ];

        return view('pages.unit_kerja.arsip-surat-tugas.index', [
            'archives' => $archives,
            'paginator' => $paginator,
            'priorityColors' => $priorityColors,
        ]);
    }

    public function exportArchives(Request $request)
    {
        $user = Auth::user();
        $deptId = $user?->department_id;
        $query = Letter::query()
            ->withCount('attachments')
            ->where('direction','outgoing')
            ->where('status','archived')
            ->when($deptId, fn($q) => $q->where('from_department_id', $deptId));

        if ($q = trim((string)$request->get('q',''))) {
            $query->where(function($w) use ($q){
                $w->where('letter_number','like',"%$q%")
                  ->orWhere('subject','like',"%$q%")
                  ->orWhere('recipient_name','like',"%$q%");
            });
        }
        if ($date = $request->get('date')) {
            $query->whereDate('letter_date', $date);
        }
        if ($priority = $request->get('priority')) {
            $query->where('priority', $priority);
        }
        if ($startFrom = $request->get('start_from')) {
            $query->whereDate('archived_at', '>=', $startFrom);
        }
        if ($endTo = $request->get('end_to')) {
            $query->whereDate('archived_at', '<=', $endTo);
        }

        $rows = $query->orderByDesc('archived_at')->orderByDesc('id')->limit(5000)->get();

        $filename = 'arsip-surat-tugas-'.now()->format('Ymd_His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        return response()->streamDownload(function() use ($rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, ['Nomor','Perihal','Tujuan','Tanggal Surat','Periode','Prioritas','Lampiran','Tanggal Arsip','Alasan Arsip']);
            foreach ($rows as $l) {
                $notes = $l->notes ?: [];
                $start = $notes['start_date'] ?? '';
                $end = $notes['end_date'] ?? '';
                $reason = $notes['archive_reason'] ?? '';
                $periode = ($start && $end) ? ($start.' s/d '.$end) : '';

                fputcsv($out, [
                    $l->letter_number,
                    $l->subject,
                    $l->recipient_name,
                    optional($l->letter_date)->format('Y-m-d'),
                    $periode,
                    $l->priority,
                    (int) $l->attachments_count,
                    optional($l->archived_at)->format('Y-m-d H:i'),
                    $reason,
                ]);
            }
            fclose($out);
        }, $filename, $headers);
    }

    public function buatSurat()
    {
        $letterTypes = LetterType::where('is_active', true)
            ->orderBy('name')
            ->get(['id','name','code','number_format']);
        $userDeptId = Auth::user()->department_id;
        return view('pages.unit_kerja.buat-surat.index', compact('letterTypes','userDeptId'));
    }

    public function suratMasuk(Request $request)
    {
        $user = Auth::user();
        $deptId = $user?->department_id;

        $query = Letter::query()
            ->with(['fromDepartment:id,name'])
            ->withCount(['attachments','dispositions'])
            ->where('direction','incoming')
            ->when($deptId, fn($q) => $q->where('to_department_id', $deptId));

        if ($q = trim((string)$request->get('q',''))) {
            $query->where(function($w) use ($q){
                $w->where('letter_number','like',"%$q%")
                  ->orWhere('subject','like',"%$q%")
                  ->orWhere('sender_name','like',"%$q%")
                  ->orWhereHas('fromDepartment', function($d) use ($q){
                      $d->where('name','like',"%$q%");
                  });
            });
        }
        if ($from = $request->get('date_from')) {
            $query->whereDate('letter_date', '>=', $from);
        }
        if ($to = $request->get('date_to')) {
            $query->whereDate('letter_date', '<=', $to);
        }
        if ($priority = $request->get('priority')) {
            $query->where('priority', $priority);
        }
        if ($status = $request->get('status')) {
            if ($status === 'processed') {
                $query->whereNotNull('processed_at');
            } elseif ($status === 'in_progress') {
                $query->whereNull('processed_at')->whereNotNull('received_at');
            } elseif ($status === 'pending') {
                $query->whereNull('received_at')->whereNull('processed_at');
            } elseif ($status === 'review') {
                $query->whereHas('dispositions', function($d){
                    $d->whereIn('status',[ 'pending','in_progress' ]);
                });
            }
        }
        if ($category = $request->get('category')) {
            if ($category === 'Internal') {
                $query->whereNotNull('from_department_id');
            } elseif ($category === 'Eksternal') {
                $query->whereNull('from_department_id');
            }
        }

        $query->orderByDesc('letter_date')->orderByDesc('id');

        $rows = $query->limit(50)->get();

        $incoming = $rows->map(function(Letter $l){
            $hasReview = $l->dispositions()->whereIn('status',[ 'pending','in_progress' ])->exists();
            if ($hasReview) {
                $status = 'review';
            } elseif ($l->processed_at) {
                $status = 'processed';
            } elseif ($l->received_at) {
                $status = 'in_progress';
            } else {
                $status = 'pending';
            }

            $atts = $l->attachments()->limit(10)->get(['original_name','file_path','file_size']);
            $attachments = $atts->map(function($a){
                return [
                    'name' => $a->original_name,
                    'url' => Storage::url($a->file_path),
                    'size_human' => $this->humanFileSize((int) $a->file_size),
                ];
            })->all();

            return [
                'id' => $l->id,
                'number' => $l->letter_number,
                'subject' => $l->subject,
                'from' => $l->sender_name ?: ($l->fromDepartment->name ?? '—'),
                'date' => optional($l->letter_date)->format('Y-m-d'),
                'category' => $l->from_department_id ? 'Internal' : 'Eksternal',
                'priority' => $l->priority,
                'status' => $status,
                'attachments' => (int) $l->attachments_count,
                'dispositions' => (int) $l->dispositions_count,
                'attachments_list' => $attachments,
            ];
        })->all();

        $priorityColors = [
            'low' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
            'normal' => 'bg-slate-500/10 text-slate-600 dark:text-slate-300',
            'high' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400',
            'urgent' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400',
        ];
        $statusColors = [
            'pending' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400',
            'in_progress' => 'bg-blue-500/10 text-blue-600 dark:text-blue-400',
            'processed' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
            'review' => 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400',
        ];

        return view('pages.unit_kerja.surat-masuk.index', [
            'incoming' => $incoming,
            'priorityColors' => $priorityColors,
            'statusColors' => $statusColors,
        ]);
    }

    public function inboxDisposisi(Request $request)
    {
        return view('pages.unit_kerja.inbox-disposisi.index');
    }

    public function exportIncoming(Request $request)
    {
        $user = Auth::user();
        $deptId = $user?->department_id;

        $query = Letter::query()
            ->withCount(['attachments','dispositions'])
            ->where('direction','incoming')
            ->when($deptId, fn($q) => $q->where('to_department_id',$deptId));

        if ($q = trim((string)$request->get('q',''))) {
            $query->where(function($w) use ($q){
                $w->where('letter_number','like',"%$q%")
                  ->orWhere('subject','like',"%$q%")
                  ->orWhere('sender_name','like',"%$q%");
            });
        }
        if ($from = $request->get('date_from')) { $query->whereDate('letter_date','>=',$from); }
        if ($to = $request->get('date_to')) { $query->whereDate('letter_date','<=',$to); }
        if ($priority = $request->get('priority')) { $query->where('priority',$priority); }
        if ($category = $request->get('category')) {
            if ($category === 'Internal') $query->whereNotNull('from_department_id');
            if ($category === 'Eksternal') $query->whereNull('from_department_id');
        }

        $rows = $query->orderByDesc('letter_date')->orderByDesc('id')->limit(5000)->get();

        $filename = 'surat-masuk-'.now()->format('Ymd_His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        return response()->streamDownload(function() use ($rows) {
            $out = fopen('php://output','w');
            fwrite($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, ['Nomor','Perihal','Pengirim','Tanggal Surat','Kategori','Prioritas','Status','Jumlah Disposisi','Lampiran']);
            foreach ($rows as $l) {
                $hasReview = $l->dispositions()->whereIn('status',[ 'pending','in_progress' ])->exists();
                if ($hasReview) $status = 'review';
                elseif ($l->processed_at) $status = 'processed';
                elseif ($l->received_at) $status = 'in_progress';
                else $status = 'pending';

                fputcsv($out, [
                    $l->letter_number,
                    $l->subject,
                    $l->sender_name,
                    optional($l->letter_date)->format('Y-m-d'),
                    $l->from_department_id ? 'Internal' : 'Eksternal',
                    $l->priority,
                    $status,
                    (int) $l->dispositions_count,
                    (int) $l->attachments_count,
                ]);
            }
            fclose($out);
        }, $filename, $headers);
    }

    public function letterTypes(Request $request)
    {
        $types = LetterType::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id','name','code','number_format']);

        return response()->json([
            'success' => true,
            'data' => $types
        ]);
    }

    /**
     * List potential signers (all active users from database)
     */
    public function signers(Request $request)
    {
        $q = trim($request->get('q',''));
        $signers = User::query()
            ->with('department:id,name,code')
            ->when($q, function($query) use ($q){
                $query->where(function($w) use ($q){
                    $w->where('name','like',"%$q%")
                      ->orWhere('position','like',"%$q%")
                      ->orWhere('nip','like',"%$q%")
                      ->orWhere('email','like',"%$q%") ;
                });
            })
            ->where('status','active')
            ->orderBy('name')
            ->limit(50)
            ->get(['id','name','position','nip','email','department_id']);

        $mapped = $signers->map(function($u){
            return [
                'id' => $u->id,
                'nama' => $u->name,
                'jabatan' => $u->position,
                'nip' => $u->nip,
                'department' => $u->department?->only(['id','name','code'])
            ];
        });

        return response()->json(['success'=>true,'data'=>$mapped]);
    }

    /**
     * Endpoint baru: daftar penandatangan (alias signers) khusus untuk modal pilih penandatangan.
     * Accepts: q (string, optional), limit (int, optional default 50)
     * Response: { success: bool, data: [ { id, nama, jabatan, nip, department:{id,name,code} } ] }
     */
    public function penandatangan(Request $request)
    {
        // Ambil semua user aktif dari database tanpa filter role
        $q = trim($request->get('q',''));
        $limit = (int) $request->get('limit', 50);
        if($limit > 100) { $limit = 100; } // batasi agar ringan

        $query = User::query()
            ->with('department:id,name,code')
            ->where('status','active')
            ->when($q, function($builder) use ($q){
                $builder->where(function($w) use ($q){
                    $w->where('name','like',"%$q%")
                      ->orWhere('position','like',"%$q%")
                      ->orWhere('nip','like',"%$q%")
                      ->orWhere('email','like',"%$q%") ;
                });
            })
            ->orderBy('name');

        $users = $query->limit($limit)->get(['id','name','position','nip','email','department_id']);

        $data = $users->map(function($u){
            return [
                'id' => $u->id,
                'nama' => $u->name,
                'jabatan' => $u->position,
                'nip' => $u->nip,
                'department' => $u->department?->only(['id','name','code'])
            ];
        });

        return response()->json(['success'=>true,'data'=>$data]);
    }

    /**
     * Mendapatkan daftar template surat yang tersedia
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTemplates(Request $request)
    {
        $category = $request->get('category', null);
        $search = trim($request->get('q', ''));
        
        $templates = [
            [
                'id' => 'surat-tugas',
                'name' => 'Surat Tugas',
                'description' => 'Surat penugasan kepada pegawai/dosen untuk melaksanakan tugas tertentu',
                'category' => 'Umum',
                'path' => 'templates.letters.surat-tugas'
            ],
            [
                'id' => 'surat-edaran',
                'name' => 'Surat Edaran',
                'description' => 'Surat pemberitahuan resmi yang ditujukan kepada unit kerja',
                'category' => 'Umum',
                'path' => 'templates.letters.surat-edaran'
            ],
            [
                'id' => 'internal-memo',
                'name' => 'Internal Memo',
                'description' => 'Memo untuk komunikasi internal antar unit/departemen',
                'category' => 'Internal',
                'path' => 'templates.letters.internal-memo'
            ],
            [
                'id' => 'perjanjian-kerjasama',
                'name' => 'Perjanjian Kerjasama (PKS)',
                'description' => 'Dokumen perjanjian kerjasama dengan pihak eksternal',
                'category' => 'Kerjasama',
                'path' => 'templates.letters.perjanjian-kerjasama'
            ],
            [
                'id' => 'surat-izin-atasan',
                'name' => 'Surat Izin Atasan',
                'description' => 'Surat izin dari atasan untuk kegiatan/program tertentu',
                'category' => 'Personalia',
                'path' => 'templates.letters.surat-izin-atasan'
            ],
            [
                'id' => 'surat-keterangan',
                'name' => 'Surat Keterangan',
                'description' => 'Surat yang menerangkan status/fakta tertentu',
                'category' => 'Umum',
                'path' => 'templates.letters.surat-keterangan'
            ]
        ];
        
        // Filter by category if specified
        if ($category && $category !== 'Semua Kategori') {
            $templates = array_filter($templates, function($template) use ($category) {
                return $template['category'] === $category;
            });
        }
        
        // Filter by search term if provided
        if (!empty($search)) {
            $templates = array_filter($templates, function($template) use ($search) {
                return stripos($template['name'], $search) !== false || 
                      stripos($template['description'], $search) !== false;
            });
        }
        
        // Reset array keys
        $templates = array_values($templates);
        
        // Get unique categories for filter dropdown
        $categories = array_unique(array_column($templates, 'category'));
        sort($categories);
        array_unshift($categories, 'Semua Kategori');
        
        return response()->json([
            'success' => true,
            'data' => $templates,
            'categories' => $categories
        ]);
    }

    /**
     * Mendapatkan konten template surat berdasarkan ID
     * 
     * @param Request $request
     * @param string $id ID template
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function getTemplateContent(Request $request, $id)
    {
        $templateList = $this->getTemplates($request)->original['data'];
        
        $template = null;
        foreach ($templateList as $t) {
            if ($t['id'] === $id) {
                $template = $t;
                break;
            }
        }
        
        if (!$template) {
            return response()->json([
                'success' => false,
                'message' => 'Template tidak ditemukan'
            ], 404);
        }
        
        // Siapkan data default untuk template
        $user = Auth::user();
        $department = $user->department;
        
        $data = [
            'letterNumber' => '',
            'subject' => $request->get('subject', ''),
            'letterDate' => now()->format('Y-m-d'),
            'signerName' => $request->get('signer_name', ''),
            'signerPosition' => $request->get('signer_position', ''),
            'recipientName' => $request->get('recipient_name', ''),
            'content' => $request->get('content', ''),
            'departmentName' => $department ? $department->name : 'Universitas Bakrie'
        ];
        
        if ($request->wantsJson()) {
            // Render template sebagai string
            $html = view($template['path'], $data)->render();
            return response()->json([
                'success' => true,
                'data' => [
                    'template' => $template,
                    'html' => $html
                ]
            ]);
        }
        
        // Render template view
        return view($template['path'], $data);
    }

    /**
     * Menyimpan template khusus yang dibuat pengguna
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveCustomTemplate(Request $request)
    {
        $user = Auth::user();
        
        $validated = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:100'],
            'category' => ['required', 'string', 'max:50'],
            'content' => ['required', 'string'],
            'description' => ['nullable', 'string', 'max:255'],
        ])->validate();
        
        // Buat ID dari nama template (slug)
        $templateId = 'custom-' . Str::slug($validated['name']) . '-' . Str::random(8);
        
        // Simpan template kustom ke storage
        $content = $validated['content'];
        $path = 'templates/custom/' . $user->id;
        Storage::put($path . '/' . $templateId . '.blade.php', $content);
        
        // Opsional: Jika Anda ingin menyimpan metadata template ke database
        // bisa dibuat model dan tabel untuk itu
        
        return response()->json([
            'success' => true,
            'message' => 'Template berhasil disimpan',
            'data' => [
                'id' => $templateId,
                'name' => $validated['name'],
                'category' => $validated['category'],
                'description' => $validated['description'] ?? '',
                'path' => $path . '/' . $templateId . '.blade.php'
            ]
        ]);
    }

    /* =============================================================
     *  API: DRAFT LETTER CRUD
     * ============================================================= */

    /**
     * Upload temporary attachment (not tied to a Letter yet).
     * Frontend expects: POST /unit-kerja/api/attachments/temp with 'file'.
     * Response shape used by Alpine:
     * {
     *   success: true,
     *   data: { id, nama, size_human, path }
     * }
     */
    public function uploadTempAttachment(Request $request)
    {
        $user = Auth::user();
        $validated = Validator::make($request->all(), [
            'file' => ['required','file','max:5120','mimes:pdf,doc,docx,zip,jpg,jpeg,png']
        ])->validate();

        $uploaded = $request->file('file');
        // Store under a temp area per-user to avoid clashes
        $dir = 'temp/attachments/'.($user?->id ?? 'guest');
        $storedPath = $uploaded->store($dir);

        $bytes = $uploaded->getSize();
        $human = $this->humanFileSize($bytes);
        $data = [
            'id' => (string) Str::uuid(),
            'nama' => $uploaded->getClientOriginalName(),
            'size_human' => $human,
            'path' => $storedPath,
        ];

        // Optionally, you might persist temp metadata in cache/session for later attach to draft
        // but for now we just return the stored path so client can include it when saving draft.

        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * Store a new draft letter.
     */
    public function storeDraft(Request $request)
    {
        $user = Auth::user();

        $validated = Validator::make($request->all(), [
            'letter_type_id' => ['nullable','integer','exists:letter_types,id'],
            'jenis' => ['nullable','string'], // alternative if id not sent
            'perihal' => ['required','string','max:255'],
            'tanggal' => ['required','date'],
            'prioritas' => ['nullable', Rule::in(['low','normal','high','urgent'])],
            'ringkasan' => ['nullable','string'],
            'konten' => ['nullable','string'],
            'catatanInternal' => ['nullable','string'],
            'klasifikasi' => ['nullable','string','max:50'],
            'tujuanInternal' => ['array'],
            'tujuanInternal.*' => ['string','max:150'],
            'tujuanExternal' => ['array'],
            'tujuanExternal.*' => ['string','max:255'],
            'participants' => ['array'],
        ])->validate();

        // Resolve letter type id if only name provided
        $letterTypeId = $validated['letter_type_id'] ?? null;
        if(!$letterTypeId && !empty($validated['jenis'])){
            $lt = LetterType::where('name',$validated['jenis'])->first();
            if($lt) { $letterTypeId = $lt->id; }
        }

        if(!$letterTypeId){
            return response()->json([
                'success'=>false,
                'message'=>'Jenis surat (letter_type_id atau jenis) wajib dipilih.'
            ], 422);
        }

        $internal = $validated['tujuanInternal'] ?? [];
        $external = $validated['tujuanExternal'] ?? [];

        // Compose recipients (simple approach – can be normalized later)
        $recipientName = count($internal) ? implode(', ', $internal) : ($external[0] ?? null);
        $recipientAddress = count($external) ? implode('; ', $external) : null;

        $draftNumber = $this->generateDraftNumber($letterTypeId, $user->id);

        $letter = Letter::create([
            'letter_number' => $draftNumber,
            'subject' => $validated['perihal'],
            'content' => $validated['konten'] ?? null,
            'letter_date' => $validated['tanggal'],
            'direction' => 'outgoing',
            'status' => 'draft',
            'priority' => $validated['prioritas'] ?? 'normal',
            'recipient_name' => $recipientName,
            'recipient_address' => $recipientAddress,
            'letter_type_id' => $letterTypeId,
            'created_by' => $user->id,
            'from_department_id' => $user->department_id,
            'to_department_id' => null,
            'notes' => json_encode([
                'ringkasan' => $validated['ringkasan'] ?? null,
                'klasifikasi' => $validated['klasifikasi'] ?? null,
                'participants' => $validated['participants'] ?? [],
                'tujuanInternal' => $internal,
                'tujuanExternal' => $external,
                'catatanInternal' => $validated['catatanInternal'] ?? null,
            ], JSON_UNESCAPED_UNICODE)
        ]);

        return response()->json([
            'success'=>true,
            'message'=>'Draft surat berhasil dibuat.',
            'data'=>$letter
        ], 201);
    }

    /**
     * Update an existing draft letter.
     */
    public function updateDraft(Request $request, Letter $letter)
    {
        $user = Auth::user();
        if($letter->status !== 'draft') {
            return response()->json(['success'=>false,'message'=>'Surat bukan status draft.'], 409);
        }
        if($letter->created_by !== $user->id) {
            return response()->json(['success'=>false,'message'=>'Tidak memiliki izin mengubah draft ini.'], 403);
        }

        $validated = Validator::make($request->all(), [
            'perihal' => ['sometimes','required','string','max:255'],
            'tanggal' => ['sometimes','required','date'],
            'prioritas' => ['nullable', Rule::in(['low','normal','high','urgent'])],
            'ringkasan' => ['nullable','string'],
            'konten' => ['nullable','string'],
            'catatanInternal' => ['nullable','string'],
            'klasifikasi' => ['nullable','string','max:50'],
            'tujuanInternal' => ['array'],
            'tujuanInternal.*' => ['string','max:150'],
            'tujuanExternal' => ['array'],
            'tujuanExternal.*' => ['string','max:255'],
            'participants' => ['array'],
        ])->validate();

        $data = [];
        if(isset($validated['perihal'])) $data['subject'] = $validated['perihal'];
        if(isset($validated['konten'])) $data['content'] = $validated['konten'];
        if(isset($validated['tanggal'])) $data['letter_date'] = $validated['tanggal'];
        if(isset($validated['prioritas'])) $data['priority'] = $validated['prioritas'];

        // Update recipients if provided
        if(array_key_exists('tujuanInternal',$validated) || array_key_exists('tujuanExternal',$validated)) {
            $internal = $validated['tujuanInternal'] ?? ($letter->notes['tujuanInternal'] ?? []);
            $external = $validated['tujuanExternal'] ?? ($letter->notes['tujuanExternal'] ?? []);
            $data['recipient_name'] = count($internal) ? implode(', ', $internal) : ($external[0] ?? null);
            $data['recipient_address'] = count($external) ? implode('; ', $external) : null;
        }

        // Merge notes JSON
        $notes = $letter->notes ?: [];
        foreach(['ringkasan','klasifikasi','participants','tujuanInternal','tujuanExternal','catatanInternal'] as $k){
            if(array_key_exists($k, $validated)) {
                $notes[$k] = $validated[$k];
            }
        }
        $data['notes'] = $notes;

        $letter->update($data);

        return response()->json(['success'=>true,'message'=>'Draft diperbarui.','data'=>$letter]);
    }

    /**
     * Upload attachment for a draft letter.
     */
    public function uploadAttachment(Request $request, Letter $letter)
    {
        $user = Auth::user();
        if($letter->status !== 'draft') {
            return response()->json(['success'=>false,'message'=>'Lampiran hanya dapat ditambahkan pada draft.'], 409);
        }
        if($letter->created_by !== $user->id) {
            return response()->json(['success'=>false,'message'=>'Tidak memiliki izin.'], 403);
        }

        $validated = Validator::make($request->all(), [
            'file' => ['required','file','max:5120','mimes:pdf,doc,docx,zip,jpg,jpeg,png'],
            'description' => ['nullable','string','max:255']
        ])->validate();

        $uploadedFile = $request->file('file');
        $storedPath = $uploadedFile->store('letters/attachments');

        $attachment = LetterAttachment::create([
            'letter_id' => $letter->id,
            'original_name' => $uploadedFile->getClientOriginalName(),
            'file_name' => basename($storedPath),
            'file_path' => $storedPath,
            'file_type' => $uploadedFile->getClientOriginalExtension(),
            'file_size' => $uploadedFile->getSize(),
            'description' => $validated['description'] ?? null,
            'uploaded_by' => $user->id
        ]);

        return response()->json(['success'=>true,'message'=>'Lampiran diunggah.','data'=>$attachment]);
    }

    /**
     * Delete an attachment.
     */
    public function deleteAttachment(Letter $letter, LetterAttachment $attachment)
    {
        $user = Auth::user();
        if($letter->id !== $attachment->letter_id) {
            return response()->json(['success'=>false,'message'=>'Lampiran tidak terkait dengan surat ini.'], 422);
        }
        if($letter->created_by !== $user->id) {
            return response()->json(['success'=>false,'message'=>'Tidak memiliki izin.'], 403);
        }
        if($letter->status !== 'draft') {
            return response()->json(['success'=>false,'message'=>'Tidak dapat menghapus lampiran setelah diajukan.'], 409);
        }
        $attachment->deleteFile();
        $attachment->delete();
        return response()->json(['success'=>true,'message'=>'Lampiran dihapus.']);
    }

    /**
     * Preview next letter number without locking sequence.
     */
    public function previewNextNumber(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'letter_type_id' => ['required','integer','exists:letter_types,id'],
            'department_id' => ['nullable','integer','exists:departments,id'],
            'prefix' => ['nullable','string','max:30'],
            'suffix' => ['nullable','string','max:30']
        ])->validate();

        $year = now()->year;
        $sequence = LetterNumberSequence::firstOrCreate([
            'letter_type_id' => $validated['letter_type_id'],
            'department_id' => $validated['department_id'] ?? null,
            'year' => $year
        ], [ 'last_number' => 0 ]);

        $nextNumber = $sequence->last_number + 1; // do not increment
        $letterType = $sequence->letterType()->first();
        $department = $sequence->department()->first();
        // Update default preview format to UB/R-{code}/{month_roman}/{year}
        $format = $letterType->number_format ?? '{number}/UB/R-{code}/{month_roman}/{year}';
        $replacements = [
            '{number}' => str_pad($nextNumber, 3, '0', STR_PAD_LEFT),
            '{code}' => $letterType->code,
            '{department_code}' => $department->code ?? '',
            '{month}' => now()->format('m'),
            '{month_roman}' => ['','I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'][(int) now()->format('n')],
            '{year}' => $sequence->year,
            '{prefix}' => $validated['prefix'] ?? ($sequence->prefix ?? ''),
            '{suffix}' => $validated['suffix'] ?? ($sequence->suffix ?? '')
        ];
        $preview = str_replace(array_keys($replacements), array_values($replacements), $format);

        return response()->json(['success'=>true,'data'=>[
            'preview' => $preview,
            'sequence_current' => $sequence->last_number,
            'sequence_next' => $nextNumber
        ]]);
    }

    /**
     * Submit draft letter for signature (generate final number & create signature record)
     */
    public function submitForSignature(Request $request, Letter $letter)
    {
        $user = Auth::user();
        if($letter->status !== 'draft') {
            return response()->json(['success'=>false,'message'=>'Hanya draft yang dapat diajukan.'], 409);
        }
        if($letter->created_by !== $user->id) {
            return response()->json(['success'=>false,'message'=>'Tidak memiliki izin pada draft ini.'], 403);
        }

        $validated = Validator::make($request->all(), [
            'signer_user_id' => ['required','integer','exists:users,id'],
            'letter_type_id' => ['required','integer','exists:letter_types,id'], // ensure stored type matches
            'department_id' => ['nullable','integer','exists:departments,id'],
            'prefix' => ['nullable','string','max:30'],
            'suffix' => ['nullable','string','max:30']
        ])->validate();

        if($validated['letter_type_id'] != $letter->letter_type_id) {
            return response()->json(['success'=>false,'message'=>'Jenis surat tidak konsisten dengan draft.'], 422);
        }

        DB::beginTransaction();
        try {
            $sequence = LetterNumberSequence::lockForUpdate()->firstOrCreate([
                'letter_type_id' => $validated['letter_type_id'],
                'department_id' => $validated['department_id'] ?? null,
                'year' => now()->year
            ],[ 'last_number' => 0 ]);

            // Optionally update prefix/suffix if provided (non destructive)
            $updates = [];
            if(isset($validated['prefix'])) $updates['prefix'] = $validated['prefix'];
            if(isset($validated['suffix'])) $updates['suffix'] = $validated['suffix'];
            if($updates) $sequence->update($updates);

            // Ensure uniqueness on letter_number
            $finalNumber = $sequence->generateUniqueLetterNumber();

            // Update letter to pending
            $letter->update([
                'letter_number' => $finalNumber,
                'status' => 'pending'
            ]);

            // Create signature task if not exists
            LetterSignature::firstOrCreate([
                'letter_id' => $letter->id,
                'user_id' => $validated['signer_user_id']
            ], [
                'signature_type' => 'electronic',
                'status' => 'pending'
            ]);

            DB::commit();
        } catch(\Throwable $e) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>'Gagal mengajukan surat','error'=>$e->getMessage()], 500);
        }

        return response()->json(['success'=>true,'message'=>'Surat diajukan untuk tanda tangan.','data'=>$letter->fresh('signatures')]);
    }

    public function submitDirect(Request $request)
    {
        $user = Auth::user();

        $validated = Validator::make($request->all(), [
            'letter_type_id' => ['required','integer','exists:letter_types,id'],
            'perihal' => ['required','string','max:255'],
            'tanggal' => ['required','date'],
            'prioritas' => ['nullable', Rule::in(['low','normal','high','urgent'])],
            'klasifikasi' => ['nullable','string','max:50'],
            'ringkasan' => ['nullable','string'],
            'konten' => ['nullable','string'],
            'catatanInternal' => ['nullable','string'],
            'tujuanInternal' => ['array'],
            'tujuanInternal.*' => ['string','max:150'],
            'tujuanExternal' => ['array'],
            'tujuanExternal.*' => ['string','max:255'],
            'participants' => ['array'],
            'signer_user_id' => ['required','integer','exists:users,id'],
            // numbering hints
            'department_id' => ['nullable','integer','exists:departments,id'],
            'prefix' => ['nullable','string','max:30'],
            'suffix' => ['nullable','string','max:30'],
            // temp attachments from UI: [{id, nama, path, size_human}]
            'attachments' => ['array'],
            'attachments.*.path' => ['required','string'],
            'attachments.*.nama' => ['required','string'],
        ])->validate();

        // Resolve recipients
        $internal = $validated['tujuanInternal'] ?? [];
        $external = $validated['tujuanExternal'] ?? [];
        $recipientName = count($internal) ? implode(', ', $internal) : ($external[0] ?? null);
        $recipientAddress = count($external) ? implode('; ', $external) : null;

        DB::beginTransaction();
        try {
            // 1) Create draft
            $draftNumber = $this->generateDraftNumber($validated['letter_type_id'], $user->id);
            $letter = Letter::create([
                'letter_number' => $draftNumber,
                'subject' => $validated['perihal'],
                'content' => $validated['konten'] ?? null,
                'letter_date' => $validated['tanggal'],
                'direction' => 'outgoing',
                'status' => 'draft',
                'priority' => $validated['prioritas'] ?? 'normal',
                'recipient_name' => $recipientName,
                'recipient_address' => $recipientAddress,
                'letter_type_id' => $validated['letter_type_id'],
                'created_by' => $user->id,
                'from_department_id' => $user->department_id,
                'to_department_id' => null,
                'notes' => json_encode([
                    'ringkasan' => $validated['ringkasan'] ?? null,
                    'klasifikasi' => $validated['klasifikasi'] ?? null,
                    'participants' => $validated['participants'] ?? [],
                    'tujuanInternal' => $internal,
                    'tujuanExternal' => $external,
                    'catatanInternal' => $validated['catatanInternal'] ?? null,
                ], JSON_UNESCAPED_UNICODE)
            ]);

            // 2) Move temp attachments and create records
            $temps = $validated['attachments'] ?? [];
            foreach ($temps as $t) {
                $tempPath = $t['path'];
                // Allow only files under current user's temp dir
                $allowedPrefix = 'temp/attachments/'.($user->id);
                if (!Str::startsWith($tempPath, $allowedPrefix)) {
                    continue;
                }
                if (!Storage::exists($tempPath)) continue; // skip if missing

                $fileName = basename($tempPath);
                // Ensure unique name in destination to avoid overwrite
                $destinationDir = 'letters/attachments';
                $destinationPath = $destinationDir.'/'.$fileName;
                if (Storage::exists($destinationPath)) {
                    $name = pathinfo($fileName, PATHINFO_FILENAME);
                    $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                    $destinationPath = $destinationDir.'/'.$name.'-'.uniqid().($ext?'.'.$ext:'');
                }
                Storage::move($tempPath, $destinationPath);

                $size = Storage::size($destinationPath);
                LetterAttachment::create([
                    'letter_id' => $letter->id,
                    'original_name' => $t['nama'],
                    'file_name' => basename($destinationPath),
                    'file_path' => $destinationPath,
                    'file_type' => pathinfo($destinationPath, PATHINFO_EXTENSION),
                    'file_size' => $size,
                    'uploaded_by' => $user->id,
                ]);
            }

            // 3) Lock sequence and finalize number
            $seq = LetterNumberSequence::lockForUpdate()->firstOrCreate([
                'letter_type_id' => $validated['letter_type_id'],
                'department_id' => $validated['department_id'] ?? null,
                'year' => now()->year
            ], [ 'last_number' => 0 ]);
            $updates = [];
            if(isset($validated['prefix'])) $updates['prefix'] = $validated['prefix'];
            if(isset($validated['suffix'])) $updates['suffix'] = $validated['suffix'];
            if($updates) $seq->update($updates);

            // Ensure uniqueness on letter_number
            $finalNumber = $seq->generateUniqueLetterNumber();

            // 4) Update letter status and number
            $letter->update([
                'letter_number' => $finalNumber,
                'status' => 'pending'
            ]);

            // 5) Create signature task
            $signature = LetterSignature::firstOrCreate([
                'letter_id' => $letter->id,
                'user_id' => $validated['signer_user_id']
            ], [
                'signature_type' => 'electronic',
                'status' => 'pending'
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>'Gagal mengajukan surat','error'=>$e->getMessage()], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Surat berhasil diajukan untuk tanda tangan.',
            'data' => $letter->load('signatures')
        ], 201);
    }

    private function generateDraftNumber(int $letterTypeId, int $userId): string
    {
        $ts = now()->format('YmdHis');
        $rand = substr(bin2hex(random_bytes(3)),0,6);
        return "DRAFT/$letterTypeId/$userId/$ts/$rand";
    }

    private function humanFileSize(int $bytes, int $decimals = 1): string
    {
        if ($bytes < 1024) return $bytes.' B';
        $units = ['KB','MB','GB','TB'];
        $factor = (int) floor((strlen((string) $bytes) - 1) / 3);
        $factor = max(1, min($factor, count($units)));
        $val = $bytes / pow(1024, $factor);
        return number_format($val, $decimals).' '.$units[$factor-1];
    }

    /**
     * ========================================
     * DISPOSISI MANAGEMENT - UNIT KERJA (PENERIMA)
     * ========================================
     */

    /**
     * Get inbox disposisi untuk user yang login (unit_kerja).
     * Menampilkan disposisi yang dikirim ke user ini.
     */
    public function dispositionsInbox(Request $request)
    {
        $user = $request->user();
        $perPage = (int)($request->integer('per_page') ?: 15);

        $query = \App\Models\LetterDisposition::query()
            ->where('to_user_id', $user->id)
            ->with([
                'letter:id,letter_number,subject,letter_date,priority,status,from_department_id,sender_name',
                'letter.fromDepartment:id,name,code',
                'fromUser:id,name,position',
            ])
            ->withCount(['letter']);

        // Filter by status
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        // Filter by priority
        if ($priority = $request->get('priority')) {
            $query->where('priority', $priority);
        }

        // Search
        if ($s = trim($request->get('q', ''))) {
            $query->where(function($q) use ($s) {
                $q->whereHas('letter', function($l) use ($s) {
                    $l->where('letter_number', 'like', "%$s%")
                      ->orWhere('subject', 'like', "%$s%");
                })
                ->orWhere('instruction', 'like', "%$s%");
            });
        }

        // Filter unread only
        if ($request->boolean('unread')) {
            $query->whereNull('read_at');
        }

        $query->latest();

        $paginator = $query->paginate($perPage);

        $data = collect($paginator->items())->map(function($d) {
            $letter = $d->letter;
            return [
                'id' => $d->id,
                'letter_id' => $letter?->id,
                'letter_number' => $letter?->letter_number,
                'subject' => $letter?->subject,
                'from' => $letter?->sender_name ?: ($letter?->fromDepartment?->name),
                'from_user' => $d->fromUser?->name,
                'instruction' => $d->instruction,
                'priority' => $d->priority,
                'status' => $d->status,
                'due_date' => optional($d->due_date)->format('Y-m-d'),
                'read_at' => optional($d->read_at)->format('Y-m-d H:i'),
                'completed_at' => optional($d->completed_at)->format('Y-m-d H:i'),
                'is_overdue' => $d->isOverdue(),
                'created_at' => $d->created_at->format('Y-m-d H:i'),
            ];
        })->values();

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ]
        ]);
    }

    /**
     * Show detail disposisi spesifik.
     */
    public function dispositionShow(\App\Models\LetterDisposition $disposition, Request $request)
    {
        $user = $request->user();
        
        // Validasi: hanya penerima disposisi yang bisa lihat
        if ($disposition->to_user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $disposition->load([
            'letter:id,letter_number,subject,letter_date,priority,status,content,from_department_id,sender_name',
            'letter.fromDepartment:id,name,code',
            'letter.toDepartment:id,name,code',
            'letter.attachments:id,letter_id,original_name,file_name,file_path,file_type,file_size',
            'fromUser:id,name,position',
        ]);

        $letter = $disposition->letter;

        return response()->json([
            'data' => [
                'id' => $disposition->id,
                'letter_id' => $letter?->id,
                'letter_number' => $letter?->letter_number,
                'subject' => $letter?->subject,
                'content' => $letter?->content,
                'from' => $letter?->sender_name ?: ($letter?->fromDepartment?->name),
                'from_user' => $disposition->fromUser?->name,
                'from_user_position' => $disposition->fromUser?->position,
                'instruction' => $disposition->instruction,
                'priority' => $disposition->priority,
                'status' => $disposition->status,
                'response' => $disposition->response,
                'due_date' => optional($disposition->due_date)->format('Y-m-d'),
                'read_at' => optional($disposition->read_at)->format('Y-m-d H:i'),
                'completed_at' => optional($disposition->completed_at)->format('Y-m-d H:i'),
                'is_overdue' => $disposition->isOverdue(),
                'created_at' => $disposition->created_at->format('Y-m-d H:i'),
                'attachments' => $letter?->attachments?->map(fn($a) => [
                    'id' => $a->id,
                    'name' => $a->original_name,
                    'size' => $a->file_size,
                    'type' => $a->file_type,
                    'url' => Storage::url($a->file_path),
                ]),
            ]
        ]);
    }

    /**
     * Mark disposisi sebagai sudah dibaca.
     */
    public function dispositionMarkRead(\App\Models\LetterDisposition $disposition, Request $request)
    {
        $user = $request->user();
        
        // Validasi: hanya penerima disposisi yang bisa mark as read
        if ($disposition->to_user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $disposition->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Disposisi ditandai sudah dibaca',
            'data' => [
                'read_at' => $disposition->read_at->format('Y-m-d H:i')
            ]
        ]);
    }

    /**
     * Update response pada disposisi.
     */
    public function dispositionUpdateResponse(\App\Models\LetterDisposition $disposition, Request $request)
    {
        $user = $request->user();
        
        // Validasi: hanya penerima disposisi yang bisa update response
        if ($disposition->to_user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'response' => 'required|string|min:5|max:1000',
        ]);

        $disposition->update([
            'response' => $validated['response'],
            'status' => 'in_progress', // Otomatis set ke in_progress saat ada response
        ]);

        // Auto mark as read jika belum
        if (!$disposition->read_at) {
            $disposition->markAsRead();
        }

        return response()->json([
            'success' => true,
            'message' => 'Response berhasil disimpan',
            'data' => [
                'response' => $disposition->response,
                'status' => $disposition->status,
            ]
        ]);
    }

    /**
     * Tandai disposisi sebagai selesai (completed).
     */
    public function dispositionComplete(\App\Models\LetterDisposition $disposition, Request $request)
    {
        $user = $request->user();
        
        // Validasi: hanya penerima disposisi yang bisa complete
        if ($disposition->to_user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Validasi: harus ada response dulu sebelum complete
        if (!$disposition->response) {
            return response()->json([
                'success' => false,
                'message' => 'Harap berikan laporan/response terlebih dahulu sebelum menyelesaikan disposisi.'
            ], 422);
        }

        $disposition->markAsCompleted();

        // Update status surat jika semua disposisi sudah completed
        $letter = $disposition->letter;
        if ($letter) {
            $allCompleted = $letter->dispositions()->where('status', '!=', 'completed')->count() === 0;
            if ($allCompleted && $letter->status === 'pending') {
                $letter->update(['status' => 'processed']);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Disposisi berhasil diselesaikan',
            'data' => [
                'status' => $disposition->status,
                'completed_at' => $disposition->completed_at->format('Y-m-d H:i'),
            ]
        ]);
    }

    /**
     * Dashboard statistics for unit kerja
     */
    public function dashboardStats(Request $request)
    {
        $user = Auth::user();
        $departmentId = $user->department_id;
        $today = now()->toDateString();
        $thisMonth = now()->format('Y-m');

        // Stats
        $stats = [
            // Surat masuk hari ini (disposisi diterima)
            'incoming_today' => LetterDisposition::where(function($q) use ($user, $departmentId) {
                    $q->where('to_user_id', $user->id)
                      ->orWhere('to_department_id', $departmentId);
                })
                ->whereDate('created_at', $today)
                ->count(),

            // Surat masuk kemarin
            'incoming_yesterday' => LetterDisposition::where(function($q) use ($user, $departmentId) {
                    $q->where('to_user_id', $user->id)
                      ->orWhere('to_department_id', $departmentId);
                })
                ->whereDate('created_at', now()->subDay()->toDateString())
                ->count(),

            // Draft surat keluar
            'draft_outgoing' => Letter::where('from_department_id', $departmentId)
                ->where('status', 'draft')
                ->count(),

            // Surat menunggu tanda tangan
            'awaiting_signature' => Letter::where('from_department_id', $departmentId)
                ->where('status', 'active')
                ->whereDoesntHave('signatures')
                ->count(),

            // Surat dengan prioritas tinggi yang menunggu
            'high_priority_pending' => Letter::where('from_department_id', $departmentId)
                ->where('status', 'active')
                ->whereIn('priority', ['high', 'urgent'])
                ->whereDoesntHave('signatures')
                ->count(),

            // Surat tugas diarsipkan bulan ini
            'archived_this_month' => Letter::where('from_department_id', $departmentId)
                ->where('type', 'surat_tugas')
                ->where('status', 'archived')
                ->whereYear('archived_at', now()->year)
                ->whereMonth('archived_at', now()->month)
                ->count(),

            // Surat tugas diarsipkan minggu ini
            'archived_this_week' => Letter::where('from_department_id', $departmentId)
                ->where('type', 'surat_tugas')
                ->where('status', 'archived')
                ->whereBetween('archived_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),

            // Disposisi pending (belum dibaca)
            'dispositions_unread' => LetterDisposition::where(function($q) use ($user, $departmentId) {
                    $q->where('to_user_id', $user->id)
                      ->orWhere('to_department_id', $departmentId);
                })
                ->whereNull('read_at')
                ->count(),

            // Disposisi in progress
            'dispositions_in_progress' => LetterDisposition::where(function($q) use ($user, $departmentId) {
                    $q->where('to_user_id', $user->id)
                      ->orWhere('to_department_id', $departmentId);
                })
                ->where('status', 'in_progress')
                ->count(),
        ];

        return response()->json(['data' => $stats]);
    }

    /**
     * Recent incoming letters (via dispositions)
     */
    public function dashboardRecentIncoming(Request $request)
    {
        $user = Auth::user();
        $departmentId = $user->department_id;

        $dispositions = LetterDisposition::where(function($q) use ($user, $departmentId) {
                $q->where('to_user_id', $user->id)
                  ->orWhere('to_department_id', $departmentId);
            })
            ->with(['letter.letterType', 'fromUser', 'letter.agenda'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $data = $dispositions->map(function($d) {
            $letter = $d->letter;
            return [
                'id' => $letter->id,
                'number' => $letter->letter_number,
                'subject' => $letter->subject,
                'from' => $d->fromUser?->name ?? 'System',
                'date' => $letter->letter_date,
                'priority' => $letter->priority ?? 'normal',
                'status' => $d->status,
                'disposition_id' => $d->id,
            ];
        });

        return response()->json(['data' => $data]);
    }

    /**
     * Draft outgoing letters
     */
    public function dashboardDraftOutgoing(Request $request)
    {
        $user = Auth::user();
        $departmentId = $user->department_id;

        $drafts = Letter::where('from_department_id', $departmentId)
            ->where('status', 'draft')
            ->with('letterType')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $data = $drafts->map(function($letter) {
            return [
                'id' => $letter->id,
                'temp' => 'DRAFT-' . $letter->id,
                'subject' => $letter->subject,
                'type' => $letter->letterType?->code ?? 'N/A',
                'created_at' => $letter->created_at->format('Y-m-d H:i'),
                'priority' => $letter->priority ?? 'normal',
            ];
        });

        return response()->json(['data' => $data]);
    }

    /**
     * Archived assignment letters
     */
    public function dashboardArchivedAssignments(Request $request)
    {
        $user = Auth::user();
        $departmentId = $user->department_id;

        $archived = Letter::where('from_department_id', $departmentId)
            ->where('type', 'surat_tugas')
            ->where('status', 'archived')
            ->orderBy('archived_at', 'desc')
            ->limit(5)
            ->get();

        $data = $archived->map(function($letter) {
            return [
                'id' => $letter->id,
                'number' => $letter->letter_number,
                'subject' => $letter->subject,
                'archived_at' => $letter->archived_at?->format('Y-m-d'),
                'duration' => $letter->archived_at && $letter->created_at 
                    ? $letter->created_at->diffInHours($letter->archived_at) 
                    : 0,
            ];
        });

        return response()->json(['data' => $data]);
    }

    /**
     * Signature queue (letters awaiting signature)
     */
    public function dashboardSignatureQueue(Request $request)
    {
        $user = Auth::user();
        $departmentId = $user->department_id;

        $queue = Letter::where('from_department_id', $departmentId)
            ->where('status', 'active')
            ->whereDoesntHave('signatures')
            ->with('letterType')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $data = $queue->map(function($letter) {
            return [
                'id' => $letter->id,
                'temp' => 'DRAFT-' . $letter->id,
                'subject' => $letter->subject,
                'type' => $letter->letterType?->code ?? 'N/A',
                'requested_at' => $letter->created_at->format('Y-m-d H:i'),
                'priority' => $letter->priority ?? 'normal',
            ];
        });

        return response()->json(['data' => $data]);
    }

    /**
     * Chart data - monthly incoming/outgoing letters
     */
    public function dashboardChartMonthly(Request $request)
    {
        $user = Auth::user();
        $departmentId = $user->department_id;
        $months = 6; // Last 6 months

        $labels = [];
        $incomingData = [];
        $outgoingData = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->format('M Y');

            // Incoming (dispositions received)
            $incoming = LetterDisposition::where(function($q) use ($user, $departmentId) {
                    $q->where('to_user_id', $user->id)
                      ->orWhere('to_department_id', $departmentId);
                })
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            // Outgoing (letters created by department)
            $outgoing = Letter::where('from_department_id', $departmentId)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $incomingData[] = $incoming;
            $outgoingData[] = $outgoing;
        }

        return response()->json([
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Surat Masuk',
                        'data' => $incomingData,
                        'borderColor' => 'rgb(59, 130, 246)',
                        'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    ],
                    [
                        'label' => 'Surat Keluar',
                        'data' => $outgoingData,
                        'borderColor' => 'rgb(245, 158, 11)',
                        'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    ]
                ]
            ]
        ]);
    }

    /**
     * Chart data - disposition status distribution
     */
    public function dashboardChartDispositionStatus(Request $request)
    {
        $user = Auth::user();
        $departmentId = $user->department_id;

        $pending = LetterDisposition::where(function($q) use ($user, $departmentId) {
                $q->where('to_user_id', $user->id)
                  ->orWhere('to_department_id', $departmentId);
            })
            ->where('status', 'pending')
            ->count();

        $inProgress = LetterDisposition::where(function($q) use ($user, $departmentId) {
                $q->where('to_user_id', $user->id)
                  ->orWhere('to_department_id', $departmentId);
            })
            ->where('status', 'in_progress')
            ->count();

        $completed = LetterDisposition::where(function($q) use ($user, $departmentId) {
                $q->where('to_user_id', $user->id)
                  ->orWhere('to_department_id', $departmentId);
            })
            ->where('status', 'completed')
            ->count();

        return response()->json([
            'data' => [
                'labels' => ['Pending', 'Dalam Proses', 'Selesai'],
                'datasets' => [
                    [
                        'data' => [$pending, $inProgress, $completed],
                        'backgroundColor' => [
                            'rgba(148, 163, 184, 0.8)',
                            'rgba(245, 158, 11, 0.8)',
                            'rgba(34, 197, 94, 0.8)',
                        ],
                        'borderColor' => [
                            'rgb(148, 163, 184)',
                            'rgb(245, 158, 11)',
                            'rgb(34, 197, 94)',
                        ],
                        'borderWidth' => 1,
                    ]
                ]
            ]
        ]);
    }

    /**
     * Pending dispositions notifications
     */
    public function dashboardPendingNotifications(Request $request)
    {
        $user = Auth::user();
        $departmentId = $user->department_id;

        $pendingDispositions = LetterDisposition::where(function($q) use ($user, $departmentId) {
                $q->where('to_user_id', $user->id)
                  ->orWhere('to_department_id', $departmentId);
            })
            ->whereNull('read_at')
            ->with(['letter', 'fromUser'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $data = $pendingDispositions->map(function($d) {
            return [
                'id' => $d->id,
                'letter_id' => $d->letter_id,
                'letter_number' => $d->letter?->letter_number,
                'subject' => $d->letter?->subject,
                'from' => $d->fromUser?->name ?? 'System',
                'priority' => $d->letter?->priority ?? 'normal',
                'created_at' => $d->created_at->diffForHumans(),
                'notes' => $d->notes,
            ];
        });

        return response()->json(['data' => $data]);
    }

    /**
     * API: Get list of all active departments
     */
    public function departmentsIndex()
    {
        $departments = Department::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'type']);

        return response()->json([
            'success' => true,
            'data' => $departments
        ]);
    }

    /**
     * API: Get list of all active users
     */
    public function usersIndex()
    {
        $users = User::where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'department_id']);

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }
}
