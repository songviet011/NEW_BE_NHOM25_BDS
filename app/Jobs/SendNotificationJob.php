<?php

namespace App\Jobs;

use App\Models\BatDongSan;
use App\Models\KhachHang;
use App\Models\YeuThich;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

// ============================================================
// JOB: SendNotificationJob  
// Mục đích: Gửi thông báo đến khách hàng (ASYNC - không block API)
// 
// Cách sử dụng:
//   SendNotificationJob::dispatch($batDongSan, $khachHang, 'BDS mới phù hợp');
//   hoặc SendNotificationJob::dispatch($batDongSan, null, 'BDS mới'); // gửi tất cả
//
// Cấu hình Queue:
//   php artisan queue:work (development)
//   hoặc cấu hình Supervisor trong production
// ============================================================
class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Thông tin về BDS vừa tạo/update
    protected BatDongSan $batDongSan;

    // Khách hàng cụ thể (nếu null thì gửi tất cả)
    protected ?KhachHang $khachHang;

    // Nội dung thông báo
    protected string $message;

    /**
     * Constructor đơn giản - lưu dữ liệu vào properties
     */
    public function __construct(BatDongSan $batDongSan, ?KhachHang $khachHang = null, string $message = '')
    {
        $this->batDongSan = $batDongSan;
        $this->khachHang = $khachHang;
        $this->message = $message ?: "Có BDS mới phù hợp với bạn";
    }

    /**
     * Handle: Logic chính của job
     * Hàm này sẽ được gọi khi queue worker xử lý job
     */
    public function handle(): void
    {
        try {
            // Nếu có khách hàng cụ thể -> gửi cho 1 người
            if ($this->khachHang) {
                $this->sendToCustomer($this->khachHang);
            } else {
                // Gửi cho tất cả khách hàng có yêu thích liên quan
                $this->sendToAllRelevantCustomers();
            }

            // TODO: Log thành công nếu cần
        } catch (\Exception $e) {
            // TODO: Log error, hoặc send sentry notification
            logger()->error('SendNotificationJob failed: ' . $e->getMessage());
        }
    }

    /**
     * Gửi thông báo cho 1 khách hàng cụ thể
     */
    private function sendToCustomer(KhachHang $khachHang): void
    {
        // TODO: Thay thế bằng thực tế (Email, SMS, Push notification, v.v)
        // Ví dụ:
        //   Mail::to($khachHang->email)->queue(new BatDongSanNotification(...));
        //   hoặc $khachHang->notify(new BatDongSanNotification(...));

        logger()->info("Notification sent to customer {$khachHang->id}: {$this->message}");
    }

    /**
     * Gửi thông báo cho tất cả khách hàng liên quan
     * (có yêu thích cùng loại, cùng vị trí, v.v)
     */
    private function sendToAllRelevantCustomers(): void
    {
        // TODO: Thay thế bằng logic thực tế
        // Ví dụ: Tìm các khách hàng có yêu thích loại BDS này, trong khu vực này
        // rồi gửi thông báo cho từng người

        // Dummy: Log
        logger()->info("Broadcast notification for BDS: {$this->message}");
    }
}
