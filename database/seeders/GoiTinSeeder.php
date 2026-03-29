<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GoiTinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('goi_tins')->insert([
            [
                'ten_goi' => 'Gói Cơ Bản',
                'gia' => 50000,
                'so_ngay' => 7,
                'so_luong_tin' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten_goi' => 'Gói Tiêu Chuẩn',
                'gia' => 100000,
                'so_ngay' => 15,
                'so_luong_tin' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten_goi' => 'Gói VIP',
                'gia' => 250000,
                'so_ngay' => 30,
                'so_luong_tin' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten_goi' => 'Gói Cao Cấp',
                'gia' => 500000,
                'so_ngay' => 60,
                'so_luong_tin' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
