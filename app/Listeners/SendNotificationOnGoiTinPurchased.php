<?php

namespace App\Listeners;

use App\Events\GoiTinPurchased;

// ============================================================
// LISTENER: SendNotificationOnGoiTinPurchased
// Mục đích: Khi MUA GÓI TIN
//          - Gửi email xác nhận
//          - Cập nhật hạn ngạch/quota cho môi giới
//          - Ghi lịch sử mua gói tin
//
// TODO: Thêm job để handle:
//   - SendEmailJob (gửi invoice email)
//   - UpdateQuotaJob (cập nhật số tin có thể đăng)
//   - CreateInvoiceJob (tạo hóa đơn)
// ============================================================
class SendNotificationOnGoiTinPurchased
{
    /**
     * Handle the event.
     * 
     * @param GoiTinPurchased $event
     * @return void
     */
    public function handle(GoiTinPurchased $event): void
    {
        // TODO: Gửi email xác nhận mua gói tin cho môi giới
        // SendEmailJob::dispatch($event->moiGioi, 'purchase-confirmation', [
        //     'goi_tin' => $event->goiTin,
        //     'quantity' => $event->quantity,
        //     'total_price' => $event->totalPrice,
        // ]);

        // TODO: Cập nhật hạn ngạch (quota) của môi giới
        // - Tăng số tin còn lại có thể đăng
        // - Update field so_tin_co_the_dang hoặc tương tự
        // $event->moiGioi->increment('so_tin_co_the_dang', $event->quantity);
        // hoặc
        // UpdateQuotaJob::dispatch($event->moiGioi, $event->goiTin, $event->quantity);

        // TODO: Ghi lịch sử mua gói tin (LichSuGoiTin)
        // LichSuGoiTin::create([
        //     'goi_tin_id' => $event->goiTin->id,
        //     'moi_gioi_id' => $event->moiGioi->id,
        //     'so_luong' => $event->quantity,
        //     'tong_tien' => $event->totalPrice,
        //     'trang_thai' => 'completed', // TODO: nếu thanh toán thành công
        // ]);

        logger()->info(
            "GoiTinPurchased - Package purchased",
            [
                'goi_tin_id' => $event->goiTin->id,
                'moi_gioi_id' => $event->moiGioi->id,
                'quantity' => $event->quantity,
                'total_price' => $event->totalPrice,
            ]
        );
    }
}
