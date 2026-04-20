<?php

namespace App\Http\Controllers;

use App\Models\QuanHuyen;
use App\Models\TinhThanh;
use Illuminate\Http\Request;

class TinhThanhController extends Controller
{
    public function getTinhThanh()
    {
        $data = TinhThanh::select('id', 'ten')
            ->orderBy('ten', 'ASC')
            ->get();
        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }
    
}
