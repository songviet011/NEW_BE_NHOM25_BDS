<?php

namespace App\Events;

use App\Models\GiaoDich;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// ============================================================
// EVENT: GiaoDichCreated
// Mục đích: Trigger khi TẠO giao dịch mới
// Sự kiện này sẽ gọi các Listeners để xử lý:
//   - Gửi email/thông báo cho buyer & seller
//   - Ghi lịch sử giao dịch
//   - Cập nhật thống kê (số giao dịch, doanh số)
//   - Tính hoa hồng cho môi giới
//   - Update trạng thái BDS
// ============================================================
class GiaoDichCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // Model GiaoDich vừa tạo
    public GiaoDich $gaoDich;

    public function __construct(GiaoDich $gaoDich)
    {
        // Lưu instance giao dịch vừa tạo
        // Có thể load với relationships: load(['batDongSan', 'khachHang', 'moiGioi'])
        $this->gaoDich = $gaoDich;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
