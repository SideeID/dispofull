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

class UnitKerjaController extends Controller
{
    public function arsipSuratTugas()
    {
        return view('pages.unit_kerja.arsip-surat-tugas.index');
    }

    public function buatSurat()
    {
        $letterTypes = LetterType::where('is_active', true)
            ->orderBy('name')
            ->get(['id','name','code','number_format']);
        $userDeptId = Auth::user()->department_id;
        return view('pages.unit_kerja.buat-surat.index', compact('letterTypes','userDeptId'));
    }

    public function suratMasuk()
    {
        return view('pages.unit_kerja.surat-masuk.index');
    }

    /* =============================================================
     *  API: MASTER DATA
     * ============================================================= */

    /**
     * Get active letter types (for dropdown Jenis Surat)
     */
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
        $format = $letterType->number_format ?? '{number}/{code}/{month}/{year}';
        $replacements = [
            '{number}' => str_pad($nextNumber, 3, '0', STR_PAD_LEFT),
            '{code}' => $letterType->code,
            '{department_code}' => $department->code ?? '',
            '{month}' => now()->format('m'),
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

            $finalNumber = $sequence->generateLetterNumber();

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

    /* =============================================================
     *  Helpers
     * ============================================================= */
    private function generateDraftNumber(int $letterTypeId, int $userId): string
    {
        $ts = now()->format('YmdHis');
        $rand = substr(bin2hex(random_bytes(3)),0,6);
        return "DRAFT/$letterTypeId/$userId/$ts/$rand"; // unique placeholder
    }
}
