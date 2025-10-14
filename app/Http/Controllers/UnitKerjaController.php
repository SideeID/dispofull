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
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class UnitKerjaController extends Controller
{
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

        $notes = json_decode($letter->notes, true) ?: [];
        foreach (['start_date','end_date','archive_reason'] as $k) {
            if (array_key_exists($k, $data)) {
                $notes[$k] = $data[$k];
            }
        }

        $letter->update([
            'status' => 'archived',
            'archived_at' => now(),
            'notes' => json_encode($notes, JSON_UNESCAPED_UNICODE)
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
            $notes = json_decode($l->notes, true) ?: [];
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
                $notes = json_decode($l->notes, true) ?: [];
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
     * List potential signers (default: users with role rektorat OR admin)
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
            ->whereIn('role',['rektorat','admin'])
            ->where('status','active')
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
        // Reuse logic dari signers agar konsisten filter role & mapping
        $q = trim($request->get('q',''));
        $limit = (int) $request->get('limit', 50);
        if($limit > 100) { $limit = 100; } // batasi agar ringan

        $query = User::query()
            ->with('department:id,name,code')
            ->whereIn('role',['rektorat','admin'])
            ->when($q, function($builder) use ($q){
                $builder->where(function($w) use ($q){
                    $w->where('name','like',"%$q%")
                      ->orWhere('position','like',"%$q%")
                      ->orWhere('nip','like',"%$q%")
                      ->orWhere('email','like',"%$q%") ;
                });
            })
            ->orderBy('name');

    // Gunakan kolom status (active) sebagai filter aktif
    $query->where('status','active');

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
            $internal = $validated['tujuanInternal'] ?? json_decode($letter->notes,true)['tujuanInternal'] ?? [];
            $external = $validated['tujuanExternal'] ?? json_decode($letter->notes,true)['tujuanExternal'] ?? [];
            $data['recipient_name'] = count($internal) ? implode(', ', $internal) : ($external[0] ?? null);
            $data['recipient_address'] = count($external) ? implode('; ', $external) : null;
        }

        // Merge notes JSON
        $notes = json_decode($letter->notes, true) ?: [];
        foreach(['ringkasan','klasifikasi','participants','tujuanInternal','tujuanExternal','catatanInternal'] as $k){
            if(array_key_exists($k, $validated)) {
                $notes[$k] = $validated[$k];
            }
        }
        $data['notes'] = json_encode($notes, JSON_UNESCAPED_UNICODE);

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
}
