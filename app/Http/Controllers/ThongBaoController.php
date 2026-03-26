<?php

namespace App\Http\Controllers;

use App\Models\ThongBao;
use App\Models\MoiGioi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThongBaoController extends Controller
{
    public function getThongBao()
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $thongBaos = ThongBao::where('moi_gioi_id', $user->id) // Assume field
                ->with('yeuThich.bds') // Link to YeuThich
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return response()->json(['status' => 1, 'data' => $thongBaos]);
        } else {
            return response()->json(['status' => 0, 'message' => "Có lỗi xảy ra"]);
        }
    }
}
