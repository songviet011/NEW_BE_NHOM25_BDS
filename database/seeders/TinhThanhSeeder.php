<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TinhThanhSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tinh_thanhs')->insert([
            [
                'ten' => 'TP. Hồ Chí Minh',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten' => 'Hà Nội',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten' => 'Đà Nẵng',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten' => 'Bình Dương',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten' => 'Đồng Nai',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten' => 'Bà Rịa - Vũng Tàu',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten' => 'Long An',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten' => 'Tiền Giang',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten' => 'Cần Thơ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten' => 'Kiên Giang',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
