<?php

namespace App\Http\Controllers;

use App\Models\ThongBao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThongBaoController extends Controller
{
    // Lấy danh sách thông báo
    public function getThongBao()
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $data = ThongBao::where('moi_gioi_id', $user->id)
            ->with(['khachHang', 'batDongSan'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    public function sseStream(Request $request)
    {
        return app(SSEController::class)->stream($request);
    }
    
    // Đánh dấu đã đọc tất cả
    public function markAllAsRead()
    {
        $user = Auth::guard('sanctum')->user();
        
        ThongBao::where('moi_gioi_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        
        return response()->json(['success' => true]);
    }
    
    // Đánh dấu đã đọc một thông báo
    public function markAsRead($id)
    {
        $user = Auth::guard('sanctum')->user();
        
        $notification = ThongBao::where('id', $id)
            ->where('moi_gioi_id', $user->id)
            ->first();
        
        if ($notification) {
            $notification->update(['read_at' => now()]);
            return response()->json(['success' => true]);
        }
        
        return response()->json(['message' => 'Not found'], 404);
    }
    
    // Xóa thông báo
    public function destroy($id)
    {
        $user = Auth::guard('sanctum')->user();
        
        $notification = ThongBao::where('id', $id)
            ->where('moi_gioi_id', $user->id)
            ->first();
        
        if ($notification) {
            $notification->delete();
            return response()->json(['success' => true]);
        }
        
        return response()->json(['message' => 'Not found'], 404);
    }
}

