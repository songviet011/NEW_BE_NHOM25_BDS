<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeletePhanQuyenRequest;
use App\Http\Requests\PhanQuyenRequest;
use App\Models\PhanQuyen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PhanQuyenController extends Controller
{
    // Lấy danh sách phân quyền theo chức vụ
    public function getData($id_chuc_vu)
    {
        $login = Auth::guard('sanctum')->user();
        if (!$login->is_super) {
            $id_chuc_nang_check = 58;
            
            $current_user_role_id = $login->id_chuc_vu; 

            $check_quyen = PhanQuyen::where('id_chuc_vu', $current_user_role_id)
                ->where('id_chuc_nang', $id_chuc_nang_check)
                ->first();

            if (!$check_quyen) {
                return response()->json([
                    'status' => false, 
                    'message' => "bạn không có quyền!"
                ], 403); 
            }
        }
        $data = PhanQuyen::where('id_chuc_vu', $id_chuc_vu)
            ->join('chuc_nangs', 'phan_quyens.id_chuc_nang', '=', 'chuc_nangs.id')
            ->join('chuc_vus', 'phan_quyens.id_chuc_vu', '=', 'chuc_vus.id')
            ->select('phan_quyens.*', 'chuc_nangs.ten_chuc_nang', 'chuc_vus.ten_chuc_vu')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    // Thêm phân quyền
    public function store(PhanQuyenRequest $request)
    {
        // $login = Auth::guard('sanctum')->user();
        // if (!$login->is_super) {
        //     $id_chuc_nang_check = 55;
        //     $current_user_role_id = $login->id_chuc_vu;

        //     $check_quyen = PhanQuyen::where('id_chuc_vu', $current_user_role_id)
        //         ->where('id_chuc_nang', $id_chuc_nang_check)
        //         ->first();

        //     if (!$check_quyen) {
        //         return response()->json([
        //             'status' => false,
        //             'message' => "bạn không có quyền!"
        //         ], 403);
        //     }
        // }

        $data = PhanQuyen::firstOrCreate([
            'id_chuc_vu'   => $request->id_chuc_vu,
            'id_chuc_nang' => $request->id_chuc_nang,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Đã thêm phân quyền thành công!',
            'data'    => $data
        ]);
    }

    // Xóa phân quyền
    public function destroy(DeletePhanQuyenRequest $request)
    {
        // $login = Auth::guard('sanctum')->user();

        // if (!$login->is_super) {
        //     $id_chuc_nang_check = 56;
        //     $current_user_role_id = $login->id_chuc_vu;

        //     $check_quyen = PhanQuyen::where('id_chuc_vu', $current_user_role_id)
        //         ->where('id_chuc_nang', $id_chuc_nang_check)
        //         ->first();

        //     if (!$check_quyen) {
        //         return response()->json([
        //             'status' => false,
        //             'message' => "bạn không có quyền!"
        //         ], 403);
        //     }
        // }

        $data = PhanQuyen::where('id_chuc_vu', $request->id_chuc_vu)
            ->where('id_chuc_nang', $request->id_chuc_nang)
            ->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Đã xóa phân quyền thành công!',
        ]);
    }
}