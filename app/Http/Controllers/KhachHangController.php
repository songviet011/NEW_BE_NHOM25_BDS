<?php

namespace App\Http\Controllers;

use App\Models\KhachHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class KhachHangController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $khachHang = KhachHang::where('email', $request->email)->first();

        if (! $khachHang || ! Hash::check($request->password, $khachHang->password) || ! $khachHang->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Thông tin đăng nhập sai hoặc tài khoản bị khóa.'],
            ]);
        }

        $token = $khachHang->createToken('khach-hang-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'khach_hang' => $khachHang,
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'ten' => 'required|string|max:255',
            'email' => 'required|email|unique:khach_hangs,email',
            'so_dien_thoai' => 'required|string',
            'password' => 'required|min:8|confirmed',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $khachHang = KhachHang::create($validated);

        $token = $khachHang->createToken('khach-hang-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'khach_hang' => $khachHang,
        ], 201);
    }

    // Admin CRUD
    public function getData(Request $request)
    {
        $query = KhachHang::query();

        if ($request->search) {
            $query->where('ten', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        return response()->json($query->paginate(10));
    }

    public function search(Request $request)
    {
        return $this->getData($request);
    }

    public function update(Request $request)
    {
        $khachHang = KhachHang::findOrFail($request->id);

        $validated = $request->validate([
            'ten' => 'string|max:255',
            'email' => 'email|unique:khach_hangs,email,' . $khachHang->id,
            'so_dien_thoai' => 'string',
            'is_active' => 'boolean',
        ]);

        $khachHang->update($validated);

        return response()->json($khachHang);
    }

    public function destroy(Request $request)
    {
        KhachHang::findOrFail($request->id)->delete();

        return response()->json(['message' => 'Xóa thành công']);
    }

    public function checkToken()
    {
        $khachHang = Auth::guard('sanctum')->user();

        if ($khachHang) {
            return response()->json([
                'status' => 1,
                'ten' => $khachHang->ten,
            ]);
        }

        return response()->json([
            'status' => 0,
            'message' => 'Chưa đăng nhập',
        ]);
    }
}

