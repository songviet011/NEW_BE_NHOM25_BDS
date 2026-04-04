<?php

namespace App\Events;

use App\Models\BatDongSan;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// ============================================================
// EVENT: BatDongSanCreated
// Mục đích: Trigger khi tạo mới 1 bất động sản
// Sự kiện này sẽ gọi các Listeners để xử lý:
//   - Gửi thông báo cho khách hàng yêu thích
//   - Ghi lịch sử
//   - Dispatch job AI định giá (async)
// ============================================================
class BatDongSanCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // Model BatDongSan được tạo
    public BatDongSan $batDongSan;

    public function __construct(BatDongSan $batDongSan)
    {
        // Lưu lại instance của BDS vừa tạo
        // SerializesModels sẽ tự động serialize model này khi push vào queue
        $this->batDongSan = $batDongSan;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
