<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $this->call([
            DepartmentSeeder::class,
            LetterTypeSeeder::class,
            DashboardTableSeeder::class,
            UserSeeder::class,
            LetterSeeder::class,
            LetterDispositionSeeder::class,
            LetterAttachmentSeeder::class,
        ]);
    }
}
