<?php

namespace App\Http\Controllers;

use App\Models\GoiTin;
use App\Models\GiaoDich;
use App\Models\LichSuGoiTin;
use App\Models\MoiGioi;
use App\Models\KhachHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GoiTinController extends Controller
{
    public function getData()
    {
        $goiTins = GoiTin::all();

        return response()->json($goiTins);
    }

    public function getAll()
    {
        $goiTins = GoiTin::all();

        return response()->json($goiTins);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten_goi' => 'required|string',
            'gia' => 'required|numeric',
            'so_ngay' => 'required|integer',
            'so_luong_tin' => 'required|integer',
        ]);

        $goiTin = GoiTin::create($validated);

        return response()->json($goiTin, 201);
    }

    public function update(Request $request, $id)
    {
        $goiTin = GoiTin::findOrFail($id);

        $validated = $request->validate([
            'ten_goi' => 'string',
            'gia' => 'numeric',
            'so_ngay' => 'integer',
            'so_luong_tin' => 'integer',
        ]);

        $goiTin->update($validated);

        return response()->json($goiTin);
    }

    public function destroy(Request $request)
    {
        GoiTin::findOrFail($request->id)->delete();

        return response()->json(['message' => 'Xóa thành công']);
    }

    public function muaGoi(Request $request)
    {
        $validated = $request->validate([
            'goi_tin_id' => 'required|exists:goi_tins,id',
        ]);

        $user = Auth::guard('sanctum')->user();

        // Giả sử thanh toán success
        $giaoDich = GiaoDich::create([
            'moi_gioi_id' => $user->id, // or khach_hang_id if KhachHang
            'goi_tin_id' => $validated['goi_tin_id'],
            'so_tien' => GoiTin::find($validated['goi_tin_id'])->gia,
            'phuong_thuc' => $request->phuong_thuc ?? 'cash',
            'trang_thai' => 'success',
            'ma_giao_dich' => 'TXN' . time(),
        ]);

        $goiTin = GoiTin::find($validated['goi_tin_id']);
        $ngayKetThuc = now()->addDays($goiTin->so_ngay);

        LichSuGoiTin::create([
            'moi_gioi_id' => $user->id,
            'goi_tin_id' => $validated['goi_tin_id'],
            'ngay_bat_dau' => now(),
            'ngay_ket_thuc' => $ngayKetThuc,
        ]);

        return response()->json($giaoDich);
    }
}
