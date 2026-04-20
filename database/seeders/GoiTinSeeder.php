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
        DB::table('goi_tins')->insertOrIgnore([
            [
                'ten_goi' => 'Gói Cơ Bản',
                'mo_ta' => 'Gói tin cơ bản cho người mới bắt đầu',
                'gia' => 50000,
                'so_ngay' => 7,
                'so_luong_tin' => 3,
                'trang_thai' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten_goi' => 'Gói Tiêu Chuẩn',
                'mo_ta' => 'Gói tin tiêu chuẩn với nhiều tính năng hơn',
                'gia' => 100000,
                'so_ngay' => 15,
                'so_luong_tin' => 10,
                'trang_thai' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten_goi' => 'Gói VIP',
                'mo_ta' => 'Gói tin VIP dành cho môi giới chuyên nghiệp',
                'gia' => 250000,
                'so_ngay' => 30,
                'so_luong_tin' => 30,
                'trang_thai' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten_goi' => 'Gói Cao Cấp',
                'mo_ta' => 'Gói tin cao cấp với đầy đủ các tính năng nâng cao',
                'gia' => 500000,
                'so_ngay' => 60,
                'so_luong_tin' => 100,
                'trang_thai' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
