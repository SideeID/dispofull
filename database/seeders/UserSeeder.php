<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin BTI',
            'username' => 'admin',
            'email' => 'admin@bakrie.ac.id',
            'password' => bcrypt('123'),
            'role' => 'admin',
            'status' => 'active',
            'nip' => '1234567890',
            'position' => 'Administrator Sistem',
            'department_id' => 13, // BTI
        ]);

        User::create([
            'name' => 'Rektor',
            'username' => 'rektor',
            'email' => 'rektor@bakrie.ac.id',
            'password' => bcrypt('123'),
            'role' => 'rektorat',
            'status' => 'active',
            'nip' => '1234567891',
            'position' => 'Rektor',
            'department_id' => 1, // Rektorat
        ]);

        User::create([
            'name' => 'Kepala BAA',
            'username' => 'kepala_baa',
            'email' => 'baa@bakrie.ac.id',
            'password' => bcrypt('123'),
            'role' => 'unit_kerja',
            'status' => 'active',
            'nip' => '1234567892',
            'position' => 'Kepala Biro Administrasi Akademik',
            'department_id' => 5, // BAA
        ]);

        User::create([
            'name' => 'Staff Prodi TI',
            'username' => 'staff_ti',
            'email' => 'ti@bakrie.ac.id',
            'password' => bcrypt('123'),
            'role' => 'unit_kerja',
            'status' => 'active',
            'nip' => '1234567893',
            'position' => 'Staff Program Studi',
            'department_id' => 10, // Prodi TI
        ]);
    }
}
