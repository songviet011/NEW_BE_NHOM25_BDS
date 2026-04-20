<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChucVucreateRequest;
use App\Http\Requests\ChucVuDeleteRequest;
use App\Http\Requests\ChucVuUpdateRequest;
use App\Models\ChucVu;
use App\Models\PhanQuyen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChucVuController extends Controller
{
    public function getData()
    {
        $id_chuc_nang = 52;
        $user = Auth::guard('sanctum')->user();
        $id_chuc_vu = $user->id_chuc_vu;
        $check_quyen = PhanQuyen::where('id_chuc_vu', $id_chuc_vu)
            ->where('id_chuc_nang', $id_chuc_nang)
            ->first();
        if (!$user->is_super && !$check_quyen) {
            return response()->json([
                'data' => false,
                'message' => "bạn không có quyền thực hiện chức năng này!"
            ]);
        }
        $data = ChucVu::all();
        return response()->json([
            'data' => $data
        ]);
    }
    public function store(ChucVucreateRequest $request)
    {
        $id_chuc_nang = 51;
        $user = Auth::guard('sanctum')->user();
        $id_chuc_vu = $user->id_chuc_vu;
        $check_quyen = PhanQuyen::where('id_chuc_vu', $id_chuc_vu)
            ->where('id_chuc_nang', $id_chuc_nang)
            ->first();
        if (!$user->is_super && !$check_quyen) {
            return response()->json([
                'data' => false,
                'message' => "bạn không có quyền thực hiện chức năng này!"
            ]);
        }
        $data = ChucVu::create([
            'ten_chuc_vu' => $request->ten_chuc_vu,
            'tinh_trang' => $request->tinh_trang ?? 1,
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Thêm chức vụ ' . $data->ten_chuc_vu . ' thành công',
        ]);
    }
    public function update(ChucVuUpdateRequest $request)
    {
        $id_chuc_nang = 53;
        $user = Auth::guard('sanctum')->user();
        $id_chuc_vu = $user->id_chuc_vu;
        $check_quyen = PhanQuyen::where('id_chuc_vu', $id_chuc_vu)
            ->where('id_chuc_nang', $id_chuc_nang)
            ->first();
        if (!$user->is_super && !$check_quyen) {
            return response()->json([
                'data' => false,
                'message' => "bạn không có quyền thực hiện chức năng này!"
            ]);
        }
        $data = ChucVu::where('id', $request->id)
            ->update([
                'ten_chuc_vu' => $request->ten_chuc_vu,
                'tinh_trang' => $request->tinh_trang,
            ]);
        return response()->json([
            'status' => true,
            'message' => 'Cập nhật chức vụ ' . $request->ten_chuc_vu . ' thành công',
        ]);
    }
    public function destroy(ChucVuDeleteRequest $request)
    {
            $id_chuc_nang = 54;
            $user = Auth::guard('sanctum')->user();
            $id_chuc_vu = $user->id_chuc_vu;
            $check_quyen = PhanQuyen::where('id_chuc_vu', $id_chuc_vu)
                ->where('id_chuc_nang', $id_chuc_nang)
                ->first();
            if (!$user->is_super && !$check_quyen) {
                return response()->json([
                    'data' => false,
                    'message' => "bạn không có quyền thực hiện chức năng này!"
                ]);
            }
        $data = ChucVu::where('id', $request->id)->delete();
        return response()->json([
            'status' => true,
            'message' => 'Xóa chức vụ ' . $request->ten_chuc_vu . ' thành công',
        ]);
    }
}
