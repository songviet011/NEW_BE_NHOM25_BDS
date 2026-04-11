<?php

namespace App\Http\Controllers;

use App\Http\Requests\YeuThichRequest;
use App\Models\BatDongSan;
use App\Models\ThongBao;
use App\Models\YeuThich;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
                YeuThich::create([
                    'moi_gioi_id' => $bds->moi_gioi_id,
                    'khach_hang_id' => $user->id,
                    'bds_id' => $bdsId,
                    'noi_dung' => "Khach hang {$user->ten} da tha tim BDS {$bdsId}",
                ]);

                ThongBao::create([
                    'moi_gioi_id' => $bds->moi_gioi_id,
                    'khach_hang_id' => $user->id,
                    'bat_dong_san_id' => $bdsId,
                    'tieu_de' => 'Khach hang vua tuong tac bat dong san',
                    'noi_dung' => "Khach hang {$user->ten} da tha tim BDS {$bdsId}",
                    'trang_thai' => 0,
                ]);
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
            ->with('batDongSan', 'batDongSan.moiGioi')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $yeuThichs,
        ]);
    }
}
