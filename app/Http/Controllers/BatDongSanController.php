<?php

namespace App\Http\Controllers;

use App\Models\BatDongSan;
use App\Models\MoiGioi;
use App\Models\LoaiBatDongSan;
use App\Models\TrangThaiBatDongSan;
use App\Models\TinhThanh;
use App\Models\QuanHuyen;
use App\Models\DiaChi;
use App\Models\HinhAnhBatDongSan;
use App\Http\Requests\CreateBatDongSanRequest;
use App\Http\Requests\UpdateBatDongSanRequest;
use App\Http\Requests\SearchBatDongSanRequest;
use App\Http\Requests\ApproveOrRejectBatDongSanRequest;
use App\Http\Requests\ChangeBatDongSanStatusRequest;
use App\Http\Requests\DestroyRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// ============================================================
// IMPORT EVENTS
// ============================================================
use App\Events\BatDongSanCreated;
use App\Events\BatDongSanUpdated;
use App\Models\PhanQuyen;

class BatDongSanController extends Controller
{
    // Admin
    //Lấy danh sách BDS cho admin
    public function getData(Request $request) //chính sác rồi 
    {
        $id_chuc_nang = 1; // ID chức năng xem danh sách BDS cho admin
        $user = Auth::guard('sanctum')->user();
        $check = PhanQuyen::where('id_chuc_vu', $user->id_chuc_vu)
            ->where('id_chuc_nang', $id_chuc_nang)
            ->first();
        if (!$user->is_super &&  !$check) {
            return response()->json([
                'status' => false,
                'message' => "Bạn không có quyền thực hiện chức năng này"
            ]);
        }
        $data = BatDongSan::join('bat_dong_sans', 'loai_bat_dong_sans.id', 'bat_dong_sans.loai_id')
            ->join('trang_thai_bat_dong_sans', 'trang_thai_bat_dong_sans.id', 'bat_dong_sans.trang_thai_id')
            ->join('tinh_thanhs', 'tinh_thanhs.id', 'bat_dong_sans.tinh_id')
            ->join('quan_huyens', 'quan_huyens.id', 'bat_dong_sans.quan_id')
            ->join('dia_chis', 'dia_chis.id', 'bat_dong_sans.dia_chi_id')
            ->join('hinh_anh_bat_dong_sans', 'hinh_anh_bat_dong_sans.bds_id', 'bat_dong_sans.id')
            ->select('bat_dong_sans.*', 'hinh_anh_bat_dong_sans.url', 'loai_bat_dong_sans.ten_loai', 'dia_chis.dia_chi_chi_tiet', 'tinh_thanhs.ten_tinh', 'quan_huyens.ten_quan', 'trang_thai_bat_dong_sans.ten_trang_thai')
            ->get();
        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    // Tìm kiếm BDS cho admin (có thể tìm theo tiêu đề, mô tả, giá, loại, địa chỉ)
    public function searchAdmin(Request $request) // Chính xác
    {
        $id_chuc_nang = 60; // ID tìm kiếm BDS cho admin
        $user = Auth::guard('sanctum')->user();
        $check = PhanQuyen::where('id_chuc_vu', $user->id_chuc_vu)
            ->where('id_chuc_nang', $id_chuc_nang)
            ->first();
        if (!$user->is_super &&  !$check) {
            return response()->json([
                'status' => false,
                'message' => "Bạn không có quyền thực hiện chức năng này"
            ]);
        }
        $noi_dung = '%' . $request->noi_dung_tim . '%';
        $data = BatDongSan::where('tieu_de', 'like', $noi_dung)
            ->orWhere('mo_ta', 'like', $noi_dung)
            ->orWhere('gia', 'like', $noi_dung)
            ->orWhere('loai_id', 'like', $noi_dung)
            ->orWhere('dia_chi_id', 'like', $noi_dung)->get();
        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    // Duyệt tin BDS (Dành cho admin, môi giới không có quyền này, khách hàng không có quyền này)
    public function duyetTin(ApproveOrRejectBatDongSanRequest $request) //chính xác
    {
        $id_chuc_nang = 5; // ID chức năng duyệt BDS
        $user = Auth::guard('sanctum')->user();
        $check = PhanQuyen::where('id_chuc_vu', $user->id_chuc_vu)
            ->where('id_chuc_nang', $id_chuc_nang)
            ->first();
        if (!$user->is_super &&  !$check) {
            return response()->json([
                'status' => false,
                'message' => "Bạn không có quyền thực hiện chức năng này"
            ]);
        }
        $admin = BatDongSan::find($request->id);
        if ($admin) {
            if ($admin->is_duyet == 1) {
                $admin->is_duyet = 0;
            } else {
                $admin->is_duyet = 1;
            }
            $admin->save();
            return response()->json([
                'status' => true,
                'message' => 'Thay đổi trạng thái duyệt thành công'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy BDS'
            ]);
        }
    }

    // Xóa BDS (Dành cho admin, môi giới có thể xóa nhưng khách hàng không có quyền này)
    public function delete(DestroyRequest $request) //chính xác 
    {
        $id_chuc_nang = 4; // ID chức năng xóa BDS
        $user = Auth::guard('sanctum')->user();
        $check = PhanQuyen::where('id_chuc_vu', $user->id_chuc_vu)
            ->where('id_chuc_nang', $id_chuc_nang)
            ->first();
        if (!$user->is_super &&  !$check) {
            return response()->json([
                'status' => false,
                'message' => "Bạn không có quyền thực hiện chức năng này"
            ]);
        }
        $data = BatDongSan::find($request->id);
        if ($data) {
            $data->delete();
            return response()->json([
                'status' => true,
                'message' => 'Xóa thành công'
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Không tìm thấy BDS'
        ]);
    }

    // Thay đổi trạng thái BDS (Dành cho admin, môi giới, khách hàng không có quyền này)
    public function changeStatus(ChangeBatDongSanStatusRequest $request) //chính xác
    {
        $id_chuc_nang = 61; // ID chức năng thay đổi trạng thái BDS ví dụ (đang bán, đã bán, tạm ngưng)
        $user = Auth::guard('sanctum')->user();
        $check = PhanQuyen::where('id_chuc_vu', $user->id_chuc_vu)
            ->where('id_chuc_nang', $id_chuc_nang)
            ->first();
        if (!$user->is_super &&  !$check) {
            return response()->json([
                'status' => false,
                'message' => "Bạn không có quyền thực hiện chức năng này"
            ]);
        }
        $bds = BatDongSan::find($request->id);
        if ($bds) {
            $bds->trang_thai_id = $request->trang_thai_id;
            $bds->save();
            return response()->json([
                'status' => true,
                'message' => 'Cập nhật trạng thái thành công'
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy BDS'
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Không tìm thấy BDS'
        ]);
    }

    // Chi tiết BDS (Dành cho tất cả mọi người)
    public function xemChiTietBDS($id)
    {
        $id_chuc_nang = 59; // ID chức năng xem danh sách BDS cho admin
        $user = Auth::guard('sanctum')->user();
        $check = PhanQuyen::where('id_chuc_vu', $user->id_chuc_vu)
            ->where('id_chuc_nang', $id_chuc_nang)
            ->first();
        if (!$user->is_super &&  !$check) {
            return response()->json([
                'status' => false,
                'message' => "Bạn không có quyền thực hiện chức năng này"
            ]);
        }
        $data = BatDongSan::join('bat_dong_sans', 'loai_bat_dong_sans.id', '=', 'bat_dong_sans.loai_id')
            ->join('moi_giois', 'moi_giois.id', '=', 'bat_dong_sans.moi_gioi_id')
            ->join('tinh_thanhs', 'tinh_thanhs.id', '=', 'bat_dong_sans.tinh_id')
            ->join('quan_huyens', 'quan_huyens.id', '=', 'bat_dong_sans.quan_id')
            ->join('dia_chis', 'dia_chis.id', '=', 'bat_dong_sans.dia_chi_id')
            ->join('hinh_anh_bat_dong_sans', 'hinh_anh_bat_dong_sans.bds_id', 'bat_dong_sans.id')
            ->where('bat_dong_sans.id', $id)
            ->select(
                'bat_dong_sans.*',
                'hinh_anh_bat_dong_sans.url',
                'loai_bat_dong_sans.ten_loai',
                'moi_giois.ten_moi_gioi',
                'dia_chis.dia_chi_chi_tiet',
                'tinh_thanhs.ten_tinh',
                'quan_huyens.ten_quan'
            )
            ->get();
        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    // MoiGioi
    // Lấy danh sách BDS của môi giới đang đăng (Dành cho môi giới, admin có thể xem tất cả, khách hàng không có quyền này)
    public function getDataDanhChoMoiGioi(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $data = BatDongSan::query()
                ->join('loai_bat_dong_sans', 'bat_dong_sans.loai_id', '=', 'loai_bat_dong_sans.id')
                ->join('trang_thai_bat_dong_sans', 'bat_dong_sans.trang_thai_id', '=', 'trang_thai_bat_dong_sans.id')
                ->join('dia_chis', 'bat_dong_sans.dia_chi_id', '=', 'dia_chis.id')
                ->join('tinh_thanhs', 'dia_chis.tinh_id', '=', 'tinh_thanhs.id')
                ->join('quan_huyens', 'dia_chis.quan_id', '=', 'quan_huyens.id')
                ->where('bat_dong_sans.moi_gioi_id', $user->id)
                ->select(
                    'bat_dong_sans.tieu_de',
                    'bat_dong_sans.gia',
                    'bat_dong_sans.dien_tich',
                    'dia_chis.dia_chi_chi_tiet as dia_chi',
                    'quan_huyens.ten as ten_quan',
                    'tinh_thanhs.ten as ten_tinh',
                    'loai_bat_dong_sans.ten as ten_loai',
                    'trang_thai_bat_dong_sans.ten as ten_trang_thai'
                )
                ->get();

            return response()->json([
                'status' => true,
                'data'   => $data
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Có lỗi xảy ra"
            ]);
        }
    }

    // tạo bài đăng BDS (Dành cho môi giới,admin chỉ có thể duyệt khách hàng không có quyền này)
    public function store(CreateBatDongSanRequest $request)
    {
        $user = Auth::guard('sanctum')->user();

        $data = $request->validated();

        $data['moi_gioi_id'] = $user->id;
        $data['is_duyet'] = false;
        $data['is_noi_bat'] = $data['is_noi_bat'] ?? false;

        $batDongSan = BatDongSan::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Tạo BDS thành công và đang chờ duyệt',
            'data' => $batDongSan
        ], 201);
    }

    // Cập nhật bài đăng BDS (Dành cho môi giới, admin chỉ có thể duyệt khách hàng không có quyền này)
    public function update(UpdateBatDongSanRequest $request)
    {
        $user = Auth::guard('sanctum')->user();
        //Lấy bài đăng
        $data = BatDongSan::find($request->id);
        if (!$data || $data->moi_gioi_id !== $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn chỉ được cập nhật bài đăng của chính mình'
            ], 403);
        }
        $oldData = $data->toArray();
        //Lấy dữ liệu hợp lệ
        $updateData = $request->validated();
        unset($updateData['id']);
        //Update
        $data->fill($updateData);
        $data->is_duyet = false;
        $data->save();
        //Event
        // BatDongSanUpdated::dispatch($data, $oldData);
        return response()->json([
            'status' => true,
            'message' => 'Cập nhật data thành công và đang chờ duyệt lại',
            'data' => $data
        ]);
    }

    // Xóa bài đăng BDS (Dành cho môi giới, admin chỉ có thể duyệt khách hàng không có quyền này)
    public function destroy(DestroyRequest $request)
    {
        $user = Auth::guard('sanctum')->user();
        $data = BatDongSan::find($request->id);
        if (!$data || $data->moi_gioi_id !== $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn chỉ được xóa bài đăng của chính mình'
            ], 403);
        }
        $data->delete();
        return response()->json([
            'status' => true,
            'message' => 'Xóa bài đăng thành công'
        ]);
    }
}
