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
use Illuminate\Support\Str;

class RektoratController extends Controller
{
    public function suratMasuk()
    {
        // Ambil 50 surat masuk terbaru untuk initial render FE (server-side hydration)
        $letters = Letter::query()
            ->where('direction','incoming')
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

    public function arsipSuratTugas()
    {
        return view('pages.rektorat.arsip-surat-tugas.index');
    }

    /* ============================= */
    /*  API JSON: Surat Masuk (Rektor)
    /* ============================= */

    /**
     * List incoming letters (surat masuk) with filters.
     * Query params: q (search), status, priority, date_from, date_to, has_disposition, has_attachment, per_page
     */
    public function incomingIndex(Request $request)
    {
        $perPage = (int)($request->integer('per_page') ?: 15);

        $query = Letter::query()
            ->where('direction', 'incoming')
            ->with(['letterType:id,code,name', 'fromDepartment:id,name,code', 'toDepartment:id,name,code', 'dispositions:id,letter_id,status'])
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
    public function incomingShow(Letter $letter)
    {
        if ($letter->direction !== 'incoming') {
            abort(404);
        }
        $letter->load([
            'letterType:id,code,name',
            'fromDepartment:id,name,code',
            'toDepartment:id,name,code',
            'attachments:id,letter_id,original_name,file_name,file_path,file_type,file_size,created_at,description,uploaded_by',
            'dispositions.fromUser:id,name,position',
            'dispositions.toUser:id,name,position',
        ])->loadCount(['attachments','dispositions']);

        return response()->json([
            'data' => $this->transformLetter($letter, includeRelations: true)
        ]);
    }

    /** Mark letter as received (set received_at & status pending if still draft). */
    public function incomingMarkReceived(Letter $letter)
    {
        if ($letter->direction !== 'incoming') abort(404);
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
    public function incomingDispositionsIndex(Letter $letter)
    {
        if ($letter->direction !== 'incoming') abort(404);
        $dispositions = $letter->dispositions()->with(['fromUser:id,name,position','toUser:id,name,position','toDepartment:id,name,code'])->latest()->get();
        return response()->json($dispositions);
    }

    /** Store new disposition for incoming letter. */
    public function incomingDispositionsStore(Request $request, Letter $letter)
    {
        if ($letter->direction !== 'incoming') abort(404);
        $data = $request->validate([
            'to_user_id' => 'required|exists:users,id',
            'to_department_id' => 'nullable|exists:departments,id',
            'instruction' => 'required|string|min:5',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'due_date' => 'nullable|date|after_or_equal:today',
        ]);
        $data['priority'] = $data['priority'] ?? 'normal';
        $data['letter_id'] = $letter->id;
        $data['from_user_id'] = $request->user()->id;
        $disposition = LetterDisposition::create($data);
        return response()->json(['message' => 'Disposition created','data'=>$disposition->load(['fromUser:id,name','toUser:id,name'])], 201);
    }

    /** List attachments for incoming letter. */
    public function incomingAttachmentsIndex(Letter $letter)
    {
        if ($letter->direction !== 'incoming') abort(404);
        $attachments = $letter->attachments()->select('id','letter_id','original_name','file_name','file_path','file_type','file_size','created_at')->latest()->get();
        return response()->json($attachments);
    }

    /** Store attachment for incoming letter. */
    public function incomingAttachmentsStore(Request $request, Letter $letter)
    {
        if ($letter->direction !== 'incoming') abort(404);
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
    public function incomingHistory(Letter $letter)
    {
        if ($letter->direction !== 'incoming') abort(404);
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
    public function incomingSignaturesIndex(Letter $letter)
    {
        if ($letter->direction !== 'incoming') abort(404);
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
        if ($letter->direction !== 'incoming') abort(404);
        $data = $request->validate([
            'signature_type' => 'required|in:digital,electronic',
            'signature_data' => 'nullable|string',
            'notes' => 'nullable|string|max:500'
        ]);
        $signature = $letter->signatures()->create([
            'user_id' => $request->user()->id,
            'signature_type' => $data['signature_type'],
            'signature_data' => $data['signature_data'] ?? null,
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

        $data = [
            'id' => $letter->id,
            'number' => $letter->letter_number,
            'subject' => $letter->subject,
            'from' => $letter->sender_name ?: ($letter->fromDepartment->name ?? null),
            'date' => optional($letter->letter_date)->format('Y-m-d'),
            'priority' => $letter->priority,
            'status' => $displayStatus,
            'attachments' => $letter->attachments_count ?? ($letter->attachments?->count() ?? 0),
            'dispositions' => $letter->dispositions_count ?? ($dispositions->count()),
            'agenda' => false, // Placeholder - belum ada relasi langsung
        ];

        if ($includeRelations) {
            $data['attachments_list'] = $letter->attachments?->map(fn($a)=>[
                'id'=>$a->id,
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
        }

        return $data;
    }
}
