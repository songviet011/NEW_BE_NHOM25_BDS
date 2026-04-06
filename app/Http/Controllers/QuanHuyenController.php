<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuanHuyenRequest;
use App\Models\QuanHuyen;
use Illuminate\Http\Request;

class QuanHuyenController extends Controller
{
    public function getQuanHuyen(QuanHuyenRequest $request)
    {
        $data = QuanHuyen::where('tinh_id', $request->tinh_id)
            ->select('id', 'ten')
            ->orderBy('ten', 'ASC')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }
}
