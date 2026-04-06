<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\MoiGioi;
use App\Models\KhachHang;
use App\Http\Requests\AdminLoginRequest;
use App\Http\Requests\AdminUpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
   public function login(AdminLoginRequest $request)
    {
        $admin = Admin::where('email', $request->email)->first();

        // Bước 2: Kiểm tra tồn tại và mật khẩu đúng
        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Email hoặc mật khẩu không đúng'
            ], 401);
        }

        // Bước 3: Xóa token cũ (optional - để tránh nhiều token rác)
        // $admin->tokens()->delete();

        // Bước 4: Tạo token mới
        $token = $admin->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Đăng nhập thành công',
            'token' => $token,
            'token_type' => 'Bearer',
            'data' => $admin
        ], 200);
    }

    public function checkToken()
    {
        $user = Auth::guard('sanctum')->user();
        if ($user && $user instanceof Admin) {
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
            $user->currentAccessToken()->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Đăng xuất thành công'
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy người dùng hoặc token không hợp lệ'
            ], 401);
        }
    }

    public function logoutAll()
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $user->tokens()->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Đăng xuất tất cả thiết bị thành công'
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy người dùng hoặc token không hợp lệ'
            ], 401);
        }
    }

    //Gửi OTP
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = KhachHang::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => 0,
                'message' => 'Email không tồn tại'
            ]);
        }

        $otp = rand(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $otp,
                'created_at' => now()
            ]
        );

        return response()->json([
            'status' => 1,
            'message' => 'OTP đã gửi',
            'otp' => $otp // dev thôi
        ]);
    }

    //Reset password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required',
            'password' => 'required|min:6'
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->otp)
            ->first();

        if (!$record) {
            return response()->json([
                'status' => 0,
                'message' => 'OTP không đúng'
            ]);
        }

        $user = Admin::where('email', $request->email)->first();

        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Đổi mật khẩu thành công'
        ]);
    }
}
