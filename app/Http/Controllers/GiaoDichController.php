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

    public function muaGoi(MuaGoiTinRequest $request)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $goiTin = GoiTin::find($request->goi_tin_id);
            if (!$goiTin) {
                return response()->json(['status' => 0, 'message' => 'Gói tin không tồn tại']);
            }

            // Giả sử thanh toán success
            $giaoDich = GiaoDich::create([
                'moi_gioi_id' => $user->id, // or khach_hang_id if KhachHang
                'goi_tin_id' => $request->goi_tin_id,
                'so_tien' => $goiTin->gia,
                'phuong_thuc' => $request->phuong_thuc ?? 'cash',
                'trang_thai' => 'success',
                'ma_giao_dich' => 'TXN' . time(),
            ]);

            $ngayKetThuc = now()->addDays($goiTin->so_ngay);

            LichSuGoiTin::create([
                'moi_gioi_id' => $user->id,
                'goi_tin_id' => $request->goi_tin_id,
                'ngay_bat_dau' => now(),
                'ngay_ket_thuc' => $ngayKetThuc,
            ]);

            return response()->json([
                'status' => 1,
                'message' => 'Mua gói thành công',
                'data' => $giaoDich
            ]);
        } else {
            return response()->json(['status' => 0, 'message' => "Có lỗi xảy ra"]);
        }
    }
}
