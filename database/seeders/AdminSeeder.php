<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('admins')->insert([
            [
                'ten' => 'Admin Super',
                'email' => 'admin@bds.com',
                'password' => Hash::make('123456789'),
                'is_super' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten' => 'Admin Duyệt Tin',
                'email' => 'duyet@bds.com',
                'password' => Hash::make('123456789'),
                'is_super' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten' => 'Admin Quản Lý',
                'email' => 'quanly@bds.com',
                'password' => Hash::make('123456789'),
                'is_super' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
