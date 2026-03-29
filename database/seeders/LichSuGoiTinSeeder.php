<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LichSuGoiTinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('lich_su_goi_tins')->insert([
            [
                'moi_gioi_id' => 1,
                'goi_tin_id' => 1,
                'ngay_bat_dau' => '2026-03-01',
                'ngay_ket_thuc' => '2026-03-08',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'moi_gioi_id' => 1,
                'goi_tin_id' => 2,
                'ngay_bat_dau' => '2026-03-09',
                'ngay_ket_thuc' => '2026-03-24',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'moi_gioi_id' => 2,
                'goi_tin_id' => 3,
                'ngay_bat_dau' => '2026-03-10',
                'ngay_ket_thuc' => '2026-04-09',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'moi_gioi_id' => 3,
                'goi_tin_id' => 1,
                'ngay_bat_dau' => '2026-03-15',
                'ngay_ket_thuc' => '2026-03-22',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'moi_gioi_id' => 4,
                'goi_tin_id' => 2,
                'ngay_bat_dau' => '2026-03-20',
                'ngay_ket_thuc' => '2026-04-04',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
