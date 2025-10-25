<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\LetterAgenda;
use App\Models\Letter;
use App\Models\LetterType;
use App\Models\Department;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class AgendaController extends Controller
{
    /**
     * Display agenda management page
     */
    public function index()
    {
        return view('pages.agenda.index');
    }

    /**
     * Get list of agendas with filters
     */
    public function getAgendas(Request $request)
    {
        $user = Auth::user();
        
        $query = LetterAgenda::with(['department', 'creator']);

        // Filter by role - check role string directly
        if ($user->role === 'unit_kerja') {
            $query->where('department_id', $user->department_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('agenda_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('agenda_date', '<=', $request->date_to);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $agendas = $query->orderBy('agenda_date', 'desc')
            ->paginate(10);

        // Add letter count for each agenda
        foreach ($agendas as $agenda) {
            $agenda->letter_count = $agenda->getFilteredLetters()->count();
        }

        return response()->json([
            'data' => $agendas->items(),
            'meta' => [
                'current_page' => $agendas->currentPage(),
                'last_page' => $agendas->lastPage(),
                'per_page' => $agendas->perPage(),
                'total' => $agendas->total(),
            ]
        ]);
    }

    /**
     * Get agenda details with letters
     */
    public function show($id)
    {
        $agenda = LetterAgenda::with(['department', 'creator'])->findOrFail($id);
        
        $letters = $agenda->getFilteredLetters();

        return response()->json([
            'data' => [
                'agenda' => $agenda,
                'letters' => $letters,
                'stats' => [
                    'total' => $letters->count(),
                    'by_type' => $letters->groupBy('letterType.code')->map->count(),
                    'by_direction' => $letters->groupBy('direction')->map->count(),
                ]
            ]
        ]);
    }

    /**
     * Create new agenda
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:daily,weekly,monthly',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'department_id' => 'nullable|exists:departments,id',
            'filters' => 'nullable|array',
            'filters.letter_types' => 'nullable|array',
            'filters.departments' => 'nullable|array',
            'filters.direction' => 'nullable|string|in:incoming,outgoing',
            'filters.status' => 'nullable|array',
        ]);

        // Set agenda_date based on type
        $agendaDate = $this->calculateAgendaDate($validated['type'], $validated['start_date'], $validated['end_date']);

        $agenda = LetterAgenda::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'agenda_date' => $agendaDate,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'type' => $validated['type'],
            'department_id' => $validated['department_id'] ?? $user->department_id,
            'created_by' => $user->id,
            'status' => 'draft',
            'filters' => $validated['filters'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Agenda berhasil dibuat',
            'data' => $agenda
        ], 201);
    }

    /**
     * Update agenda
     */
    public function update(Request $request, $id)
    {
        $agenda = LetterAgenda::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'sometimes|required|in:daily,weekly,monthly',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after_or_equal:start_date',
            'department_id' => 'nullable|exists:departments,id',
            'filters' => 'nullable|array',
        ]);

        // Recalculate agenda_date if dates or type changed
        if (isset($validated['type']) || isset($validated['start_date']) || isset($validated['end_date'])) {
            $type = $validated['type'] ?? $agenda->type;
            $startDate = $validated['start_date'] ?? Carbon::parse($agenda->start_date)->format('Y-m-d');
            $endDate = $validated['end_date'] ?? Carbon::parse($agenda->end_date)->format('Y-m-d');
            $validated['agenda_date'] = $this->calculateAgendaDate($type, $startDate, $endDate);
        }

        $agenda->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Agenda berhasil diupdate',
            'data' => $agenda
        ]);
    }

    /**
     * Publish agenda
     */
    public function publish($id)
    {
        $agenda = LetterAgenda::findOrFail($id);

        if ($agenda->status === 'published') {
            return response()->json([
                'success' => false,
                'message' => 'Agenda sudah dipublikasikan'
            ], 422);
        }

        $agenda->update(['status' => 'published']);

        return response()->json([
            'success' => true,
            'message' => 'Agenda berhasil dipublikasikan'
        ]);
    }

    /**
     * Archive agenda
     */
    public function archive($id)
    {
        $agenda = LetterAgenda::findOrFail($id);

        $agenda->update(['status' => 'archived']);

        return response()->json([
            'success' => true,
            'message' => 'Agenda berhasil diarsipkan'
        ]);
    }

    /**
     * Delete agenda
     */
    public function destroy($id)
    {
        $agenda = LetterAgenda::findOrFail($id);

        // Delete PDF file if exists
        if ($agenda->pdf_path && Storage::exists($agenda->pdf_path)) {
            Storage::delete($agenda->pdf_path);
        }

        $agenda->delete();

        return response()->json([
            'success' => true,
            'message' => 'Agenda berhasil dihapus'
        ]);
    }

    /**
     * Generate and download PDF
     */
    public function exportPdf($id)
    {
        $agenda = LetterAgenda::with(['department', 'creator'])->findOrFail($id);
        $letters = $agenda->getFilteredLetters();

        // Group letters by date
        $lettersByDate = $letters->groupBy(function($letter) {
            return Carbon::parse($letter->letter_date)->format('Y-m-d');
        });

        $pdf = Pdf::loadView('pdf.agenda', [
            'agenda' => $agenda,
            'letters' => $letters,
            'lettersByDate' => $lettersByDate,
        ]);

        $pdf->setPaper('a4', 'portrait');

        // Save PDF
        $filename = 'agenda_' . $agenda->type . '_' . Carbon::parse($agenda->start_date)->format('Ymd') . '_' . Carbon::parse($agenda->end_date)->format('Ymd') . '.pdf';
        $path = 'agendas/' . $filename;
        
        Storage::put($path, $pdf->output());
        
        // Update agenda with PDF path
        $agenda->update(['pdf_path' => $path]);

        // Download PDF
        return $pdf->download($filename);
    }

    /**
     * Preview PDF in browser
     */
    public function previewPdf($id)
    {
        $agenda = LetterAgenda::with(['department', 'creator'])->findOrFail($id);
        $letters = $agenda->getFilteredLetters();

        $lettersByDate = $letters->groupBy(function($letter) {
            return Carbon::parse($letter->letter_date)->format('Y-m-d');
        });

        $pdf = Pdf::loadView('pdf.agenda', [
            'agenda' => $agenda,
            'letters' => $letters,
            'lettersByDate' => $lettersByDate,
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('preview_agenda.pdf');
    }

    /**
     * Get filter options (letter types, departments)
     */
    public function getFilterOptions()
    {
        $letterTypes = LetterType::select('id', 'name', 'code')->get();
        $departments = Department::select('id', 'name', 'code')->get();

        return response()->json([
            'data' => [
                'letter_types' => $letterTypes,
                'departments' => $departments,
                'statuses' => [
                    ['value' => 'draft', 'label' => 'Draft'],
                    ['value' => 'active', 'label' => 'Aktif'],
                    ['value' => 'pending', 'label' => 'Pending'],
                    ['value' => 'processed', 'label' => 'Diproses'],
                    ['value' => 'signed', 'label' => 'Ditandatangani'],
                    ['value' => 'published', 'label' => 'Dipublikasi'],
                    ['value' => 'archived', 'label' => 'Diarsipkan'],
                ]
            ]
        ]);
    }

    /**
     * Auto-generate agenda for specific period
     */
    public function autoGenerate(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'type' => 'required|in:daily,weekly,monthly',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $type = $validated['type'];
        $departmentId = $validated['department_id'] ?? $user->department_id;

        // Calculate date range based on type
        $dates = $this->calculateDateRange($type);

        // Check if agenda already exists
        $exists = LetterAgenda::where('type', $type)
            ->where('start_date', $dates['start'])
            ->where('end_date', $dates['end'])
            ->where('department_id', $departmentId)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Agenda untuk periode ini sudah ada'
            ], 422);
        }

        // Create agenda
        $agenda = LetterAgenda::create([
            'title' => $this->generateTitle($type, $dates['start'], $dates['end']),
            'description' => 'Auto-generated agenda',
            'agenda_date' => $dates['agenda'],
            'start_date' => $dates['start'],
            'end_date' => $dates['end'],
            'type' => $type,
            'department_id' => $departmentId,
            'created_by' => $user->id,
            'status' => 'draft',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Agenda berhasil di-generate otomatis',
            'data' => $agenda
        ], 201);
    }

    /**
     * Calculate agenda date based on type and period
     */
    private function calculateAgendaDate($type, $startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        switch ($type) {
            case 'daily':
                return $start->format('Y-m-d');
            case 'weekly':
                return $start->format('Y-m-d');
            case 'monthly':
                return $start->startOfMonth()->format('Y-m-d');
            default:
                return $start->format('Y-m-d');
        }
    }

    /**
     * Calculate date range for auto-generation
     */
    private function calculateDateRange($type)
    {
        $now = now();

        switch ($type) {
            case 'daily':
                return [
                    'start' => $now->format('Y-m-d'),
                    'end' => $now->format('Y-m-d'),
                    'agenda' => $now->format('Y-m-d'),
                ];
            case 'weekly':
                return [
                    'start' => $now->startOfWeek()->format('Y-m-d'),
                    'end' => $now->endOfWeek()->format('Y-m-d'),
                    'agenda' => $now->startOfWeek()->format('Y-m-d'),
                ];
            case 'monthly':
                return [
                    'start' => $now->startOfMonth()->format('Y-m-d'),
                    'end' => $now->endOfMonth()->format('Y-m-d'),
                    'agenda' => $now->startOfMonth()->format('Y-m-d'),
                ];
        }
    }

    /**
     * Generate title for auto-generated agenda
     */
    private function generateTitle($type, $start, $end)
    {
        $startDate = Carbon::parse($start);
        $endDate = Carbon::parse($end);

        switch ($type) {
            case 'daily':
                return 'Agenda Harian - ' . $startDate->format('d M Y');
            case 'weekly':
                return 'Agenda Mingguan - ' . $startDate->format('d M') . ' s/d ' . $endDate->format('d M Y');
            case 'monthly':
                return 'Agenda Bulanan - ' . $startDate->format('F Y');
            default:
                return 'Agenda Surat';
        }
    }
}
