<?php

namespace App\Http\Controllers;

use App\Models\GoiTin;
use App\Models\GiaoDich;
use App\Models\LichSuGoiTin;
use App\Models\MoiGioi;
use App\Models\KhachHang;
use App\Http\Requests\CreateGoiTinRequest;
use App\Http\Requests\UpdateGoiTinRequest;
use App\Http\Requests\MuaGoiTinRequest;
use App\Http\Requests\DestroyRequest;
use App\Http\Requests\UpdateTrangThaiGoiTinRequest;
use App\Models\PhanQuyen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GoiTinController extends Controller
{
    // Admin xem danh sách gói tin
    public function getData()
    {
        $id_chuc_nang = 19; // ID chức năng xem gói tin
        $user = Auth::guard('sanctum')->user();
        $check = PhanQuyen::where('id_chuc_vu', $user->id_chuc_vu)
            ->where('id_chuc_nang', $id_chuc_nang)
            ->first();
        if (!$user->is_super &&  !$check) {
            return response()->json([
                'status' => false,
                'message' => "Bạn không có quyền thực hiện chức năng này"
            ], 403);
        }
        return response()->json([
            'status' => true,
            'data' => GoiTin::all()
        ]);
    }

    // Admin tạo gói tin
    public function store(CreateGoiTinRequest $request)
    {
        $id_chuc_nang = 20; // ID chức năng tạo gói tin
        $user = Auth::guard('sanctum')->user();
        $check = PhanQuyen::where('id_chuc_vu', $user->id_chuc_vu)
            ->where('id_chuc_nang', $id_chuc_nang)
            ->first();
        if (!$user->is_super &&  !$check) {
            return response()->json([
                'status' => false,
                'message' => "Bạn không có quyền thực hiện chức năng này"
            ], 403);
        }
        $data = GoiTin::create([
            'ten_goi'      => $request->ten_goi,
            'gia'          => $request->gia,
            'so_ngay'      => $request->so_ngay,
            'so_luong_tin' => $request->so_luong_tin,
        ]);
        return response()->json([
            'status'  => true,
            'message' => 'Tạo thành công',
            'data'    => $data
        ]);
    }

    // Admin cập nhật gói tin
    public function update(UpdateGoiTinRequest $request)
    {
        $id_chuc_nang = 21; // ID chức năng cập nhật gói tin
        $user = Auth::guard('sanctum')->user();
        $check = PhanQuyen::where('id_chuc_vu', $user->id_chuc_vu)
            ->where('id_chuc_nang', $id_chuc_nang)
            ->first();
        if (!$user->is_super &&  !$check) {
            return response()->json([
                'status' => false,
                'message' => "Bạn không có quyền thực hiện chức năng này"
            ], 403);
        }
        GoiTin::where('id', $request->id)
            ->update([
                'ten_goi'      => $request->ten_goi,
                'gia'          => $request->gia,
                'so_ngay'      => $request->so_ngay,
                'so_luong_tin' => $request->so_luong_tin,
            ]);
        return response()->json([
            'status'  => true,
            'message' => 'Cập nhật thành công',
        ]);
    }

    // Admin xóa gói tin
    public function destroy(Request $request)
    {
        $id_chuc_nang = 22; // ID chức năng xóa gói tin
        $user = Auth::guard('sanctum')->user();
        $check = PhanQuyen::where('id_chuc_vu', $user->id_chuc_vu)
            ->where('id_chuc_nang', $id_chuc_nang)
            ->first();
        if (!$user->is_super &&  !$check) {
            return response()->json([
                'status' => false,
                'message' => "Bạn không có quyền thực hiện chức năng này"
            ], 403);
        }
        GoiTin::where('id', $request->id)->delete();
        return response()->json([
            'status' => true,
            'message' => 'Xóa thành công'
        ], 200);
    }

    public function changeStatus(UpdateTrangThaiGoiTinRequest $request)
    {
        $id_chuc_nang = 23; // ID chức năng thay đổi trạng thái gói tin
        $user = Auth::guard('sanctum')->user();
        $check = PhanQuyen::where('id_chuc_vu', $user->id_chuc_vu)
            ->where('id_chuc_nang', $id_chuc_nang)
            ->first();
        if (!$user->is_super &&  !$check) {
            return response()->json([
                'status' => false,
                'message' => "Bạn không có quyền thực hiện chức năng này"
            ], 403);
        }

        try {
            $goiTin = GoiTin::findOrFail($request->id);
            $goiTin->update(['trang_thai' => $request->trang_thai]);

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật trạng thái thành công',
                'data' => $goiTin
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi cập nhật: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAll()
    {
        // ✅ Lấy tổng số lượng TRƯỚC khi map
        $plans = GoiTin::where('trang_thai', 'active')
            ->orderBy('gia', 'asc')
            ->get();

        $total = $plans->count(); // ✅ Đếm ở đây

        $formattedPlans = $plans->map(function ($plan, $index) use ($total) { // ✅ Chỉ 2 tham số + use ($total)
            $isPopular = ($total === 3 && $index === 1) || ($plan->so_luong_tin >= 20 && $plan->so_luong_tin <= 50);

            return [
                'id'            => $plan->id,
                'name'          => $plan->ten_goi,
                'description'   => $plan->mo_ta,
                'monthlyPrice'  => (int) $plan->gia,
                'yearlyPrice'   => (int) ($plan->gia * 12 * 0.8),
                'durationDays'  => (int) $plan->so_ngay,
                'postLimit'     => (int) $plan->so_luong_tin,
                'isPopular'     => $isPopular,
                'features' => [
                    "Đăng tối đa <strong>{$plan->so_luong_tin}</strong> tin",
                    "Hiệu lực <strong>{$plan->so_ngay}</strong> ngày",
                    $plan->so_luong_tin >= 20
                        ? "Ưu tiên hiển thị & Gắn nhãn VIP"
                        : "Hiển thị tin đăng tiêu chuẩn",
                    "Hỗ trợ kỹ thuật qua Email/Zalo"
                ],
                'btnText'  => $isPopular ? 'Nâng Cấp Ngay' : 'Chọn Gói',
                'btnClass' => $isPopular ? 'btn-primary' : 'btn-outline-primary',
                'cardClass' => $isPopular ? 'popular' : ''
            ];
        });

        return response()->json(['status' => true, 'data' => $formattedPlans]);
    }
}
