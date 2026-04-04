<?php

namespace App\Http\Controllers;

use App\Models\QuanHuyen;
use Illuminate\Http\Request;

class QuanHuyenController extends Controller
{
    public function getQuanHuyen(Request $request)
    {
        $tinhId = $request->tinh_id;

        if (!$tinhId) {
            return response()->json([
                'status' => 0,
                'message' => 'Thiếu tinh_id'
            ]);
        }

        $quanHuyens = QuanHuyen::where('tinh_id', $tinhId)
            ->select('id', 'ten')
            ->get();

        return response()->json([
            'status' => 1,
            'data' => $quanHuyens
        ]);
    }
}
