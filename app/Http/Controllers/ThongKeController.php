<?php

namespace App\Http\Controllers;

use App\Models\GiaoDich;
use App\Models\MoiGioi;
use App\Models\KhachHang;
use App\Models\BatDongSan;
use App\Http\Requests\ThongKeDoanhThuRequest;
use App\Models\YeuThich;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ThongKeController extends Controller
{
    // Dashboard Stats (4 cards) - tổng số môi giới, khách hàng, bất động sản đã duyệt, giao dịch thành công
    public function getDashboardStats()
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

    // Dashboard Chart (biểu đồ) - doanh thu và số giao dịch theo ngày trong khoảng thời gian chọn
    public function getRevenueChart(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json(['status' => 0, 'message' => "Có lỗi xảy ra"], 401);
        }

        $request->validate([
            'period' => 'required|in:7days,30days,3months,6months,1year',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // Xác định khoảng ngày
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
        } else {
            $period = $request->period;
            $startDate = match ($period) {
                '7days' => now()->subDays(7),
                '30days' => now()->subDays(30),
                '3months' => now()->subMonths(3),
                '6months' => now()->subMonths(6),
                '1year' => now()->subYear(),
                default => now()->subDays(30),
            };
            $endDate = now();
        }

        // Lấy dữ liệu doanh thu và giao dịch
        $chartData = $this->getRevenueAndTransactionData($startDate, $endDate);

        return response()->json([
            'status' => true,
            'data' => $chartData,
            'summary' => [
                'total_revenue' => GiaoDich::whereBetween('created_at', [$startDate, $endDate])
                    ->where('trang_thai', 'success')
                    ->sum('so_tien'),
                'total_transactions' => GiaoDich::whereBetween('created_at', [$startDate, $endDate])
                    ->where('trang_thai', 'success')
                    ->count(),
            ]
        ]);
    }

    private function getRevenueAndTransactionData($startDate, $endDate)
    {
        $data = GiaoDich::whereBetween('created_at', [$startDate, $endDate])
            ->where('trang_thai', 'success')
            ->selectRaw('DATE(created_at) as date')
            ->selectRaw('COUNT(*) as transaction_count')
            ->selectRaw('SUM(so_tien) as revenue')
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        // Fill các ngày trống (nếu không có giao dịch)
        $chartData = [];
        $period = CarbonPeriod::create($startDate, $endDate);

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $item = $data->firstWhere('date', $dateStr);

            $chartData[] = [
                'date' => $dateStr,
                'date_display' => $date->format('d/m'),
                'revenue' => $item ? (float)$item->revenue : 0,
                'transaction_count' => $item ? (int)$item->transaction_count : 0,
            ];
        }

        return $chartData;
    }

    // Khách hàng yêu thích BĐS gần đây (5 mục mới nhất)
    public function getRecentFavorites(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json(['status' => 0, 'message' => "Có lỗi xảy ra"], 401);
        }

        $limit = $request->input('limit', 5);

        $favorites = YeuThich::with([
            'khachHang',
            'batDongSan.loai',
            'batDongSan.diaChi.tinh',
            'batDongSan.diaChi.quan',
            'batDongSan.moiGioi'
        ])
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->map(function ($yt) {
                return [
                    'id' => $yt->id,
                    'date' => $yt->created_at->format('d/m/Y'),
                    'date_full' => $yt->created_at->format('d/m/Y H:i'),
                    'khach_hang' => $yt->khachHang->ten ?? 'N/A',
                    'khach_hang_email' => $yt->khachHang->email ?? 'N/A',
                    'bat_dong_san' => $yt->batDongSan->tieu_de ?? 'N/A',
                    'loai_bds' => $yt->batDongSan->loai->ten_loai ?? 'N/A',
                    'gia' => $yt->batDongSan->gia,
                    'gia_formatted' => number_format($yt->batDongSan->gia, 0, ',', '.') . ' VNĐ',
                    'dia_chi' => $yt->batDongSan->diaChi->dia_chi_chi_tiet ?? 'N/A',
                    'moi_gioi' => $yt->batDongSan->moiGioi->ten ?? 'N/A',
                ];
            });

        return response()->json([
            'status' => 1,
            'data' => $favorites
        ]);
    }

    // Giao dịch mua gói tin gần đây (5 mục mới nhất)
    public function getRecentPackagePurchases(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json(['status' => 0, 'message' => "Có lỗi xảy ra"], 401);
        }
        $limit = $request->input('limit', 5);
        // Giả sử bạn có bảng giao_dich hoặc goi_tin_da_mua
        // Điều chỉnh theo model thực tế của bạn
        $purchases = GiaoDich::with([
            'moiGioi',
            'goiTin'  // Nếu có relationship với GoiTin
        ])
            ->where('trang_thai', 'success') // hoặc 'completed'
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->map(function ($gd) {
                return [
                    'id' => $gd->id,
                    'date' => $gd->created_at->format('d/m/Y'),
                    'date_full' => $gd->created_at->format('d/m/Y H:i'),
                    'moi_gioi' => $gd->moiGioi->ten ?? 'N/A',
                    'moi_gioi_email' => $gd->moiGioi->email ?? 'N/A',
                    'goi_tin' => $gd->goiTin->ten_goi ?? 'Gói tin',
                    'so_tien' => $gd->so_tien,
                    'so_tien_formatted' => number_format($gd->so_tien, 0, ',', '.') . ' VNĐ',
                    'trang_thai' => $gd->trang_thai,
                    'trang_thai_label' => $this->getPaymentStatusLabel($gd->trang_thai),
                ];
            });

        return response()->json([
            'status' => 1,
            'data' => $purchases
        ]);
    }

    private function getPaymentStatusLabel($status)
    {
        return match ($status) {
            'success' => 'Đã thanh toán',
            'pending' => 'Chờ thanh toán',
            'cancelled' => 'Đã hủy',
            'expired' => 'Hết hạn',
            default => 'Unknown',
        };
    }
}
