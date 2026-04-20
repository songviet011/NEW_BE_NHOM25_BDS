<?php

namespace App\Listeners;

use App\Events\BatDongSanDuocYeuThich;
use App\Models\ThongBao;

class TaoThongBaoKhiYeuThich
{
    public function handle(BatDongSanDuocYeuThich $event): void
    {
        $khachHang = $event->khachHang;
        $batDongSan = $event->batDongSan;

        ThongBao::create([
            'moi_gioi_id' => $batDongSan->moi_gioi_id,
            'khach_hang_id' => $khachHang->id,
            'bat_dong_san_id' => $batDongSan->id,
            'tieu_de' => 'Khach hang vua tuong tac bat dong san',
            'noi_dung' => "Khach hang {$khachHang->ten} da tha tim BDS {$batDongSan->tieu_de}",
            'trang_thai' => 0,
        ]);
    }
}