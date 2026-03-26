<?php

namespace App\Http\Controllers;

use App\Models\GoiTin;
use App\Models\GiaoDich;
use App\Models\LichSuGoiTin;
use App\Models\MoiGioi;
use App\Models\KhachHang;
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

    public function getAll()
    {
        return $this->getData();
    }

    public function store(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $ten_goi = $request->input('ten_goi');
            $gia = $request->input('gia');
            $so_ngay = $request->input('so_ngay');
            $so_luong_tin = $request->input('so_luong_tin');

            if (empty($ten_goi) || !isset($gia) || !isset($so_ngay) || !isset($so_luong_tin)) {
                return response()->json(['status' => 0, 'message' => 'Vui lòng điền đầy đủ thông tin']);
            }

            $goiTin = GoiTin::create([
                'ten_goi' => $ten_goi,
                'gia' => $gia,
                'so_ngay' => $so_ngay,
                'so_luong_tin' => $so_luong_tin,
            ]);

            return response()->json(['status' => 1, 'message' => 'Tạo thành công', 'data' => $goiTin]);
        } else {
            return response()->json(['status' => 0, 'message' => "Có lỗi xảy ra"]);
        }
    }

    public function update(Request $request, $id)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $goiTin = GoiTin::find($id);
            if (!$goiTin) {
                return response()->json(['status' => 0, 'message' => 'Không tìm thấy gói tin']);
            }

            $goiTin->ten_goi = $request->input('ten_goi', $goiTin->ten_goi);
            $goiTin->gia = $request->input('gia', $goiTin->gia);
            $goiTin->so_ngay = $request->input('so_ngay', $goiTin->so_ngay);
            $goiTin->so_luong_tin = $request->input('so_luong_tin', $goiTin->so_luong_tin);
            $goiTin->save();

            return response()->json(['status' => 1, 'message' => 'Cập nhật thành công', 'data' => $goiTin]);
        } else {
            return response()->json(['status' => 0, 'message' => "Có lỗi xảy ra"]);
        }
    }

    public function destroy(Request $request)
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

    public function muaGoi(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $goi_tin_id = $request->input('goi_tin_id');
            if (empty($goi_tin_id)) {
                return response()->json(['status' => 0, 'message' => 'Vui lòng chọn gói tin']);
            }

            $goiTin = GoiTin::find($goi_tin_id);
            if (!$goiTin) {
                return response()->json(['status' => 0, 'message' => 'Gói tin không tồn tại']);
            }

            // Giả sử thanh toán success
            $giaoDich = GiaoDich::create([
                'moi_gioi_id' => $user->id, // or khach_hang_id if KhachHang
                'goi_tin_id' => $goi_tin_id,
                'so_tien' => $goiTin->gia,
                'phuong_thuc' => $request->phuong_thuc ?? 'cash',
                'trang_thai' => 'success',
                'ma_giao_dich' => 'TXN' . time(),
            ]);

            $ngayKetThuc = now()->addDays($goiTin->so_ngay);

            LichSuGoiTin::create([
                'moi_gioi_id' => $user->id,
                'goi_tin_id' => $goi_tin_id,
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
