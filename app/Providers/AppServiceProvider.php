<?php

namespace App\Providers;

use App\Events\BatDongSanCreated;
use App\Events\BatDongSanUpdated;
use App\Events\GiaoDichCreated;
use App\Events\GoiTinPurchased;

use App\Listeners\SendNotificationOnBatDongSanCreated;
use App\Listeners\SendNotificationOnBatDongSanUpdated;
use App\Listeners\SendNotificationOnGiaoDichCreated;
use App\Listeners\SendNotificationOnGoiTinPurchased;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     * 
     * Nơi đăng ký Event Listeners
     * Khi Event được fire -> Listener tương ứng sẽ được gọi
     * 
     * ============================================================
     * EVENT-LISTENER MAPPING
     * ============================================================
     */
    public function boot(): void
    {
        // (1) BatDongSanCreated - Khi TẠO BDS mới
        // Listener: Gửi thông báo + dispatch AI định giá job
        $this->app['events']->listen(
            BatDongSanCreated::class,
            SendNotificationOnBatDongSanCreated::class
        );

        // (2) BatDongSanUpdated - Khi UPDATE BDS
        // Listener: Gửi thông báo + retrigger AI định giá (nếu field quan trọng thay đổi)
        $this->app['events']->listen(
            BatDongSanUpdated::class,
            SendNotificationOnBatDongSanUpdated::class
        );

        // (3) GiaoDichCreated - Khi TẠO giao dịch mới
        // Listener: Gửi thông báo cho buyer & seller
        // TODO: Thêm listener khác: Cập nhật thống kê, Tính hoa hồng
        $this->app['events']->listen(
            GiaoDichCreated::class,
            SendNotificationOnGiaoDichCreated::class
        );

        // (4) GoiTinPurchased - Khi MUA gói tin
        // Listener: Ghi lịch sử, Cập nhật quota, Gửi email
        // TODO: Thêm listener khác: Tạo invoice, Cập nhật doanh số
        $this->app['events']->listen(
            GoiTinPurchased::class,
            SendNotificationOnGoiTinPurchased::class
        );
    }
}
