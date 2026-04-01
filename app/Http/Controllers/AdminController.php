<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\MoiGioi;
use App\Models\KhachHang;
use App\Http\Requests\AdminLoginRequest;
use App\Http\Requests\AdminUpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    public function login(AdminLoginRequest $request)
    {
        $user = Auth::guard('admin')->attempt([
            'email' => $request->email,
            'password' => $request->password,
        ]);

         if ($user) {
            $user = Auth::guard('admin')->user();
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'status' => 'success',
                'message' => 'Dang nhap thanh cong',
                'token' => $token,
                'data' => $user
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Email hoặc mật khẩu không đúng'
            ], 401);
        }

        return response()->json([
            'data' => $request->validate(),
        ], 200);
    }

    public function checkToken()
    {
         $user = Auth::guard('sanctum')->user();
        if ($user) {
            return response()->json([
                'status' => 'success',
                'data' => $user,
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Token không hợp lệ'
            ], 401);
        }
    }

    public function profile()
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            return response()->json([
                'status' => 1,
                'data' => $user
            ]);
        } else {
            return response()->json([
                'status'  => 0,
                'message' => "Có lỗi xảy ra",
            ]);
        }
    }

    public function updateProfile(AdminUpdateProfileRequest $request)
    {
        /** @var Admin|null $user */
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $user->ten = $request->input('ten');
            $user->email = $request->input('email');
            $user->save();

            return response()->json([
                'status' => 1,
                'message' => "Cập nhật thành công",
                'data' => $user
            ]);
        } else {
            return response()->json([
                'status'  => 0,
                'message' => "Có lỗi xảy ra",
            ]);
        }
    }

    public function logout()
    {
        /** @var Admin|null $user */
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $token = $user->currentAccessToken();
            if ($token) {
                $token->delete();
            }

            return response()->json([
                'status'  => 1,
                'message' => "Đăng xuất thành công",
            ]);
        } else {
            return response()->json([
                'status'  => 0,
                'message' => "Có lỗi xảy ra",
            ]);
        }
    }

    public function logoutAll()
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $ds_token = $user->tokens;
            foreach ($ds_token as $key => $value) {
                $value->delete();
            }
            return response()->json([
                'status'  => 1,
                'message' => "Đăng xuất tất cả thiết bị thành công",
            ]);
        } else {
            return response()->json([
                'status'  => 0,
                'message' => "Có lỗi xảy ra",
            ]);
        }
    }
}
