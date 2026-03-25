<?php

namespace App\Http\Controllers;

use App\Models\BatDongSan;
use App\Models\LoaiBatDongSan;
use App\Models\DiaChi;
use App\Models\TinhThanh;
use App\Models\QuanHuyen;
use Illuminate\Http\Request;

class AIDinhGiaController extends Controller
{
    public function predictPrice(Request $request)
    {
        $validated = $request->validate([
            'loai_id' => 'exists:loai_bat_dong_sans,id',
            'dien_tich' => 'numeric',
            'tinh_id' => 'exists:tinh_thanhs,id',
            'quan_id' => 'exists:quan_huyens,id',
            'so_phong_ngu' => 'integer',
            // Add more features
        ]);

        // Simple ML placeholder - mean price by loai/dien_tich/tinh
        $avgPrice = BatDongSan::where('loai_id', $validated['loai_id'])
            ->where('tinh_id', $validated['tinh_id'])
            ->avg('gia') * ($validated['dien_tich'] / 100);

        $predicted = $avgPrice ?: 0;

        return response()->json([
            'gia_du_doan' => $predicted,
            'note' => 'Cần ML model thật (php-ml or Python API)',
        ]);
    }
}
