<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchBatDongSanRequest;
use App\Models\BatDongSan;
use Illuminate\Http\Request;

class ClientHomeController extends Controller
{
    // Public
    public function getAllPublic(Request $request)
    {
        $query = BatDongSan::with(['loai', 'trangThai', 'moiGioi', 'tinh', 'quan', 'diaChi', 'hinhAnh'])
            ->where('is_duyet', true);

        return response()->json([
            'status' => 1,
            'data' => $query->paginate(10)
        ]);
    }

    // public function xemChiTiet($id)
    // {
    //     $bds = BatDongSan::with(['loai', 'trangThai', 'moiGioi', 'tinh', 'quan', 'diaChi', 'hinhAnh'])->find($id);
    //     if ($bds) {
    //         return response()->json(['status' => 1, 'data' => $bds]);
    //     }
    //     return response()->json([
    //         'status' => 0,
    //         'message' => 'Không tìm thấy bất động sản'
    //     ]);
    // }

    // public function getMoiGioi($id)
    // {
    //     $bds = BatDongSan::find($id);
    //     if ($bds) {
    //         return response()->json(['status' => 1, 'data' => $bds->moiGioi]);
    //     }
    //     return response()->json([
    //         'status' => 0,
    //         'message' => 'Không tìm thấy thông tin'
    //     ]);
    // }

    public function search(SearchBatDongSanRequest $request)
    {
        $query = BatDongSan::query();

        $query->when($request->tinh_id, fn($q) => $q->where('tinh_id', $request->tinh_id));
        $query->when($request->loai_id, fn($q) => $q->where('loai_id', $request->loai_id));
        $query->when($request->gia_min, fn($q) => $q->where('gia', '>=', $request->gia_min));
        $query->when($request->gia_max, fn($q) => $q->where('gia', '<=', $request->gia_max));
        $query->when($request->tieu_de, fn($q) => $q->where('tieu_de', 'like', '%' . $request->tieu_de . '%'));

        $query->where('is_duyet', true);

        return response()->json(['status' => 1, 'data' => $query->with(['loai', 'moiGioi'])->paginate(10)]);
    }
}
