<?php

namespace App\Http\Controllers;

use App\Models\BatDongSan;
use App\Models\LoaiBatDongSan;
use App\Models\DiaChi;
use App\Models\TinhThanh;
use App\Models\QuanHuyen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AIDinhGiaController extends Controller
{
    public function predictPrice(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $loai_id = $request->input('loai_id');
            $dien_tich = $request->input('dien_tich');
            $tinh_id = $request->input('tinh_id');

            if (empty($loai_id) || empty($dien_tich) || empty($tinh_id)) {
                return response()->json(['status' => false, 'message' => 'Vui lòng nhập đủ thông tin loại, tỉnh, diện tích']);
            }

            // Simple ML placeholder - mean price by loai/dien_tich/tinh
            $avgPrice = BatDongSan::where('loai_id', $loai_id)
                ->where('tinh_id', $tinh_id)
                ->avg('gia') * ($dien_tich / 100);

            $predicted = $avgPrice ?: 0;

            return response()->json([
                'status' => true,
                'data' => [
                    'gia_du_doan' => $predicted,
                    'note' => 'Cần ML model thật (php-ml or Python API)',
                ]
            ]);
        } else {
            return response()->json(['status' => false, 'message' => "Có lỗi xảy ra"]);
        }
    }
}
