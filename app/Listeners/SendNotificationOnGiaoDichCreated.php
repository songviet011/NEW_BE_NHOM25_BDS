<?php

namespace App\Listeners;

use App\Events\GiaoDichCreated;
use App\Jobs\SendNotificationJob;

// ============================================================
// LISTENER: SendNotificationOnGiaoDichCreated
// Mục đích: Khi giao dịch được TẠO MỚI
//          - Gửi thông báo cho buyer & seller
//          - Ghi lịch sử
//          - Cập nhật thống kê
//
// TODO: Thêm listeners khác để handle:
//   - Tính hoa hồng/commission cho môi giới
//   - Update trạng thái BDS -> "đã bán"
//   - Tạo invoice/hóa đơn
// ============================================================
class SendNotificationOnGiaoDichCreated
{
    /**
     * Handle the event.
     * 
     * @param GiaoDichCreated $event
     * @return void
     */
    public function handle(GiaoDichCreated $event): void
    {
        // Load relationships để có đủ thông tin
        $gaoDich = $event->gaoDich->load(['batDongSan', 'khachHang', 'moiGioi']);

        // TODO: Gửi thông báo cho khách hàng (buyer)
        if ($gaoDich->khachHang) {
            SendNotificationJob::dispatch(
                $gaoDich->batDongSan,
                $gaoDich->khachHang,
                "Giao dịch '{$gaoDich->batDongSan->tieu_de}' được khởi tạo"
            );
        }

        // TODO: Gửi thông báo cho môi giới (seller)
        if ($gaoDich->moiGioi) {
            SendNotificationJob::dispatch(
                $gaoDich->batDongSan,
                null, // TODO: nếu có khách hàng cho môi giới, thêm vào đây
                "Có giao dịch mới cho BDS '{$gaoDich->batDongSan->tieu_de}'"
            );
        }

        logger()->info(
            "GiaoDich created - notifications sent for transaction ID: {$gaoDich->id}",
            [
                'bds_id' => $gaoDich->batDongSan->id,
                'khach_hang_id' => $gaoDich->khachHang?->id,
                'moi_gioi_id' => $gaoDich->moiGioi?->id,
            ]
        );
    }
}
