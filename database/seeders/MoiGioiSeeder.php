<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MoiGioiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('moi_giois')->insertOrIgnore([
            [
                'ten' => 'Nguyễn Văn An',
                'email' => 'nva@bds.com',
                'so_dien_thoai' => '0901234567',
                'password' => Hash::make('123456789'),
                'avatar' => null,
                'mo_ta' => 'Môi giới bất động sản chuyên nghiệp, kinh nghiệm 5 năm',
                'zalo_link' => 'https://zalo.me/0901234567',
                'is_active' => true,
                'trang_thai' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten' => 'Trần Thị Bình',
                'email' => 'ttb@bds.com',
                'so_dien_thoai' => '0912345678',
                'password' => Hash::make('123456789'),
                'avatar' => null,
                'mo_ta' => 'Chuyên môi giới các dự án bất động sản cao cấp',
                'zalo_link' => 'https://zalo.me/0912345678',
                'is_active' => true,
                'trang_thai' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten' => 'Lê Minh Khánh',
                'email' => 'lmk@bds.com',
                'so_dien_thoai' => '0923456789',
                'password' => Hash::make('123456789'),
                'avatar' => null,
                'mo_ta' => 'Móigiới đất nền, nhà riêng tại khu vực Bình Dương',
                'zalo_link' => 'https://zalo.me/0923456789',
                'is_active' => true,
                'trang_thai' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten' => 'Phạm Hương Giang',
                'email' => 'phg@bds.com',
                'so_dien_thoai' => '0934567890',
                'password' => Hash::make('123456789'),
                'avatar' => null,
                'mo_ta' => 'Chuyên môi giới căn hộ chung cư tại TPHCM',
                'zalo_link' => 'https://zalo.me/0934567890',
                'is_active' => true,
                'trang_thai' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten' => 'Đỗ Quang Huy',
                'email' => 'dqh@bds.com',
                'so_dien_thoai' => '0945678901',
                'password' => Hash::make('123456789'),
                'avatar' => null,
                'mo_ta' => 'Móigiới bất động sản thương mại, văn phòng',
                'zalo_link' => 'https://zalo.me/0945678901',
                'is_active' => false,
                'trang_thai' => 'inactive',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
