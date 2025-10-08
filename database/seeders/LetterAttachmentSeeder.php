<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Letter;
use App\Models\LetterAttachment;
use App\Models\User;

class LetterAttachmentSeeder extends Seeder
{
    public function run(): void
    {
        $letters = Letter::where('direction','incoming')->latest()->take(3)->get();
        $admin = User::where('username','admin')->first();
        if (!$letters->count() || !$admin) return;

        foreach ($letters as $letter) {
            LetterAttachment::create([
                'letter_id' => $letter->id,
                'original_name' => 'proposal_kerjasama.pdf',
                'file_name' => 'proposal_kerjasama.pdf',
                'file_path' => 'letters/attachments/proposal_kerjasama.pdf',
                'file_type' => 'pdf',
                'file_size' => 1200000,
                'description' => 'Draft proposal kerjasama',
                'uploaded_by' => $admin->id,
            ]);
            LetterAttachment::create([
                'letter_id' => $letter->id,
                'original_name' => 'profil_mitra.pdf',
                'file_name' => 'profil_mitra.pdf',
                'file_path' => 'letters/attachments/profil_mitra.pdf',
                'file_type' => 'pdf',
                'file_size' => 860000,
                'description' => 'Profil singkat mitra',
                'uploaded_by' => $admin->id,
            ]);
        }
    }
}
