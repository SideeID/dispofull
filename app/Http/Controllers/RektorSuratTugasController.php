<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use App\Models\LetterType;
use App\Models\LetterNumberSequence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RektorSuratTugasController extends Controller
{
    /**
     * Show the Surat Tugas page (FE view already exists).
     */
    public function index()
    {
        return view('pages.rektorat.surat-tugas.index');
    }

    /**
     * List Surat Tugas with filters.
     * Filters: q, date, start_from, end_to, status, priority
     */
    public function apiIndex(Request $request)
    {
        $perPage = (int) $request->integer('per_page', 10);
        $q = trim((string) $request->get('q', ''));
        $date = $request->date('date');
        $startFrom = $request->date('start_from');
        $endTo = $request->date('end_to');
    $status = $request->get('status');
        $priority = $request->get('priority');

        $letterTypeId = LetterType::where('code', 'ST')->value('id');
        if (!$letterTypeId) {
            return response()->json(['data' => [], 'meta' => ['total' => 0]], 200);
        }

        $query = Letter::query()
            ->where('letter_type_id', $letterTypeId)
            ->where('direction', 'outgoing')
            ->withCount('attachments');

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('letter_number', 'like', "%{$q}%")
                    ->orWhere('subject', 'like', "%{$q}%")
                    ->orWhere('recipient_name', 'like', "%{$q}%")
                    ->orWhere('notes', 'like', "%{$q}%");
            });
        }

        if ($date) {
            $query->whereDate('letter_date', $date);
        }
        if ($startFrom) {
            $query->whereDate('letter_date', '>=', $startFrom);
        }
        if ($endTo) {
            $query->whereDate('letter_date', '<=', $endTo);
        }
        if ($status) {
            // Align directly with DB enum statuses: draft, pending, processed, archived, rejected, closed
            $query->where('status', $status);
        }
        if ($priority) {
            $query->where('priority', $priority);
        }

        $query->orderByDesc('letter_date')->orderByDesc('id');

        $paginator = $query->paginate($perPage);

        $items = collect($paginator->items())->map(function (Letter $l) {
            $notes = $this->parseNotes($l->notes);
            return [
                'id' => $l->id,
                'number' => $l->letter_number,
                'subject' => $l->subject,
                'destination' => $notes['tujuanInternal'][0] ?? ($notes['tujuanExternal'][0] ?? null),
                'date' => optional($l->letter_date)->toDateString(),
                'start' => $notes['start_date'] ?? null,
                'end' => $notes['end_date'] ?? null,
                'priority' => $l->priority,
                'status' => $l->status,
                'participants' => isset($notes['participants']) ? count($notes['participants']) : 0,
                'files' => $l->attachments_count,
            ];
        });

        return response()->json([
            'data' => $items,
            'meta' => [
                'total' => $paginator->total(),
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'last_page' => $paginator->lastPage(),
            ]
        ]);
    }

    /**
     * Create a draft Surat Tugas.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'letter_date' => 'required|date',
            'priority' => 'required|in:low,normal,high,urgent',
            'notes' => 'nullable|array',
            'notes.ringkasan' => 'nullable|string',
            'notes.klasifikasi' => 'nullable|string',
            'notes.participants' => 'nullable|array',
            'notes.tujuanInternal' => 'nullable|array',
            'notes.tujuanExternal' => 'nullable|array',
            'notes.start_date' => 'nullable|date',
            'notes.end_date' => 'nullable|date',
            'notes.catatanInternal' => 'nullable',
        ]);

        $letterType = LetterType::where('code', 'ST')->firstOrFail();

        $letter = DB::transaction(function () use ($validated, $letterType) {
            $sequence = LetterNumberSequence::findOrCreate($letterType->id, Auth::user()->department_id);
            // For draft, we can still assign number or leave null; follow FE showing number, so generate now
            $number = $sequence->generateUniqueLetterNumber();

            $letter = Letter::create([
                'letter_number' => $number,
                'subject' => $validated['subject'],
                'letter_date' => $validated['letter_date'],
                'direction' => 'outgoing',
                'status' => 'draft',
                'priority' => $validated['priority'],
                'recipient_name' => $validated['notes']['tujuanInternal'][0] ?? ($validated['notes']['tujuanExternal'][0] ?? null),
                'letter_type_id' => $letterType->id,
                'created_by' => Auth::id(),
                'from_department_id' => Auth::user()->department_id,
                'to_department_id' => null,
                    'notes' => $validated['notes'] ?? null,
            ]);
            return $letter;
        });

        return response()->json([
            'message' => 'Draft surat tugas berhasil dibuat',
            'data' => [
                'id' => $letter->id,
                'number' => $letter->letter_number,
            ]
        ], 201);
    }

    /**
     * Show a single Surat Tugas detail.
     */
    public function show(Letter $letter)
    {
        // authorize type ST and outgoing
        $isST = optional($letter->letterType)->code === 'ST' && $letter->direction === 'outgoing';
        abort_unless($isST, 404);

        $notes = $this->parseNotes($letter->notes);
        return response()->json([
            'id' => $letter->id,
            'number' => $letter->letter_number,
            'subject' => $letter->subject,
            'date' => optional($letter->letter_date)->toDateString(),
            'start' => $notes['start_date'] ?? null,
            'end' => $notes['end_date'] ?? null,
            'priority' => $letter->priority,
            'status' => $letter->status,
            'destination' => $notes['tujuanInternal'][0] ?? ($notes['tujuanExternal'][0] ?? null),
            'notes' => $notes,
            'participants' => $notes['participants'] ?? [],
            'attachments' => $letter->attachments()->get(['id','original_name','file_path','file_type','file_size'])
                ->map(fn($a) => [
                    'id' => $a->id,
                    'name' => $a->original_name,
                    'path' => $a->file_path,
                    'type' => $a->file_type,
                    'size' => $a->file_size,
                ]),
        ]);
    }

    /**
     * Get participants (from notes JSON).
     */
    public function participantsIndex(Letter $letter)
    {
        $this->ensureST($letter);
        $notes = $this->parseNotes($letter->notes);
        return response()->json(['participants' => $notes['participants'] ?? []]);
    }

    /**
     * Add a participant (mutate notes JSON and save).
     */
    public function participantsStore(Request $request, Letter $letter)
    {
        $this->ensureST($letter);
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50',
            'jabatan' => 'nullable|string|max:100',
            'status' => 'nullable|string|max:50',
        ]);

        $notes = $this->parseNotes($letter->notes);
        $participants = $notes['participants'] ?? [];
        $participants[] = $data;
        $notes['participants'] = $participants;
            $letter->notes = $notes;
        $letter->save();

        return response()->json(['message' => 'Peserta ditambahkan', 'participants' => $participants], 201);
    }

    /**
     * Remove a participant by index.
     */
    public function participantsDestroy(Request $request, Letter $letter, int $index)
    {
        $this->ensureST($letter);
        $notes = $this->parseNotes($letter->notes);
        $participants = $notes['participants'] ?? [];
        if (!array_key_exists($index, $participants)) {
            return response()->json(['message' => 'Peserta tidak ditemukan'], 404);
        }
        array_splice($participants, $index, 1);
        $notes['participants'] = $participants;
            $letter->notes = $notes;
        $letter->save();

        return response()->json(['message' => 'Peserta dihapus', 'participants' => $participants]);
    }

    /**
     * History placeholder based on created_at and simple events.
     */
    public function history(Letter $letter)
    {
        $this->ensureST($letter);
        $logs = [
            ['time' => optional($letter->created_at)->toDateTimeString(), 'actor' => 'Sekretariat', 'action' => 'Membuat draft surat', 'status' => 'success'],
        ];
        return response()->json(['logs' => $logs]);
    }

    /**
     * Confirm a draft Surat Tugas to move into signature flow (status: pending).
     */
    public function confirm(Request $request, Letter $letter)
    {
        $this->ensureST($letter);
        if (!in_array($letter->status, ['draft', 'pending'])) {
            return response()->json(['message' => 'Surat tidak dapat dikonfirmasi pada status saat ini'], 422);
        }
        $data = $request->validate([
            'note' => 'nullable|string|max:1000',
        ]);

        // Optionally append note into notes.catatanInternal
        $notes = $this->parseNotes($letter->notes);
        if (!empty($data['note'])) {
            $existing = isset($notes['catatanInternal']) ? trim((string) $notes['catatanInternal']) : '';
            $notes['catatanInternal'] = trim($existing . (strlen($existing) ? "\n" : '') . '[' . now()->toDateTimeString() . "] " . $data['note']);
            $letter->notes = $notes;
        }

        $letter->status = 'closed';
        $letter->save();

        return response()->json(['message' => 'Surat dikonfirmasi untuk tanda tangan', 'status' => $letter->status]);
    }

    /**
     * Change letter status (limited set for ST outgoing letters).
     */
    public function updateStatus(Request $request, Letter $letter)
    {
        $this->ensureST($letter);
        $data = $request->validate([
            'status' => 'required|in:draft,pending,processed,archived,rejected,closed',
            'note' => 'nullable|string|max:1000',
        ]);

        // basic transition rules could be enforced here if needed
        $notes = $this->parseNotes($letter->notes);
        if (!empty($data['note'])) {
            $existing = isset($notes['catatanInternal']) ? trim((string) $notes['catatanInternal']) : '';
            $notes['catatanInternal'] = trim($existing . (strlen($existing) ? "\n" : '') . '[' . now()->toDateTimeString() . "] Status -> {$data['status']}: " . $data['note']);
            $letter->notes = $notes;
        }

        $letter->status = $data['status'];
        $letter->save();

        return response()->json(['message' => 'Status surat diperbarui', 'status' => $letter->status]);
    }

    private function ensureST(Letter $letter): void
    {
        $isST = optional($letter->letterType)->code === 'ST' && $letter->direction === 'outgoing';
        abort_unless($isST, 404);
    }

    private function parseNotes($notes): array
    {
        if (is_array($notes)) return $notes;
        if (is_string($notes) && $notes !== '') {
            try {
                $parsed = json_decode($notes, true, 512, JSON_THROW_ON_ERROR);
                return is_array($parsed) ? $parsed : [];
            } catch (\Throwable $e) {
                return [];
            }
        }
        return [];
    }
}
