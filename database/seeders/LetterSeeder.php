<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Letter;
use App\Models\LetterType;
use App\Models\Department;
use App\Models\LetterNumberSequence;
use App\Models\User;
use Illuminate\Support\Str;

class LetterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have basics
        $incomingType = LetterType::where('code','SM')->first();
        if (!$incomingType) return; // letter types not seeded

        $rektorat = Department::where('code','REKT')->first();
        $wr1 = Department::where('code','WR1')->first();
        $p3m = Department::where('code','P3M')->first();
        $baa = Department::where('code','BAA')->first();
        $st = Department::where('code','ST')->first();

        $rektorUser = User::where('username','rektor')->first();
        $adminUser = User::where('username','admin')->first();

        if (!$rektorat || !$rektorUser) return; // base data incomplete

        $data = [
            [
                'subject' => 'Permohonan Kerja Sama Penelitian',
                'sender_name' => 'Kementerian Pendidikan',
                'priority' => 'high',
                'status' => 'pending',
                'from_department_id' => $wr1?->id,
                'to_department_id' => $rektorat->id,
                'received_at' => now()->subDays(1)->setTime(8,30),
            ],
            [
                'subject' => 'Surat Tugas Dosen Pembimbing',
                'sender_name' => 'Rektorat',
                'priority' => 'urgent',
                'status' => 'closed',
                'from_department_id' => $rektorat->id,
                'to_department_id' => $st?->id,
                'received_at' => now()->subDays(5)->setTime(14,0),
                'processed_at' => now()->subDays(4),
            ],
            [
                'subject' => 'Undangan Seminar Nasional',
                'sender_name' => 'Universitas Negeri A',
                'priority' => 'normal',
                'status' => 'processed',
                'from_department_id' => $p3m?->id,
                'to_department_id' => $rektorat->id,
                'received_at' => now()->subDays(2)->setTime(9,0),
                'processed_at' => now()->subDay(),
            ],
            [
                'subject' => 'Rekap Yudisium Program Studi',
                'sender_name' => 'BAA',
                'priority' => 'low',
                'status' => 'processed',
                'from_department_id' => $baa?->id,
                'to_department_id' => $rektorat->id,
                'received_at' => now()->subDays(3)->setTime(10,15),
                'processed_at' => now()->subDays(2),
            ],
            [
                'subject' => 'Usulan Kerjasama Industri',
                'sender_name' => 'Wakil Rektor II',
                'priority' => 'urgent',
                'status' => 'pending',
                'from_department_id' => $wr1?->id,
                'to_department_id' => $rektorat->id,
                'received_at' => now()->subDay()->setTime(11,45),
            ],
        ];

        foreach ($data as $i => $row) {
            $sequence = LetterNumberSequence::findOrCreate($incomingType->id, null, now()->year);
            $letterNumber = $sequence->generateLetterNumber();
            Letter::create(array_merge([
                'letter_number' => $letterNumber,
                'content' => 'Konten placeholder untuk surat '.$row['subject'],
                'direction' => 'incoming',
                'letter_type_id' => $incomingType->id,
                'created_by' => $adminUser?->id ?? $rektorUser->id,
                'letter_date' => now()->subDays($i+1)->toDateString(),
            ], $row));
        }
    }
}
