<?php

namespace App\Http\Controllers;

use App\Http\Requests\YeuThichRequest;
use App\Models\BatDongSan;
use App\Models\YeuThich;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Events\BatDongSanDuocYeuThich;
use App\Models\ThongBao;

class YeuThichController extends Controller
{
    public function like(YeuThichRequest $request)
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Co loi xay ra',
            ]);
        }

        $bdsId = $request->input('bds_id');

        $yeuThich = YeuThich::where('khach_hang_id', $user->id)
            ->where('bds_id', $bdsId)
            ->first();

        if ($yeuThich) {
            $yeuThich->delete();

            ThongBao::where('khach_hang_id', $user->id)
                ->where('bat_dong_san_id', $bdsId)
                ->delete();

            $message = 'Bo thich';
        } else {
            $bds = BatDongSan::find($bdsId);

            if (!$bds) {
                return response()->json([
                    'status' => false,
                    'message' => 'Khong tim thay BDS',
                ]);
            }

            DB::transaction(function () use ($user, $bds, $bdsId) {
                // FIX: lưu lịch sử yêu thích theo user đang đăng nhập và BĐS được like
                YeuThich::create([
                    'moi_gioi_id' => $bds->moi_gioi_id,
                    'khach_hang_id' => $user->id,
                    'bds_id' => $bdsId,
                    'noi_dung' => "Khach hang {$user->ten} da tha tim BDS {$bds->tieu_de}",
                ]);

                // FIX: dispatch event để listener tạo Thông báo cho môi giới
                event(new BatDongSanDuocYeuThich($user, $bds));
            });

            $message = 'Da thich';
        }

        return response()->json([
            'status' => true,
            'message' => $message,
        ]);
    }

    public function getData()
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Co loi xay ra',
            ]);
        }

        $yeuThichs = YeuThich::where('khach_hang_id', $user->id)
            ->with([
                'batDongSan',
                'batDongSan.moiGioi',
                'batDongSan.hinhAnh',
                'batDongSan.anhDaiDien',
            ])
            ->get();

        // FIX: đảm bảo BĐS trả sẵn URL ảnh đại diện chuẩn cho FE dùng thống nhất
        // $yeuThichs->each(function ($item) {
        //     $item->batDongSan?->setAttribute('anh_dai_dien_url', $item->batDongSan->anhDaiDienUrl);
        // });

        return response()->json([
            'status' => true,
            'data' => $yeuThichs,
        ]);
    }
}
