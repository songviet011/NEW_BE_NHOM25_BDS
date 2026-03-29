<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class YeuThichSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('yeu_thichs')->insert([
            [
                'moi_gioi_id' => 1,
                'khach_hang_id' => 1,
                'bds_id' => 1,
                'noi_dung' => 'Khách hàng quan tâm đến bất động sản này',
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'moi_gioi_id' => 1,
                'khach_hang_id' => 2,
                'bds_id' => 2,
                'noi_dung' => 'Khách hàng muốn xem thêm thông tin',
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'moi_gioi_id' => 2,
                'khach_hang_id' => 1,
                'bds_id' => 3,
                'noi_dung' => 'Khách hàng yêu cầu dẫn xem bất động sản',
                'is_read' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'moi_gioi_id' => 3,
                'khach_hang_id' => 3,
                'bds_id' => 4,
                'noi_dung' => 'Khách hàng hỏi về giá cả',
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'moi_gioi_id' => 4,
                'khach_hang_id' => 4,
                'bds_id' => 5,
                'noi_dung' => 'Khách hàng quan tâm',
                'is_read' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
