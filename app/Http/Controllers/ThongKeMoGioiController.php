<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ThongBao;
use App\Models\GiaoDich;
use App\Models\YeuThich;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ThongKeMoGioiController extends Controller
{
    public function tongTinDaDang()
    {
        try {
            $user = auth('sanctum')->user();

            // ✅ Đếm BĐS đã được duyệt VÀ chưa hết hạn
            $count = $user->batDongSans()
                ->where('is_duyet', true)           // Đã duyệt
                ->where(function ($q) {               // VÀ (chưa có hạn HOẶC còn hạn)
                    $q->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                })
                ->count();

            return response()->json([
                'status' => true,
                'message' => 'Thành công',
                'data' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function tinConLai()
    {
        try {
            $user = auth('sanctum')->user();

            return response()->json([
                'status' => true,
                'message' => 'Thành công',
                'data' => $user->so_tin_con_lai ?? 0
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function tongYeuThich()
    {
        try {
            $user = auth('sanctum')->user();

            // ✅ Sửa logic: đếm khách hàng yêu thích BĐS của môi giới
            $count = YeuThich::whereHas('batDongSan', function ($q) use ($user) {
                $q->where('moi_gioi_id', $user->id);
            })
                ->distinct('khach_hang_id')
                ->count('khach_hang_id');

            return response()->json([
                'status' => true,
                'message' => 'Thành công',
                'data' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function tongTien()
    {
        try {
            $user = auth('sanctum')->user();

            $total = GiaoDich::where('moi_gioi_id', $user->id)
                ->where('trang_thai', GiaoDich::STATUS_SUCCESS)
                ->sum('so_tien');

            return response()->json([
                'status' => true,
                'message' => 'Thành công',
                'data' => $total
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function bieuDoBaiDang(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            $range = $request->input('range', 7);
            $startDate = Carbon::now()->subDays($range);

            $rawData = $user->batDongSans()
                ->where('created_at', '>=', $startDate)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->pluck('total', 'date');

            $labels = [];
            $values = [];
            $current = clone $startDate;

            while ($current <= Carbon::now()) {
                $dateStr = $current->format('Y-m-d');
                $labels[] = $dateStr;
                $values[] = $rawData->get($dateStr, 0);
                $current->addDay();
            }

            return response()->json([
                'status' => true,
                'message' => 'Thành công',
                'data' => [
                    'labels' => $labels,
                    'values' => $values,
                    'range' => $range
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function khachHangLienHe()
    {
        try {
            $user = auth('sanctum')->user();

            $data = ThongBao::select(
                'khach_hang_id',
                DB::raw('MAX(created_at) as last_contact'),
                DB::raw('COUNT(*) as contact_count')
            )
                ->with(['khachHang', 'batDongSan'])
                ->where('moi_gioi_id', $user->id)
                ->whereNotNull('khach_hang_id')
                ->groupBy('khach_hang_id')
                ->orderByDesc('last_contact')
                ->take(10)
                ->get()
                ->map(function ($item) {
                    return [
                        'khach_hang' => $item->khachHang,
                        'bat_dong_san' => $item->batDongSan,
                        'last_contact' => $item->last_contact,
                        'contact_count' => $item->contact_count
                    ];
                });

            return response()->json([
                'status' => true,
                'message' => 'Thành công',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
