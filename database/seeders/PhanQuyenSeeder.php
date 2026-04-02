<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhanQuyenSeeder extends Seeder
{

    public function run(): void
    {
        DB::table('phan_quyens')->delete();
        DB::table('phan_quyens')->truncate();
        // Nhân Viên Kiểm Duyệt
        $hr_permissions = [5];
        foreach ($hr_permissions as $permission) {
            DB::table('phan_quyens')->insert([
                ['id_chuc_vu' => 1, 'id_chuc_nang' => $permission],
            ]);
        }

        $all_permissions = DB::table('chuc_nangs')->pluck('id')->toArray();
        foreach ($all_permissions as $permission) {
            DB::table('phan_quyens')->insert([
                'id_chuc_vu' => 2,
                'id_chuc_nang' => $permission,
            ]);
        }
    }
}
