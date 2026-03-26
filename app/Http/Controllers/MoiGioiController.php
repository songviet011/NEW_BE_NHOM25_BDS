<?php

namespace App\Http\Controllers;

use App\Http\Requests\DestroyRequest;
use App\Http\Requests\MoiGioiLoginRequest;
use App\Http\Requests\MoiGioiRegisterRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Requests\UpdateMoiGioiRequest;
use App\Models\MoiGioi;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class MoiGioiController extends Controller
{
    public function login(MoiGioiLoginRequest $request): JsonResponse
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
                'status' => 0,
                'message' => 'Thông tin đăng nhập sai hoặc tài khoản bị khóa.',
            ]);
        }

        $token = $moiGioi->createToken('moi-gioi-token')->plainTextToken;

        return response()->json([
            'status' => 1,
            'message' => 'Đăng nhập thành công',
            'data' => [
                'token' => $token,
                'moi_gioi' => $moiGioi,
            ],
        ]);
    }

    public function register(MoiGioiRegisterRequest $request): JsonResponse
    {
        $moiGioi = MoiGioi::create([
            'ten' => $request->input('ten'),
            'email' => $request->input('email'),
            'so_dien_thoai' => $request->input('so_dien_thoai'),
            'password' => Hash::make($request->input('password')),
            'zalo_link' => $request->input('zalo_link'),
            'mo_ta' => $request->input('mo_ta'),
            'is_active' => true,
        ]);

        $token = $moiGioi->createToken('moi-gioi-token')->plainTextToken;

        return response()->json([
            'status' => 1,
            'message' => 'Đăng ký thành công',
            'data' => [
                'token' => $token,
                'moi_gioi' => $moiGioi,
            ],
        ]);
    }

    public function profile(): JsonResponse
    {
        /** @var MoiGioi|null $user */
        $user = Auth::guard('sanctum')->user();

        if ($user) {
            return response()->json([
                'status' => 1,
                'data' => $user,
            ]);
        } else {
            return response()->json(['status' => 0, 'message' => "Có lỗi xảy ra"]);
        }

        return response()->json([
            'status' => 0,
            'message' => 'Có lỗi xảy ra',
        ]);
    }

    public function checkToken(): JsonResponse
    {
        /** @var MoiGioi|null $user */
        $user = Auth::guard('sanctum')->user();

        if ($user) {
            return response()->json([
                'status' => 1,
                'data' => [
                    'ten' => $user->ten,
                ],
            ]);
        } else {
            return response()->json(['status' => 0, 'message' => "Có lỗi xảy ra"]);
        }

        return response()->json([
            'status' => 0,
            'message' => 'Có lỗi xảy ra',
        ]);
    }

    public function logout(): JsonResponse
    {
        /** @var MoiGioi|null $user */
        $user = Auth::guard('sanctum')->user();

        if ($user) {
            $token = $user->currentAccessToken();

            if ($token) {
                $token->delete();
            }

            return response()->json([
                'status' => 1,
                'message' => 'Đăng xuất thành công',
            ]);
        }

        return response()->json([
            'status' => 0,
            'message' => 'Có lỗi xảy ra',
        ]);
    }

    public function getData(SearchRequest $request): JsonResponse
    {
        /** @var MoiGioi|null $user */
        $user = Auth::guard('sanctum')->user();

        if ($user) {
            $query = MoiGioi::query();

            if ($request->filled('search')) {
                $search = $request->input('search');

                $query->where(function ($q) use ($search) {
                    $q->where('ten', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('so_dien_thoai', 'like', '%' . $search . '%');
                });
            }

            return response()->json([
                'status' => 1,
                'data' => $query->paginate(10),
            ]);
        }

        return response()->json([
            'status' => 0,
            'message' => 'Có lỗi xảy ra',
        ]);
    }

    public function search(SearchRequest $request): JsonResponse
    {
        return $this->getData($request);
    }

    public function update(UpdateMoiGioiRequest $request): JsonResponse
    {
        /** @var MoiGioi|null $user */
        $user = Auth::guard('sanctum')->user();

        if ($user) {
            $moiGioi = MoiGioi::find($request->input('id'));

            if (! $moiGioi) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Không tìm thấy',
                ]);
            }

            if ($request->filled('ten')) {
                $moiGioi->ten = $request->input('ten');
            }

            if ($request->filled('so_dien_thoai')) {
                $moiGioi->so_dien_thoai = $request->input('so_dien_thoai');
            }

            if ($request->has('is_active')) {
                $moiGioi->is_active = $request->boolean('is_active');
            }

            $moiGioi->save();

            return response()->json([
                'status' => 1,
                'message' => 'Cập nhật thành công',
                'data' => $moiGioi,
            ]);
        }

        return response()->json([
            'status' => 0,
            'message' => 'Có lỗi xảy ra',
        ]);
    }

    public function destroy(DestroyRequest $request): JsonResponse
    {
        /** @var MoiGioi|null $user */
        $user = Auth::guard('sanctum')->user();

        if ($user) {
            $moiGioi = MoiGioi::find($request->input('id'));

            if (! $moiGioi) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Không tìm thấy',
                ]);
            }

            $moiGioi->delete();

            return response()->json([
                'status' => 1,
                'message' => 'Xóa thành công',
            ]);
        }

        return response()->json([
            'status' => 0,
            'message' => 'Có lỗi xảy ra',
        ]);
    }
}
