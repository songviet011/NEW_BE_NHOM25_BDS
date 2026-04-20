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

        // Kiểm tra role admin có tồn tại không
        $adminRole = DB::table('chuc_vus')->where('id', 2)->first();
        $staffRole = DB::table('chuc_vus')->where('id', 1)->first();

        if ($adminRole) {
            foreach ($all_permissions as $permission) {
                DB::table('phan_quyens')->insert([
                    [
                        'id_chuc_vu' => $adminRole->id,
                        'id_chuc_nang' => $permission,
                    ]
                ]);
            }
        }

        if ($staffRole) {
            $hr_permissions = [5];
            foreach ($hr_permissions as $permission) {
                DB::table('phan_quyens')->insert([
                    [
                        'id_chuc_vu' => $staffRole->id,
                        'id_chuc_nang' => $permission,
                    ]
                ]);
            }
        }
    }
}
