<?php

namespace App\Http\Controllers;

use App\Http\Requests\deletePhanQuyenRequest;
use App\Http\Requests\PhanQuyenRequest;
use App\Models\PhanQuyen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PhanQuyenController extends Controller
{
    public function getData($id_chuc_vu)
    {
        $login = Auth::guard('admin')->user();

        // ✅ super → cho qua
        if (!$login->is_super) {
            $id_chuc_nang = 58;
            $id_chuc_vu = $login->id_chuc_vu;

            $check_quyen = PhanQuyen::where('id_chuc_vu', $id_chuc_vu)
                ->where('id_chuc_nang', $id_chuc_nang)
                ->first();

            if (!$check_quyen) {
                return response()->json([
                    'data' => false,
                    'message' => "bạn không có quyền!"
                ]);
            }
        }
        $data = PhanQuyen::where('id_chuc_vu', $id_chuc_vu)
            ->join('chuc_nangs', 'phan_quyens.id_chuc_nang', 'chuc_nangs.id')
            ->join('chuc_vus', 'phan_quyens.id_chuc_vu', 'chuc_vus.id')
            ->select('phan_quyens.*', 'chuc_nangs.ten_chuc_nang', 'chuc_vus.ten_chuc_vu')
            ->get();
        return response()->json([
            'data' => $data,
        ]);
    }

    public function store(PhanQuyenRequest $request)
    {
        $login = Auth::guard('admin')->user();

        // ✅ super → cho qua
        if (!$login->is_super) {
            $id_chuc_nang = 55;
            $id_chuc_vu = $login->id_chuc_vu;

            $check_quyen = PhanQuyen::where('id_chuc_vu', $id_chuc_vu)
                ->where('id_chuc_nang', $id_chuc_nang)
                ->first();

            if (!$check_quyen) {
                return response()->json([
                    'data' => false,
                    'message' => "bạn không có quyền!"
                ]);
            }
        }
        $data = PhanQuyen::firstOrCreate([
            'id_chuc_vu'  => $request->id_chuc_vu,
            'id_chuc_nang'  => $request->id_chuc_nang,
        ]);
        return response()->json([
            'status'    => true,
            'message'   => 'Đã thêm phân quyền thành công!',
            'data'     => $data
        ]);
    }

    public function destroy(deletePhanQuyenRequest $request)
    {
        $login = Auth::guard('admin')->user();

        // ✅ super → cho qua
        if (!$login->is_super) {
            $id_chuc_nang = 56;
            $id_chuc_vu = $login->id_chuc_vu;

            $check_quyen = PhanQuyen::where('id_chuc_vu', $id_chuc_vu)
                ->where('id_chuc_nang', $id_chuc_nang)
                ->first();

            if (!$check_quyen) {
                return response()->json([
                    'data' => false,
                    'message' => "bạn không có quyền!"
                ]);
            }
        }
        $data = PhanQuyen::where('id_chuc_vu', $request->id_chuc_vu)
            ->where('id_chuc_nang', $request->id_chuc_nang)
            ->delete();
        return response()->json([
            'status'    => true,
            'message'   => 'Đã xóa phân quyền thành công!',
        ]);
    }
}
