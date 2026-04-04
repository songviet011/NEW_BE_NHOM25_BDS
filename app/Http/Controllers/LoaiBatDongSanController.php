<?php

namespace App\Http\Controllers;

use App\Models\LoaiBatDongSan;
use App\Models\TinhThanh;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoaiBatDongSanController extends Controller
{
    public function getAll()
    {
        return response()->json([
            'status' => 1,
            'data' => LoaiBatDongSan::all()
        ]);
    }
}
