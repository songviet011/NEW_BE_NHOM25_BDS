<?php

namespace App\Http\Controllers;

use App\Models\GiaoDich;
use App\Models\GoiTin;
use App\Models\LichSuGoiTin;
use App\Models\MoiGioi;
use App\Models\UnmatchedPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LichSuGoiTinController extends Controller
{
    public function lichSuMua(Request $request)
{
    $perPage = $request->get('per_page', 10);
    $search = $request->get('search', '');

    // Query từ giao_dichs
    $query = DB::table('giao_dichs as gd')
        ->join('moi_giois as mg', 'gd.moi_gioi_id', '=', 'mg.id')
        ->join('goi_tins as gt', 'gd.goi_tin_id', '=', 'gt.id')
        ->select(
            'mg.id as moi_gioi_id',
            'mg.ten',
            'mg.email',
            'mg.so_dien_thoai',
            DB::raw('COUNT(DISTINCT gd.id) as tong_so_lan_mua'),
            DB::raw('SUM(CASE WHEN gd.trang_thai = "success" THEN gd.so_tien ELSE 0 END) as tong_tien_da_mua'),
            DB::raw('COUNT(DISTINCT CASE 
                WHEN gd.trang_thai = "success" 
                AND DATE_ADD(gd.paid_at, INTERVAL gt.so_ngay DAY) >= NOW() 
                THEN gd.id 
            END) as so_goi_dang_hoat_dong'),
            DB::raw('MAX(gd.created_at) as lan_mua_gan_nhat')
        );

    // Filter
    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('mg.ten', 'LIKE', "%{$search}%")
              ->orWhere('mg.email', 'LIKE', "%{$search}%")
              ->orWhere('mg.so_dien_thoai', 'LIKE', "%{$search}%");
        });
    }

    // ✅ SỬA: CHỈ GROUP BY USER, KHÔNG GROUP BY GÓI
    $data = $query->groupBy(
            'mg.id', 
            'mg.ten', 
            'mg.email', 
            'mg.so_dien_thoai'
            // ❌ BỎ: 'gt.id', 'gt.ten_goi'
        )
        ->orderBy('lan_mua_gan_nhat', 'desc')
        ->paginate($perPage);

    // Transform
    $transformedData = $data->map(function($item) {
        return [
            'moi_gioi' => [
                'id' => $item->moi_gioi_id,
                'ten' => $item->ten,
                'email' => $item->email,
                'so_dien_thoai' => $item->so_dien_thoai,
            ],
            'thong_ke' => [
                'tong_so_lan_mua' => (int) $item->tong_so_lan_mua,
                'tong_tien_da_mua' => (float) $item->tong_tien_da_mua,
                'tong_tien_formatted' => number_format($item->tong_tien_da_mua, 0, ',', '.') . ' đ',
                'so_goi_dang_hoat_dong' => (int) $item->so_goi_dang_hoat_dong,
            ],
            'thoi_gian' => [
                'lan_mua_gan_nhat' => $item->lan_mua_gan_nhat ? 
                    Carbon::parse($item->lan_mua_gan_nhat)->format('d/m/Y H:i') : null,
            ],
        ];
    });

    return response()->json([
        'status' => true,
        'data' => [
            'current_page' => $data->currentPage(),
            'data' => $transformedData,
            'from' => $data->firstItem(),
            'last_page' => $data->lastPage(),
            'to' => $data->lastItem(),
            'total' => $data->total(),
            'per_page' => $data->perPage(),
        ]
    ]);
}

    /**
     * API: Chi tiết lịch sử mua (Query từ giao_dichs)
     */
    public function chiTietLichSuMua($moiGioiId)
    {
        $moiGioi = MoiGioi::find($moiGioiId);
        if (!$moiGioi) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy môi giới'
            ], 404);
        }

        // Lấy TẤT CẢ giao dịch (success + pending)
        $giaoDichs = GiaoDich::where('moi_gioi_id', $moiGioiId)
            ->join('goi_tins', 'giao_dichs.goi_tin_id', '=', 'goi_tins.id')
            ->select(
                'giao_dichs.*',
                'goi_tins.ten_goi',
                'goi_tins.so_ngay',
                'goi_tins.so_luong_tin'
            )
            ->orderBy('giao_dichs.created_at', 'desc')
            ->get();

        // Transform
        $lichSuMua = $giaoDichs->map(function ($gd) {
            $ngayHetHan = $gd->paid_at ?
                Carbon::parse($gd->paid_at)->addDays($gd->so_ngay) : null;

            $trangThai = $gd->trang_thai === 'success' ?
                ($ngayHetHan && $ngayHetHan->isFuture() ? 'active' : 'expired') :
                $gd->trang_thai;

            $trangThaiLabel = match ($trangThai) {
                'active' => ['text' => 'Đang active', 'class' => 'success'],
                'expired' => ['text' => 'Hết hạn', 'class' => 'danger'],
                'pending' => ['text' => 'Chờ thanh toán', 'class' => 'warning'],
                default => ['text' => 'Thất bại', 'class' => 'secondary'],
            };

            return [
                'id' => $gd->id,
                'ma_don_hang' => $gd->ma_giao_dich,
                'goi_tin' => [
                    'ten_goi' => $gd->ten_goi,
                    'so_ngay' => $gd->so_ngay,
                    'so_luong_tin' => $gd->so_luong_tin,
                ],
                'so_tien' => (float) $gd->so_tien,
                'so_tien_formatted' => number_format($gd->so_tien, 0, ',', '.') . ' đ',
                'phuong_thuc' => $gd->phuong_thuc,
                'ngay_mua' => Carbon::parse($gd->created_at)->format('H:i d/m/Y'),
                'ngay_bat_dau' => $gd->paid_at ? Carbon::parse($gd->paid_at)->format('H:i d/m/Y') : '-',
                'ngay_ket_thuc' => $ngayHetHan ? $ngayHetHan->format('H:i d/m/Y') : '-',
                'trang_thai' => $trangThai,
                'trang_thai_giao_dich' => $gd->trang_thai,
                'trang_thai_label' => $trangThaiLabel,
            ];
        });

        // Stats
        $stats = [
            'tong_don' => $giaoDichs->count(),
            'don_dang_hoat_dong' => $giaoDichs->where('trang_thai', 'active')->count(),
            'don_pending' => $giaoDichs->where('trang_thai', 'pending')->count(),
            'tong_tien' => $giaoDichs->where('trang_thai', 'success')->sum('so_tien'),
        ];

        return response()->json([
            'status' => true,
            'data' => [
                'moi_gioi' => [
                    'id' => $moiGioi->id,
                    'ten' => $moiGioi->ten,
                    'email' => $moiGioi->email,
                    'so_dien_thoai' => $moiGioi->so_dien_thoai,
                ],
                'thong_ke' => [
                    'tong_don' => $stats['tong_don'],
                    'don_dang_hoat_dong' => $stats['don_dang_hoat_dong'],
                    'don_pending' => $stats['don_pending'],
                    'tong_tien' => number_format($stats['tong_tien'], 0, ',', '.') . ' đ',
                ],
                'lich_su_mua' => $lichSuMua,
            ]
        ]);
    }

    /**
     * API: Giao dịch pending (cho unmatched payments)
     */
    public function getPendingTransactions()
    {
        $pending = DB::table('giao_dichs')
            ->where('trang_thai', 'pending')
            ->join('moi_giois', 'giao_dichs.moi_gioi_id', '=', 'moi_giois.id')
            ->select(
                'giao_dichs.id',
                'giao_dichs.ma_giao_dich',
                'giao_dichs.so_tien',
                'giao_dichs.created_at',
                'moi_giois.ten as moi_gioi_ten'
            )
            ->orderBy('giao_dichs.created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $pending
        ]);
    }

    /**
     * ✅ Danh sách unmatched payments
     */
    public function index()
    {
        $unmatched = UnmatchedPayment::where('status', 'unmatched')
            ->latest()
            ->paginate(20);

        return response()->json([
            'status' => true,
            'data' => $unmatched,
            'total' => $unmatched->total()
        ]);
    }

    /**
     * ✅ Khớp payment thủ công
     */
    public function match(Request $request, $id)
    {
        $request->validate([
            'giao_dich_id' => 'required|exists:giao_dichs,id'
        ]);

        DB::beginTransaction();
        try {
            $unmatched = UnmatchedPayment::findOrFail($id);
            $transaction = GiaoDich::findOrFail($request->giao_dich_id);

            // Update giao dịch thành success
            $transaction->update([
                'trang_thai' => 'success',
                'paid_at' => now(),
                'ma_sepay_txn_ref' => $unmatched->sepayer_reference,
            ]);

            // Kích hoạt gói tin
            $this->activatePackage($transaction);

            // Đánh dấu unmatched payment đã match
            $unmatched->update([
                'status' => 'matched',
                'giao_dich_id' => $transaction->id,
                'admin_notes' => "Matched by " . auth()->user()->email
            ]);

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Đã khớp giao dịch thành công!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ Bỏ qua payment
     */
    public function ignore(Request $request, $id)
    {
        $unmatched = UnmatchedPayment::findOrFail($id);

        $unmatched->update([
            'status' => 'ignored',
            'admin_notes' => "Ignored by " . auth()->user()->email . ": " . ($request->reason ?? 'Không rõ lý do')
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Đã bỏ qua giao dịch'
        ]);
    }

    /**
     * ✅ Helper: Kích hoạt gói tin
     */
    private function activatePackage(GiaoDich $transaction)
    {
        $goiTin = GoiTin::find($transaction->goi_tin_id);
        $user = MoiGioi::find($transaction->moi_gioi_id);

        if (!$goiTin || !$user) {
            throw new \Exception("Related models not found");
        }

        $baseDate = $user->ngay_het_han_goi && $user->ngay_het_han_goi->isFuture()
            ? Carbon::parse($user->ngay_het_han_goi)
            : now();

        $user->update([
            'goi_tin_id' => $goiTin->id,
            'so_tin_con_lai' => ($user->so_tin_con_lai ?? 0) + $goiTin->so_luong_tin,
            'ngay_het_han_goi' => $baseDate->addDays($goiTin->so_ngay),
        ]);
    }
}
