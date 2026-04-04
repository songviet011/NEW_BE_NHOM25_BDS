<?php

namespace App\Http\Controllers;

use App\Models\GoiTin;
use App\Models\LichSuGoiTin;
use Illuminate\Http\Request;

class LichSuGoiTinController extends Controller
{
    public function lichSuMua()
    {
        $data = LichSuGoiTin::with(['moiGioi', 'goiTin'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'status' => 1,
            'data' => $data
        ]);
    }
}
