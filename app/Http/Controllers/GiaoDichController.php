<?php

namespace App\Http\Controllers;

use App\Models\GoiTin;
use App\Models\GiaoDich;
use App\Models\MoiGioi;
use App\Services\SePayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Barryvdh\DomPDF\Facade\Pdf;

class GiaoDichController extends Controller
{
    protected $sePay;

    public function __construct(SePayService $sePay)
    {
        $this->sePay = $sePay;
    }

    private function resolveSePayReturnUrl(Request $request): string
    {
        $configuredReturnUrl = env('SEPAY_RETURN_URL');

        if ($configuredReturnUrl && !str_contains($configuredReturnUrl, 'localhost:3000')) {
            return $configuredReturnUrl;
        }

        return rtrim($request->getSchemeAndHttpHost(), '/') . '/api/payment/sepay-return';
    }

    //1. Tạo đơn hàng & Redirect sang SePay (POST /api/moi-gioi/payment/create)
    public function createPayment(Request $request)
    {
        $request->validate([
            'goi_tin_id' => 'required|exists:goi_tins,id'
        ]);

        $user = Auth::guard('sanctum')->user();
        if (!$user) return response()->json(['status' => false, 'message' => 'Chưa đăng nhập'], 401);

        $goiTin = GoiTin::findOrFail($request->goi_tin_id);

        // Log cấu hình SePay trước khi tạo URL
        Log::info('=== SEPAY DEBUG START ===', [
            'time' => now(),
        ]);

        // Tạo mã giao dịch duy nhất
        $orderCode = 'GOPKG' . time() . rand(100, 999);

        // Lưu đơn hàng pending
        $transaction = GiaoDich::create([
            'ma_giao_dich' => $orderCode,
            'moi_gioi_id' => $user->id,
            'goi_tin_id'   => $goiTin->id,
            'so_tien'      => $goiTin->gia,
            'phuong_thuc'  => 'sepay',
            'trang_thai'   => GiaoDich::STATUS_PENDING
        ]);

        // Log thông tin giao dịch
        Log::info('Transaction Data', [
            'order_code' => $orderCode,
            'amount' => $goiTin->gia,
            'goi_tin' => $goiTin->ten_goi,
        ]);

        // Tạo URL thanh toán
        $returnUrl = $this->resolveSePayReturnUrl($request);
        $paymentForm = $this->sePay->createPaymentUrl(
            $orderCode,
            $goiTin->gia,
            "Thanh toan goi tin: " . preg_replace('/[^\x20-\x7E]/', '', $goiTin->ten_goi),
            $returnUrl
        );

        return response()->json([
            'status' => true,
            'data' => [
                'payment_form' => $paymentForm,  // SePay trả về HTML form
                'order_code' => $orderCode,
                'redirect_method' => 'form_post'  // Frontend cần xử lý khác SePay
            ]
        ]);
    }

    //2. Xử lý return URL từ SePay
    public function handleSePayReturn(Request $request)
    {
        $configuredReturnUrl = env('SEPAY_RETURN_URL');

        if ($configuredReturnUrl && str_contains($configuredReturnUrl, 'localhost:3000')) {
            $queryString = http_build_query($request->query());
            $redirectUrl = $configuredReturnUrl;

            if ($queryString) {
                $redirectUrl .= (str_contains($configuredReturnUrl, '?') ? '&' : '?') . $queryString;
            }

            return redirect()->away($redirectUrl);
        }

        return response()->json([
            'status' => true,
            'message' => 'SePay return received',
            'data' => $request->all()
        ]);
    }

    public function handleSePayWebhook(Request $request)
    {
        Log::info('=== SePay Webhook Received ===', ['data' => $request->all()]);

        // 1. Xác thực webhook
        if (!$this->sePay->verifyWebhook($request)) {
            return response()->json(['success' => false], 401);
        }

        $data = $request->json()->all();

        // 2. Chỉ xử lý khi ORDER_PAID
        if ($data['notification_type'] !== 'ORDER_PAID') {
            return response()->json(['success' => true], 200);
        }

        $orderCode = $data['order']['order_invoice_number'];
        $amount = (float) $data['order']['order_amount'];
        $status = $data['transaction']['transaction_status'];

        // 3. Tìm đơn hàng
        DB::transaction(function () use ($orderCode, $amount, $status, $data) {
            $transaction = GiaoDich::where('ma_giao_dich', $orderCode)
                ->lockForUpdate()
                ->first();
        
            if (!$transaction) {
                Log::error('SePay Webhook: Order not found', ['code' => $orderCode]);
                throw new \Exception('Order not found');
            }
        
            // chống xử lý trùng
            if (
                $transaction->trang_thai === GiaoDich::STATUS_SUCCESS ||
                !empty($transaction->ma_sepay_txn_ref)
            ) {
                return;
            }
        
            if ((float) $amount !== (float) $transaction->so_tien) {
                $transaction->update([
                    'trang_thai' => GiaoDich::STATUS_FAILED,
                ]);
                return;
            }
        
            if ($status === 'APPROVED') {
                $transaction->update([
                    'trang_thai' => GiaoDich::STATUS_SUCCESS,
                    'paid_at' => now(),
                    'ma_sepay_txn_ref' => $data['transaction']['transaction_id'] ?? null,
                ]);
        
                $goiTin = GoiTin::find($transaction->goi_tin_id);
                $user = MoiGioi::find($transaction->moi_gioi_id);
        
                if ($user && $goiTin) {
                    $user->so_tin_con_lai = ($user->so_tin_con_lai ?? 0) + $goiTin->so_luong_tin;
                    $user->ngay_het_han_goi = Carbon::now()->addDays($goiTin->so_ngay);
                    $user->save();
                }
                return;
            }
        
            $transaction->update([
                'trang_thai' => GiaoDich::STATUS_FAILED,
            ]);
        });
        return response()->json(['success' => true], 200);
    }

