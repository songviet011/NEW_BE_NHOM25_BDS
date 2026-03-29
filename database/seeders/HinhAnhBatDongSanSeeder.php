<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HinhAnhBatDongSanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('hinh_anh_bat_dong_sans')->insert([
            [
                'bds_id' => 1,
                'url' => 'https://cms.luatvietnam.vn/uploaded/Images/Original/2018/09/29/nha-dang-the-chap_2909092944.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'bds_id' => 1,
                'url' => 'https://file4.batdongsan.com.vn/crop/562x284/2026/03/23/20260323155317-63ba_wm.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'bds_id' => 2,
                'url' => 'https://cms.luatvietnam.vn/uploaded/Images/Original/2018/09/29/nha-dang-the-chap_2909092944.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'bds_id' => 2,
                'url' => 'https://cms.luatvietnam.vn/uploaded/Images/Original/2018/09/29/nha-dang-the-chap_2909092944.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'bds_id' => 3,
                'url' => 'https://cms.luatvietnam.vn/uploaded/Images/Original/2018/09/29/nha-dang-the-chap_2909092944.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'bds_id' => 4,
                'url' => 'https://cms.luatvietnam.vn/uploaded/Images/Original/2018/09/29/nha-dang-the-chap_2909092944.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'bds_id' => 5,
                'url' => 'https://images.unsplash.com/photo-1505228395891-9a51e7e86e81?w=800',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'bds_id' => 5,
                'url' => 'https://images.unsplash.com/photo-1522499881294-f32e3153c5e9?w=800',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
