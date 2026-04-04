<?php

namespace App\Http\Controllers;

use App\Models\GiaoDich;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GiaoDichController extends Controller
{
    public function getData(Request $request)
    {
        // 1. Lấy danh sách giao dịch kèm thông tin Môi giới và Gói tin
        $query = GiaoDich::with(['moiGioi', 'goiTin'])
            ->orderBy('created_at', 'desc');

        // 2. Xử lý tìm kiếm (Nếu Admin nhập ô tìm kiếm)
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ma_giao_dich', 'like', "%$search%")
                    ->orWhereHas('moiGioi', function ($sq) use ($search) {
                        $sq->where('ten', 'like', "%$search%");
                    });
            });
        }

        // 3. Phân trang
        $giaoDichs = $query->paginate(10);

        // 4. Tính toán thống kê nhanh (Để fix lỗi NaN ở giao diện)
        // Chỉ tính những giao dịch có trạng thái 'success'
        $totalRevenue = GiaoDich::where('trang_thai', 'success')->sum('so_tien');
        $totalDeals = GiaoDich::where('trang_thai', 'success')->count();
        $dealsToday = GiaoDich::where('trang_thai', 'success')
            ->whereDate('created_at', now())
            ->count();

        return response()->json([
            'status'         => 1,
            'data'           => $giaoDichs,
            'total_revenue'  => $totalRevenue,
            'total_deals'    => $totalDeals,
            'deals_today'    => $dealsToday
        ]);
    }
}
