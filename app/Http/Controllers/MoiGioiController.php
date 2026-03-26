<?php

namespace App\Http\Controllers;

use App\Models\MoiGioi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class MoiGioiController extends Controller
{
    public function login(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        if (empty($email) || empty($password)) {
            return response()->json([
                'status'  => 0,
                'message' => "Vui lòng nhập đầy đủ email và mật khẩu",
            ]);
        }

        $moiGioi = MoiGioi::where('email', $email)->first();

        if (! $moiGioi || ! Hash::check($password, $moiGioi->password) || ! $moiGioi->is_active) {
            return response()->json([
                'status'  => 0,
                'message' => "Thông tin đăng nhập sai hoặc tài khoản bị khóa.",
            ]);
        }

        $token = $moiGioi->createToken('moi-gioi-token')->plainTextToken;

        return response()->json([
            'status' => 1,
            'message' => "Đăng nhập thành công",
            'data' => [
                'token' => $token,
                'moi_gioi' => $moiGioi,
            ]
        ]);
    }

    public function register(Request $request)
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
        
        if (MoiGioi::where('email', $email)->exists()) {
            return response()->json([
                'status'  => 0,
                'message' => "Email đã được sử dụng",
            ]);
        }

        $moiGioi = MoiGioi::create([
            'ten' => $ten,
            'email' => $email,
            'so_dien_thoai' => $so_dien_thoai,
            'password' => Hash::make($password),
            'zalo_link' => $request->input('zalo_link'),
            'mo_ta' => $request->input('mo_ta'),
        ]);

        $token = $moiGioi->createToken('moi-gioi-token')->plainTextToken;

        return response()->json([
            'status' => 1,
            'message' => "Đăng ký thành công",
            'data' => [
                'token' => $token,
                'moi_gioi' => $moiGioi,
            ]
        ]);
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
        } else {
            return response()->json(['status' => 0, 'message' => "Có lỗi xảy ra"]);
        }
    }
    
    public function logout()
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $ds_token = $user->tokens;
            foreach ($ds_token as $key => $value) {
                $value->delete();
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

    // Admin CRUD
    public function getData(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $query = MoiGioi::query();
            if ($request->search) {
                $query->where('ten', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            }
            return response()->json(['status' => 1, 'data' => $query->paginate(10)]);
        } else {
            return response()->json(['status' => 0, 'message' => "Có lỗi xảy ra"]);
        }
    }

    public function search(Request $request)
    {
        return $this->getData($request);
    }

    public function update(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $moiGioi = MoiGioi::find($request->id);
            if (!$moiGioi) {
                return response()->json(['status' => 0, 'message' => 'Không tìm thấy']);
            }
            $moiGioi->ten = $request->input('ten', $moiGioi->ten);
            $moiGioi->so_dien_thoai = $request->input('so_dien_thoai', $moiGioi->so_dien_thoai);
            $moiGioi->is_active = $request->input('is_active', $moiGioi->is_active);
            $moiGioi->save();
            
            return response()->json(['status' => 1, 'message' => 'Cập nhật thành công', 'data' => $moiGioi]);
        } else {
            return response()->json(['status' => 0, 'message' => "Có lỗi xảy ra"]);
        }
    }

    public function destroy(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $moiGioi = MoiGioi::find($request->id);
            if ($moiGioi) {
                $moiGioi->delete();
                return response()->json(['status' => 1, 'message' => 'Xóa thành công']);
            }
            return response()->json(['status' => 0, 'message' => 'Không tìm thấy']);
        } else {
             return response()->json(['status' => 0, 'message' => "Có lỗi xảy ra"]);
        }
    }
}
