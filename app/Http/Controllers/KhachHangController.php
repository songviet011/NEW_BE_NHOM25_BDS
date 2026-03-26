<?php

namespace App\Http\Controllers;

use App\Models\KhachHang;
use App\Http\Requests\KhachHangLoginRequest;
use App\Http\Requests\KhachHangRegisterRequest;
use App\Http\Requests\UpdateKhachHangRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Requests\DestroyRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class KhachHangController extends Controller
{
    public function login(KhachHangLoginRequest $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        $khachHang = KhachHang::where('email', $email)->first();

        if (! $khachHang || ! Hash::check($password, $khachHang->password) || ! $khachHang->is_active) {
            return response()->json([
                'status'  => 0,
                'message' => "Thông tin đăng nhập sai hoặc tài khoản bị khóa.",
            ]);
        }

        $token = $khachHang->createToken('khach-hang-token')->plainTextToken;

        return response()->json([
            'status' => 1,
            'message' => "Đăng nhập thành công",
            'data' => [
                'token' => $token,
                'khach_hang' => $khachHang,
            ]
        ]);
    }

    public function register(KhachHangRegisterRequest $request)
    {
        $ten = $request->input('ten');
        $email = $request->input('email');
        $so_dien_thoai = $request->input('so_dien_thoai');
        $password = $request->input('password');
        $password_confirmation = $request->input('password_confirmation');

        if (empty($ten) || empty($email) || empty($so_dien_thoai) || empty($password)) {
            return response()->json([
                'status'  => 0,
                'message' => "Vui lòng nhập đầy đủ các trường bắt buộc",
            ]);
        }

        if ($password !== $password_confirmation) {
            return response()->json([
                'status'  => 0,
                'message' => "Mật khẩu xác nhận không khớp",
            ]);
        }

        if (KhachHang::where('email', $email)->exists()) {
            return response()->json([
                'status'  => 0,
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
            'status' => 1,
            'message' => "Đăng ký thành công",
            'data' => [
                'token' => $token,
                'khach_hang' => $khachHang,
            ]
        ]);
    }
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

            return response()->json(['status' => 1, 'message' => 'Cập nhật thành công', 'data' => $user]);
        } else {
            return response()->json(['status' => 0, 'message' => "Có lỗi xảy ra"]);
        }
    }

    // Admin CRUD
    public function getData(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $query = KhachHang::query();

            if ($request->search) {
                $query->where('ten', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            }
            return response()->json(['status' => 1, 'data' => $query->paginate(10)]);
        } else {
            return response()->json(['status' => 0, 'message' => "Có lỗi xảy ra"]);
        }
    }

    public function search(SearchRequest $request)
    {
        return $this->getData($request);
    }

    public function update(UpdateKhachHangRequest $request)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $khachHang = KhachHang::find($request->id);
            if (!$khachHang) {
                return response()->json(['status' => 0, 'message' => 'Không tìm thấy khách hàng']);
            }

            $khachHang->ten = $request->input('ten', $khachHang->ten);
            $khachHang->so_dien_thoai = $request->input('so_dien_thoai', $khachHang->so_dien_thoai);
            $khachHang->is_active = $request->input('is_active', $khachHang->is_active);
            $khachHang->save();

            return response()->json(['status' => 1, 'message' => 'Cập nhật thành công', 'data' => $khachHang]);
        } else {
            return response()->json(['status' => 0, 'message' => "Có lỗi xảy ra"]);
        }
    }

    public function destroy(DestroyRequest $request)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $khachHang = KhachHang::find($request->id);
            if ($khachHang) {
                $khachHang->delete();
                return response()->json(['status' => 1, 'message' => 'Xóa thành công']);
            }
            return response()->json(['status' => 0, 'message' => 'Không tìm thấy khách hàng']);
        } else {
            return response()->json(['status' => 0, 'message' => "Có lỗi xảy ra"]);
        }
    }

    public function checkToken()
    {
        $user = Auth::guard('sanctum')->user();

        if ($user) {
            return response()->json([
                'status' => 1,
                'data' => ['ten' => $user->ten],
            ]);
        }

        return response()->json([
            'status' => 0,
            'message' => 'Chưa đăng nhập',
        ]);
    }
}
