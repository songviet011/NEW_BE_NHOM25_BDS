<?php

namespace App\Http\Controllers;

use App\Models\BatDongSan;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function map()
    {
        $bdss = BatDongSan::where('is_duyet', true)->select('id', 'dia_chi_id', 'gia', 'dien_tich')->with('diaChi')->get();

        return response()->json(['status' => 1, 'data' => $bdss]);
    }
}
