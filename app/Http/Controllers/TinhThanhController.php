<?php

namespace App\Http\Controllers;

use App\Models\TinhThanh;
use Illuminate\Http\Request;

class TinhThanhController extends Controller
{
    public function getTinhThanh()
    {
        return response()->json([
        'status' => 1,
        'data' => TinhThanh::all()
    ]);
    }
}
