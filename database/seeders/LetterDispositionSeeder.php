<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Letter;
use App\Models\LetterDisposition;
use App\Models\User;

class LetterDispositionSeeder extends Seeder
{
    public function run(): void
    {
        $letters = Letter::where('direction','incoming')->latest()->take(3)->get();
        $rektor = User::where('username','rektor')->first();
        $admin = User::where('username','admin')->first();
        $staff = User::where('username','staff_ti')->first();
        $kepalaBaa = User::where('username','kepala_baa')->first();
        if (!$letters->count() || !$rektor) return;

        foreach ($letters as $idx => $letter) {
            // First disposition from Rektor to staff/kepala
            if ($staff) {
                LetterDisposition::create([
                    'letter_id' => $letter->id,
                    'from_user_id' => $rektor->id,
                    'to_user_id' => $staff->id,
                    'instruction' => 'Mohon kajian akademik dan rekomendasi.',
                    'priority' => 'normal',
                    'status' => 'pending'
                ]);
            }
            if ($kepalaBaa) {
                LetterDisposition::create([
                    'letter_id' => $letter->id,
                    'from_user_id' => $rektor->id,
                    'to_user_id' => $kepalaBaa->id,
                    'instruction' => 'Verifikasi administrasi dan laporkan hasilnya.',
                    'priority' => 'high',
                    'status' => $idx % 2 === 0 ? 'in_progress' : 'pending'
                ]);
            }
        }
    }
}
