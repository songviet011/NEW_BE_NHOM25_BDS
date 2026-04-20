<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminDoiMatKhauRequest;
use App\Models\Admin;
use App\Models\MoiGioi;
use App\Models\KhachHang;
use App\Http\Requests\AdminLoginRequest;
use App\Http\Requests\AdminUpdateProfileRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SendOtpRequest;
use App\Http\Requests\VerifyOtpRequest;
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

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json([
                'status' => 0,  // ✅ Integer 0
                'message' => 'Email hoặc mật khẩu không đúng'
            ], 401);
        }

        $token = $admin->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 1,  // ✅ Integer 1
            'message' => 'Đăng nhập thành công',
            'token' => $token,
            'token_type' => 'Bearer',
            'data' => $admin  // Không cần thêm 'role' vì FE tự xác định qua user_type
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

    public function profile(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'id' => $user->id,
                'ten' => $user->ten,
                'email' => $user->email,
                'so_dien_thoai' => $user->so_dien_thoai,
                'mo_ta' => $user->mo_ta,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,

            ]
        ]);
    }

    public function updateProfile(AdminUpdateProfileRequest $request)
    {
        $user = Auth::guard('sanctum')->user();

        // Cập nhật thông tin
        $user->update([
            'ten' => $request->ten,
            'email' => $request->email,
            'so_dien_thoai' => $request->so_dien_thoai,
            'mo_ta' => $request->mo_ta,
            'create_at' => now(),
            'update_at' => now(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật profile thành công!',
            'data' => $user
        ]);
    }

    public function doiMatKhau(AdminDoiMatKhauRequest $request)
    {
        // ✅ 1. Lấy user đang đăng nhập (qua Sanctum)
        $user = Auth::guard('sanctum')->user();

        // ✅ 2. Kiểm tra mật khẩu cũ
        if (!Hash::check($request->mat_khau_cu, $user->password)) {
            return response()->json([
                'status'  => false,
                'message' => 'Mật khẩu cũ không đúng!',
            ], 400);
        }
        $currentTokenId = $user->currentAccessToken()->id;

        $user->tokens()->where('id', '!=', $currentTokenId)->delete();

        $user->password = Hash::make($request->mat_khau_moi);
        $user->save();

        return response()->json([
            'status'  => true,
            'message' => 'Đổi mật khẩu thành công! Các thiết bị khác đã được đăng xuất.',
        ]);
    }

    // public function getActiveSessions(Request $request)
    // {
    //     $user = Auth::guard('sanctum')->user();
    //     $currentTokenId = $user->currentAccessToken()->id;

    //     $sessions = $user->tokens()->get()->map(function ($token) use ($currentTokenId) {
    //         return [
    //             'id' => $token->id,
    //             'name' => $token->name, // Có thể lưu device name khi tạo token
    //             'created_at' => $token->created_at,
    //             'last_used_at' => $token->last_used_at,
    //             'is_current' => $token->id === $currentTokenId,
    //         ];
    //     });

    //     return response()->json(['sessions' => $sessions]);
    // }

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
        try {
            // Lấy user từ token hiện tại (trước khi xóa)
            $user = Auth::guard('sanctum')->user();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không tìm thấy người dùng'
                ], 401);
            }

            // ✅ QUAN TRỌNG: Xóa token hiện tại TRƯỚC
            $currentToken = $user->currentAccessToken();
            if ($currentToken) {
                $currentToken->delete();
            }

            // ✅ Sau đó xóa tất cả token còn lại
            $user->tokens()->delete();

            // ✅ Trả về 200 (không phải 401)
            return response()->json([
                'status' => 'success',
                'message' => 'Đã đăng xuất tất cả thiết bị'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    //Gửi OTP
    public function sendOtp(SendOtpRequest $request)
    {

        $user = KhachHang::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
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
            'status' => true,
            'message' => 'OTP đã gửi',
            'otp' => $otp // dev thôi
        ]);
    }

    public function verifyOtp(VerifyOtpRequest $request)
    {
        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->otp)
            ->first();

        if (!$record) {
            return response()->json([
                'status' => false,
                'message' => 'OTP không đúng'
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'OTP hợp lệ'
        ]);
    }

    //Reset password
    public function resetPassword(ResetPasswordRequest $request)
    {

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->otp)
            ->first();

        if (!$record) {
            return response()->json([
                'status' => false,
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
            'status' => true,
            'message' => 'Đổi mật khẩu thành công'
        ]);
    }
}
