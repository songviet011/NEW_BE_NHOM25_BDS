<?php

namespace App\Http\Controllers;

use App\Models\GiaoDich;
use App\Models\MoiGioi;
use App\Models\KhachHang;
use App\Models\BatDongSan;
use App\Http\Requests\ThongKeDoanhThuRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ThongKeController extends Controller
{
    public function doanhThu(ThongKeDoanhThuRequest $request)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $startDate = $request->input('start_date') ?? now()->startOfMonth();
            $endDate = $request->input('end_date') ?? now()->endOfMonth();

            $doanhThu = GiaoDich::whereBetween('created_at', [$startDate, $endDate])
                ->where('trang_thai', 'success')
                ->sum('so_tien');

            $chartData = GiaoDich::whereBetween('created_at', [$startDate, $endDate])
                ->where('trang_thai', 'success')
                ->groupBy(DB::raw('DATE(created_at)'))
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(so_tien) as total'))
                ->get();

            return response()->json([
                'status' => 1,
                'data' => [
                    'total' => $doanhThu,
                    'chart' => $chartData,
                ]
            ]);
        } else {
            return response()->json(['status' => 0, 'message' => "Có lỗi xảy ra"]);
        }
    }

    public function user()
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $moiGioiCount = MoiGioi::count();
            $khachHangCount = KhachHang::count();
            $bdsCount = BatDongSan::where('is_duyet', true)->count();
            $giaoDichCount = GiaoDich::where('trang_thai', 'success')->count();

            return response()->json([
                'status' => 1,
                'data' => [
                    'moi_gioi' => $moiGioiCount,
                    'khach_hang' => $khachHangCount,
                    'bat_dong_san' => $bdsCount,
                    'giao_dich' => $giaoDichCount,
                ]
            ]);
        } else {
            return response()->json(['status' => 0, 'message' => "Có lỗi xảy ra"]);
        }
    }
}
