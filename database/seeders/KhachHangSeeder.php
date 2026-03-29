<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class KhachHangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('khach_hangs')->insert([
            [
                'ten' => 'Hoàng Văn A',
                'email' => 'hva@example.com',
                'so_dien_thoai' => '0987654321',
                'password' => Hash::make('123456789'),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten' => 'Vũ Thị B',
                'email' => 'vtb@example.com',
                'so_dien_thoai' => '0976543210',
                'password' => Hash::make('123456789'),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten' => 'Phan Minh C',
                'email' => 'pmc@example.com',
                'so_dien_thoai' => '0965432109',
                'password' => Hash::make('123456789'),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten' => 'Đặng Hải D',
                'email' => 'dhd@example.com',
                'so_dien_thoai' => '0954321098',
                'password' => Hash::make('123456789'),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten' => 'Bùi Kim E',
                'email' => 'bke@example.com',
                'so_dien_thoai' => '0943210987',
                'password' => Hash::make('123456789'),
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
