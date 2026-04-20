<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchBatDongSanRequest;
use App\Models\BatDongSan;
use App\Models\LoaiBatDongSan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ClientHomeController extends Controller
{
    /**
     * Helper: Xử lý ẩn/hiện dữ liệu nhạy cảm
     */
    private function prepareData($bds)
    {
        $isGuest = !Auth::guard('sanctum')->check();

        if ($isGuest) {
            // 👉 nếu cần thì bật lại logic mask
        }

        return $bds;
    }

    /**
     * 📌 DANH SÁCH BĐS
     */
    public function getAllPublic(Request $request)
    {
        $data = BatDongSan::with([
            'loai',
            'moiGioi',
            'hinhAnh',
            'anhDaiDien',
            'diaChi'
        ])
            ->where('is_duyet', true)
            ->latest()
            ->paginate(6);

        if (!Auth::guard('sanctum')->check()) {
            $data->getCollection()->transform(fn($item) => $this->prepareData($item));
        }

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    /**
     * 📌 CHI TIẾT BĐS
     */
    public function xemChiTiet($id)
    {
        $bds = BatDongSan::with([
            'loai',
            'trangThai',
            'moiGioi',
            'diaChi.tinh',
            'diaChi.quan',
            'diaChi',
            'hinhAnh',
            'anhDaiDien'
        ])->find($id);

        if (!$bds) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy bất động sản'
            ], 404);
        }

        if (!Auth::guard('sanctum')->check()) {
            $bds = $this->prepareData($bds);
        }

        return response()->json([
            'status' => true,
            'data' => $bds
        ]);
    }

    //tìm kiếm 
    public function search(SearchBatDongSanRequest $request)
    {
        $query = BatDongSan::query();

        $query->when($request->tinh_id, fn($q) => $q->where('tinh_id', $request->tinh_id));
        $query->when($request->loai_id, fn($q) => $q->where('loai_id', $request->loai_id));
        $query->when($request->gia_min, fn($q) => $q->where('gia', '>=', $request->gia_min));
        $query->when($request->gia_max, fn($q) => $q->where('gia', '<=', $request->gia_max));
        $query->when($request->tieu_de, fn($q) => $q->where('tieu_de', 'like', '%' . $request->tieu_de . '%'));

        $query->where('is_duyet', true);

        $data = $query->with([
            'loai',
            'moiGioi',
            'hinhAnh',
            'anhDaiDien',
            'diaChi'
        ])->paginate(6);

        if (!Auth::guard('sanctum')->check()) {
            $data->getCollection()->transform(fn($item) => $this->prepareData($item));
        }

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    // Loại bất động sản
    public function getLoaiBDS(Request $request)
    {
        $data = LoaiBatDongSan::all();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

        // FIX: Tìm kiếm nâng cao cho trang client theo nhiều bộ lọc
        public function searchAdvanced(Request $request)
        {
            $query = BatDongSan::with([
                'loai',
                'moiGioi',
                'hinhAnh',
                'anhDaiDien',
                'diaChi.tinh',
                'diaChi.quan',
            ])->where('is_duyet', true);
    
            // FIX: lọc theo tỉnh/thành
            if ($request->filled('tinh_id')) {
                $query->whereHas('diaChi', function ($q) use ($request) {
                    $q->where('tinh_id', $request->tinh_id);
                });
            }
    
            // FIX: lọc theo quận/huyện
            if ($request->filled('quan_id')) {
                $query->whereHas('diaChi', function ($q) use ($request) {
                    $q->where('quan_id', $request->quan_id);
                });
            }
    
            // FIX: lọc theo loại BĐS
            if ($request->filled('loai_id')) {
                $query->where('loai_id', $request->loai_id);
            }
    
            // FIX: lọc theo khoảng giá
            if ($request->filled('gia_min')) {
                $query->where('gia', '>=', $request->gia_min);
            }
    
            if ($request->filled('gia_max')) {
                $query->where('gia', '<=', $request->gia_max);
            }
    
            // FIX: lọc theo diện tích
            if ($request->filled('dien_tich_min')) {
                $query->where('dien_tich', '>=', $request->dien_tich_min);
            }
    
            if ($request->filled('dien_tich_max')) {
                $query->where('dien_tich', '<=', $request->dien_tich_max);
            }
    
            // FIX: lọc theo phòng ngủ
            if ($request->filled('so_phong_ngu')) {
                $query->where('so_phong_ngu', $request->so_phong_ngu);
            }
    
            // FIX: lọc theo phòng tắm
            if ($request->filled('so_phong_tam')) {
                $query->where('so_phong_tam', $request->so_phong_tam);
            }
    
            // FIX: lọc theo từ khóa tiêu đề
            if ($request->filled('keyword')) {
                $query->where('tieu_de', 'like', '%' . $request->keyword . '%');
            }
    
            // FIX: sắp xếp
            switch ($request->input('sort', 'newest')) {
                case 'price_asc':
                    $query->orderBy('gia', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('gia', 'desc');
                    break;
                case 'area_asc':
                    $query->orderBy('dien_tich', 'asc');
                    break;
                case 'area_desc':
                    $query->orderBy('dien_tich', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }
    
            $data = $query->paginate($request->input('limit', 12));
    
            // FIX: trả sẵn ảnh đại diện chuẩn để FE hiển thị ổn định
            $data->getCollection()->transform(function ($item) {
                $item->image = $item->anhDaiDienUrl;
                $item->location_label = optional($item->diaChi?->tinh)->ten . ', ' . optional($item->diaChi?->quan)->ten;
                return $item;
            });
    
            return response()->json([
                'status' => true,
                'data' => $data
            ]);
        }
}
