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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GoiTinController extends Controller
{
    public function getData()
    {
        return response()->json([
            'status' => 1,
            'data' => GoiTin::all()
        ]);
    }

    // public function getAll()
    // {
    //     return $this->getData();
    // }

    public function store(CreateGoiTinRequest $request)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $goiTin = GoiTin::create([
                'ten_goi' => $request->ten_goi,
                'gia' => $request->gia,
                'so_ngay' => $request->so_ngay,
                'so_luong_tin' => $request->so_luong_tin,
            ]);

            return response()->json(['status' => 1, 'message' => 'Tạo thành công', 'data' => $goiTin]);
        } else {
            return response()->json(['status' => 0, 'message' => "Có lỗi xảy ra"]);
        }
    }

    public function update(UpdateGoiTinRequest $request)
{
    $user = Auth::guard('sanctum')->user();

    if ($user) {
        $id = $request->input('id'); // 👈 lấy id từ body

        $goiTin = GoiTin::find($id);

        if (!$goiTin) {
            return response()->json([
                'status' => 0,
                'message' => 'Không tìm thấy gói tin'
            ]);
        }

        $goiTin->ten_goi = $request->input('ten_goi', $goiTin->ten_goi);
        $goiTin->gia = $request->input('gia', $goiTin->gia);
        $goiTin->so_ngay = $request->input('so_ngay', $goiTin->so_ngay);
        $goiTin->so_luong_tin = $request->input('so_luong_tin', $goiTin->so_luong_tin);
        $goiTin->save();

        return response()->json([
            'status' => 1,
            'message' => 'Cập nhật thành công',
            'data' => $goiTin
        ]);
    }

    return response()->json([
        'status' => 0,
        'message' => 'Có lỗi xảy ra'
    ]);
}

    public function destroy(DestroyRequest $request)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $goiTin = GoiTin::find($request->id);
            if ($goiTin) {
                $goiTin->delete();
                return response()->json(['status' => 1, 'message' => 'Xóa thành công']);
            }
            return response()->json(['status' => 0, 'message' => 'Không tìm thấy gói tin']);
        } else {
            return response()->json(['status' => 0, 'message' => "Có lỗi xảy ra"]);
        }
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
