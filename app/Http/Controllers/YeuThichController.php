<?php

namespace App\Http\Controllers;

use App\Models\YeuThich;
use App\Models\BatDongSan;
use App\Http\Requests\YeuThichRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class YeuThichController extends Controller
{
    public function like(YeuThichRequest $request)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $bds_id = $request->input('bds_id');

            // Toggle like
            $yeuThich = YeuThich::where('khach_hang_id', $user->id)
                ->where('bds_id', $bds_id)
                ->first();

            if ($yeuThich) {
                $yeuThich->delete();
                $message = 'Bỏ thích';
            } else {
                $bds = BatDongSan::find($bds_id);
                if (!$bds) {
                    return response()->json(['status' => 0, 'message' => 'Không tìm thấy BDS']);
                }
                YeuThich::create([
                    'moi_gioi_id' => $bds->moi_gioi_id,
                    'khach_hang_id' => $user->id,
                    'bds_id' => $bds_id,
                    'noi_dung' => "Khách hàng {$user->ten} đã thả tim BDS {$bds_id}",
                ]);
                $message = 'Đã thích';
            }

            return response()->json(['status' => 1, 'message' => $message]);
        } else {
            return response()->json(['status' => 0, 'message' => "Có lỗi xảy ra"]);
        }
    }

    public function getData()
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $yeuThichs = YeuThich::where('khach_hang_id', $user->id)
                ->with('batDongSan', 'batDongSan.moiGioi')
                ->paginate(10);

            return response()->json(['status' => 1, 'data' => $yeuThichs]);
        } else {
            return response()->json(['status' => 0, 'message' => "Có lỗi xảy ra"]);
        }
    }
}
