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
        $email = $request->input('email');
        $password = $request->input('password');

        $admin = Admin::where('email', $email)->first();

        if (! $admin || ! Hash::check($password, $admin->password)) {
            return response()->json([
                'status'  => 0,
                'message' => "Thông tin đăng nhập không chính xác",
            ]);
        }

        $token = $admin->createToken('admin-token')->plainTextToken;

        return response()->json([
            'status' => 1,
            'message' => "Đăng nhập thành công",
            'data' => [
                'token' => $token,
                'admin' => $admin,
            ]
        ]);
    }

    public function checkToken()
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            return response()->json([
                'status' => 1,
                'data' => ['ten' => $user->ten],
            ]);
        } else {
            return response()->json([
                'status'  => 0,
                'message' => "Chưa đăng nhập",
            ]);
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
