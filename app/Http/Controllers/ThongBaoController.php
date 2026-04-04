<?php

namespace App\Http\Controllers;

use App\Models\ThongBao;
use App\Models\MoiGioi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThongBaoController extends Controller
{
    // Trong Controller của Môi giới
    public function getThongBao()
    {
        $user = Auth::guard('sanctum')->user();

        // Lấy danh sách yêu thích liên quan đến các BĐS của môi giới này
        $activities = \App\Models\YeuThich::where('moi_gioi_id', $user->id)
    ->with(['khachHang', 'batDongSan'])
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

        return response()->json([
            'status' => 1,
            'data'   => $activities
        ]);
    }
}
