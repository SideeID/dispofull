<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Letter;
use App\Models\LetterDisposition;
use App\Models\LetterAttachment;
use App\Models\Department;
use App\Models\LetterSignature;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RektoratController extends Controller
{
    public function suratMasuk()
    {
        // Ambil 50 surat masuk terbaru untuk initial render FE (server-side hydration)
        $user = request()->user();
        $deptId = $user?->department_id;
        $userId = $user?->id;

        // Dapatkan ID dari letter_type Surat Tugas (ST) untuk dikecualikan
        $suratTugasTypeId = \App\Models\LetterType::where('code', 'ST')->value('id');

        $letters = Letter::query()
            ->whereNull('archived_at') // Exclude archived letters
            // Exclude Surat Tugas (ST) from incoming letters
            ->when($suratTugasTypeId, function($q) use ($suratTugasTypeId) {
                $q->where('letter_type_id', '!=', $suratTugasTypeId);
            })
            ->where(function($q) use ($deptId, $userId){
                $q->where(function($w) use ($deptId){
                    $w->where('direction','incoming')
                      ->when($deptId, function($qq) use ($deptId){
                          $qq->where(function($x) use ($deptId){
                              $x->where('to_department_id',$deptId)
                                ->orWhereNull('to_department_id');
                          });
                      });
                })
                ->orWhereHas('signatures', function($s) use ($userId){
                    $s->where('user_id', $userId);
                });
            })
            ->with(['fromDepartment:id,name,code', 'toDepartment:id,name,code', 'letterType:id,code'])
            ->withCount(['attachments','dispositions'])
            ->latest('letter_date')
            ->latest()
            ->limit(50)
            ->get();

        $incoming = $letters->map(fn($l) => $this->transformLetter($l))->values();

        return view('pages.rektorat.surat-masuk.index', compact('incoming'));
    }

    public function suratTugas()
    {
        return view('pages.rektorat.surat-tugas.index');
    }

    public function inboxSuratTugas()
    {
        return view('pages.rektorat.inbox-surat-tugas.index');
    }

    public function historyDisposisi()
    {
        return view('pages.rektorat.history-disposisi.index');
    }

    public function tindakLanjutSuratTugas()
    {
        return view('pages.rektorat.tindaklanjut-surat-tugas.index');
    }

    /**
     * Get assignment letters (surat tugas) with follow-up monitoring
     * Filters: q, date_from, date_to, status, priority, period_from, period_to
     */
    public function tindakLanjutIndex(Request $request)
    {
        // Log start of execution
        \Log::info('tindakLanjutIndex: Started', [
            'user_id' => $request->user()?->id,
            'filters' => $request->all()
        ]);

        try {
            // Get Surat Tugas letter type dynamically
            $letterType = \App\Models\LetterType::where('code', 'ST')->first();
            
            if (!$letterType) {
                \Log::warning('tindakLanjutIndex: Letter type ST not found');
                return response()->json([
                    'data' => [],
                    'meta' => [
                        'current_page' => 1,
                        'last_page' => 1,
                        'per_page' => 10,
                        'total' => 0,
                    ]
                ]);
            }

            // Build query with all necessary relations
            $query = Letter::where('letter_type_id', $letterType->id)
                ->where('direction', 'outgoing')
                ->whereNull('archived_at') // Exclude archived letters
                ->with([
                    'letterType:id,code,name',
                    'agenda:id,name,agenda_number',
                    'fromDepartment:id,name,code',
                    'toDepartment:id,name,code',
                    'dispositions' => function ($q) {
                        $q->with([
                            'toUser:id,name,position',
                            'toDepartment:id,name,code',
                            'fromUser:id,name,position'
                        ]);
                    },
                    'signatures' => function ($q) {
                        $q->with('user:id,name,position');
                    },
                    'attachments:id,letter_id,original_name,file_path,file_type,file_size'
                ]);

            // Apply search filter
            if ($request->filled('q')) {
                $search = trim($request->q);
                $query->where(function ($q) use ($search) {
                    $q->where('letter_number', 'like', "%{$search}%")
                      ->orWhere('subject', 'like', "%{$search}%");
                });
            }

            // Apply date filters
            if ($request->filled('date_from')) {
                $query->whereDate('letter_date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('letter_date', '<=', $request->date_to);
            }

            // Apply period filters (task assignment period)
            if ($request->filled('period_from')) {
                $query->whereDate('created_at', '>=', $request->period_from);
            }
            if ($request->filled('period_to')) {
                $query->whereDate('created_at', '<=', $request->period_to);
            }

            // Apply status filter
            if ($request->filled('status')) {
                $status = $request->status;
                if ($status === 'draft') {
                    $query->where('status', 'draft');
                } elseif ($status === 'need_signature') {
                    $query->where('status', 'active')
                          ->whereDoesntHave('signatures');
                } elseif ($status === 'signed') {
                    $query->where('status', 'active')
                          ->whereHas('signatures');
                } elseif ($status === 'published') {
                    $query->where('status', 'published');
                } elseif ($status === 'archived') {
                    $query->where('status', 'archived');
                }
            }

            // Apply priority filter
            if ($request->filled('priority')) {
                $query->where('priority', $request->priority);
            }

            // Execute query with pagination
            $perPage = $request->integer('per_page', 10);
            $letters = $query->orderBy('letter_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            \Log::info('tindakLanjutIndex: Query executed', [
                'total_results' => $letters->total(),
                'current_page' => $letters->currentPage()
            ]);

            // Transform data with error handling for each item
            $data = collect($letters->items())->map(function($letter) {
                try {
                    // Ensure all relations are loaded to avoid N+1 query
                    if (!$letter->relationLoaded('dispositions')) {
                        $letter->load('dispositions');
                    }
                    if (!$letter->relationLoaded('attachments')) {
                        $letter->load('attachments');
                    }
                    if (!$letter->relationLoaded('signatures')) {
                        $letter->load('signatures');
                    }
                    if (!$letter->relationLoaded('agenda')) {
                        $letter->load('agenda');
                    }

                    // Parse notes safely to get followup data
                    $notes = [];
                    if (is_string($letter->notes)) {
                        $decoded = @json_decode($letter->notes, true);
                        $notes = is_array($decoded) ? $decoded : [];
                    } elseif (is_array($letter->notes)) {
                        $notes = $letter->notes;
                    }
                    $followups = $notes['followups'] ?? [];
                    
                    // Calculate completion percentage from followups
                    $completionPercentage = 0;
                    if (!empty($followups) && is_array($followups)) {
                        $latestFollowup = collect($followups)
                            ->sortByDesc('created_at')
                            ->first();
                        $completionPercentage = $latestFollowup['completion_percentage'] ?? 0;
                    }
                    
                    // Calculate recipient statistics safely
                    $totalRecipients = $letter->dispositions->count();
                    $completedRecipients = $letter->dispositions->filter(function($d) {
                        return !is_null($d->completed_at) || $d->status === 'completed';
                    })->count();
                    
                    // Transform letter data
                    return [
                        'id' => $letter->id,
                        'letter_number' => $letter->letter_number ?? '',
                        'subject' => $letter->subject ?? '',
                        'perihal' => $letter->subject ?? '',
                        'letter_date' => optional($letter->letter_date)->format('Y-m-d'),
                        'created_at' => optional($letter->created_at)->format('Y-m-d H:i:s'),
                        'priority' => $letter->priority ?? 'normal',
                        'status' => $letter->status ?? 'draft',
                        'agenda' => optional($letter->agenda)->id ? [
                            'id' => $letter->agenda->id,
                            'name' => $letter->agenda->name ?? $letter->agenda->agenda_number ?? '',
                        ] : null,
                        'total_recipients' => $totalRecipients,
                        'completed_recipients' => $completedRecipients,
                        'completion_rate' => $totalRecipients > 0 
                            ? round(($completedRecipients / $totalRecipients) * 100, 1)
                            : $completionPercentage,
                        'overall_status' => $this->determineOverallStatus($letter),
                        'attachments' => $letter->attachments->map(function($a) {
                            return [
                                'id' => $a->id ?? null,
                                'original_name' => $a->original_name ?? '',
                                'file_path' => $a->file_path ?? '',
                                'file_type' => $a->file_type ?? '',
                            ];
                        })->toArray(),
                        'signature' => optional($letter->signatures->first())->id ? [
                            'signer_name' => $letter->signatures->first()->signer_name ?? '',
                            'signer_title' => $letter->signatures->first()->signer_title ?? '',
                            'signature_data' => $letter->signatures->first()->signature_data ?? null,
                            'signature_path' => $letter->signatures->first()->signature_path ?? null,
                            'signed_at' => optional($letter->signatures->first()->created_at)->format('Y-m-d H:i:s'),
                        ] : null,
                        'followups_count' => count($followups),
                        'latest_followup' => !empty($followups) && is_array($followups) 
                            ? collect($followups)->sortByDesc('created_at')->first() 
                            : null,
                    ];
                } catch (\Throwable $e) {
                    // Error handling for individual letter transformation
                    \Log::error('tindakLanjutIndex: Error transforming letter', [
                        'letter_id' => $letter->id ?? 'unknown',
                        'error' => $e->getMessage(),
                        'line' => $e->getLine(),
                        'file' => $e->getFile()
                    ]);
                    return null;
                }
            })->filter()->values(); // Filter out null values from failed transformations

            // Log successful completion
            \Log::info('tindakLanjutIndex: Completed successfully', [
                'data_count' => $data->count()
            ]);

            // Return successful response
            return response()->json([
                'data' => $data,
                'meta' => [
                    'current_page' => $letters->currentPage(),
                    'last_page' => $letters->lastPage(),
                    'per_page' => $letters->perPage(),
                    'total' => $letters->total(),
                ]
            ]);

        } catch (\Throwable $e) {
            // Global error handling
            \Log::error('tindakLanjutIndex: Critical error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            // Return error response
            return response()->json([
                'error' => 'Terjadi kesalahan pada server',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error',
                'data' => [],
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 10,
                    'total' => 0,
                ]
            ], 500);
        }
    }

    /**
     * Get detailed follow-up status for a specific assignment letter
     */
    public function tindakLanjutShow($id)
    {
        // Get Surat Tugas letter type
        $letterType = \App\Models\LetterType::where('code', 'ST')->first();
        if (!$letterType) {
            abort(404, 'Letter type not found');
        }

        $letter = Letter::where('letter_type_id', $letterType->id)
            ->with([
                'letterType',
                'dispositions' => function ($q) {
                    $q->with(['toUser', 'toDepartment', 'fromUser'])
                      ->orderBy('created_at', 'asc');
                },
                'signatures' => function ($q) {
                    $q->with('user')->orderBy('signed_at', 'asc');
                },
                'attachments'
            ])
            ->findOrFail($id);

        // Group dispositions by recipient for tracking
        $recipients = $letter->dispositions->groupBy(function ($disposition) {
            return $disposition->to_user_id 
                ? 'user_' . $disposition->to_user_id 
                : 'dept_' . $disposition->to_department_id;
        })->map(function ($dispositions) {
            $latest = $dispositions->sortByDesc('created_at')->first();
            return [
                'recipient_type' => $latest->to_user_id ? 'user' : 'department',
                'recipient' => $latest->to_user_id ? $latest->toUser : $latest->toDepartment,
                'status' => $latest->status,
                'response' => $latest->response,
                'completed_at' => $latest->completed_at,
                'timeline' => $dispositions->map(function ($d) {
                    return [
                        'id' => $d->id,
                        'status' => $d->status,
                        'notes' => $d->notes,
                        'response' => $d->response,
                        'created_at' => $d->created_at,
                        'read_at' => $d->read_at,
                        'completed_at' => $d->completed_at,
                    ];
                })
            ];
        })->values();

        // Parse metadata dari notes
        $metadata = [];
        if ($letter->notes && is_string($letter->notes)) {
            $decoded = json_decode($letter->notes, true);
            if (is_string($decoded)) {
                $metadata = json_decode($decoded, true) ?? [];
            } else {
                $metadata = $decoded ?? [];
            }
        } elseif (is_array($letter->notes)) {
            $metadata = $letter->notes;
        }

        // Transform letter data untuk preview
        $transformedLetter = [
            'id' => $letter->id,
            'number' => $letter->letter_number,
            'subject' => $letter->subject,
            'perihal' => $letter->subject,
            'from' => $letter->from,
            'date' => $letter->letter_date,
            'tanggal' => $letter->letter_date,
            'priority' => $letter->priority,
            'status' => $letter->status,
            'konten' => $letter->content,
            'tujuanInternal' => $metadata['tujuanInternal'] ?? [],
            'tujuanExternal' => $metadata['tujuanExternal'] ?? [],
            'agenda' => $letter->agenda_number,
            'notes' => $letter->notes,
            'letter_type' => $letter->letterType,
            'signature' => $letter->signatures->isNotEmpty() ? [
                'signer_name' => $letter->signatures->first()->signer_name,
                'signer_title' => $letter->signatures->first()->signer_title,
                'signature_data' => $letter->signatures->first()->signature_data,
                'signature_path' => $letter->signatures->first()->signature_path,
                'signed_at' => $letter->signatures->first()->signed_at,
            ] : null,
            'attachments' => $letter->attachments->map(function ($attachment) {
                return [
                    'id' => $attachment->id,
                    'filename' => $attachment->filename,
                    'size' => $attachment->size,
                    'type' => $attachment->type,
                ];
            })->toArray(),
        ];

        return response()->json([
            'data' => [
                'letter' => $transformedLetter,
                'recipients' => $recipients,
                'stats' => [
                    'total' => $recipients->count(),
                    'completed' => $recipients->where('status', 'completed')->count(),
                    'in_progress' => $recipients->where('status', 'in_progress')->count(),
                    'pending' => $recipients->where('status', 'pending')->count(),
                ]
            ]
        ]);
    }

    /**
     * Get response history for a specific recipient
     */
    public function tindakLanjutResponses($letterId, $recipientId, $recipientType)
    {
        $query = LetterDisposition::where('letter_id', $letterId);

        if ($recipientType === 'user') {
            $query->where('to_user_id', $recipientId);
        } else {
            $query->where('to_department_id', $recipientId);
        }

        $responses = $query->with(['fromUser', 'toUser', 'toDepartment'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['data' => $responses]);
    }

    public function arsipSuratTugas()
    {
        return view('pages.rektorat.arsip-surat-tugas.index');
    }

    /**
     * History Disposisi — index list for role rektorat
     * Filters: q (number/subject/origin), date_from, date_to, priority, status
     * Returns paginated dispositions joined with letter info.
     */
    public function historyDispositionsIndex(Request $request)
    {
        $perPage = (int)($request->integer('per_page') ?: 15);
        $user = $request->user();
        $deptId = $user?->department_id;

        $query = LetterDisposition::query()
            ->with([
                'letter:id,letter_number,subject,letter_date,priority,status,from_department_id,to_department_id,sender_name',
                'letter.fromDepartment:id,name,code',
                'letter.toDepartment:id,name,code',
                'fromUser:id,name,position',
                'toUser:id,name,position',
            ])
            // Scope: only dispositions for letters visible to rektorat (to_department or global)
            ->whereHas('letter', function($q) use ($deptId){
                $q->where('direction','incoming')
                  ->when($deptId, function($qq) use ($deptId){
                      $qq->where(function($x) use ($deptId){
                          $x->where('to_department_id',$deptId)
                            ->orWhereNull('to_department_id');
                      });
                  });
            });

        if ($s = trim($request->get('q',''))) {
            $query->where(function($q) use ($s){
                $q->whereHas('letter', function($l) use ($s){
                    $l->where('letter_number','like',"%$s%")
                      ->orWhere('subject','like',"%$s%");
                })
                ->orWhereHas('fromUser', fn($u)=>$u->where('name','like',"%$s%"))
                ->orWhereHas('toUser', fn($u)=>$u->where('name','like',"%$s%"));
            });
        }
        if ($status = $request->get('status')) {
            if (in_array($status, ['pending','in_progress','completed','returned'])) {
                $query->where('status', $status);
            } elseif ($status === 'archived') {
                $query->whereHas('letter', fn($l) => $l->where('status','archived'));
            } elseif ($status === 'forwarded') {
                // Sederhana: treat forwarded as pending dispositions
                $query->where('status','pending');
            }
        }
        if ($priority = $request->get('priority')) {
            $query->whereHas('letter', fn($l)=>$l->where('priority',$priority));
        }
        if ($to = trim($request->get('to',''))) {
            $query->where(function($q) use ($to){
                $q->whereHas('toUser', fn($u)=>$u->where('name','like',"%$to%"))
                  ->orWhereHas('letter.toDepartment', fn($d)=>$d->where('name','like',"%$to%"));
            });
        }
        if ($request->filled('date_from')) {
            $query->whereHas('letter', fn($l)=>$l->whereDate('letter_date','>=',$request->date('date_from')));
        }
        if ($request->filled('date_to')) {
            $query->whereHas('letter', fn($l)=>$l->whereDate('letter_date','<=',$request->date('date_to')));
        }

        $query->latest();
        $paginator = $query->paginate($perPage);

        $data = collect($paginator->items())->map(function(LetterDisposition $d){
            $letter = $d->letter;
            return [
                'id' => $d->id,
                'number' => $letter?->letter_number,
                'subject' => $letter?->subject,
                'origin' => $letter?->sender_name ?: ($letter?->fromDepartment?->name),
                'date' => optional($letter?->letter_date)->format('Y-m-d'),
                'to' => $letter?->toDepartment?->name ?: ($d->toUser?->name),
                'priority' => $letter?->priority,
                'status' => $d->status,
                'letter_status' => $letter?->status,
                'chain' => $letter?->dispositions()->count() ?? 0,
                'attachments' => $letter?->attachments()->count() ?? 0,
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

    /** Detail satu record history disposisi (berdasarkan disposition id). */
    public function historyDispositionsShow(LetterDisposition $disposition, Request $request)
    {
        if (!$this->dispositionVisibleToRektor($request, $disposition)) abort(404);
        $disposition->load(['letter.fromDepartment:id,name,code','letter.toDepartment:id,name,code','fromUser:id,name,position','toUser:id,name,position']);
        $letter = $disposition->letter;
        return response()->json([
            'data' => [
                'id' => $disposition->id,
                'number' => $letter?->letter_number,
                'subject' => $letter?->subject,
                'origin' => $letter?->sender_name ?: ($letter?->fromDepartment?->name),
                'date' => optional($letter?->letter_date)->format('Y-m-d'),
                'to' => $letter?->toDepartment?->name ?: ($disposition->toUser?->name),
                'priority' => $letter?->priority,
                'status' => $disposition->status,
                'chain' => $letter?->dispositions()->count() ?? 0,
                'attachments' => $letter?->attachments()->count() ?? 0,
            ]
        ]);
    }

    /** Daftar lampiran terkait surat dari disposition. */
    public function historyDispositionsAttachments(LetterDisposition $disposition, Request $request)
    {
        if (!$this->dispositionVisibleToRektor($request, $disposition)) abort(404);
        $letter = $disposition->letter()->withCount('attachments')->first();
        $attachments = $letter->attachments()->select('id','original_name as name','file_size as size','file_type as type','created_at')->latest()->get();
        return response()->json(['data'=>$attachments]);
    }

    /** Route/Timeline untuk disposition spesifik (gabungan activity sederhana). */
    public function historyDispositionsRoute(LetterDisposition $disposition, Request $request)
    {
        if (!$this->dispositionVisibleToRektor($request, $disposition)) abort(404);
        $steps = [];
        $letter = $disposition->letter;
        if ($letter?->received_at) {
            $steps[] = [
                'time' => $letter->received_at->format('Y-m-d H:i'),
                'unit' => $letter->toDepartment?->name ?: 'Tujuan',
                'action' => 'Menerima surat',
                'status' => 'success',
            ];
        }
        // step disposisi dibuat
        $steps[] = [
            'time' => $disposition->created_at->format('Y-m-d H:i'),
            'unit' => $disposition->fromUser?->name ?: 'Pengirim',
            'action' => 'Mendisposisikan ke '.($disposition->toUser?->name ?: ($disposition->toDepartment?->name ?: 'Penerima')),
            'status' => 'success',
        ];
        if ($disposition->read_at) {
            $steps[] = [
                'time' => $disposition->read_at->format('Y-m-d H:i'),
                'unit' => $disposition->toUser?->name ?: 'Penerima',
                'action' => 'Membaca disposisi',
                'status' => 'success',
            ];
        }
        if ($disposition->completed_at) {
            $steps[] = [
                'time' => $disposition->completed_at->format('Y-m-d H:i'),
                'unit' => $disposition->toUser?->name ?: 'Penerima',
                'action' => 'Menandai selesai',
                'status' => 'success',
            ];
        }
        return response()->json(['data'=>$steps]);
    }

    /** Catatan untuk disposition (gunakan response dan instruction untuk saat ini). */
    public function historyDispositionsNotes(LetterDisposition $disposition, Request $request)
    {
        if (!$this->dispositionVisibleToRektor($request, $disposition)) abort(404);
        $notes = [];
        $notes[] = [
            'time' => $disposition->created_at->format('Y-m-d H:i'),
            'unit' => $disposition->fromUser?->name ?: 'Pengirim',
            'text' => $disposition->instruction,
        ];
        if ($disposition->response) {
            $notes[] = [
                'time' => optional($disposition->updated_at)->format('Y-m-d H:i') ?? $disposition->created_at->format('Y-m-d H:i'),
                'unit' => $disposition->toUser?->name ?: 'Penerima',
                'text' => $disposition->response,
            ];
        }
        return response()->json(['data'=>$notes]);
    }

    /** Timeline/History gabungan untuk disposition & suratnya. */
    public function historyDispositionsTimeline(LetterDisposition $disposition, Request $request)
    {
        if (!$this->dispositionVisibleToRektor($request, $disposition)) abort(404);
        $letter = $disposition->letter()->with(['attachments.uploader:id,name'])->first();
        $logs = [];
        if ($letter?->received_at) {
            $logs[] = [
                'time' => $letter->received_at->format('Y-m-d H:i'),
                'actor' => 'System',
                'action' => 'Surat diterima',
            ];
        }
        $logs[] = [
            'time' => $disposition->created_at->format('Y-m-d H:i'),
            'actor' => $disposition->fromUser?->name ?: 'User',
            'action' => 'Membuat disposisi',
        ];
        if ($disposition->read_at) {
            $logs[] = [
                'time' => $disposition->read_at->format('Y-m-d H:i'),
                'actor' => $disposition->toUser?->name ?: 'User',
                'action' => 'Membaca disposisi',
            ];
        }
        if ($disposition->completed_at) {
            $logs[] = [
                'time' => $disposition->completed_at->format('Y-m-d H:i'),
                'actor' => $disposition->toUser?->name ?: 'User',
                'action' => 'Menyelesaikan disposisi',
            ];
        }
        foreach ($letter->attachments as $a) {
            $logs[] = [
                'time' => $a->created_at->format('Y-m-d H:i'),
                'actor' => $a->uploader->name ?? 'User',
                'action' => 'Menambahkan lampiran '.$a->original_name,
            ];
        }
        usort($logs, fn($a,$b)=>strcmp($b['time'],$a['time']));
        return response()->json(['data'=>$logs]);
    }

    public function incomingIndex(Request $request)
    {
        $perPage = (int)($request->integer('per_page') ?: 15);
        $deptId = $request->user()?->department_id;
        $userId = $request->user()?->id;

        // Dapatkan ID dari letter_type Surat Tugas (ST) untuk dikecualikan
        $suratTugasTypeId = \App\Models\LetterType::where('code', 'ST')->value('id');

        $query = Letter::query()
            ->whereNull('archived_at') // Exclude archived letters
            // Exclude Surat Tugas (ST) from incoming letters
            ->when($suratTugasTypeId, function($q) use ($suratTugasTypeId) {
                $q->where('letter_type_id', '!=', $suratTugasTypeId);
            })
            ->where(function($q) use ($deptId, $userId){
                $q->where(function($w) use ($deptId){
                    $w->where('direction', 'incoming')
                      ->when($deptId, function($qq) use ($deptId){
                          $qq->where(function($x) use ($deptId){
                              $x->where('to_department_id',$deptId)
                                ->orWhereNull('to_department_id');
                          });
                      });
                })
                ->orWhereHas('signatures', function($s) use ($userId){
                    $s->where('user_id',$userId);
                });
            })
            ->with(['letterType:id,code,name', 'fromDepartment:id,name,code', 'toDepartment:id,name,code', 'dispositions:id,letter_id,status', 'signatures', 'attachments'])
            ->withCount(['attachments', 'dispositions']);

        if ($s = trim($request->get('q', ''))) {
            $query->where(function($q) use ($s){
                $q->where('letter_number','like',"%$s%")
                  ->orWhere('subject','like',"%$s%")
                  ->orWhere('sender_name','like',"%$s%");
            });
        }
        if ($status = $request->get('status')) {
            // Mapping khusus FE untuk status pseudo
            if (in_array($status, ['draft','pending','processed','archived','rejected'])) {
                $query->where('status', $status);
            } elseif ($status === 'in_progress') {
                $query->whereHas('dispositions', fn($q)=>$q->where('status','in_progress'));
            } elseif ($status === 'review') {
                // Interpretasi: surat pending tapi sudah punya disposisi (menunggu review)
                $query->where('status','pending')->whereHas('dispositions');
            }
        }
        if ($priority = $request->get('priority')) {
            $query->where('priority', $priority);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('letter_date', '>=', $request->date('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('letter_date', '<=', $request->date('date_to'));
        }
        if ($request->boolean('has_disposition')) {
            $query->whereHas('dispositions');
        }
        if ($request->boolean('has_attachment')) {
            $query->whereHas('attachments');
        }

        $query->latest('letter_date')->latest();

        $letters = $query->paginate($perPage);

        $collection = collect($letters->items())->map(fn($l)=>$this->transformLetter($l));
        return response()->json([
            'data' => $collection,
            'meta' => [
                'current_page' => $letters->currentPage(),
                'last_page' => $letters->lastPage(),
                'per_page' => $letters->perPage(),
                'total' => $letters->total(),
            ]
        ]);
    }

    /** Show a single incoming letter detail. */
    public function incomingShow(Letter $letter, Request $request)
    {
        if (!$this->visibleToRektor($request, $letter)) abort(404);
        $letter->load([
            'letterType:id,code,name',
            'fromDepartment:id,name,code',
            'toDepartment:id,name,code',
            'signatures',
            'attachments:id,letter_id,original_name,file_name,file_path,file_type,file_size,created_at,description,uploaded_by',
            'dispositions.fromUser:id,name,position',
            'dispositions.toUser:id,name,position',
        ])->loadCount(['attachments','dispositions']);

        return response()->json([
            'data' => $this->transformLetter($letter, includeRelations: true)
        ]);
    }

    /** Mark letter as received (set received_at & status pending if still draft). */
    public function incomingMarkReceived(Letter $letter, Request $request)
    {
        if (!$this->visibleToRektor($request, $letter)) abort(404);
        if (!$letter->received_at) {
            $letter->received_at = now();
        }
        if ($letter->status === 'draft') {
            $letter->status = 'pending';
        }
        $letter->save();
        return response()->json(['message' => 'Letter marked as received', 'data' => $letter]);
    }

    /** List dispositions for incoming letter. */
    public function incomingDispositionsIndex(Letter $letter, Request $request)
    {
        if (!$this->visibleToRektor($request, $letter)) abort(404);
        $dispositions = $letter->dispositions()->with(['fromUser:id,name,position','toUser:id,name,position','toDepartment:id,name,code'])->latest()->get();
        return response()->json($dispositions);
    }

    /** Store new disposition for incoming letter. */
    public function incomingDispositionsStore(Request $request, Letter $letter)
    {
        if (!$this->visibleToRektor($request, $letter)) abort(404);
        
        $data = $request->validate([
            'to_user_id' => 'nullable|exists:users,id',
            'to_department_id' => 'nullable|exists:departments,id',
            'instruction' => 'required|string|min:5',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'due_date' => 'nullable|date|after_or_equal:today',
        ]);
        
        // Validasi: minimal salah satu harus diisi (user atau department)
        if (empty($data['to_user_id']) && empty($data['to_department_id'])) {
            return response()->json([
                'message' => 'Pilih penerima disposisi (User atau Departemen)'
            ], 422);
        }
        
        // Validasi: tidak boleh keduanya diisi
        if (!empty($data['to_user_id']) && !empty($data['to_department_id'])) {
            return response()->json([
                'message' => 'Pilih salah satu: User atau Departemen, tidak boleh keduanya'
            ], 422);
        }
        
        $data['priority'] = $data['priority'] ?? 'normal';
        $data['letter_id'] = $letter->id;
        $data['from_user_id'] = $request->user()->id;
        
        $disposition = LetterDisposition::create($data);
        
        return response()->json(['message' => 'Disposition created','data'=>$disposition->load(['fromUser:id,name','toUser:id,name','toDepartment:id,name'])], 201);
    }

    /** List attachments for incoming letter. */
    public function incomingAttachmentsIndex(Letter $letter, Request $request)
    {
        if (!$this->visibleToRektor($request, $letter)) abort(404);
        $attachments = $letter->attachments()->select('id','letter_id','original_name','file_name','file_path','file_type','file_size','created_at')->latest()->get();
        return response()->json($attachments);
    }

    /** Store attachment for incoming letter. */
    public function incomingAttachmentsStore(Request $request, Letter $letter)
    {
        if (!$this->visibleToRektor($request, $letter)) abort(404);
        $validated = $request->validate([
            'file' => 'required|file|max:5120|mimes:pdf,jpg,jpeg,png',
            'description' => 'nullable|string|max:500'
        ]);
        $file = $validated['file'];
        $originalName = $file->getClientOriginalName();
        $storedName = Str::random(40) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('letters/attachments', $storedName, 'public');

        $attachment = $letter->attachments()->create([
            'original_name' => $originalName,
            'file_name' => $storedName,
            'file_path' => $path,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
            'description' => $validated['description'] ?? null,
            'uploaded_by' => $request->user()->id,
        ]);

        return response()->json(['message' => 'Attachment uploaded','data'=>$attachment], 201);
    }

    /** Get unified history log for a letter (attachments, dispositions, status changes). */
    public function incomingHistory(Letter $letter, Request $request)
    {
        if (!$this->visibleToRektor($request, $letter)) abort(404);
        $letter->load(['attachments.uploader:id,name','dispositions.fromUser:id,name','dispositions.toUser:id,name']);
        $logs = [];
        if ($letter->received_at) {
            $logs[] = [
                'time' => $letter->received_at->format('Y-m-d H:i'),
                'actor' => 'System',
                'action' => 'Surat diterima',
                'status' => 'success'
            ];
        }
        foreach ($letter->attachments as $a) {
            $logs[] = [
                'time' => $a->created_at->format('Y-m-d H:i'),
                'actor' => $a->uploader->name ?? 'User',
                'action' => 'Menambahkan lampiran '.$a->original_name,
                'status' => 'info'
            ];
        }
        foreach ($letter->dispositions as $d) {
            $logs[] = [
                'time' => $d->created_at->format('Y-m-d H:i'),
                'actor' => $d->fromUser->name ?? 'User',
                'action' => 'Disposisi ke '.($d->toUser->name ?? 'User').' · '.$d->instruction,
                'status' => $d->status === 'completed' ? 'success' : ($d->status === 'in_progress' ? 'info' : 'warning')
            ];
        }
        // Sort desc
        usort($logs, fn($a,$b)=> strcmp($b['time'],$a['time']));
        return response()->json(['data'=>$logs]);
    }

    /** List signatures for a letter. */
    public function incomingSignaturesIndex(Letter $letter, Request $request)
    {
        if (!$this->visibleToRektor($request, $letter)) abort(404);
        $signatures = $letter->signatures()->with('user:id,name,position')->latest()->get()->map(fn($s)=>[
            'id'=>$s->id,
            'user'=>$s->user?->name,
            'position'=>$s->user?->position,
            'type'=>$s->signature_type,
            'status'=>$s->status,
            'signed_at'=>optional($s->signed_at)->format('Y-m-d H:i'),
            'notes'=>$s->notes,
        ]);
        return response()->json(['data'=>$signatures]);
    }

    /** Store signature (digital/electronic) for letter. */
    public function incomingSignaturesStore(Request $request, Letter $letter)
    {
        if (!$this->visibleToRektor($request, $letter)) abort(404);
        
        $data = $request->validate([
            'signature_type' => 'required|in:digital,electronic',
            'signature_data' => 'nullable|string',
            'signature_file' => 'nullable|file|mimes:png,jpg,jpeg|max:2048',
            'notes' => 'nullable|string|max:500'
        ]);
        
        $signaturePath = null;
        $signatureData = $data['signature_data'] ?? null;
        
        // Handle file upload
        if ($request->hasFile('signature_file')) {
            $file = $request->file('signature_file');
            $filename = 'signature_' . $letter->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('signatures', $filename, 'public');
            $signaturePath = $path;
            $signatureData = null; // Clear signature_data if file is uploaded
        }
        
        $signature = $letter->signatures()->create([
            'user_id' => $request->user()->id,
            'signature_type' => $data['signature_type'],
            'signature_path' => $signaturePath,
            'signature_data' => $signatureData,
            'signed_at' => now(),
            'status' => 'signed',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'notes' => $data['notes'] ?? null,
        ]);
        
        return response()->json(['message'=>'Letter signed','data'=>$signature], 201);
    }

    /** List disposition recipients (users) (simple filter by active & not current user). */
    public function incomingDispositionRecipients(Request $request)
    {
        $users = User::query()
            ->where('status','active')
            ->where('id','!=',$request->user()->id)
            ->select('id','name','position','department_id')
            ->with('department:id,name,code')
            ->orderBy('name')
            ->limit(200)
            ->get()
            ->map(fn($u)=>[
                'id'=>$u->id,
                'name'=>$u->name,
                'position'=>$u->position,
                'department'=>$u->department?->name,
            ]);
        return response()->json(['data'=>$users]);
    }

    /** Archive incoming letter (only Surat Tugas) */
    public function incomingArchive(Request $request, Letter $letter)
    {
        // Validasi bahwa letter adalah Surat Tugas (letter_type_id = 3)
        if ($letter->letter_type_id !== 3) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Surat Tugas yang bisa diarsipkan'
            ], 400);
        }

        try {
            // Set archived_at timestamp
            $letter->update(['archived_at' => now()]);
            
            Log::info('Letter archived', [
                'letter_id' => $letter->id,
                'letter_number' => $letter->letter_number,
                'letter_type' => 'Surat Tugas',
                'archived_at' => $letter->archived_at
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Surat Tugas berhasil diarsipkan',
                'data' => $this->transformLetter($letter)
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to archive letter', [
                'letter_id' => $letter->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengarsipkan surat: ' . $e->getMessage()
            ], 500);
        }
    }

    /** Unarchive (Pulihkan) archived letter - only Surat Tugas */
    public function incomingUnarchive(Request $request, Letter $letter)
    {
        // Validasi bahwa letter adalah Surat Tugas (letter_type_id = 3)
        if ($letter->letter_type_id !== 3) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Surat Tugas yang bisa dipulihkan'
            ], 400);
        }

        // Validasi bahwa surat sudah diarsipkan
        if (is_null($letter->archived_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Surat ini belum diarsipkan'
            ], 400);
        }

        try {
            // Set archived_at to null (unarchive)
            $letter->update(['archived_at' => null]);
            
            Log::info('Letter unarchived', [
                'letter_id' => $letter->id,
                'letter_number' => $letter->letter_number,
                'letter_type' => 'Surat Tugas',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Surat Tugas berhasil dipulihkan',
                'data' => $this->transformLetter($letter)
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to unarchive letter', [
                'letter_id' => $letter->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memulihkan surat: ' . $e->getMessage()
            ], 500);
        }
    }

    /** List departments for disposition */
    public function getDepartments(Request $request)
    {
        $departments = Department::query()
            ->where('is_active', true)
            ->select('id', 'name', 'code', 'type')
            ->orderBy('name')
            ->get();
        return response()->json($departments);
    }

    /**
     * Transform model Letter menjadi struktur yang dibutuhkan FE.
     */
    private function transformLetter(Letter $letter, bool $includeRelations = false): array
    {
        // Tentukan status presentasi (status pseudo) berdasarkan disposisi
        $displayStatus = $letter->status; // default
        $dispositions = $letter->relationLoaded('dispositions') ? $letter->dispositions : collect();
        if ($dispositions->where('status','in_progress')->count()) {
            $displayStatus = 'in_progress';
        } elseif ($letter->status === 'pending' && $dispositions->count()) {
            $displayStatus = 'review';
        }

        // Parse metadata dari notes
        $metadata = [];
        if ($letter->notes && is_string($letter->notes)) {
            $decoded = json_decode($letter->notes, true);
            if (is_string($decoded)) {
                $metadata = json_decode($decoded, true) ?? [];
            } else {
                $metadata = $decoded ?? [];
            }
        } elseif (is_array($letter->notes)) {
            $metadata = $letter->notes;
        }

        $data = [
            'id' => $letter->id,
            'number' => $letter->letter_number,
            'subject' => $letter->subject,
            'perihal' => $letter->subject, // Alias untuk frontend
            'from' => $letter->sender_name ?: ($letter->fromDepartment->name ?? null),
            'date' => optional($letter->letter_date)->format('Y-m-d'),
            'tanggal' => optional($letter->letter_date)->format('Y-m-d'), // Alias untuk frontend
            'priority' => $letter->priority,
            'status' => $displayStatus,
            'konten' => $letter->content, // Konten HTML dari editor
            'tujuanInternal' => $metadata['tujuanInternal'] ?? [],
            'tujuanExternal' => $metadata['tujuanExternal'] ?? [],
            'agenda' => $letter->agenda_number ?? null,
            'notes' => $letter->notes,
            'metadata' => $metadata,
            'attachments' => $letter->attachments_count ?? ($letter->attachments?->count() ?? 0),
            'dispositions' => $letter->dispositions_count ?? ($dispositions->count()),
            'letter_type' => $letter->letterType ? [
                'id' => $letter->letterType->id,
                'code' => $letter->letterType->code,
                'name' => $letter->letterType->name,
            ] : null,
            'signature' => $letter->signatures?->first() ? [
                'signer_name' => $letter->signatures->first()->signer_name,
                'signer_title' => $letter->signatures->first()->signer_title,
                'signature_data' => $letter->signatures->first()->signature_data,
                'signature_path' => $letter->signatures->first()->signature_path,
                'signed_at' => optional($letter->signatures->first()->created_at)->format('Y-m-d H:i:s'),
            ] : null,
        ];

        if ($includeRelations) {
            $data['attachments_list'] = $letter->attachments?->map(fn($a)=>[
                'id'=>$a->id,
                'filename'=>$a->original_name,
                'name'=>$a->original_name,
                'size'=>$a->file_size,
                'type'=>$a->file_type,
            ])->values();
            $data['dispositions_list'] = $dispositions->map(fn($d)=>[
                'id'=>$d->id,
                'status'=>$d->status,
                'priority'=>$d->priority,
                'instruction'=>$d->instruction,
                'to_user'=>$d->toUser?->name,
                'from_user'=>$d->fromUser?->name,
                'due_date'=>optional($d->due_date)->format('Y-m-d'),
            ])->values();
        } else {
            // Tambahkan attachments list jika relasi sudah dimuat
            if ($letter->relationLoaded('attachments')) {
                $data['attachments'] = $letter->attachments->map(fn($a)=>[
                    'id'=>$a->id,
                    'filename'=>$a->original_name,
                    'size'=>$a->file_size,
                    'type'=>$a->file_type,
                ])->values();
            }
        }

        return $data;
    }

    /** Determine if a letter is visible to current rektor user. */
    private function visibleToRektor(Request $request, Letter $letter): bool
    {
        $deptId = $request->user()?->department_id;
        $userId = $request->user()?->id;
        if ($letter->direction === 'incoming') {
            if (!$deptId) return true;
            return $letter->to_department_id === $deptId || is_null($letter->to_department_id);
        }
        return $letter->signatures()->where('user_id',$userId)->exists();
    }

    /** Visibility check for a disposition via its letter visibility. */
    private function dispositionVisibleToRektor(Request $request, LetterDisposition $disposition): bool
    {
        $letter = $disposition->letter;
        if (!$letter) return false;
        return $this->visibleToRektor($request, $letter);
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

    /**
     * Determine overall status of a letter
     */
    private function determineOverallStatus($letter): string
    {
        if ($letter->status === 'draft') {
            return 'draft';
        } elseif ($letter->signatures->isEmpty()) {
            return 'need_signature';
        } elseif ($letter->status === 'published') {
            return 'published';
        } elseif ($letter->status === 'archived') {
            return 'archived';
        } else {
            return 'signed';
        }
    }
}
