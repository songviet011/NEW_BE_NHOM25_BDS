<?php

namespace App\Listeners;

use App\Events\BatDongSanMoiDang;
use App\Models\KhachHang;
use App\Models\ThongBao;

class TaoThongBaoBDSMoi
{
    public function handle(BatDongSanMoiDang $event): void
    {
        $batDongSan = $event->batDongSan;

        $khachHangs = KhachHang::where('is_active', true)->get();

        foreach ($khachHangs as $khachHang) {
            ThongBao::create([
                'moi_gioi_id' => $batDongSan->moi_gioi_id,
                'khach_hang_id' => $khachHang->id,
                'bat_dong_san_id' => $batDongSan->id,
                'tieu_de' => 'Có bất động sản mới',
                'noi_dung' => "Môi giới vừa đăng BĐS {$batDongSan->tieu_de}",
                'trang_thai' => 0,
            ]);
        }
    }
}