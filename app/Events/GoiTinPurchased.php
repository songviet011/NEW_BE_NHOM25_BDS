<?php

namespace App\Events;

use App\Models\GoiTin;
use App\Models\MoiGioi;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// ============================================================
// EVENT: GoiTinPurchased
// Mục đích: Trigger khi MUA gói tin (package)
// Sự kiện này sẽ gọi các Listeners để xử lý:
//   - Gửi email xác nhận mua hàng
//   - Cập nhật hạn ngạch (quota) đăng tin cho môi giới
//   - Ghi lịch sử mua (LichSuGoiTin)
//   - Ghi thống kê doanh thu
//   - Tạo invoice/hóa đơn
// ============================================================
class GoiTinPurchased
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // Model GoiTin được mua
    public GoiTin $goiTin;

    // Môi giới/người mua
    public MoiGioi $moiGioi;

    // Số lượng gói mua
    public int $quantity;

    // Tổng tiền
    public float $totalPrice;

    public function __construct(GoiTin $goiTin, MoiGioi $moiGioi, int $quantity, float $totalPrice)
    {
        // Lưu thông tin gói tin
        $this->goiTin = $goiTin;

        // Lưu thông tin môi giới/khách mua
        $this->moiGioi = $moiGioi;

        // Số lượng
        $this->quantity = $quantity;

        // Tổng tiền thanh toán
        $this->totalPrice = $totalPrice;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
