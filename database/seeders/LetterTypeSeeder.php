<?php

namespace Database\Seeders;

use App\Models\LetterType;
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
                'name' => 'Surat Keluar',
                'code' => 'SKel',
                'description' => 'Surat Keluar Umum',
                'number_format' => '{number}/SKel/{department_code}/{month}/{year}',
                'is_active' => true
            ],
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
                'name' => 'Surat Pengumuman',
                'code' => 'SPeng',
                'description' => 'Surat Pengumuman',
                'number_format' => '{number}/SPeng/{department_code}/{month}/{year}',
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
                'name' => 'Internal Memo',
                'code' => 'IM',
                'description' => 'Internal Memo',
                'number_format' => '{number}/IM/{department_code}/{month}/{year}',
                'is_active' => true
            ],
            [
                'name' => 'Surat Ijin Atasan',
                'code' => 'SIA',
                'description' => 'Surat Ijin Atasan',
                'number_format' => '{number}/SIA/{department_code}/{month}/{year}',
                'is_active' => true
            ],
            [
                'name' => 'Surat Perintah',
                'code' => 'SPth',
                'description' => 'Surat Perintah',
                'number_format' => '{number}/SPth/{department_code}/{month}/{year}',
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
                'name' => 'Surat Pernyataan',
                'code' => 'SPny',
                'description' => 'Surat Pernyataan',
                'number_format' => '{number}/SPny/{department_code}/{month}/{year}',
                'is_active' => true
            ],
            [
                'name' => 'Surat Peringatan',
                'code' => 'SPering',
                'description' => 'Surat Peringatan',
                'number_format' => '{number}/SPering/{department_code}/{month}/{year}',
                'is_active' => true
            ],
            [
                'name' => 'Surat Pernyataan Tanggung Jawab Mutlak',
                'code' => 'SPTJM',
                'description' => 'Surat Pernyataan Tanggung Jawab Mutlak',
                'number_format' => '{number}/SPTJM/{department_code}/{month}/{year}',
                'is_active' => true
            ],
            [
                'name' => 'Surat Kuasa',
                'code' => 'SKu',
                'description' => 'Surat Kuasa',
                'number_format' => '{number}/SKu/{department_code}/{month}/{year}',
                'is_active' => true
            ],
            [
                'name' => 'Pakta Integritas',
                'code' => 'PI',
                'description' => 'Pakta Integritas',
                'number_format' => '{number}/PI/{department_code}/{month}/{year}',
                'is_active' => true
            ],
            [
                'name' => 'Surat Perintah Kerja',
                'code' => 'SPK',
                'description' => 'Surat Perintah Kerja',
                'number_format' => '{number}/SPK/{department_code}/{month}/{year}',
                'is_active' => true
            ],
            [
                'name' => 'Surat Keputusan Rektor',
                'code' => 'SKR',
                'description' => 'Surat Keputusan Rektor',
                'number_format' => '{number}/SKR/{department_code}/{month}/{year}',
                'is_active' => true
            ],
            [
                'name' => 'Peraturan Rektor',
                'code' => 'PRR',
                'description' => 'Peraturan Rektor',
                'number_format' => '{number}/PRR/{department_code}/{month}/{year}',
                'is_active' => true
            ],
            [
                'name' => 'Peraturan Universitas',
                'code' => 'PRU',
                'description' => 'Peraturan Universitas',
                'number_format' => '{number}/PRU/{department_code}/{month}/{year}',
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
                'name' => 'Memorandum of Understanding',
                'code' => 'MOU',
                'description' => 'Dokumen MOU',
                'number_format' => '{number}/MOU/{department_code}/{month}/{year}',
                'is_active' => true
            ],
            [
                'name' => 'Perjanjian Kerja Sama',
                'code' => 'PKS',
                'description' => 'Perjanjian Kerja Sama',
                'number_format' => '{number}/PKS/{department_code}/{month}/{year}',
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
            LetterType::firstOrCreate(['code' => $letterType['code']], $letterType);
        }
    }
}
