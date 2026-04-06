<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhanQuyenSeeder extends Seeder
{
    public function run(): void
{
    DB::table('phan_quyens')->delete();

    // Lấy TẤT CẢ chức năng trong hệ thống
    $all_permissions = DB::table('chuc_nangs')->pluck('id')->toArray();

    // Admin (id_chuc_vu = 2) có TẤT CẢ quyền
    foreach ($all_permissions as $permission) {
        DB::table('phan_quyens')->insert([[
            'id_chuc_vu' => 2,  // ID của Admin
            'id_chuc_nang' => $permission,
        ]]);
    }

    // Các role khác (Nhân Viên...) chỉ có quyền cụ thể
    $hr_permissions = [5];
    foreach ($hr_permissions as $permission) {
        DB::table('phan_quyens')->insert([[
            'id_chuc_vu' => 1,  // ID của Nhân Viên
            'id_chuc_nang' => $permission,
        ]]);
    }
}
}