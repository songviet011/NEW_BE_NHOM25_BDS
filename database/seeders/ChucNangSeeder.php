<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChucNangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('chuc_nangs')->insert([
            [
                'ten_chuc_nang' => 'Xem danh sách bất động sản',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten_chuc_nang' => 'Tạo tin bất động sản',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten_chuc_nang' => 'Chỉnh sửa tin bất động sản',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten_chuc_nang' => 'Xóa tin bất động sản',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten_chuc_nang' => 'Duyệt tin bất động sản',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten_chuc_nang' => 'Quản lý gói tin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten_chuc_nang' => 'Quản lý giao dịch',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten_chuc_nang' => 'Quản lý người dùng',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten_chuc_nang' => 'Quản lý admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten_chuc_nang' => 'Xem thống kê báo cáo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
