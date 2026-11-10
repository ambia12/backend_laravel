<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KantingoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Data Dummy Meja (Tables)
        DB::table('tables')->insert([
            ['table_number' => 'Meja 01', 'qr_code_token' => Str::random(16), 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],
            ['table_number' => 'Meja 02', 'qr_code_token' => Str::random(16), 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],
            ['table_number' => 'Meja 03', 'qr_code_token' => Str::random(16), 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 2. Data Dummy Kategori Menu (Categories)
        $catMakanan = DB::table('categories')->insertGetId([
            'name' => 'Makanan Berat',
            'slug' => 'makanan-berat',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $catMinuman = DB::table('categories')->insertGetId([
            'name' => 'Minuman Segar',
            'slug' => 'minuman-segar',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // 3. Data Dummy Menu (Menus)
        DB::table('menus')->insert([
            [
                'category_id' => $catMakanan,
                'name' => 'Mie Setan Level 1',
                'price' => 15000,
                'description' => 'Mie pedas nikmat dengan level cabai pilihan khas Kantingo',
                'image' => 'mie_setan.jpg',
                'is_available' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'category_id' => $catMakanan,
                'name' => 'Nasi Goreng Spesial',
                'price' => 18000,
                'description' => 'Nasi goreng gurih lengkap dengan telur mata sapi dan ayam suwir',
                'image' => 'nasgor.jpg',
                'is_available' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'category_id' => $catMinuman,
                'name' => 'Es Teh Manis',
                'price' => 5000,
                'description' => 'Teh melati segar manis pelepas dahaga',
                'image' => 'es_teh.jpg',
                'is_available' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'category_id' => $catMinuman,
                'name' => 'Es Jeruk Peras',
                'price' => 7000,
                'description' => 'Jeruk murni peras segar kaya vitamin C',
                'image' => 'es_jeruk.jpg',
                'is_available' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}