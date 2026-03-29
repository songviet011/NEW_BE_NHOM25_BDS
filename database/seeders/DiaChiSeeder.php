<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiaChiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('dia_chis')->insert([
            [
                'tinh_id' => 1,
                'quan_id' => 1,
                'dia_chi_chi_tiet' => '123 Đường Nguyễn Huệ, Phường Bến Nghé',
                'lat' => 10.7769,
                'lng' => 106.7009,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tinh_id' => 1,
                'quan_id' => 4,
                'dia_chi_chi_tiet' => '456 Đường Lê Lợi, Phường Bến Thành',
                'lat' => 10.7738,
                'lng' => 106.6981,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tinh_id' => 1,
                'quan_id' => 5,
                'dia_chi_chi_tiet' => '789 Đường Nguyễn Văn Trỗi, Phường 1',
                'lat' => 10.7892,
                'lng' => 106.7654,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tinh_id' => 4,
                'quan_id' => 8,
                'dia_chi_chi_tiet' => '321 Đường Quốc Lộ 1K, Thị trấn Dĩ An',
                'lat' => 10.8074,
                'lng' => 106.7903,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tinh_id' => 4,
                'quan_id' => 9,
                'dia_chi_chi_tiet' => '654 Đường Phạm Hùng, Phường Hiệp Thành',
                'lat' => 10.8295,
                'lng' => 106.7462,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tinh_id' => 1,
                'quan_id' => 2,
                'dia_chi_chi_tiet' => '987 Đường Nguyễn Duy Trinh, Quận 2',
                'lat' => 10.7970,
                'lng' => 106.7661,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
