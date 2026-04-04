<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AIDinhGiaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $batDongSanId = DB::table('bat_dong_sans')->value('id');

        if (!$batDongSanId) {
            return;
        }

        DB::table('a_i_dinh_gias')->insert([
            [
                'bat_dong_san_id' => $batDongSanId,
                'gia_du_doan' => 3200000000,
                'do_tin_cay' => 0.82,
                'ly_do' => 'Seeded AI pricing result',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'bat_dong_san_id' => $batDongSanId,
                'gia_du_doan' => 3350000000,
                'do_tin_cay' => 0.88,
                'ly_do' => 'Seeded AI pricing result',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
