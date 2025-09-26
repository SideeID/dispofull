<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LetterTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $letterTypes = [
            [
                'name' => 'Surat Keputusan',
                'code' => 'SK',
                'description' => 'Surat Keputusan',
                'number_format' => '{number}/SK/{department_code}/{month}/{year}',
                'is_active' => true
            ],
            [
                'name' => 'Surat Tugas',
                'code' => 'ST',
                'description' => 'Surat Tugas',
                'number_format' => '{number}/ST/{department_code}/{month}/{year}',
                'is_active' => true
            ],
            [
                'name' => 'Surat Edaran',
                'code' => 'SE',
                'description' => 'Surat Edaran',
                'number_format' => '{number}/SE/{department_code}/{month}/{year}',
                'is_active' => true
            ],
            [
                'name' => 'Surat Undangan',
                'code' => 'SU',
                'description' => 'Surat Undangan',
                'number_format' => '{number}/SU/{department_code}/{month}/{year}',
                'is_active' => true
            ],
            [
                'name' => 'Surat Permohonan',
                'code' => 'SP',
                'description' => 'Surat Permohonan',
                'number_format' => '{number}/SP/{department_code}/{month}/{year}',
                'is_active' => true
            ],
            [
                'name' => 'Surat Keterangan',
                'code' => 'SKet',
                'description' => 'Surat Keterangan',
                'number_format' => '{number}/SKet/{department_code}/{month}/{year}',
                'is_active' => true
            ],
            [
                'name' => 'Surat Rekomendasi',
                'code' => 'SR',
                'description' => 'Surat Rekomendasi',
                'number_format' => '{number}/SR/{department_code}/{month}/{year}',
                'is_active' => true
            ],
            [
                'name' => 'Nota Dinas',
                'code' => 'ND',
                'description' => 'Nota Dinas',
                'number_format' => '{number}/ND/{department_code}/{month}/{year}',
                'is_active' => true
            ],
            [
                'name' => 'Berita Acara',
                'code' => 'BA',
                'description' => 'Berita Acara',
                'number_format' => '{number}/BA/{department_code}/{month}/{year}',
                'is_active' => true
            ],
            [
                'name' => 'Surat Perjanjian',
                'code' => 'SPj',
                'description' => 'Surat Perjanjian/Kontrak',
                'number_format' => '{number}/SPj/{department_code}/{month}/{year}',
                'is_active' => true
            ],
            [
                'name' => 'Surat Masuk Umum',
                'code' => 'SM',
                'description' => 'Surat Masuk dari Pihak Eksternal',
                'number_format' => '{number}/SM/{month}/{year}',
                'is_active' => true
            ]
        ];

        foreach ($letterTypes as $letterType) {
            \App\Models\LetterType::create($letterType);
        }
    }
}
