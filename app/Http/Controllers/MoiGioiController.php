<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteMoiGioiRequest;
use App\Http\Requests\DestroyRequest;
use App\Http\Requests\MoiGioiLoginRequest;
use App\Http\Requests\MoiGioiRegisterRequest;
use App\Http\Requests\SearchMoiGioiRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Requests\UpdateMoiGioiRequest;
use App\Http\Requests\updatePasswordMoiGioiRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SendOtpRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Models\MoiGioi;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\KhachHang;
use App\Models\PhanQuyen;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Barryvdh\DomPDF\Facade\Pdf;

class MoiGioiController extends Controller
{
    public function login(MoiGioiLoginRequest $request): JsonResponse
    {
        $moigioi = MoiGioi::where('email', $request->email)->first();

        if (!$moigioi || !Hash::check($request->password, $moigioi->password)) {
            return response()->json([
                'status' => 0,  // ✅ Integer 0
                'message' => 'Email hoặc mật khẩu không đúng'
            ], 401);
        }

        $token = $moigioi->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 1,  // ✅ Integer 1
            'message' => 'Đăng nhập thành công',
            'token' => $token,
            'token_type' => 'Bearer',
            'data' => $moigioi  // Không cần thêm 'role' vì FE tự xác định qua user_type
        ], 200);
    }

    public function updatePassword(updatePasswordMoiGioiRequest $request)
    {
        // ✅ 1. Lấy user đang đăng nhập (qua Sanctum)
        $user = Auth::guard('sanctum')->user();

        // ✅ 2. Kiểm tra mật khẩu cũ
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'status'  => false,
                'message' => 'Mật khẩu cũ không đúng!',
            ], 400);
        }
        $currentTokenId = $user->currentAccessToken()->id;

        $user->tokens()->where('id', '!=', $currentTokenId)->delete();

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'status'  => true,
            'message' => 'Đổi mật khẩu thành công! Các thiết bị khác đã được đăng xuất.',
        ]);
    }

    public function updateProfile(UpdateMoiGioiRequest $request)
    {
        $user = Auth::guard('sanctum')->user();
        $data = MoiGioi::find($user->id);

        if ($data) {
            $data->update([
                'ten'           => $request->ten,
                'email'         => $request->email,
                'so_dien_thoai' => $request->so_dien_thoai,
                'mo_ta'         => $request->mo_ta,
                'zalo_link'     => $request->zalo_link,
            ]);

            return response()->json([
                'status'  => 1,
                'message' => 'Cập nhật thông tin thành công!',
            ]);
        } else {
            return response()->json([
                'status'  => 0,
                'message' => 'Thông tin môi giới không tồn tại!',
            ]);
        }
    }

    public function register(MoiGioiRegisterRequest $request)
    {
        $moiGioi = MoiGioi::create([
            'ten' => $request->ten,
            'email' => $request->email,
            'so_dien_thoai' => $request->so_dien_thoai,
            'password' => Hash::make($request->input('password')),
            'zalo_link' => $request->zalo_link ?? '',
            'mo_ta' => $request->mo_ta ?? '',
            'is_active' => true,
        ]);

        $token = $moiGioi->createToken('moi-gioi-token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Đăng ký thành công',
            'data' => [
                'token' => $token,
                'moi_gioi' => $moiGioi,
            ],
        ]);
    }

    public function profile()
    {
        /** @var MoiGioi|null $user */
        $user = Auth::guard('sanctum')->user();

        if ($user) {
            return response()->json([
                'status' => true,
                'data' => $user,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Có lỗi xảy ra"
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Có lỗi xảy ra',
        ]);
    }

    public function checkToken(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            return response()->json([
                'status' => 'success',
                'data' => $user,
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Token không hợp lệ'
            ], 401);
        }
    }

    public function logout(Request $request)
    {
        /** @var MoiGioi|null $user */
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $user->currentAccessToken()->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Đăng xuất thành công'
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy người dùng hoặc token không hợp lệ'
            ], 401);
        }
    }

    public function logoutAll()
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $user->tokens()->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Đã đăng xuất tất cả thiết bị'
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy người dùng hoặc token không hợp lệ'
            ], 401);
        }
    }

    //Gửi OTP
    public function sendOtp(SendOtpRequest $request)
    {

        $user = MoiGioi::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Email không tồn tại'
            ]);
        }

        $otp = rand(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $otp,
                'created_at' => now()
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'OTP đã gửi',
            'otp' => $otp // dev thôi
        ]);
    }

    public function verifyOtp(VerifyOtpRequest $request)
    {
        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->otp)
            ->first();

        if (!$record) {
            return response()->json([
                'status' => false,
                'message' => 'OTP không đúng'
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'OTP hợp lệ'
        ]);
    }

    //Reset password
    public function resetPassword(ResetPasswordRequest $request)
    {

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->otp)
            ->first();

        if (!$record) {
            return response()->json([
                'status' => false,
                'message' => 'OTP không đúng'
            ]);
        }

        $user = MoiGioi::where('email', $request->email)->first();

        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        return response()->json([
            'status' => true,
            'message' => 'Đổi mật khẩu thành công'
        ]);
    }

    // Admin lấy danh sách môi giới
    public function getData(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);

        // ✅ Join 2 bảng, lấy tên gói từ lịch sử mua MỚI NHẤT
        $data = MoiGioi::select('moi_giois.*', 'gt.ten_goi', 'gt.gia')
            ->leftJoin('lich_su_goi_tins as lst', function ($join) {
                $join->on('lst.moi_gioi_id', '=', 'moi_giois.id')
                    ->whereRaw('lst.id = (SELECT id FROM lich_su_goi_tins WHERE moi_gioi_id = moi_giois.id ORDER BY created_at DESC LIMIT 1)');
            })
            ->leftJoin('goi_tins as gt', 'lst.goi_tin_id', '=', 'gt.id')
            ->orderBy('moi_giois.created_at', 'desc')
            ->get();

        return response()->json(['status' => true, 'data' => $data]);
    }

    // Admin tìm kiếm môi giới
    public function search(SearchMoiGioiRequest $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => "Có lỗi xảy ra"
            ], 401);
        }

        $keyword = $request->keyword;
        $data = MoiGioi::where('ten', 'like', '%' . $keyword . '%')
            ->orWhere('email', 'like', '%' . $keyword . '%')
            ->orWhere('so_dien_thoai', 'like', '%' . $keyword . '%')
            ->orWhere('mo_ta', 'like', '%' . $keyword . '%')
            ->orWhere('zalo_link', 'like', '%' . $keyword . '%')
            ->get();

        if ($data->isEmpty()) {
            return response()->json([
                'status' => true,  // Vẫn là true vì request hợp lệ, chỉ là không có kết quả
                'message' => 'Không tìm thấy môi giới nào phù hợp với từ khóa "' . $keyword . '"',
                'data' => []
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }


    public function update(UpdateMoiGioiRequest $request)
    {
        $id_chuc_nang = 17;
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
        $data = MoiGioi::find($request->id);
        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy môi giới'
            ], 404);
        }
        $data->update([
            'ten' => $request->ten,
            'so_dien_thoai' => $request->so_dien_thoai,
            'email' => $request->email,
            'mo_ta' => $request->mo_ta,
            'zalo_link' => $request->zalo_link,
            'is_active' => $request->is_active,
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Cập nhật thành công',
            'data' => $data
        ]);
    }

    public function destroy(DeleteMoiGioiRequest $request)
    {
        $id_chuc_nang = 18;
        $user = Auth::guard('sanctum')->user();
        $check = PhanQuyen::where('id_chuc_vu', $user->id_chuc_vu)
            ->where('id_chuc_nang', $id_chuc_nang)
            ->first();
        if (!$user->is_super && !$check) {
            return response()->json([
                'status' => false,
                'message' => "Bạn không có quyền thực hiện chức năng này"
            ], 403);
        }

        $data = MoiGioi::find($request->id);
        if ($data) {
            $data->delete();
            return response()->json([
                'status' => true,
                'message' => 'Xóa thành công'
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Không tìm thấy môi giới để xóa'
        ], 404);
    }

    // Admin đổi trạng thái môi giới (active/inactive)
    public function changeStatus(Request $request)
    {
        $id_chuc_nang = 67;
        $user = Auth::guard('sanctum')->user();
        $check = PhanQuyen::where('id_chuc_vu', $user->id_chuc_vu)
            ->where('id_chuc_nang', $id_chuc_nang)
            ->first();
        if (!$user->is_super && !$check) {
            return response()->json([
                'status' => false,
                'message' => "Bạn không có quyền thực hiện chức năng này"
            ], 403);
        }
        $data = MoiGioi::find($request->id);
        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy môi giới'
            ], 404);
        }
        $data->update([
            'is_active' => $request->is_active
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Cập nhật trạng thái thành công',
            'data' => $data
        ]);
    }

    public function exportMoiGioi(Request $request)
    {
        $format = $request->input('format', 'csv');
        $filename = "moi_gioi_" . now()->format('Ymd_His');

        $query = MoiGioi::query()
            ->withCount(['giaoDichs as tong_giao_dich' => fn($q) => $q->where('trang_thai', 'success')])
            ->withSum(['giaoDichs as tong_doanh_thu' => fn($q) => $q->where('trang_thai', 'success')], 'so_tien');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('ten', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%")
                    ->orWhere('so_dien_thoai', 'like', "%{$request->search}%");
            });
        }
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->whereDate('created_at', '<=', $request->date_to);

        return match ($format) {
            'excel' => $this->exportExcel($query, $filename),
            'pdf'   => $this->exportPdf($query, $filename),
            default => $this->exportCsv($query, $filename)
        };
    }

    private function exportCsv($query, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
        ];

        return response()->stream(function () use ($query) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, ['ID', 'Họ tên', 'Email', 'SĐT', 'Tổng GD', 'Doanh thu', 'Ngày tham gia'], ';');

            $query->chunk(500, function ($items) use ($file) {
                foreach ($items as $item) {
                    fputcsv($file, [
                        $item->id,
                        $item->ten,
                        $item->email,
                        $item->so_dien_thoai,
                        $item->tong_giao_dich ?? 0,
                        number_format($item->tong_doanh_thu ?? 0, 0, ',', '.'),
                        $item->created_at->format('d/m/Y')
                    ], ';');
                }
            });
            fclose($file);
        }, 200, $headers);
    }

    private function exportExcel($query, $filename)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $headers = ['ID', 'Họ tên', 'Email', 'SĐT', 'Tổng GD', 'Doanh thu', 'Trạng thái', 'Ngày tham gia'];
        $sheet->fromArray([$headers], null, 'A1');
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);

        // Data
        $row = 2;
        $query->chunk(500, function ($items) use ($sheet, &$row) {
            foreach ($items as $item) {
                $trangThai = ($item->is_active == 1 || $item->is_active === true) ? 'Đang hoạt động' : 'Bị khóa';

                $sheet->fromArray([
                    $item->id,
                    $item->ten,
                    $item->email,
                    $item->so_dien_thoai,
                    $item->tong_giao_dich ?? 0,
                    $item->tong_doanh_thu ?? 0,
                    $trangThai,
                    $item->created_at->format('d/m/Y H:i')
                ], null, 'A' . $row);

                $row++;
            }
        });

        // Auto-size
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        return response()->stream(fn() => $writer->save('php://output'), 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}.xlsx\"",
        ]);
    }

    private function exportPdf($query, $filename)
    {
        $data = $query->limit(1000)->get()->map(fn($item) => [
            'id' => $item->id,
            'ten' => $item->ten,
            'email' => $item->email,
            'sdt' => $item->so_dien_thoai,
            'tong_gd' => $item->tong_giao_dich ?? 0,
            'doanh_thu' => number_format($item->tong_doanh_thu ?? 0, 0, ',', '.'),
            'ngay' => $item->created_at->format('d/m/Y')
        ]);

        $html = view('moi_gioi_pdf', compact('data'))->render();
        return Pdf::loadHTML($html)->setPaper('a4', 'portrait')->download("{$filename}.pdf");
    }

    public function exportChiTietMoiGioi(Request $request, $id)
    {
        $format = $request->input('format', 'pdf');
        $filename = "chi_tiet_moi_gioi_{$id}_" . now()->format('Ymd_His');

        // Lấy thông tin môi giới + lịch sử giao dịch
        $moiGioi = MoiGioi::with([
            'giaoDichs.goiTin',
            'giaoDichs' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }
        ])->findOrFail($id);

        // Tính thống kê
        $tongDon = $moiGioi->giaoDichs->count();
        $donActive = $moiGioi->giaoDichs->where('trang_thai', 'success')->count();
        $tongTien = $moiGioi->giaoDichs->where('trang_thai', 'success')->sum('so_tien');

        return match ($format) {
            'excel' => $this->exportChiTietExcel($moiGioi, $tongDon, $donActive, $tongTien, $filename),
            'pdf'   => $this->exportChiTietPdf($moiGioi, $tongDon, $donActive, $tongTien, $filename),
            default => $this->exportChiTietCsv($moiGioi, $tongDon, $donActive, $tongTien, $filename)
        };
    }

    private function exportChiTietCsv($moiGioi, $tongDon, $donActive, $tongTien, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
        ];

        return response()->stream(function () use ($moiGioi, $tongDon, $donActive, $tongTien) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header info
            fputcsv($file, ['THÔNG TIN MÔI GIỚI'], ';');
            fputcsv($file, ['Họ tên', $moiGioi->ten], ';');
            fputcsv($file, ['Email', $moiGioi->email], ';');
            fputcsv($file, ['SĐT', $moiGioi->so_dien_thoai], ';');
            fputcsv($file, [], ';');

            // Stats
            fputcsv($file, ['THỐNG KÊ'], ';');
            fputcsv($file, ['Tổng đơn', $tongDon], ';');
            fputcsv($file, ['Đơn active', $donActive], ';');
            fputcsv($file, ['Tổng tiền', number_format($tongTien, 0, ',', '.') . ' đ'], ';');
            fputcsv($file, [], ';');

            // Transactions
            fputcsv($file, ['LỊCH SỬ GIAO DỊCH'], ';');
            fputcsv($file, ['Mã GD', 'Gói tin', 'Số tiền', 'Phương thức', 'Trạng thái', 'Ngày tạo'], ';');

            foreach ($moiGioi->giaoDichs as $gd) {
                fputcsv($file, [
                    $gd->ma_giao_dich,
                    $gd->goiTin->ten_goi ?? '',
                    number_format($gd->so_tien, 0, ',', '.'),
                    $gd->phuong_thuc ?? 'N/A',
                    match ($gd->trang_thai) {
                        'success' => 'Thành công',
                        'pending' => 'Chờ xử lý',
                        'failed' => 'Thất bại',
                        default => $gd->trang_thai
                    },
                    $gd->created_at->format('d/m/Y H:i')
                ], ';');
            }

            fclose($file);
        }, 200, $headers);
    }

    private function exportChiTietExcel($moiGioi, $tongDon, $donActive, $tongTien, $filename)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Info
        $sheet->mergeCells('A1:B1');
        $sheet->setCellValue('A1', 'THÔNG TIN MÔI GIỚI');
        $sheet->getStyle('A1')->getFont()->setBold(true);

        $sheet->setCellValue('A2', 'Họ tên')->setCellValue('B2', $moiGioi->ten);
        $sheet->setCellValue('A3', 'Email')->setCellValue('B3', $moiGioi->email);
        $sheet->setCellValue('A4', 'SĐT')->setCellValue('B4', $moiGioi->so_dien_thoai);

        // Stats
        $sheet->setCellValue('A6', 'THỐNG KÊ')->getStyle('A6')->getFont()->setBold(true);
        $sheet->setCellValue('A7', 'Tổng đơn')->setCellValue('B7', $tongDon);
        $sheet->setCellValue('A8', 'Đơn active')->setCellValue('B8', $donActive);
        $sheet->setCellValue('A9', 'Tổng tiền')->setCellValue('B9', number_format($tongTien, 0, ',', '.') . ' đ');

        // Transactions
        $row = 11;
        $sheet->setCellValue('A' . $row, 'LỊCH SỬ GIAO DỊCH')->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;

        $headers = ['Mã GD', 'Gói tin', 'Số tiền', 'Phương thức', 'Trạng thái', 'Ngày tạo'];
        $sheet->fromArray([$headers], null, 'A' . $row);
        $sheet->getStyle('A' . $row . ':F' . $row)->getFont()->setBold(true);
        $row++;

        foreach ($moiGioi->giaoDichs as $gd) {
            $sheet->fromArray([
                $gd->ma_giao_dich,
                $gd->goiTin->ten_goi ?? '',
                $gd->so_tien,
                $gd->phuong_thuc ?? 'N/A',
                match ($gd->trang_thai) {
                    'success' => 'Thành công',
                    'pending' => 'Chờ xử lý',
                    'failed' => 'Thất bại',
                    default => $gd->trang_thai
                },
                $gd->created_at->format('d/m/Y H:i')
            ], null, 'A' . $row);
            $row++;
        }

        // Auto-size
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        return response()->stream(fn() => $writer->save('php://output'), 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}.xlsx\"",
        ]);
    }

    private function exportChiTietPdf($moiGioi, $tongDon, $donActive, $tongTien, $filename)
    {
        $data = [
            'moi_gioi' => $moiGioi,
            'tong_don' => $tongDon,
            'don_active' => $donActive,
            'tong_tien' => $tongTien,
            'giao_dichs' => $moiGioi->giaoDichs
        ];

        $html = view('chi_tiet_moi_gioi_pdf', compact('data'))->render();
        return \PDF::loadHTML($html)
            ->setPaper('a4', 'portrait')
            ->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])
            ->download("{$filename}.pdf");
    }
}
