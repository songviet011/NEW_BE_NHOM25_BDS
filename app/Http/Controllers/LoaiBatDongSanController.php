<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateLoaiBDSRequest;
use App\Http\Requests\UpdateLoaiBDSRequest;
use App\Models\LoaiBatDongSan;
use App\Models\PhanQuyen;
use App\Models\TinhThanh;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoaiBatDongSanController extends Controller
{
    //Admin
    // Lấy dữ liệu loại BĐS 
    public function getData(Request $request)
    {
        $id_chuc_nang = 62; // ID chức năng xem loại BĐS
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

        $data = LoaiBatDongSan::all();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    //Tạo loại BĐS mới
    public function store(CreateLoaiBDSRequest $request)
    {
        $id_chuc_nang = 63; // ID chức năng xem loại BĐS
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
        $data = LoaiBatDongSan::create([
            'ten_loai' => $request->ten_loai,
            'is_active' => $request->is_active ?? true, // Mặc định là true nếu không có giá trị
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Tạo thành công',
            'data' => $data
        ]);
    }

    //Cập nhật loại BĐS
    public function update(UpdateLoaiBDSRequest $request)
    {
        $id_chuc_nang = 64; // ID chức năng xem loại BĐS
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
        $data = LoaiBatDongSan::find($request->id);
        $data->update([
            'ten_loai' => $request->ten_loai,
            'is_active' => $request->is_active
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Loại BĐS đã được cập nhật thành công',
            'data' => $data
        ]);
    }

    //Xóa loại BĐS
    public function destroy($id)
    {
        $id_chuc_nang = 65; // ID chức năng xem loại BĐS
        $user = Auth::guard('sanctum')->user();
        $check = PhanQuyen::where('id_chuc_vu', $user->id_chuc_vu)
            ->where('id_chuc_nang', $id_chuc_nang)
            ->first();
        if (!$user->is_super && !$check) {
            return response()->json([
                'status' => false,
                'message' => "Bạn không có quyền thực hiện chức năng này"
            ], 403);
        }
        $data = LoaiBatDongSan::findOrFail($id);
        $data->delete();
        return response()->json([
            'status' => true,
            'message' => 'Xóa loại BĐS thành công'
        ]);
    }

    //Cập nhật trạng thái
    public function changeStatus(Request $request)
    {
        $id_chuc_nang = 66; // ID chức năng xem loại BĐS
        $user = Auth::guard('sanctum')->user();
        $check = PhanQuyen::where('id_chuc_vu', $user->id_chuc_vu)
            ->where('id_chuc_nang', $id_chuc_nang)
            ->first();
        if (!$user->is_super && !$check) {
            return response()->json([
                'status' => false,
                'message' => "Bạn không có quyền thực hiện chức năng này"
            ], 403);
        }
        $data = LoaiBatDongSan::find($request->id);
        $data->update([
            'is_active' => $request->is_active
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật trạng thái thành công',
            'data' => $data
        ]);
    }

    // Lấy loại bất động sản cho Môi Giới 
    public function getDataMoiGioi(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => "Bạn cần đăng nhập để thực hiện chức năng này"
            ], 401);
        }
        $data = LoaiBatDongSan::all();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }
}
