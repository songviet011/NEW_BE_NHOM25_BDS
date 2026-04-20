<?php

namespace App\Events;

use App\Models\BatDongSan;
use App\Models\KhachHang;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BatDongSanDuocYeuThich
{
    use Dispatchable, SerializesModels;

    public KhachHang $khachHang;
    public BatDongSan $batDongSan;

    public function __construct(KhachHang $khachHang, BatDongSan $batDongSan)
    {
        $this->khachHang = $khachHang;
        $this->batDongSan = $batDongSan;
    }
}