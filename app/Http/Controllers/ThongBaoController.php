<?php

namespace App\Http\Controllers;

use App\Models\ThongBao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThongBaoController extends Controller
{
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
            ->limit(5)
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
}
