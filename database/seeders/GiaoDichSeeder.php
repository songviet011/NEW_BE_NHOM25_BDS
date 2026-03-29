<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GiaoDichSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('giao_dichs')->insert([
            [
                'moi_gioi_id' => 1,
                'goi_tin_id' => 1,
                'so_tien' => 50000,
                'phuong_thuc' => 'bank',
                'trang_thai' => 'success',
                'ma_giao_dich' => 'GD001-2026-03-29',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'moi_gioi_id' => 2,
                'goi_tin_id' => 2,
                'so_tien' => 100000,
                'phuong_thuc' => 'momo',
                'trang_thai' => 'success',
                'ma_giao_dich' => 'GD002-2026-03-29',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'moi_gioi_id' => 3,
                'goi_tin_id' => 3,
                'so_tien' => 250000,
                'phuong_thuc' => 'bank',
                'trang_thai' => 'pending',
                'ma_giao_dich' => 'GD003-2026-03-29',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'moi_gioi_id' => 4,
                'goi_tin_id' => 2,
                'so_tien' => 100000,
                'phuong_thuc' => 'cash',
                'trang_thai' => 'success',
                'ma_giao_dich' => 'GD004-2026-03-29',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'moi_gioi_id' => 1,
                'goi_tin_id' => 4,
                'so_tien' => 500000,
                'phuong_thuc' => 'bank',
                'trang_thai' => 'fail',
                'ma_giao_dich' => 'GD005-2026-03-29',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
