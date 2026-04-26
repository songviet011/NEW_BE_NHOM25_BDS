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
use App\Http\Requests\CreateBatDongSanRequest;
use App\Http\Requests\UpdateBatDongSanRequest;
use App\Http\Requests\SearchBatDongSanRequest;
use App\Http\Requests\ApproveOrRejectBatDongSanRequest;
use App\Http\Requests\ChangeBatDongSanStatusRequest;
use App\Http\Requests\DestroyRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\SearchBatDongSanAdminRequest;
use App\Http\Requests\UpdateImageBatDongSanRequest;
use App\Models\PhanQuyen;
use App\Events\BatDongSanMoiDang;
use App\Http\Requests\DeleteBatDongSanRequest;

class BatDongSanController extends Controller
{
    // Admin
    //Lấy danh sách BDS cho admin
    public function getData(Request $request) //chính sác rồi 
    {
        // $id_chuc_nang = 1; // ID chức năng xem danh sách BDS cho admin
        // $user = Auth::guard('sanctum')->user();
        // $check = PhanQuyen::where('id_chuc_vu', $user->id_chuc_vu)
        //     ->where('id_chuc_nang', $id_chuc_nang)
        //     ->first();
        // if (!$user->is_super &&  !$check) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => "Bạn không có quyền thực hiện chức năng này"
        //     ]);
        // }
        $data = BatDongSan::with([
            'loai',
            'moiGioi',
            // 'trangThai',
            'diaChi.tinh',
            'diaChi.quan',
            'hinhAnh' // Lấy danh sách ảnh
        ])->paginate(10); // nếu dữ liệu nhiều

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    // Tìm kiếm BDS cho admin (có thể tìm theo tiêu đề, mô tả, giá, loại, địa chỉ)
    public function searchAdmin(SearchBatDongSanAdminRequest $request) // Chính xác
    {
        $id_chuc_nang = 60; // ID tìm kiếm BDS cho admin
        $user = Auth::guard('sanctum')->user();
        $check = PhanQuyen::where('id_chuc_vu', $user->id_chuc_vu)
            ->where('id_chuc_nang', $id_chuc_nang)
            ->first();
        if (!$user->is_super &&  !$check) {
            return response()->json([
                'status' => false,
                'message' => "Bạn không có quyền thực hiện chức năng này"
            ]);
        }
        // Lấy đúng keyword từ request
        $keyword = $request->keyword;

        if (!$keyword || trim($keyword) === '') {
            return response()->json([
                'status' => false,
                'message' => 'Vui lòng nhập từ khóa tìm kiếm'
            ], 400);
        }

        $noi_dung = '%' . $keyword . '%';

        // Tìm kiếm
        $data = BatDongSan::where('tieu_de', 'like', $noi_dung)
            ->orWhere('mo_ta', 'like', $noi_dung)
            ->orWhere('gia', 'like', $noi_dung)
            ->orWhere('loai_id', 'like', $noi_dung)
            ->orWhere('dia_chi_id', 'like', $noi_dung)
            ->get();

        // Kiểm tra nếu không có kết quả
        if ($data->isEmpty()) {
            return response()->json([
                'status' => true,
                'message' => 'Không tìm thấy bất động sản nào phù hợp',
                'data' => []
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    // Duyệt tin BDS (Dành cho admin, môi giới không có quyền này, khách hàng không có quyền này)
    public function duyetTin(ApproveOrRejectBatDongSanRequest $request) //chính xác
    {
        $id_chuc_nang = 5; // ID chức năng duyệt BDS
        $user = Auth::guard('sanctum')->user();
        $check = PhanQuyen::where('id_chuc_vu', $user->id_chuc_vu)
            ->where('id_chuc_nang', $id_chuc_nang)
            ->first();
        if (!$user->is_super &&  !$check) {
            return response()->json([
                'status' => false,
                'message' => "Bạn không có quyền thực hiện chức năng này"
            ]);
        }
        $admin = BatDongSan::find($request->id);
        if ($admin) {
            $isDuyet = (int) $request->input('is_duyet', 0);
            $admin->is_duyet = $isDuyet;
            if ($request->is_duyet == 1) {
                $admin->trang_thai_id = 2; // Đã duyệt
            } else {
                $admin->trang_thai_id = 3; // Từ chối
            }

            $admin->save();
            return response()->json([
                'status' => true,
                'message' => 'Thay đổi trạng thái duyệt thành công',
                'data' => [
                    'id' => $admin->id,
                    'is_duyet' => $admin->is_duyet,
                    'trang_thai_id' => $admin->trang_thai_id
                ]
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy BDS'
            ]);
        }
    }

    // Xóa BDS (Dành cho admin, môi giới có thể xóa nhưng khách hàng không có quyền này)
    public function delete($id) //chính xác 
    {
        // $id_chuc_nang = 4; // ID chức năng xóa BDS
        // $user = Auth::guard('sanctum')->user();
        // $check = PhanQuyen::where('id_chuc_vu', $user->id_chuc_vu)
        //     ->where('id_chuc_nang', $id_chuc_nang)
        //     ->first();
        // if (!$user->is_super &&  !$check) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => "Bạn không có quyền thực hiện chức năng này"
        //     ]);
        // }
        $data = BatDongSan::find($id);
        if ($data) {
            $data->delete();
            return response()->json([
                'status' => true,
                'message' => 'Xóa thành công'
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Không tìm thấy BDS'
        ]);
    }

    // Thay đổi trạng thái BDS (Dành cho admin, môi giới, khách hàng không có quyền này)
    public function changeStatus(ChangeBatDongSanStatusRequest $request) //chính xác
    {
        $id_chuc_nang = 61; // ID chức năng thay đổi trạng thái BDS ví dụ (đang bán, đã bán, tạm ngưng)
        $user = Auth::guard('sanctum')->user();
        $check = PhanQuyen::where('id_chuc_vu', $user->id_chuc_vu)
            ->where('id_chuc_nang', $id_chuc_nang)
            ->first();
        if (!$user->is_super &&  !$check) {
            return response()->json([
                'status' => false,
                'message' => "Bạn không có quyền thực hiện chức năng này"
            ]);
        }
        $bds = BatDongSan::find($request->id);
        if ($bds) {
            $bds->trang_thai_id = $request->trang_thai_id;
            $bds->save();
            return response()->json([
                'status' => true,
                'message' => 'Cập nhật trạng thái thành công'
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy BDS'
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Không tìm thấy BDS'
        ]);
    }

    // Chi tiết BDS (Dành cho tất cả mọi người)
    public function xemChiTietBDS($id)
    {
        // $id_chuc_nang = 59; // ID chức năng xem danh sách BDS cho admin
        // $user = Auth::guard('sanctum')->user();
        // $check = PhanQuyen::where('id_chuc_vu', $user->id_chuc_vu)
        //     ->where('id_chuc_nang', $id_chuc_nang)
        //     ->first();
        // if (!$user->is_super &&  !$check) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => "Bạn không có quyền thực hiện chức năng này"
        //     ]);
        // }
        $data = BatDongSan::with([
            'anhDaiDien',
            'hinhAnh',
            'loai',
            'moiGioi',
            'diaChi',
            'diaChi.tinh',
            'diaChi.quan'
        ])
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->find($id);
        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => "Không tìm thấy bất động sản"
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    // MoiGioi
    // Lấy danh sách BDS của môi giới đang đăng (Dành cho môi giới, admin có thể xem tất cả, khách hàng không có quyền này)
    public function getDataDanhChoMoiGioi(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Chưa đăng nhập hoặc token không hợp lệ!'
            ], 401);
        }

        $data = BatDongSan::with([
            'loai',          // Lấy thông tin loại BĐS
            'trangThai',     // Lấy thông tin trạng thái
            'diaChi.quan',   // Lấy quận
            'diaChi.tinh',    // Lấy tỉnh
            'hinhAnh'
        ])
            ->where('moi_gioi_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'status'  => true,
            'message' => 'Lấy danh sách BĐS thành công!',
            'data'    => $data
        ]);
    }

    // Tạo bài đăng BĐS (Dành cho môi giới)
    public function store(CreateBatDongSanRequest $request)
    {
        DB::beginTransaction();

        try {
            $user = Auth::guard('sanctum')->user();

            if (!$user->is_active) {
                return response()->json(['status' => false, 'message' => 'Tài khoản bị khóa'], 403);
            }

            if (!$user->goi_tin_id) {
                return response()->json(['status' => false, 'message' => 'Bạn chưa mua gói'], 403);
            }

            if (now()->greaterThan($user->ngay_het_han_goi)) {
                return response()->json(['status' => false, 'message' => 'Gói đã hết hạn'], 403);
            }

            if ($user->so_tin_con_lai <= 0) {
                return response()->json(['status' => false, 'message' => 'Đã hết số tin đăng'], 403);
            }

            $goi = $user->goiTin;

            if (!$goi) {
                return response()->json(['status' => false, 'message' => 'Gói không hợp lệ'], 403);
            }

            $batDongSan = BatDongSan::create([
                'tieu_de'       => $request->tieu_de,
                'gia'           => $request->gia,
                'dien_tich'     => $request->dien_tich,
                'loai_id'       => $request->loai_id,
                'trang_thai_id' => $request->trang_thai_id,
                'mo_ta'         => $request->mo_ta,
                'dia_chi_id'    => $request->dia_chi_id,
                'so_phong_ngu'  => $request->so_phong_ngu,
                'so_phong_tam'  => $request->so_phong_tam,

                // 🔥 QUAN TRỌNG
                'is_noi_bat'  => $goi->gan_nhan_vip,
                'expires_at'  => now()->addDays($goi->so_ngay),

                'moi_gioi_id' => $user->id,
                'is_duyet'    => false,
            ]);

            if ($request->hasFile('hinh_anh')) {
                foreach ($request->file('hinh_anh') as $index => $file) {
                    $path = $file->store('bds/' . $batDongSan->id, 'public');

                    HinhAnhBatDongSan::create([
                        'bds_id' => $batDongSan->id,
                        'url' => $path,
                        'thu_tu' => $index,
                        'is_anh_dai_dien' => $index == 0,
                    ]);
                }
            }

            $user->decrement('so_tin_con_lai');

            DB::commit();
            event(new BatDongSanMoiDang($batDongSan));
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }

    // Cập nhật bài đăng BDS (Dành cho môi giới, admin chỉ có thể duyệt khách hàng không có quyền này)
    public function update(UpdateBatDongSanRequest $request)
    {
        $user = Auth::guard('sanctum')->user();
        //Lấy bài đăng
        $data = BatDongSan::find($request->id);
        if (!$data || $data->moi_gioi_id !== $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn chỉ được cập nhật bài đăng của chính mình'
            ], 403);
        }
        $oldData = $data->toArray();
        //Lấy dữ liệu hợp lệ
        $updateData = $request->validated();
        unset($updateData['id']);
        //Update
        $data->fill($updateData);
        $data->is_duyet = false;
        $data->save();

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật data thành công và đang chờ duyệt lại',
            'data' => $data
        ]);
    }

    // Xóa bài đăng BDS (Dành cho môi giới, admin chỉ có thể duyệt khách hàng không có quyền này)
    public function destroy(DeleteBatDongSanRequest $request)
    {
        $user = Auth::guard('sanctum')->user();
        $data = BatDongSan::find($request->id);
        if (!$data || $data->moi_gioi_id !== $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn chỉ được xóa bài đăng của chính mình'
            ], 403);
        }
        $data->delete();
        return response()->json([
            'status' => true,
            'message' => 'Xóa bài đăng thành công'
        ]);
    }

    public function setImage(UpdateImageBatDongSanRequest $request, $id)
    {
        $bds = BatDongSan::find($id);

        // Kiểm tra BĐS tồn tại
        if (!$bds) {
            return response()->json([
                'status'  => false,
                'message' => 'Bất động sản không tồn tại!',
            ], 404);
        }

        // Kiểm tra phân quyền: Chỉ môi giới sở hữu BĐS mới được sửa
        $user = Auth::guard('sanctum')->user();
        if ($bds->moi_gioi_id !== $user->id) {
            return response()->json([
                'status'  => false,
                'message' => 'Bạn không có quyền sửa ảnh của BĐS này!',
            ], 403);
        }

        // Kiểm tra ảnh thuộc về BĐS này (tránh thao tác chéo)
        $anh = HinhAnhBatDongSan::where('id', $request->anh_id)
            ->where('bds_id', $id)
            ->first();

        if (!$anh) {
            return response()->json([
                'status'  => false,
                'message' => 'Ảnh không thuộc về BĐS này!',
            ], 400);
        }

        // Bỏ đánh dấu ảnh đại diện cũ của BĐS này
        HinhAnhBatDongSan::where('bds_id', $id)
            ->update(['is_anh_dai_dien' => false]);

        // Đánh dấu ảnh mới là đại diện
        $anh->update(['is_anh_dai_dien' => true]);

        return response()->json([
            'status'  => true,
            'message' => 'Đã chọn ảnh đại diện thành công!',
            'data'    => [
                'anh_id' => $anh->id,
                'anh_dai_dien_url' => asset('storage/' . $anh->url)
            ]
        ]);
    }
}


