<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\MoiGioi;
use App\Models\KhachHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if (! $admin || ! Hash::check($request->password, $admin->password)) {
            throw ValidationException::withMessages([
                'email' => ['credentials sai.'],
            ]);
        }

        $token = $admin->createToken('admin-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'admin' => $admin,
        ]);
    }

    public function checkToken()
    {
        $admin = Auth::guard('sanctum')->user();

        if ($admin) {
            return response()->json([
                'status' => 1,
                'ten' => $admin->ten,
            ]);
        }

        return response()->json([
            'status' => 0,
            'message' => 'Chưa đăng nhập',
        ]);
    }

    public function profile()
    {
        $admin = Auth::guard('sanctum')->user();

        return response()->json($admin);
    }

    public function updateProfile(Request $request)
    {
        $admin = Auth::guard('sanctum')->user();

        $validated = $request->validate([
            'ten' => 'string|max:255',
            'email' => 'email|unique:admins,email,' . $admin->id,
        ]);

        $admin->update($validated);

        return response()->json($admin);
    }

    public function logout()
    {
        Auth::guard('sanctum')->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Đăng xuất thành công']);
    }

    public function logoutAll()
    {
        Auth::guard('sanctum')->user()->tokens()->delete();

        return response()->json(['message' => 'Đăng xuất tất cả thiết bị']);
    }
}

