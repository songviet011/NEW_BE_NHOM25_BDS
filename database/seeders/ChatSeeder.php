<?php

namespace Database\Seeders;

use App\Models\BatDongSan;
use App\Models\Conversation;
use App\Models\KhachHang;
use App\Models\Message;
use App\Models\MoiGioi;
use Illuminate\Database\Seeder;

class ChatSeeder extends Seeder
{
    public function run(): void
    {
        $khachHang = KhachHang::first();
        $moiGioi = MoiGioi::first();
        $batDongSan = BatDongSan::first();

        if (!$khachHang || !$moiGioi || !$batDongSan) {
            return;
        }

        $conversation = Conversation::firstOrCreate([
            'khach_hang_id' => $khachHang->id,
            'moi_gioi_id' => $moiGioi->id,
            'bat_dong_san_id' => $batDongSan->id,
        ], [
            'last_message_id' => null,
        ]);

        $message1 = Message::firstOrCreate([
            'conversation_id' => $conversation->id,
            'sender_id' => $khachHang->id,
            'sender_type' => 'khach_hang',
            'content' => 'Xin chao',
        ], [
            'type' => 'text',
            'is_read' => true,
        ]);

        Message::firstOrCreate([
            'conversation_id' => $conversation->id,
            'sender_id' => $moiGioi->id,
            'sender_type' => 'moi_gioi',
            'content' => 'Chao ban, toi co the ho tro gi?',
        ], [
            'type' => 'text',
            'is_read' => false,
        ]);

        $conversation->update([
            'last_message_id' => $message1->id,
        ]);
    }
}
