<?php

namespace App\Http\Controllers;

use App\Models\BatDongSan;
use App\Models\MoiGioi;
use App\Models\LoaiBatDongSan;
use App\Models\TrangThaiBatDongSan;
use App\Models\TinhThanh;
use App\Models\QuanHuyen;
use App\Models\DiaChi;
use App\Models\HinhAnhBatDongSan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BatDongSanController extends Controller
{
    // Public
    public function getAllPublic(Request $request)
    {
        $query = BatDongSan::with(['loai', 'trangThai', 'moiGioi', 'tinh', 'quan', 'diaChi', 'hinhAnh'])
            ->where('is_duyet', true);

        return response()->json($query->paginate(10));
    }

    public function xemChiTiet($id)
    {
        $bds = BatDongSan::with(['loai', 'trangThai', 'moiGioi', 'tinh', 'quan', 'diaChi', 'hinhAnh'])->findOrFail($id);

        return response()->json($bds);
    }

    public function getMoiGioi($id)
    {
        $bds = BatDongSan::findOrFail($id);
        return response()->json($bds->moiGioi);
    }

    public function search(Request $request)
    {
        $query = BatDongSan::query();

        $query->when($request->tinh_id, fn($q) => $q->where('tinh_id', $request->tinh_id));
        $query->when($request->loai_id, fn($q) => $q->where('loai_id', $request->loai_id));
        $query->when($request->gia_min, fn($q) => $q->where('gia', '>=', $request->gia_min));
        $query->when($request->gia_max, fn($q) => $q->where('gia', '<=', $request->gia_max));
        $query->when($request->tieu_de, fn($q) => $q->where('tieu_de', 'like', '%' . $request->tieu_de . '%'));

        $query->where('is_duyet', true);

        return response()->json($query->with(['loai', 'moiGioi'])->paginate(10));
    }

    public function map()
    {
        $bdss = BatDongSan::where('is_duyet', true)->select('id', 'dia_chi_id', 'gia', 'dien_tich')->with('diaChi')->get();

        return response()->json($bdss);
    }

    // Admin
    public function getData(Request $request)
    {
        $moiGioiId = Auth::guard('sanctum')->user()->id;

        $query = BatDongSan::with(['loai', 'trangThai', 'moiGioi']);

        if ($request->moi_gioi_id) {
            $query->where('moi_gioi_id', $request->moi_gioi_id);
        }

        return response()->json($query->paginate(10));
    }

    public function searchAdmin(Request $request)
    {
        $query = BatDongSan::query();

        $query->when($request->tinh_id, fn($q) => $q->where('tinh_id', $request->tinh_id));
        $query->when($request->loai_id, fn($q) => $q->where('loai_id', $request->loai_id));
        $query->when($request->gia_min, fn($q) => $q->where('gia', '>=', $request->gia_min));
        $query->when($request->tieu_de, fn($q) => $q->where('tieu_de', 'like', '%' . $request->tieu_de . '%'));

        return response()->json($query->paginate(10));
    }

    public function duyetTin(Request $request)
    {
        $bds = BatDongSan::findOrFail($request->id);
        $bds->is_duyet = true;
        $bds->save();

        return response()->json(['message' => 'Duyệt thành công']);
    }

    public function changeStatus(Request $request)
    {
        $bds = BatDongSan::findOrFail($request->id);
        $bds->trang_thai_id = $request->trang_thai_id;
        $bds->save();

        return response()->json(['message' => 'Cập nhật trạng thái thành công']);
    }

    public function delete(Request $request)
    {
        BatDongSan::findOrFail($request->id)->delete();

        return response()->json(['message' => 'Xóa thành công']);
    }

    // MoiGioi
    public function dataMoiGioi(Request $request)
    {
        $moiGioi = Auth::guard('sanctum')->user();
        $query = BatDongSan::where('moi_gioi_id', $moiGioi->id)->with(['loai', 'trangThai']);

        return response()->json($query->paginate(10));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tieu_de' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
            'gia' => 'required|numeric',
            'dien_tich' => 'required|numeric',
            'loai_id' => 'required|exists:loai_bat_dong_sans,id',
            'trang_thai_id' => 'required|exists:trang_thai_bat_dong_sans,id',
            'tinh_id' => 'required|exists:tinh_thanhs,id',
            'quan_id' => 'required|exists:quan_huyens,id',
            'dia_chi_id' => 'required|exists:dia_chis,id',
            'so_phong_ngu' => 'nullable|integer',
            'so_phong_tam' => 'nullable|integer',
            'is_noi_bat' => 'boolean',
        ]);

        $moiGioi = Auth::guard('sanctum')->user();
        $validated['moi_gioi_id'] = $moiGioi->id;
        $validated['is_duyet'] = false;

        $bds = BatDongSan::create($validated);

        return response()->json($bds, 201);
    }

    public function update(Request $request, $id)
    {
        $bds = BatDongSan::findOrFail($id);
        $moiGioi = Auth::guard('sanctum')->user();

        if ($bds->moi_gioi_id !== $moiGioi->id) {
            return response()->json(['message' => 'Không có quyền'], 403);
        }

        $validated = $request->validate([
            'tieu_de' => 'required|string|max:255',
            // ... same as store
        ]);

        $bds->update($validated);

        return response()->json($bds);
    }

    public function destroy(Request $request)
    {
        $bds = BatDongSan::findOrFail($request->id);
        $moiGioi = Auth::guard('sanctum')->user();

        if ($bds->moi_gioi_id !== $moiGioi->id) {
            return response()->json(['message' => 'Không có quyền'], 403);
        }

        $bds->delete();

        return response()->json(['message' => 'Xóa thành công']);
    }
}
