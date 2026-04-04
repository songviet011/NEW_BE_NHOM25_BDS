<?php

namespace App\Listeners;

use App\Events\BatDongSanCreated;
use App\Jobs\SendNotificationJob;

// ============================================================
// LISTENER: SendNotificationOnBatDongSanCreated
// Mục đích: Khi Event BatDongSanCreated được fire
//          Listener này sẽ dispatch SendNotificationJob vào queue
//
// Flow:
//   Controller tạo BDS -> fire BatDongSanCreated event
//                      -> Listener listen & dispatch Job
//                      -> Job vào queue (database)
//                      -> Queue worker xử lý (async - không block API)
// ============================================================
class SendNotificationOnBatDongSanCreated
{
    /**
     * Handle the event.
     * 
     * @param BatDongSanCreated $event Event được fire từ Controller/Model
     * @return void
     */
    public function handle(BatDongSanCreated $event): void
    {
        // Dispatch SendNotificationJob vào queue
        // Được xử lý async - không chờ kết quả
        SendNotificationJob::dispatch(
            $event->batDongSan,
            null, // null = gửi tất cả khách hàng liên quan
            "Có BDS mới: {$event->batDongSan->tieu_de}"
        );

        // Log để dễ debug
        logger()->info(
            "Notification job dispatched for BDS ID: {$event->batDongSan->id}",
            ['bds_title' => $event->batDongSan->tieu_de]
        );
    }
}
