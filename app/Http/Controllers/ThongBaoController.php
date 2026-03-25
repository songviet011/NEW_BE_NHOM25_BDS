<?php

namespace App\Http\Controllers;

use App\Models\ThongBao;
use App\Models\MoiGioi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThongBaoController extends Controller
{
    public function getThongBao()
    {
        $moiGioi = Auth::guard('sanctum')->user();

        $thongBaos = ThongBao::where('moi_gioi_id', $moiGioi->id) // Assume field
            ->with('yeuThich.bds') // Link to YeuThich
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($thongBaos);
    }
}
