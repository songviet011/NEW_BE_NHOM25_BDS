<?php

namespace App\Http\Controllers;

use App\Models\BatDongSan;
use App\Models\DiaChi;
use App\Http\Requests\DiaChiRequest;

class DiaChiController extends Controller
{
    // Tìm theo địa chỉ chi tiết HOẶC tên quận HOẶC tên tỉnh gợi ý các bds đã duyệt có địa chỉ phù hợp
    public function getDiaChi(DiaChiRequest $request)
    {
        $keyword = $request->validated()['keyword'] ?? null;

        if (!$keyword) {
            return response()->json([
                'status' => true,
                'data' => [],
                'message' => 'Vui lòng cung cấp từ khóa để tìm kiếm địa chỉ.'
            ]);
        }

        $data = DiaChi::with(['tinh', 'quan'])
            // Tìm theo địa chỉ chi tiết HOẶC tên quận HOẶC tên tỉnh
            ->where(function ($query) use ($keyword) {
                $query->where('dia_chi_chi_tiet', 'like', '%' . $keyword . '%')
                    ->orWhereHas('quan', function ($q) use ($keyword) {
                        $q->where('ten', 'like', '%' . $keyword . '%');
                    })
                    ->orWhereHas('tinh', function ($q) use ($keyword) {
                        $q->where('ten', 'like', '%' . $keyword . '%');
                    });
            })
            // Chỉ lấy địa chỉ có BĐS đã duyệt
            ->whereHas('batDongSans', function ($q) {
                $q->where('is_duyet', true);
            })
            ->limit(5)
            ->get();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    // Tìm BĐS theo khu vực
    public function getBdsByKhuVuc(DiaChiRequest $request)
    {
        $validated = $request->validated();
        $query = BatDongSan::with(['diaChi.tinh', 'diaChi.quan'])
            ->where('is_duyet', true) // Chỉ lấy bài đã duyệt
            ->whereHas('diaChi', function ($q) use ($validated) {
                if (!empty($validated['tinh_id'])) {
                    $q->where('tinh_id', $validated['tinh_id']);
                }
                if (!empty($validated['quan_id'])) {
                    $q->where('quan_id', $validated['quan_id']);
                }
            });

        return response()->json([
            'status' => true,
            'data' => $query->paginate(20) // Phân trang
        ]);
    }

    // Xem chi tiết theo ID dùng cho Map có marker hiển thị thông tin chi tiết
    public function show(DiaChiRequest $request, $id)
    {
        $request->merge(['id' => $id]);
        $request->validate();

        $data = DiaChi::with(['tinh', 'quan'])->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }
}
