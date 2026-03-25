<?php

namespace App\Http\Controllers;

use App\Models\GiaoDich;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GiaoDichController extends Controller
{
    public function getData()
    {
        $user = Auth::guard('sanctum')->user();

        $giaoDichs = GiaoDich::where('moi_gioi_id', $user->id)
            ->with('goiTin')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($giaoDichs);
    }

    public function dataMoiGioi()
    {
        return $this->getData();
    }
}
