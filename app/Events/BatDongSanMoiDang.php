<?php

namespace App\Events;

use App\Models\BatDongSan;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BatDongSanMoiDang
{
    use Dispatchable, SerializesModels;

    public BatDongSan $batDongSan;

    public function __construct(BatDongSan $batDongSan)
    {
        $this->batDongSan = $batDongSan;
    }
}