<?php

namespace App\Http\Controllers;

use App\Models\GiaoDich;
use App\Models\MoiGioi;
use App\Models\KhachHang;
use App\Models\BatDongSan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ThongKeController extends Controller
{
    public function doanhThu(Request $request)
    {
        $startDate = $request->start_date ?? now()->startOfMonth();
        $endDate = $request->end_date ?? now()->endOfMonth();

        $doanhThu = GiaoDich::whereBetween('created_at', [$startDate, $endDate])
            ->where('trang_thai', 'success')
            ->sum('so_tien');

        $chartData = GiaoDich::whereBetween('created_at', [$startDate, $endDate])
            ->where('trang_thai', 'success')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(so_tien) as total'))
            ->get();

        return response()->json([
            'total' => $doanhThu,
            'chart' => $chartData,
        ]);
    }

    public function user()
    {
        $moiGioiCount = MoiGioi::count();
        $khachHangCount = KhachHang::count();
        $bdsCount = BatDongSan::where('is_duyet', true)->count();
        $giaoDichCount = GiaoDich::where('trang_thai', 'success')->count();

        return response()->json([
            'moi_gioi' => $moiGioiCount,
            'khach_hang' => $khachHangCount,
            'bat_dong_san' => $bdsCount,
            'giao_dich' => $giaoDichCount,
        ]);
    }
}
