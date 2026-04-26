<?php

namespace App\Events;

use App\Models\BatDongSan;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PropertyExpired implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $propertyId;
    public $userId;
    public $trangThaiId;
    public $isDuyet;

    public function __construct(BatDongSan $property)
    {
        $this->propertyId = $property->id;
        $this->userId = $property->moi_gioi_id; // hoặc moi_gioi_id tùy bảng
        $this->trangThaiId = 6;
        $this->isDuyet = 0;
    }

    public function broadcastOn()
    {
        return [
            new PrivateChannel('user.' . $this->userId),
            new PrivateChannel('admin'),
        ];
    }

    public function broadcastWith()
    {
        return [
            'property_id' => $this->propertyId,
            'trang_thai_id' => $this->trangThaiId,
            'is_duyet' => $this->isDuyet,
            'message' => 'Tin đăng đã tự động hết hạn sau 15 ngày.'
        ];
    }
}