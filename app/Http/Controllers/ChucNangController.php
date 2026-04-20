<?php

namespace App\Http\Controllers;

use App\Models\ChucNang;
use App\Http\Requests\CreateChucNangRequest;
use App\Models\PhanQuyen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChucNangController extends Controller
{
    public function getData(Request $request)
    {
        $id_chuc_nang = 4;
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
        $data = ChucNang::all();
        return response()->json([
            'data' => $data,
        ]);
    }
}
