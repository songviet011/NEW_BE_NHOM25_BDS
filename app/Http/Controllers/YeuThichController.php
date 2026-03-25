<?php

namespace App\Http\Controllers;

use App\Models\YeuThich;
use App\Models\BatDongSan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class YeuThichController extends Controller
{
    public function like(Request $request)
    {
        $khachHang = Auth::guard('sanctum')->user();

        $validated = $request->validate([
            'bds_id' => 'required|exists:bat_dong_sans,id',
        ]);

        // Toggle like
        $yeuThich = YeuThich::where('khach_hang_id', $khachHang->id)
            ->where('bds_id', $validated['bds_id'])
            ->first();

        if ($yeuThich) {
            $yeuThich->delete();
            $message = 'Bỏ thích';
        } else {
            $moiGioi = BatDongSan::find($validated['bds_id'])->moiGioi;
            YeuThich::create([
                'moi_gioi_id' => $moiGioi->id,
                'khach_hang_id' => $khachHang->id,
                'bds_id' => $validated['bds_id'],
                'noi_dung' => "Khách hàng {$khachHang->ten} đã thả tim BDS {$validated['bds_id']}",
            ]);
            $message = 'Đã thích';
        }

        return response()->json(['message' => $message]);
    }

    public function getData()
    {
        $khachHang = Auth::guard('sanctum')->user();

        $yeuThichs = YeuThich::where('khach_hang_id', $khachHang->id)
            ->with('batDongSan', 'batDongSan.moiGioi')
            ->paginate(10);

        return response()->json($yeuThichs);
    }
}
