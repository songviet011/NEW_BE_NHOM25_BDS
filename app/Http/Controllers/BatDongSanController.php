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

        return response()->json([
            'status' => 1,
            'data' => $query->paginate(10)
        ]);
    }

    public function xemChiTiet($id)
    {
        $bds = BatDongSan::with(['loai', 'trangThai', 'moiGioi', 'tinh', 'quan', 'diaChi', 'hinhAnh'])->find($id);
        if ($bds) {
            return response()->json(['status' => 1, 'data' => $bds]);
        }
        return response()->json([
            'status' => 0,
            'message' => 'Không tìm thấy bất động sản'
        ]);
    }

    public function getMoiGioi($id)
    {
        $bds = BatDongSan::find($id);
        if ($bds) {
            return response()->json(['status' => 1, 'data' => $bds->moiGioi]);
        }
        return response()->json([
            'status' => 0,
            'message' => 'Không tìm thấy thông tin'
        ]);
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

        return response()->json(['status' => 1, 'data' => $query->with(['loai', 'moiGioi'])->paginate(10)]);
    }

    public function map()
    {
        $bdss = BatDongSan::where('is_duyet', true)->select('id', 'dia_chi_id', 'gia', 'dien_tich')->with('diaChi')->get();

        return response()->json(['status' => 1, 'data' => $bdss]);
    }

    // Admin
    public function getData(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $query = BatDongSan::with(['loai', 'trangThai', 'moiGioi']);
            if ($request->moi_gioi_id) {
                $query->where('moi_gioi_id', $request->moi_gioi_id);
            }
            return response()->json(['status' => 1, 'data' => $query->paginate(10)]);
        } else {
            return response()->json(['status' => 0, 'message' => "Có lỗi xảy ra"]);
        }
    }

    public function searchAdmin(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $query = BatDongSan::query();
            $query->when($request->tinh_id, fn($q) => $q->where('tinh_id', $request->tinh_id));
            $query->when($request->loai_id, fn($q) => $q->where('loai_id', $request->loai_id));
            $query->when($request->gia_min, fn($q) => $q->where('gia', '>=', $request->gia_min));
            $query->when($request->tieu_de, fn($q) => $q->where('tieu_de', 'like', '%' . $request->tieu_de . '%'));
            return response()->json(['status' => 1, 'data' => $query->paginate(10)]);
        } else {
            return response()->json(['status' => 0, 'message' => "Có lỗi xảy ra"]);
        }
    }

    public function duyetTin(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $bds = BatDongSan::find($request->id);
            if ($bds) {
                $bds->is_duyet = true;
                $bds->save();
                return response()->json(['status' => 1, 'message' => 'Duyệt thành công']);
            }
            return response()->json(['status' => 0, 'message' => 'Không tìm thấy BDS']);
        } else {
            return response()->json(['status' => 0, 'message' => "Có lỗi xảy ra"]);
        }
    }

    public function changeStatus(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $bds = BatDongSan::find($request->id);
            if ($bds) {
                $bds->trang_thai_id = $request->trang_thai_id;
                $bds->save();
                return response()->json(['status' => 1, 'message' => 'Cập nhật trạng thái thành công']);
            }
            return response()->json(['status' => 0, 'message' => 'Không tìm thấy BDS']);
        } else {
            return response()->json(['status' => 0, 'message' => "Có lỗi xảy ra"]);
        }
    }

    public function delete(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $bds = BatDongSan::find($request->id);
            if ($bds) {
                $bds->delete();
                return response()->json(['status' => 1, 'message' => 'Xóa thành công']);
            }
            return response()->json(['status' => 0, 'message' => 'Không tìm thấy BDS']);
        } else {
            return response()->json(['status' => 0, 'message' => "Có lỗi xảy ra"]);
        }
    }

    // MoiGioi
    public function dataMoiGioi(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $query = BatDongSan::where('moi_gioi_id', $user->id)->with(['loai', 'trangThai']);
            return response()->json(['status' => 1, 'data' => $query->paginate(10)]);
        } else {
            return response()->json(['status' => 0, 'message' => "Có lỗi xảy ra"]);
        }
    }

    public function store(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $tieu_de = $request->input('tieu_de');
            $gia = $request->input('gia');
            $dien_tich = $request->input('dien_tich');
            $loai_id = $request->input('loai_id');
            $trang_thai_id = $request->input('trang_thai_id');
            
            if (empty($tieu_de) || empty($gia) || empty($dien_tich) || empty($loai_id) || empty($trang_thai_id)) {
                 return response()->json(['status' => 0, 'message' => 'Vui lòng nhập đầy đủ các trường bắt buộc']);
            }
            
            $bds = BatDongSan::create([
                'tieu_de' => $tieu_de,
                'mo_ta' => $request->input('mo_ta'),
                'gia' => $gia,
                'dien_tich' => $dien_tich,
                'loai_id' => $loai_id,
                'trang_thai_id' => $trang_thai_id,
                'tinh_id' => $request->input('tinh_id', 1),
                'quan_id' => $request->input('quan_id', 1),
                'dia_chi_id' => $request->input('dia_chi_id', 1),
                'so_phong_ngu' => $request->input('so_phong_ngu'),
                'so_phong_tam' => $request->input('so_phong_tam'),
                'is_noi_bat' => $request->input('is_noi_bat', false),
                'moi_gioi_id' => $user->id,
                'is_duyet' => false,
            ]);

            return response()->json(['status' => 1, 'message' => 'Tạo thành công', 'data' => $bds]);
        } else {
            return response()->json(['status' => 0, 'message' => "Có lỗi xảy ra"]);
        }
    }

    public function update(Request $request, $id)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $bds = BatDongSan::find($id);
            if (!$bds) {
                return response()->json(['status' => 0, 'message' => 'Không tìm thấy BDS']);
            }
            
            if ($bds->moi_gioi_id !== $user->id) {
                return response()->json(['status' => 0, 'message' => 'Không có quyền']);
            }

            $tieu_de = $request->input('tieu_de');
            if (empty($tieu_de)) {
                 return response()->json(['status' => 0, 'message' => 'Tiêu đề là bắt buộc']);
            }

            $bds->tieu_de = $tieu_de;
            $bds->mo_ta = $request->input('mo_ta');
            $bds->gia = $request->input('gia');
            $bds->dien_tich = $request->input('dien_tich');
            $bds->loai_id = $request->input('loai_id');
            $bds->save();

            return response()->json(['status' => 1, 'message' => 'Cập nhật thành công', 'data' => $bds]);
        } else {
            return response()->json(['status' => 0, 'message' => "Có lỗi xảy ra"]);
        }
    }

    public function destroy(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $bds = BatDongSan::find($request->id);
            if (!$bds) {
                return response()->json(['status' => 0, 'message' => 'Không tìm thấy BDS']);
            }
            
            if ($bds->moi_gioi_id !== $user->id) {
                return response()->json(['status' => 0, 'message' => 'Không có quyền']);
            }

            $bds->delete();
            return response()->json(['status' => 1, 'message' => 'Xóa thành công']);
        } else {
            return response()->json(['status' => 0, 'message' => "Có lỗi xảy ra"]);
        }
    }
}
