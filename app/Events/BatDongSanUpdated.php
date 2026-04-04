<?php

namespace App\Events;

use App\Models\BatDongSan;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// ============================================================
// EVENT: BatDongSanUpdated
// Mục đích: Trigger khi UPDATE bất động sản (thay đổi thông tin)
// Sự kiện này sẽ gọi các Listeners để xử lý:
//   - Gửi thông báo cập nhật cho khách hàng yêu thích
//   - Ghi lịch sử thay đổi
//   - Dispatch job tính lại giá (nếu thay đổi thông tin quan trọng)
// ============================================================
class BatDongSanUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // Model BatDongSan sau khi update
    public BatDongSan $batDongSan;

    // Dữ liệu cũ (để so sánh, biết field nào thay đổi)
    public array $oldData;

    public function __construct(BatDongSan $batDongSan, array $oldData = [])
    {
        // Lưu instance BDS vừa update
        $this->batDongSan = $batDongSan;

        // Lưu oldData để listener có thể so sánh
        // Ví dụ: nếu giá thay đổi -> trigger re-pricing
        $this->oldData = $oldData;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
