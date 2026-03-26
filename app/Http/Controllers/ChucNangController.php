<?php

namespace App\Http\Controllers;

use App\Models\ChucNang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChucNangController extends Controller
{
    public function index()
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $data = ChucNang::paginate(10);
            return response()->json([
                'status' => 1,
                'data' => $data
            ]);
        } else {
            return response()->json([
                'status'  => 0,
                'message' => "Không có quyền truy cập",
            ], 401);
        }
    }

    public function show($id)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $chucNang = ChucNang::find($id);
            if ($chucNang) {
                return response()->json([
                    'status' => 1,
                    'data' => $chucNang
                ]);
            } else {
                return response()->json([
                    'status'  => 0,
                    'message' => "Không tìm thấy chức năng",
                ], 404);
            }
        } else {
            return response()->json([
                'status'  => 0,
                'message' => "Không có quyền truy cập",
            ], 401);
        }
    }

    public function store(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $tenChucNang = $request->input('ten_chuc_nang');

            if (empty($tenChucNang) || !is_string($tenChucNang)) {
                return response()->json([
                    'status'  => 0,
                    'message' => "Dữ liệu không hợp lệ: 'ten_chuc_nang' là bắt buộc và phải là chuỗi.",
                ], 422);
            }

            $chucNang = ChucNang::create([
                'ten_chuc_nang' => $tenChucNang
            ]);

            return response()->json([
                'status' => 1,
                'message' => 'Tạo mới thành công',
                'data' => $chucNang
            ], 201);
        } else {
            return response()->json([
                'status'  => 0,
                'message' => "Không có quyền truy cập",
            ], 401);
        }
    }

    public function update(Request $request, $id)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $chucNang = ChucNang::find($id);

            if (!$chucNang) {
                return response()->json([
                    'status'  => 0,
                    'message' => "Không tìm thấy chức năng",
                ], 404);
            }

            $tenChucNang = $request->input('ten_chuc_nang');

            if (empty($tenChucNang) || !is_string($tenChucNang)) {
                return response()->json([
                    'status'  => 0,
                    'message' => "Dữ liệu không hợp lệ: 'ten_chuc_nang' là bắt buộc và phải là chuỗi.",
                ], 422);
            }

            $chucNang->ten_chuc_nang = $tenChucNang;
            $chucNang->save();

            return response()->json([
                'status' => 1,
                'message' => 'Cập nhật thành công',
                'data' => $chucNang
            ]);
        } else {
            return response()->json([
                'status'  => 0,
                'message' => "Không có quyền truy cập",
            ], 401);
        }
    }

    public function destroy($id)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $chucNang = ChucNang::find($id);

            if (!$chucNang) {
                return response()->json([
                    'status'  => 0,
                    'message' => "Không tìm thấy chức năng",
                ], 404);
            }

            $chucNang->delete();

            return response()->json([
                'status' => 1,
                'message' => 'Xóa thành công'
            ]);
        } else {
            return response()->json([
                'status'  => 0,
                'message' => "Không có quyền truy cập",
            ], 401);
        }
    }
}
