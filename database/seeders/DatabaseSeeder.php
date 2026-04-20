<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed dữ liệu cơ bản
        $this->call([
            TinhThanhSeeder::class,
            QuanHuyenSeeder::class,
            ChucNangSeeder::class,
            PhanQuyenSeeder::class,
            LoaiBatDongSanSeeder::class,
            TrangThaiBatDongSanSeeder::class,
            GoiTinSeeder::class,
            AdminSeeder::class,
            MoiGioiSeeder::class,
            KhachHangSeeder::class,
            DiaChiSeeder::class,
            BatDongSanSeeder::class,
            GiaoDichSeeder::class,
            HinhAnhBatDongSanSeeder::class,
            LichSuGoiTinSeeder::class,
            YeuThichSeeder::class,
            ThongBaoSeeder::class,
            AIDinhGiaSeeder::class,
            LichSuDinhGiaSeeder::class,
            ChatSeeder::class,
        ]);

        // Seed user test
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
