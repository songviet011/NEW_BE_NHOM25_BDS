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
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $moiGioi = MoiGioi::where('email', $request->email)->first();

        if (! $moiGioi || ! Hash::check($request->password, $moiGioi->password) || ! $moiGioi->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Thông tin đăng nhập sai hoặc tài khoản bị khóa.'],
            ]);
        }

        $token = $moiGioi->createToken('moi-gioi-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'moi_gioi' => $moiGioi,
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'ten' => 'required|string|max:255',
            'email' => 'required|email|unique:moi_giois,email',
            'so_dien_thoai' => 'required|string',
            'password' => 'required|min:8|confirmed',
            'zalo_link' => 'nullable|url',
            'mo_ta' => 'nullable|string',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $moiGioi = MoiGioi::create($validated);

        $token = $moiGioi->createToken('moi-gioi-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'moi_gioi' => $moiGioi,
        ], 201);
    }

    public function profile()
    {
        $moiGioi = Auth::guard('sanctum')->user();

        return response()->json($moiGioi);
    }

    public function checkToken()
    {
        $moiGioi = Auth::guard('sanctum')->user();

        if ($moiGioi) {
            return response()->json([
                'status' => 1,
                'ten' => $moiGioi->ten,
            ]);
        }

        return response()->json([
            'status' => 0,
            'message' => 'Chưa đăng nhập',
        ]);
    }

    // Admin CRUD
    public function getData(Request $request)
    {
        $query = MoiGioi::query();

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
        $moiGioi = MoiGioi::findOrFail($request->id);

        $validated = $request->validate([
            'ten' => 'string|max:255',
            'email' => 'email|unique:moi_giois,email,' . $moiGioi->id,
            'so_dien_thoai' => 'string',
            'is_active' => 'boolean',
        ]);

        $moiGioi->update($validated);

        return response()->json($moiGioi);
    }

    public function destroy(Request $request)
    {
        MoiGioi::findOrFail($request->id)->delete();

        return response()->json(['message' => 'Xóa thành công']);
    }
}

