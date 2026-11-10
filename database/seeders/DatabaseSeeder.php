<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Panggil KantingoSeeder di sini untuk mengisi tabel Kantingo
        $this->call([
            KantingoSeeder::class,
        ]);
    }
}