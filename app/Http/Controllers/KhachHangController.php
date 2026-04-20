<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteKhachHangRequest;
use App\Models\KhachHang;
use App\Http\Requests\KhachHangLoginRequest;
use App\Http\Requests\KhachHangRegisterRequest;
use App\Http\Requests\UpdateKhachHangRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Requests\DestroyRequest;
use App\Http\Requests\KhachHangUpdatePasswordRequest;
use App\Http\Requests\KhachHangUpdateProfileRequest;
use App\Http\Requests\SearchKhachHangRequest;
use App\Http\Requests\updatePasswordKhachHangRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SendOtpRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Models\PhanQuyen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class KhachHangController extends Controller
{
    //Khách hàng đăng nhập
    public function login(KhachHangLoginRequest $request)
    {
        $khachhang = KhachHang::where('email', $request->email)->first();

        if (!$khachhang || !Hash::check($request->password, $khachhang->password)) {
            return response()->json([
                'status' => 0,
                'message' => 'Email hoặc mật khẩu không đúng'
            ], 401);
        }

        $token = $khachhang->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 1,
            'message' => 'Đăng nhập thành công',
            'token' => $token,
            'token_type' => 'Bearer',
            'data' => $khachhang
        ], 200);
    }

    // Kiểm tra token còn hiệu lực không
    public function checkToken()
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            return response()->json([
                'status' => "true",
                'data' => ['ten' => $user->ten],
            ]);
        }
        return response()->json([
            'status' => "false",
            'message' => 'Chưa đăng nhập',
        ]);
    }

    // Khách hàng đăng ký
    public function register(KhachHangRegisterRequest $request)
    {
        $khachHang = KhachHang::create([
            'ten'                 => $request->ten,
            'email'               => $request->email,
            'so_dien_thoai'       => $request->so_dien_thoai,
            'password'            => Hash::make($request->password),
            'is_active' => true,
        ]);

        $token = $khachHang->createToken('khach-hang-token')->plainTextToken;

        return response()->json([
            'status'  => true,
            'message' => 'Đăng ký thành công',
            'data'    => [
                'token'        => $token,
                'token_type'   => 'Bearer',
                'khach_hang'   => [
                    'id'            => $khachHang->id,
                    'ten'           => $khachHang->ten,
                    'email'         => $khachHang->email,
                    'so_dien_thoai' => $khachHang->so_dien_thoai,
                ]
            ]
        ], 201);
    }

    // Khách hàng đăng xuất
    public function logout(Request $request)
    {
        /** @var MoiGioi|null $user */
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $user->currentAccessToken()->delete();
            return response()->json([
                'status' => 'true',
                'message' => 'Đăng xuất thành công'
            ], 200);
        } else {
            return response()->json([
                'status' => 'false',
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
                'message' => 'Đã đăng xuất tất cả thiết bị'
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy người dùng hoặc token không hợp lệ'
            ], 401);
        }
    }

    //Khách hàng xem thông tin cá nhân
    public function profile()
    {
        $user = Auth::guard('sanctum')->user();

        if ($user) {
            return response()->json([
                'status' => true,
                'data' => $user,
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Chưa đăng nhập',
        ]);
    }

    //Khách hàng cập nhật thông tin cá nhân
    public function updateProfile(KhachHangUpdateProfileRequest $request)
    {
        $user = Auth::guard('sanctum')->user();
        $data = KhachHang::find($user->id);

        if ($data) {
            $data->update([
                'ten'           => $request->ten,
                'email'         => $request->email,
                'so_dien_thoai' => $request->so_dien_thoai,
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Cập nhật thông tin thành công!',
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'Thông tin khách hàng không tồn tại!',
            ]);
        }
    }

    //Khách hàng cập nhật mật khẩu
    public function updatePassword(KhachHangUpdatePasswordRequest $request)
    {
        $user = Auth::guard('sanctum')->user();
        $data = KhachHang::find($user->id);

        if ($data) {
            if (!Hash::check($request->old_password, $data->password)) {
                return response()->json([
                    'status'  => 0,
                    'message' => 'Mật khẩu cũ không đúng!',
                ], 400);
            }

            $data->update([
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'status'  => 1,
                'message' => 'Cập nhật mật khẩu thành công!',
            ]);
        } else {
            return response()->json([
                'status'  => 0,
                'message' => 'Thông tin khách hàng không tồn tại!',
            ]);
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

        $user = KhachHang::where('email', $request->email)->first();

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

    // Admin lấy danh sách khách hàng
    public function getData(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $data = KhachHang::query();

            return response()->json([
                'status' => true,
                'data' => $data->get()
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Có lỗi xảy ra"
            ]);
        }
    }

    // Admin tìm kiếm khách hàng
    public function search(SearchKhachHangRequest $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => "Có lỗi xảy ra"
            ], 401);
        }

        $keyword = $request->keyword;
        $data = KhachHang::where('ten', 'like', '%' . $keyword . '%')
            ->orWhere('email', 'like', '%' . $keyword . '%')
            ->orWhere('so_dien_thoai', 'like', '%' . $keyword . '%')
            ->get();

        if ($data->isEmpty()) {
            return response()->json([
                'status' => true,  // Vẫn là true vì request hợp lệ, chỉ là không có kết quả
                'message' => 'Không tìm thấy khách hàng nào phù hợp với từ khóa "' . $keyword . '"',
                'data' => []
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    // Admin cập nhật thông tin khách hàng
    public function update(UpdateKhachHangRequest $request)
    {
        $id_chuc_nang = 13;
        $user = Auth::guard('sanctum')->user();
        $check = PhanQuyen::where('id_chuc_vu', $user->id_chuc_vu)
            ->where('id_chuc_nang', $id_chuc_nang)
            ->first();
        if (!$user->is_super &&  !$check) {
            return response()->json([
                'status' => false,
                'message' => "Bạn không có quyền thực hiện chức năng này"
            ]);
        }
        $data = KhachHang::find($request->id);
        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy khách hàng'
            ], 404);
        }
        $data->update([
            'ten' => $request->ten,
            'so_dien_thoai' => $request->so_dien_thoai,
            'email' => $request->email,
            'is_active' => $request->is_active,
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Cập nhật thành công',
            'data' => $data
        ]);
    }

    // Admin xóa khách hàng
    public function destroy(DeleteKhachHangRequest $request)
    {
        $id_chuc_nang = 14;
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

        $data = KhachHang::find($request->id);
        if ($data) {
            $data->delete();
            return response()->json([
                'status' => true,
                'message' => 'Xóa thành công'
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Không tìm thấy khách hàng để xóa'
        ], 404);
    }

    //MoiGioi xem thông tin chi tiết khách hàng
    public function show($id)
    {
        $user = auth('sanctum')->user();

        $khachHang = KhachHang::find($id);

        if (!$khachHang) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy khách hàng'
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $khachHang
        ]);
    }
}
