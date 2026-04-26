<?php

namespace App\Http\Controllers;

use App\Http\Requests\DatLaiMatKhauRequest;
use App\Http\Requests\DeleteKhachHangRequest;
use App\Models\KhachHang;
use App\Http\Requests\KhachHangLoginRequest;
use App\Http\Requests\KhachHangRegisterRequest;
use App\Http\Requests\UpdateKhachHangRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Requests\DestroyRequest;
use App\Http\Requests\GuiMaQuenMatKhauRequest;
use App\Http\Requests\KhachHangUpdatePasswordRequest;
use App\Http\Requests\KhachHangUpdateProfileRequest;
use App\Http\Requests\SearchKhachHangRequest;
use App\Http\Requests\updatePasswordKhachHangRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SendOtpRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Http\Requests\XacThucMaQuenMatKhauRequest;
use App\Mail\ResetPasswordCodeMail;
use App\Models\PhanQuyen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class KhachHangController extends Controller
{
    //Khách hàng đăng nhập
    public function login(KhachHangLoginRequest $request)
    {
        $user = KhachHang::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 0,
                'message' => 'Email hoặc mật khẩu không đúng'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 1,
            'message' => 'Đăng nhập thành công',
            'token' => $token,
            'token_type' => 'Bearer',
            'user_type' => 'khach-hang',
            'data' => $user
        ], 200);
    }

    // Kiểm tra token còn hiệu lực không
    public function checkToken()
    {
        $user = Auth::guard('sanctum')->user();
        if ($user && $user instanceof KhachHang) {
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

    // 1. Gửi mã xác nhận quên mật khẩu
    public function guiMaQuenMatKhau(GuiMaQuenMatKhauRequest $request)
    {
        $kh = KhachHang::where('email', $request->email)->first();

        if (!$kh) {
            return response()->json([
                'status'  => 0,
                'message' => 'Email không tồn tại trong hệ thống!',
            ], 404);
        }

        $code = rand(100000, 999999);

        $kh->update([
            'hash_reset' => Hash::make($code),
            'hash_reset_expires_at' => now()->addMinutes(5),
        ]);

        // Gửi mail
        Mail::to($kh->email)->send(new ResetPasswordCodeMail($code));
        // Mail::raw('Test mail', function ($message) {
        //     $message->to('songviet011@gmail.com')
        //         ->subject('Test');
        // });

        return response()->json([
            'status'  => 1,
            'message' => 'Đã gửi mã xác nhận quên mật khẩu đến email của bạn!',
        ]);
    }

    // 2) Xác thực mã
    public function xacThucMaQuenMatKhau(XacThucMaQuenMatKhauRequest $request)
    {
        $kh = KhachHang::where('email', $request->email)->first();

        if (!$kh || !$kh->hash_reset) {
            return response()->json([
                'status'  => 0,
                'message' => 'Mã không hợp lệ!',
            ], 400);
        }

        // Check hết hạn
        if ($kh->hash_reset_expires_at < now()) {
            return response()->json([
                'status' => 0,
                'message' => 'Mã đã hết hạn!',
            ], 400);
        }

        // Check đúng mã
        if (!Hash::check($request->code, $kh->hash_reset)) {
            return response()->json([
                'status' => 0,
                'message' => 'Mã không đúng!',
            ], 400);
        }

        return response()->json([
            'status'  => 1,
            'message' => 'Mã xác nhận hợp lệ.',
        ]);
    }

    // 3) Đặt lại mật khẩu
    public function datLaiMatKhau(DatLaiMatKhauRequest $request)
    {
        \Log::info('Reset password attempt:', [
            'email' => $request->email,
            'code' => $request->code,
        ]);

        $kh = KhachHang::where('email', $request->email)->first();

        if (!$kh) {
            \Log::error('Email not found');
            return response()->json([
                'status'  => 0,
                'message' => 'Email không tồn tại!',
            ], 400);
        }

        if (!$kh->hash_reset) {
            \Log::error('Hash reset is null - code already used or not generated');
            return response()->json([
                'status'  => 0,
                'message' => 'Mã xác nhận không tồn tại! Có thể đã được sử dụng.',
                'debug' => [
                    'hash_reset' => $kh->hash_reset,
                    'expires_at' => $kh->hash_reset_expires_at,
                    'now' => now(),
                ]
            ], 400);
        }

        if ($kh->hash_reset_expires_at < now()) {
            return response()->json([
                'status' => 0,
                'message' => 'Mã đã hết hạn!',
                'debug' => [
                    'expires_at' => $kh->hash_reset_expires_at,
                    'now' => now(),
                ]
            ], 400);
        }

        if (!Hash::check($request->code, $kh->hash_reset)) {
            return response()->json([
                'status' => 0,
                'message' => 'Mã không đúng!',
            ], 400);
        }

        $kh->update([
            'password' => Hash::make($request->password),
            'hash_reset' => null,
            'hash_reset_expires_at' => null,
        ]);

        return response()->json([
            'status'  => 1,
            'message' => 'Đặt lại mật khẩu thành công!',
        ]);
    }
}