// $user = Auth::guard('sanctum')->user();

        // // chưa mua gói
        // if (!$user->goi_tin_id) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'Bạn chưa mua gói'
        //     ], 403);
        // }

        // // hết hạn gói
        // if ($user->ngay_het_han_goi < now()) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'Gói đã hết hạn'
        //     ], 403);
        // }

        // // hết tin
        // if ($user->so_tin_con_lai <= 0) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'Đã hết số tin đăng'
        //     ], 403);
        // }

        // $goi = $user->goiTin;

        // $batDongSan = BatDongSan::create([
        //     'tieu_de'       => $request->tieu_de,
        //     'gia'           => $request->gia,
        //     'dien_tich'     => $request->dien_tich,
        //     'loai_id'       => $request->loai_id,
        //     'trang_thai_id' => $request->trang_thai_id,
        //     'mo_ta'         => $request->mo_ta,
        //     'dia_chi_id'    => $request->dia_chi_id,
        //     'so_phong_ngu'  => $request->so_phong_ngu,
        //     'so_phong_tam'  => $request->so_phong_tam,

        //     // 🔥 QUAN TRỌNG
        //     'is_noi_bat'  => $goi->gan_nhan_vip,
        //     'expires_at'  => now()->addDays($goi->so_ngay),

        //     'moi_gioi_id' => $user->id,
        //     'is_duyet'    => false,
        // ]);



        // if ($request->hasFile('hinh_anh')) {
        //     foreach ($request->file('hinh_anh') as $index => $file) {
        //         $path = $file->store('bds/' . $batDongSan->id, 'public');

        //         HinhAnhBatDongSan::create([
        //             'bds_id'          => $batDongSan->id,
        //             'url'             => $path,
        //             'thu_tu'          => $index,
        //             'is_anh_dai_dien' => false,
        //         ]);
        //     }
        // }

        // $user->decrement('so_tin_con_lai');

        // event(new BatDongSanMoiDang($batDongSan));

        // return response()->json([
        //     'status'  => true,
        //     'message' => 'Tạo BĐS thành công và đang chờ duyệt',
        //     'data'    => [
        //         'id' => $batDongSan->id,
        //         'tieu_de' => $batDongSan->tieu_de,
        //         'anh_dai_dien' => $batDongSan->anh_dai_dien_url,
        //     ]
        // ], 201);
