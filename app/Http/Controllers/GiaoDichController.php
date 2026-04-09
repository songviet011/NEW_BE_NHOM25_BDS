<?php

namespace App\Http\Controllers;

use App\Models\GoiTin;
use App\Models\GiaoDich;
use App\Models\MoiGioi;
use App\Services\VnPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GiaoDichController extends Controller
{
    protected $vnPay;

    public function __construct(VnPayService $vnPay)
    {
        $this->vnPay = $vnPay;
    }

    private function resolveVnPayReturnUrl(Request $request): string
    {
        $configuredReturnUrl = env('VNP_RETURN_URL');

        if ($configuredReturnUrl && !str_contains($configuredReturnUrl, 'localhost:3000')) {
            return $configuredReturnUrl;
        }

        return rtrim($request->getSchemeAndHttpHost(), '/') . '/api/payment/vnpay-return';
    }

    //1. Tạo đơn hàng & Redirect sang VNPay (POST /api/moi-gioi/payment/create)
    public function createPayment(Request $request)
    {
        $request->validate([
            'goi_tin_id' => 'required|exists:goi_tins,id'
        ]);

        $user = Auth::guard('sanctum')->user();
        if (!$user) return response()->json(['status' => false, 'message' => 'Chưa đăng nhập'], 401);

        $goiTin = GoiTin::findOrFail($request->goi_tin_id);

        // Log cấu hình VNPAY trước khi tạo URL
        Log::info('=== VNPAY DEBUG START ===', [
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
            'phuong_thuc'  => 'vnpay',  
            'trang_thai'   => 'pending'
        ]);

        // Log thông tin giao dịch
        Log::info('Transaction Data', [
            'order_code' => $orderCode,
            'amount' => $goiTin->gia,
            'goi_tin' => $goiTin->ten_goi,
        ]);

        // Tạo URL thanh toán
        $returnUrl = $this->resolveVnPayReturnUrl($request);
        $paymentUrl = $this->vnPay->createPaymentUrl(
            $orderCode,
            $goiTin->gia,
            $this->vnPay->getIpAddress(),
            "Thanh toan goi tin: " . preg_replace('/[^\x20-\x7E]/', '', $goiTin->ten_goi),
            $returnUrl
        );

        // Log payment URL (cắt ngắn để dễ đọc)
        Log::info('Payment URL Generated', [
            'url_length' => strlen($paymentUrl),
            'starts_with' => substr($paymentUrl, 0, 50),
            'has_tmn_code' => strpos($paymentUrl, 'vnp_TmnCode=LHDWEON5') !== false ? 'YES' : 'NO',
            'has_hash' => strpos($paymentUrl, 'vnp_SecureHash=') !== false ? 'YES' : 'NO',
        ]);

        Log::info('=== VNPAY DEBUG END ===');

        return response()->json([
            'status' => true,
            'data' => [
                'payment_url' => $paymentUrl,
                'order_code'  => $orderCode
            ]
        ]);
    }

    //2. Xử lý Callback/IPN từ VNPay (GET /api/payment/vnpay-ipn)
    public function handleVnPayCallback(Request $request)
    {
        // Log toàn bộ data nhận được từ VNPay
        Log::info('=== VNPay IPN RECEIVED ===', [
            'time' => now(),
            'all_data' => $request->all(),
        ]);

        // 1. Verify chữ ký
        if (!$this->vnPay->verifySignature($request->all())) {
            Log::error('VNPay IPN: Invalid signature', [
                'received_data' => $request->all(),
                'hash_secret_used' => substr(env('VNP_HASH_SECRET'), 0, 10) . '...',
            ]);
            return response()->json(['RspCode' => '97', 'Message' => 'Invalid signature'], 400);
        }

        $orderCode = $request->input('vnp_TxnRef');
        $amount    = $request->input('vnp_Amount') / 100; // Chia lại 100
        $responseCode = $request->input('vnp_ResponseCode');
        $vnpTxnRef = $request->input('vnp_TransactionNo');

        Log::info('VNPay Signature Valid', [
            'order_code' => $orderCode,
            'amount' => $amount,
            'response_code' => $responseCode,
        ]);

        // 2. Tìm đơn hàng
        $transaction = GiaoDich::where('ma_giao_dich', $orderCode)->first();
        if (!$transaction) {
            Log::error('VNPay IPN: Order not found', ['code' => $orderCode]);
            return response()->json(['RspCode' => '01', 'Message' => 'Order not found'], 404);
        }

        // 3. Chống xử lý trùng (Idempotency)
        if ($transaction->trang_thai === 'success') {
            Log::warning('VNPay IPN: Already processed', ['code' => $orderCode]);
            return response()->json(['RspCode' => '02', 'Message' => 'Already processed'], 200);
        }

        // 4. Kiểm tra số tiền khớp
        if ($amount != $transaction->so_tien) {
            Log::error('VNPay IPN: Amount mismatch', [
                'expected' => $transaction->so_tien,
                'received' => $amount,
            ]);
            $transaction->update(['trang_thai' => 'failed']);
            return response()->json(['RspCode' => '04', 'Message' => 'Amount mismatch'], 400);
        }

        // 5. Xử lý thanh toán thành công
        if ($responseCode == '00') {
            Log::info('VNPay IPN: Processing success', ['code' => $orderCode]);
            
            DB::transaction(function () use ($transaction, $vnpTxnRef) {
                $transaction->update([
                    'trang_thai'     => 'success',
                    'ma_vnp_txn_ref' => $vnpTxnRef
                ]);

                $goiTin = GoiTin::find($transaction->goi_tin_id);
                $user = MoiGioi::find($transaction->moi_gioi_id);

                if ($user && $goiTin) {
                    $user->so_tin_con_lai   = ($user->so_tin_con_lai ?? 0) + $goiTin->so_luong_tin;
                    $user->ngay_het_han_goi = Carbon::now()->addDays($goiTin->so_ngay);
                    $user->save();

                    Log::info('Package activated', [
                        'user_id' => $user->id,
                        'so_tin' => $goiTin->so_luong_tin,
                        'ngay_het_han' => $user->ngay_het_han_goi,
                    ]);
                }
            });

            Log::info('VNPay IPN: Success completed', ['code' => $orderCode]);
            return response()->json(['RspCode' => '00', 'Message' => 'Success'], 200);
        }

        // 6. Thanh toán thất bại/hủy
        Log::warning('VNPay IPN: Payment failed', [
            'code' => $orderCode,
            'response_code' => $responseCode,
        ]);
        $transaction->update(['trang_thai' => 'failed']);
        return response()->json(['RspCode' => '00', 'Message' => 'Payment failed'], 200);
    }
}
