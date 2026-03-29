<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrangThaiBatDongSanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('trang_thai_bat_dong_sans')->insert([
            [
                'ten_trang_thai' => 'Chưa duyệt',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten_trang_thai' => 'Đã duyệt',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten_trang_thai' => 'Đã bán',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten_trang_thai' => 'Cho thuê',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten_trang_thai' => 'Đã hết hạn',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten_trang_thai' => 'Bị từ chối',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
