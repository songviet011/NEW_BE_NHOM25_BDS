<?php

namespace App\Listeners;

use App\Events\BatDongSanUpdated;
use App\Jobs\SendNotificationJob;
use App\Jobs\AIDefinePriceJob;

// ============================================================
// LISTENER: SendNotificationOnBatDongSanUpdated
// Mục đích: Khi BDS được UPDATE
//          - Gửi thông báo cho khách hàng yêu thích
//          - Re-trigger AI định giá nếu thông tin quan trọng thay đổi
//
// Kiểm tra fields thay đổi:
//   Nếu thay đổi giá, diện tích, loại -> trigger AI định giá lại
// ============================================================
class SendNotificationOnBatDongSanUpdated
{
    /**
     * Handle the event.
     * 
     * @param BatDongSanUpdated $event
     * @return void
     */
    public function handle(BatDongSanUpdated $event): void
    {
        // TODO: Gửi thông báo cho khách hàng yêu thích cập nhật
        SendNotificationJob::dispatch(
            $event->batDongSan,
            null,
            "BDS '{$event->batDongSan->tieu_de}' vừa được cập nhật"
        );

        // Kiểm tra fields quan trọng thay đổi -> trigger re-pricing
        if ($this->shouldRetriggerPricing($event->oldData, $event->batDongSan)) {
            // TODO: Dispatch job AI định giá lại
            AIDefinePriceJob::dispatch($event->batDongSan);
            logger()->info("Re-trigger AI pricing for BDS {$event->batDongSan->id}");
        }

        logger()->info(
            "BatDongSan updated - notification sent for BDS ID: {$event->batDongSan->id}"
        );
    }

    /**
     * Kiểm tra xem có nên trigger lại AI định giá không
     * 
     * @param array $oldData Dữ liệu cũ
     * @param \App\Models\BatDongSan $batDongSan BDS sau update
     * @return bool
     */
    private function shouldRetriggerPricing(array $oldData, $batDongSan): bool
    {
        // TODO: Định nghĩa fields quan trọng
        // Nếu có 1 trong những field này thay đổi -> retrigger pricing

        $importantFields = ['dien_tich', 'loai_id', 'tinh_id', 'quan_id'];

        foreach ($importantFields as $field) {
            if (isset($oldData[$field]) && $oldData[$field] != $batDongSan->{$field}) {
                return true;
            }
        }

        return false;
    }
}
