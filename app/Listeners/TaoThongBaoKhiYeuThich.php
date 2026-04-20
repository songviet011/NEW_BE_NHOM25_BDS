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

        ThongBao::updateOrCreate(
            [
                'moi_gioi_id' => $batDongSan->moi_gioi_id,
                'khach_hang_id' => $khachHang->id,
                'bat_dong_san_id' => $batDongSan->id,
            ],
            [
                'tieu_de' => 'Khách hàng vừa tương tác bất động sản',
                'noi_dung' => "Khách hàng {$khachHang->ten} đã thả tim BĐS {$batDongSan->tieu_de}",
                'trang_thai' => 0 // luôn là chưa đọc khi có tương tác mới
            ]
        );
    }
}
