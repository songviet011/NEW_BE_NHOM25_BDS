<?php

namespace App\Http\Controllers;

use App\Models\BatDongSan;
use Illuminate\Http\Request;

class MapController extends Controller
{
    //Lấy dữ liệu BĐS để hiển thị trên bản đồ
    public function getBatDongSanMap(Request $request)
    {
        $query = BatDongSan::with([
            'loai',
            'diaChi.tinh',
            'diaChi.quan',
            'moiGioi',
            'hinhAnh'
        ])
            ->where('is_duyet', true)
            ->whereHas('diaChi', function ($q) {
                $q->whereNotNull('lat')
                    ->whereNotNull('lng');
            });

        // Filter theo bounds (viewport)
        if ($request->has('bounds')) {
            $bounds = $request->bounds;
            $query->whereHas('diaChi', function ($q) use ($bounds) {
                $q->whereBetween('lat', [$bounds['south'], $bounds['north']])
                    ->whereBetween('lng', [$bounds['west'], $bounds['east']]);
            });
        }

        // Filter theo giá
        if ($request->has('min_price')) {
            $query->where('gia', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('gia', '<=', $request->max_price);
        }

        // Filter theo loại BĐS
        if ($request->has('loai_id')) {
            $query->where('loai_id', $request->loai_id);
        }

        $batDongSans = $query->get()->map(function ($bds) {
            return [
                'id' => $bds->id,
                'tieu_de' => $bds->tieu_de,
                'gia' => $bds->gia,
                'gia_formatted' => $this->formatPrice($bds->gia),
                'dien_tich' => $bds->dien_tich,
                'dia_chi' => $bds->diaChi->dia_chi_chi_tiet ?? '',
                'tinh' => $bds->diaChi->tinh->ten_tinh ?? '',
                'quan' => $bds->diaChi->quan->ten_quan ?? '',

                // Tọa độ
                'lat' => $bds->diaChi->lat,
                'lng' => $bds->diaChi->lng,

                // Badge
                'is_vip' => $bds->is_vip ?? false,
                'is_kim_cuong' => $bds->is_kim_cuong ?? false,

                // Hình ảnh
                'hinh_anh' => $bds->hinhAnh->first()?->url ?? null,

                // Môi giới
                'moi_gioi' => [
                    'ten' => $bds->moiGioi->ten ?? '',
                    'sdt' => $bds->moiGioi->so_dien_thoai ?? '',
                ]
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $batDongSans,
            'total' => $batDongSans->count()
        ]);
    }

    //Format giá tiền
    private function formatPrice($price)
    {
        if ($price >= 1000000000) {
            return number_format($price / 1000000000, 2) . ' tỷ';
        }
        return number_format($price / 1000000, 0) . ' triệu';
    }

    //Lấy BĐS gần vị trí (?lat=16.0544&lng=108.2022&radius=5 (km))
    public function getNearbyProperties(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'radius' => 'nullable|numeric|max:50',
        ]);

        $lat = $request->lat;
        $lng = $request->lng;
        $radius = $request->radius ?? 5;

        // Lấy tất cả BĐS có diaChi, filter 
        $properties = BatDongSan::with(['diaChi', 'loai'])
            ->where('is_duyet', true)
            ->whereHas('diaChi', function ($q) use ($lat, $lng, $radius) {
                $q->whereNotNull('lat')
                    ->whereNotNull('lng');
            })
            ->get()
            ->filter(function ($bds) use ($lat, $lng, $radius) {
                // Tính khoảng cách bằng PHP
                $distance = $this->calculateDistance(
                    $lat,
                    $lng,
                    $bds->diaChi->lat,
                    $bds->diaChi->lng
                );
                return $distance < $radius;
            })
            ->values();

        return response()->json([
            'status' => true,
            'data' => $properties,
            'center' => ['lat' => $lat, 'lng' => $lng],
            'radius_km' => $radius
        ]);
    }

    // Tính khoảng cách giữa 2 điểm (Haversine formula)
    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return $distance;
    }
}