    public function exportGiaoDich(Request $request)
    {
        $format = $request->input('format', 'csv'); // default: csv
        $filename = "giao_dich_" . now()->format('Ymd_His');

        // Build query (dùng chung cho cả 3 định dạng)
        $query = GiaoDich::query()->with(['moiGioi:id,ten,so_dien_thoai,email', 'goiTin:id,ten_goi']);

        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->whereDate('created_at', '<=', $request->date_to);
        if ($request->filled('status')) $query->where('trang_thai', $request->status);
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('ma_giao_dich', 'like', "%{$request->search}%")
                    ->orWhereHas('moiGioi', fn($q) => $q->where('ten', 'like', "%{$request->search}%"));
            });
        }

        // Route sang đúng hàm export theo format
        return match ($format) {
            'excel' => $this->exportExcel($query, $filename),
            'pdf'   => $this->exportPdf($query, $filename),
            default => $this->exportCsv($query, $filename)
        };
    }

    /** CSV: Native PHP, siêu nhanh, nhẹ RAM */
    private function exportCsv($query, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
        ];

        return response()->stream(function () use ($query) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, ['Mã GD', 'Môi giới', 'SĐT', 'Gói tin', 'Số tiền', 'Phương thức', 'Trạng thái', 'Ngày tạo'], ';');

            $query->chunk(500, function ($items) use ($file) {
                foreach ($items as $item) {
                    // ✅ Fix: Kiểm tra cả 2 tên cột
                    $phuongThuc = $item->phuong_thuc_thanh_toan
                        ?? $item->phuong_thuc
                        ?? $item->payment_method
                        ?? 'N/A';

                    fputcsv($file, [
                        $item->ma_giao_dich,
                        $item->moiGioi->ten ?? '',
                        $item->moiGioi->so_dien_thoai ?? '',
                        $item->goiTin->ten_goi ?? '',
                        number_format($item->so_tien, 0, ',', '.'),
                        $phuongThuc, // ✅ Đã fix
                        match ($item->trang_thai) {
                            'success' => 'Thành công',
                            'pending' => 'Chờ xử lý',
                            'failed' => 'Thất bại',
                            'cancelled' => 'Đã hủy',
                            default => $item->trang_thai
                        },
                        $item->created_at->format('d/m/Y H:i')
                    ], ';');
                }
            });
            fclose($file);
        }, 200, $headers);
    }

    /** Excel: Dùng PhpSpreadsheet, format đẹp, hỗ trợ formula */
    private function exportExcel($query, $filename)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['Mã GD', 'Môi giới', 'SDT', 'Email', 'Gói tin', 'Số tiền', 'Phương thức', 'Trạng thái', 'Ngày tạo'];
        $sheet->fromArray([$headers], null, 'A1');
        $sheet->getStyle('A1:I1')->getFont()->setBold(true);

        $row = 2;
        $query->chunk(500, function ($items) use ($sheet, &$row) {
            foreach ($items as $item) {
                // ✅ Fix: Kiểm tra nhiều tên cột
                $phuongThuc = $item->phuong_thuc_thanh_toan
                    ?? $item->phuong_thuc
                    ?? $item->payment_method
                    ?? 'N/A';

                $sheet->fromArray([
                    $item->ma_giao_dich,
                    $item->moiGioi->ten ?? '',
                    $item->moiGioi->so_dien_thoai ?? '',
                    $item->moiGioi->email ?? '',
                    $item->goiTin->ten_goi ?? '',
                    $item->so_tien,
                    $phuongThuc, // ✅ Đã fix
                    match ($item->trang_thai) {
                        'success' => 'Thành công',
                        'pending' => 'Chờ xử lý',
                        'failed' => 'Thất bại',
                        'cancelled' => 'Đã hủy',
                        default => $item->trang_thai
                    },
                    $item->created_at->format('d/m/Y H:i')
                ], null, 'A' . $row);
                $row++;
            }
        });

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        return response()->stream(fn() => $writer->save('php://output'), 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}.xlsx\"",
        ]);
    }

    /** PDF: Dùng DomPDF, phù hợp in ấn/báo cáo ngắn */
    private function exportPdf($query, $filename)
    {
        // Lấy data (PDF không nên stream chunk lớn, giới hạn ~2000 dòng)
        $data = $query->limit(1000)->get()->map(fn($item) => [
            'ma_gd' => $item->ma_giao_dich,
            'moi_gioi' => $item->moiGioi->ten ?? '',
            'sdt' => $item->moiGioi->so_dien_thoai ?? '',
            'goi_tin' => $item->goiTin->ten_goi ?? '',
            'so_tien' => number_format($item->so_tien, 0, ',', '.') . ' đ',
            // ✅ SỬA: Kiểm tra nhiều tên cột khác nhau
            'phuong_thuc' => $item->phuong_thuc_thanh_toan
                ?? $item->phuong_thuc
                ?? $item->payment_method
                ?? 'N/A',
            'trang_thai' => match ($item->trang_thai) {
                'success' => 'Thành công',
                'pending' => 'Chờ xử lý',
                'failed' => 'Thất bại',
                'cancelled' => 'Đã hủy',
                default => $item->trang_thai
            },
            'ngay_tao' => $item->created_at->format('d/m/Y H:i')
        ]);

        $html = view('giao_dich_pdf', compact('data'))->render();
        return \PDF::loadHTML($html)
            ->setPaper('a4', 'landscape')
            ->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])
            ->download("{$filename}.pdf");
    }
}
