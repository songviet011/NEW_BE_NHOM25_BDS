<?php

namespace App\Http\Controllers;

use App\Models\GoiTin;
use App\Models\LichSuGoiTin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LichSuGoiTinController extends Controller
{
    public function lichSuMua(Request $request)
    {
        // Build query thống kê theo môi giới
        $query = DB::table('lich_su_goi_tins as lst')
            ->join('moi_giois as mg', 'lst.moi_gioi_id', '=', 'mg.id')
            ->leftJoin('goi_tins as gt', 'lst.goi_tin_id', '=', 'gt.id')
            ->leftJoin('giao_dichs as gd', function ($join) {
                $join->on('lst.moi_gioi_id', '=', 'gd.moi_gioi_id')
                    ->on('lst.goi_tin_id', '=', 'gd.goi_tin_id');
            })
            ->select(
                'mg.id as moi_gioi_id',
                'mg.ten as moi_gioi_ten',
                'mg.email as moi_gioi_email',
                'mg.so_dien_thoai as moi_gioi_sdt',
                DB::raw('COUNT(DISTINCT lst.id) as tong_so_lan_mua'),
                DB::raw('COUNT(DISTINCT CASE WHEN lst.ngay_ket_thuc >= NOW() THEN lst.id END) as so_goi_dang_hoat_dong'),
                DB::raw('COALESCE(SUM(COALESCE(gd.so_tien, gt.gia)), 0) as tong_tien_da_mua'),
                DB::raw('MAX(lst.created_at) as lan_mua_gan_nhat'),
                DB::raw('MAX(lst.ngay_ket_thuc) as ngay_het_han_muon_nhat')
            )
            ->groupBy(
                'mg.id',
                'mg.ten',
                'mg.email',
                'mg.so_dien_thoai'
            );

        // Filter nếu có
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('mg.ten', 'like', "%{$request->search}%")
                    ->orWhere('mg.so_dien_thoai', 'like', "%{$request->search}%")
                    ->orWhere('mg.email', 'like', "%{$request->search}%");
            });
        }

        $data = $query->orderBy('tong_tien_da_mua', 'desc')
            ->paginate($request->get('per_page', 10));

        // Transform data
        $transformedData = $data->map(function ($item) {
            return [
                'moi_gioi' => [
                    'id' => $item->moi_gioi_id,
                    'ten' => $item->moi_gioi_ten,
                    'email' => $item->moi_gioi_email,
                    'so_dien_thoai' => $item->moi_gioi_sdt,
                ],
                'thong_ke' => [
                    'tong_so_lan_mua' => (int) $item->tong_so_lan_mua,
                    'so_goi_dang_hoat_dong' => (int) $item->so_goi_dang_hoat_dong,
                    'tong_tien_da_mua' => (float) $item->tong_tien_da_mua,
                    'tong_tien_formatted' => number_format($item->tong_tien_da_mua, 0, ',', '.') . ' đ',
                ],
                'thoi_gian' => [
                    'lan_mua_gan_nhat' => $item->lan_mua_gan_nhat ? \Carbon\Carbon::parse($item->lan_mua_gan_nhat)->format('d/m/Y H:i') : null,
                    'ngay_het_han_muon_nhat' => $item->ngay_het_han_muon_nhat ? \Carbon\Carbon::parse($item->ngay_het_han_muon_nhat)->format('d/m/Y') : null,
                ],
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Lấy danh sách thành công',
            'data' => [
                'current_page' => $data->currentPage(),
                'data' => $transformedData,
                'first_page_url' => $data->url(1),
                'from' => $data->firstItem(),
                'last_page' => $data->lastPage(),
                'last_page_url' => $data->url($data->lastPage()),
                'next_page_url' => $data->nextPageUrl(),
                'path' => $data->path(),
                'per_page' => $data->perPage(),
                'prev_page_url' => $data->previousPageUrl(),
                'to' => $data->lastItem(),
                'total' => $data->total(),
            ]
        ]);
    }
    public function chiTietLichSuMua($moi_gioi_id)
    {
        // Kiểm tra môi giới tồn tại
        $moiGioi = DB::table('moi_giois')->find($moi_gioi_id);
        if (!$moiGioi) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy môi giới',
            ], 404);
        }

        // Lấy tất cả lịch sử mua của môi giới này
        $data = DB::table('lich_su_goi_tins as lst')
            ->join('goi_tins as gt', 'lst.goi_tin_id', '=', 'gt.id')
            ->leftJoin('giao_dichs as gd', function ($join) use ($moi_gioi_id) {
                $join->on('lst.moi_gioi_id', '=', 'gd.moi_gioi_id')
                    ->on('lst.goi_tin_id', '=', 'gd.goi_tin_id');
            })
            ->select(
                'lst.id',
                'lst.ngay_bat_dau',
                'lst.ngay_ket_thuc',
                'lst.created_at',
                'gt.ten_goi',
                'gt.gia as gia_goi',
                'gt.so_ngay',
                'gt.so_luong_tin',
                DB::raw('COALESCE(gd.so_tien, gt.gia) as so_tien'),
                DB::raw('COALESCE(gd.phuong_thuc, "Chuyển khoản") as phuong_thuc'),
                DB::raw('COALESCE(gd.ma_giao_dich, CONCAT("#LST", lst.id)) as ma_don_hang'),
                DB::raw('CASE 
                WHEN lst.ngay_ket_thuc >= NOW() THEN "active"
                WHEN lst.ngay_ket_thuc < NOW() THEN "expired"
                ELSE "inactive"
            END as trang_thai')
            )
            ->where('lst.moi_gioi_id', $moi_gioi_id)
            ->orderBy('lst.created_at', 'desc')
            ->get();

        // Transform data
        $transformedData = $data->map(function ($item) {
            $trangThai = $item->trang_thai;
            $trangThaiLabel = match ($trangThai) {
                'active' => ['text' => 'Đang hoạt động', 'class' => 'success'],
                'expired' => ['text' => 'Hết hạn', 'class' => 'danger'],
                default => ['text' => 'Không hoạt động', 'class' => 'secondary'],
            };

            return [
                'id' => $item->id,
                'ma_don_hang' => $item->ma_don_hang,
                'goi_tin' => [
                    'ten_goi' => $item->ten_goi,
                    'so_ngay' => $item->so_ngay,
                    'so_luong_tin' => $item->so_luong_tin,
                ],
                'so_tien' => (float) $item->so_tien,
                'so_tien_formatted' => number_format($item->so_tien, 0, ',', '.') . ' đ',
                'phuong_thuc' => $item->phuong_thuc,
                'ngay_mua' => \Carbon\Carbon::parse($item->created_at)->format('H:i d/m/Y'),
                'ngay_bat_dau' => $item->ngay_bat_dau ? \Carbon\Carbon::parse($item->ngay_bat_dau)->format('H:i d/m/Y') : null,
                'ngay_ket_thuc' => $item->ngay_ket_thuc ? \Carbon\Carbon::parse($item->ngay_ket_thuc)->format('H:i d/m/Y') : null,
                'trang_thai' => $trangThai,
                'trang_thai_label' => $trangThaiLabel,
            ];
        });

        // Thống kê nhanh cho chi tiết
        $stats = [
            'tong_don' => $data->count(),
            'don_dang_hoat_dong' => $data->where('trang_thai', 'active')->count(),
            'tong_tien' => $data->sum('so_tien'),
        ];

        return response()->json([
            'status' => true,
            'message' => 'Lấy chi tiết thành công',
            'data' => [
                'moi_gioi' => [
                    'id' => $moiGioi->id,
                    'ten' => $moiGioi->ten,
                    'email' => $moiGioi->email,
                    'so_dien_thoai' => $moiGioi->so_dien_thoai,
                ],
                'thong_ke' => [
                    'tong_don' => $stats['tong_don'],
                    'don_dang_hoat_dong' => $stats['don_dang_hoat_dong'],
                    'tong_tien' => number_format($stats['tong_tien'], 0, ',', '.') . ' đ',
                ],
                'lich_su_mua' => $transformedData,
            ]
        ]);
    }
}
