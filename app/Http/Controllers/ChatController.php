<?php

namespace App\Http\Controllers;

use App\Http\Requests\MarkAsReadRequest;
use App\Http\Requests\SendMessageRequest;
use App\Http\Requests\StartConversationRequest;
use App\Models\BatDongSan;
use App\Models\Conversation;
use App\Models\KhachHang;
use App\Models\Message;
use App\Models\MoiGioi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Events\MessageSent;

class ChatController extends Controller
{
    public function startConversation(StartConversationRequest $request)
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Co loi xay ra',
            ]);
        }

        if (!($user instanceof KhachHang)) {
            return response()->json([
                'status' => false,
                'message' => 'Chi khach hang moi co the bat dau cuoc chat',
            ], 403);
        }

        $conversation = Conversation::where('khach_hang_id', $user->id)
            ->where('moi_gioi_id', $request->moi_gioi_id)
            ->when($request->bat_dong_san_id, function ($query) use ($request) {
                $query->where('bat_dong_san_id', $request->bat_dong_san_id);
            })
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'khach_hang_id' => $user->id,
                'moi_gioi_id' => $request->moi_gioi_id,
                'bat_dong_san_id' => $request->bat_dong_san_id,
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Lay cuoc chat thanh cong',
            'data' => $conversation,
        ]);
    }

    public function sendMessage(SendMessageRequest $request, $id)
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Co loi xay ra',
            ]);
        }

        $conversation = Conversation::find($id);

        if (!$conversation) {
            return response()->json([
                'status' => false,
                'message' => 'Khong tim thay cuoc chat',
            ], 404);
        }

        if (
            !($user instanceof KhachHang && $conversation->khach_hang_id == $user->id) &&
            !($user instanceof MoiGioi && $conversation->moi_gioi_id == $user->id)
        ) {
            return response()->json([
                'status' => false,
                'message' => 'Ban khong co quyen truy cap cuoc chat nay',
            ], 403);
        }

        $message = DB::transaction(function () use ($request, $user, $conversation) {
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $user->id,
                'sender_type' => $user instanceof KhachHang ? 'khach_hang' : 'moi_gioi',
                'content' => $request->content,
                'type' => $request->type ?? 'text',
                'is_read' => false,
            ]);
        
            $conversation->update([
                'last_message_id' => $message->id,
            ]);
        
            event(new MessageSent($message));
        
            return $message;
        });

        return response()->json([
            'status' => true,
            'message' => 'Gui tin nhan thanh cong',
            'data' => $message,
        ]);
    }

    public function getConversations()
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Co loi xay ra',
            ]);
        }

        $query = Conversation::with(['khachHang', 'moiGioi', 'batDongSan', 'lastMessage'])
            ->latest();

        if ($user instanceof KhachHang) {
            $query->where('khach_hang_id', $user->id);
        } elseif ($user instanceof MoiGioi) {
            $query->where('moi_gioi_id', $user->id);
        }

        $data = $query->get();

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    public function getMessages($id)
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Co loi xay ra',
            ]);
        }

        $conversation = Conversation::with(['khachHang', 'moiGioi', 'batDongSan'])
            ->find($id);

        if (!$conversation) {
            return response()->json([
                'status' => false,
                'message' => 'Khong tim thay cuoc chat',
            ], 404);
        }

        if (
            !($user instanceof KhachHang && $conversation->khach_hang_id == $user->id) &&
            !($user instanceof MoiGioi && $conversation->moi_gioi_id == $user->id)
        ) {
            return response()->json([
                'status' => false,
                'message' => 'Ban khong co quyen truy cap cuoc chat nay',
            ], 403);
        }

        $data = Message::where('conversation_id', $conversation->id)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    public function markAsRead(MarkAsReadRequest $request, $id)
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Co loi xay ra',
            ]);
        }

        $conversation = Conversation::find($id);

        if (!$conversation) {
            return response()->json([
                'status' => false,
                'message' => 'Khong tim thay cuoc chat',
            ], 404);
        }

        if (
            !($user instanceof KhachHang && $conversation->khach_hang_id == $user->id) &&
            !($user instanceof MoiGioi && $conversation->moi_gioi_id == $user->id)
        ) {
            return response()->json([
                'status' => false,
                'message' => 'Ban khong co quyen truy cap cuoc chat nay',
            ], 403);
        }

        Message::where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'status' => true,
            'message' => 'Da danh dau da doc',
        ]);
    }
}
