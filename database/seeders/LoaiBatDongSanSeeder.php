<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LoaiBatDongSanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('loai_bat_dong_sans')->insert([
            [
                'ten_loai' => 'Căn hộ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten_loai' => 'Nhà phố',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten_loai' => 'Nhà riêng',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten_loai' => 'Đất nền',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten_loai' => 'Kho xưởng',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten_loai' => 'Văn phòng',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten_loai' => 'Cửa hàng',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten_loai' => 'Trang trại',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
