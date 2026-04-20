<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChucVuSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('chuc_vus')->upsert([
            ['id' => 1, 'ten_chuc_vu' => 'Nhân Viên Kiểm Duyệt', 'slug_chuc_vu' => Str::slug('Nhân Viên Kiểm Duyệt')],
            ['id' => 2, 'ten_chuc_vu' => 'Admin', 'slug_chuc_vu' => Str::slug('Admin')],
        ], ['id'], ['ten_chuc_vu', 'slug_chuc_vu']);
    }
}
