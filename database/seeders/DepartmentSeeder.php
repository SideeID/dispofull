<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            // Rektorat
            [
                'name' => 'Rektorat',
                'code' => 'REKT',
                'description' => 'Kantor Rektorat Universitas Bakrie',
                'type' => 'rektorat',
                'is_active' => true
            ],
            [
                'name' => 'Wakil Rektor I (Akademik)',
                'code' => 'WR1',
                'description' => 'Wakil Rektor I Bidang Akademik',
                'type' => 'rektorat',
                'is_active' => true
            ],
            [
                'name' => 'Wakil Rektor II (Keuangan & SDM)',
                'code' => 'WR2',
                'description' => 'Wakil Rektor II Bidang Keuangan dan SDM',
                'type' => 'rektorat',
                'is_active' => true
            ],
            [
                'name' => 'Wakil Rektor III (Kemahasiswaan)',
                'code' => 'WR3',
                'description' => 'Wakil Rektor III Bidang Kemahasiswaan',
                'type' => 'rektorat',
                'is_active' => true
            ],

            // Unit Kerja
            [
                'name' => 'Biro Administrasi Akademik (BAA)',
                'code' => 'BAA',
                'description' => 'Biro Administrasi Akademik',
                'type' => 'unit_kerja',
                'is_active' => true
            ],
            [
                'name' => 'Fakultas Teknik',
                'code' => 'FT',
                'description' => 'Fakultas Teknik',
                'type' => 'unit_kerja',
                'is_active' => true
            ],
            [
                'name' => 'Fakultas Ekonomi dan Bisnis',
                'code' => 'FEB',
                'description' => 'Fakultas Ekonomi dan Bisnis',
                'type' => 'unit_kerja',
                'is_active' => true
            ],
            [
                'name' => 'Fakultas Hukum',
                'code' => 'FH',
                'description' => 'Fakultas Hukum',
                'type' => 'unit_kerja',
                'is_active' => true
            ],
            [
                'name' => 'Fakultas Komunikasi dan Bahasa',
                'code' => 'FKB',
                'description' => 'Fakultas Komunikasi dan Bahasa',
                'type' => 'unit_kerja',
                'is_active' => true
            ],
            [
                'name' => 'Program Studi Teknik Informatika',
                'code' => 'PRODI-TI',
                'description' => 'Program Studi Teknik Informatika',
                'type' => 'unit_kerja',
                'is_active' => true
            ],
            [
                'name' => 'Program Studi Manajemen',
                'code' => 'PRODI-MAN',
                'description' => 'Program Studi Manajemen',
                'type' => 'unit_kerja',
                'is_active' => true
            ],
            [
                'name' => 'Pusat Penelitian dan Pengabdian Masyarakat',
                'code' => 'P3M',
                'description' => 'Pusat Penelitian dan Pengabdian Masyarakat',
                'type' => 'unit_kerja',
                'is_active' => true
            ],
            [
                'name' => 'Biro Teknologi Informasi (BTI)',
                'code' => 'BTI',
                'description' => 'Biro Teknologi Informasi',
                'type' => 'unit_kerja',
                'is_active' => true
            ]
        ];

        foreach ($departments as $department) {
            \App\Models\Department::create($department);
        }
    }
}
