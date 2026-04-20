<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        \App\Events\BatDongSanDuocYeuThich::class => [
            \App\Listeners\TaoThongBaoKhiYeuThich::class,
        ],
        \App\Events\BatDongSanMoiDang::class => [
            \App\Listeners\TaoThongBaoBDSMoi::class,
        ],
    ];
}
