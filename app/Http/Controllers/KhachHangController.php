<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteKhachHangRequest;
use App\Models\KhachHang;
use App\Http\Requests\KhachHangLoginRequest;
use App\Http\Requests\KhachHangRegisterRequest;
use App\Http\Requests\UpdateKhachHangRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Requests\DestroyRequest;
use App\Http\Requests\SearchKhachHangRequest;
use App\Http\Requests\updatePasswordKhachHangRequest;
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
        $user = Auth::guard('khach_hang')->attempt([
            'email' => $request->email,
            'password' => $request->password,
        ]);

        if ($user) {
            $user = Auth::guard('khach_hang')->user();
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'status' => 'true',
                'message' => 'Dang nhap thanh cong',
                'token' => $token,
                'data' => $user
            ], 200);
        } else {
            return response()->json([
                'status' => 'false',
                'message' => 'Email hoặc mật khẩu không đúng'
            ], 401);
        }
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

    //Khách hàng đăng ký
    public function register(KhachHangRegisterRequest $request)
    {
        $ten = $request->input('ten');
        $email = $request->input('email');
        $so_dien_thoai = $request->input('so_dien_thoai');
        $password = $request->input('password');
        $password_confirmation = $request->input('password_confirmation');

        if (empty($ten) || empty($email) || empty($so_dien_thoai) || empty($password)) {
            return response()->json([
                'status'  => false,
                'message' => "Vui lòng nhập đầy đủ các trường bắt buộc",
            ]);
        }

        if ($password !== $password_confirmation) {
            return response()->json([
                'status'  => false,
                'message' => "Mật khẩu xác nhận không khớp",
            ]);
        }

        if (KhachHang::where('email', $email)->exists()) {
            return response()->json([
                'status'  => false,
                'message' => "Email đã được sử dụng",
            ]);
        }

        $khachHang = KhachHang::create([
            'ten' => $ten,
            'email' => $email,
            'so_dien_thoai' => $so_dien_thoai,
            'password' => Hash::make($password),
            'zalo_link' => $request->input('zalo_link'),
            'mo_ta' => $request->input('mo_ta'),
        ]);

        $token = $khachHang->createToken('khach-hang-token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => "Đăng ký thành công",
            'data' => [
                'token' => $token,
                'khach_hang' => $khachHang,
            ]
        ]);
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
    public function updateProfile(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $user->ten = $request->input('ten', $user->ten);
            $user->so_dien_thoai = $request->input('so_dien_thoai', $user->so_dien_thoai);
            if ($request->input('password')) {
                $user->password = Hash::make($request->input('password'));
            }
            $user->save();

            return response()->json(['status' => true, 'message' => 'Cập nhật thành công', 'data' => $user]);
        } else {
            return response()->json(['status' => false, 'message' => "Có lỗi xảy ra"]);
        }
    }

    //Khách hàng cập nhật mật khẩu
    public function updatePassword(updatePasswordKhachHangRequest $request)
    {
        $user = Auth::guard('sanctum')->user();
        $data = KhachHang::where('id', $user->id)
            ->where('password', $request->old_password)
            ->first();
        if ($data) {
            $data->update([
                'password' => $request->password,
            ]);
            return response()->json([
                'status'    => true,
                'message'   => 'Cập nhật mật khẩu thành công!',
            ]);
        } else {
            return response()->json([
                'status'    => false,
                'message'   => 'Mật khẩu cũ không đúng!',
            ]);
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
}
