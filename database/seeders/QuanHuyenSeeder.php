<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuanHuyenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('quan_huyens')->insert([
            // TP. Hồ Chí Minh (tinh_id = 1)
            [
                'ten' => 'Quận 1',
                'tinh_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten' => 'Quận 2',
                'tinh_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten' => 'Quận 3',
                'tinh_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten' => 'Quận 7',
                'tinh_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten' => 'Quận Bình Thạnh',
                'tinh_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten' => 'Quận Tân Bình',
                'tinh_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten' => 'Quận Tân Phú',
                'tinh_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten' => 'Thị xã Dĩ An',
                'tinh_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten' => 'Thành phố Thuận An',
                'tinh_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten' => 'Huyện Bắc Tân Uyên',
                'tinh_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
