<?php

namespace App\Http\Controllers;

use App\Models\GoiTin;
use App\Models\GiaoDich;
use App\Models\MoiGioi;
use App\Services\VnPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GiaoDichController extends Controller
{
    protected $vnPay;

    public function __construct(VnPayService $vnPay)
    {
        $this->vnPay = $vnPay;
    }

    /**
     * 1. Tạo đơn hàng & Redirect sang VNPay
     * POST /api/payment/create
     */
    public function createPayment(Request $request)
    {
        $request->validate([
            'goi_tin_id' => 'required|exists:goi_tins,id'
        ]);

        $user = Auth::guard('sanctum')->user();
        if (!$user) return response()->json(['status' => false, 'message' => 'Chưa đăng nhập'], 401);

        $goiTin = GoiTin::findOrFail($request->goi_tin_id);

        // Tạo mã giao dịch duy nhất
        $orderCode = 'GOPKG' . time() . rand(100, 999);

        
        // Lưu đơn hàng pending
        $transaction = GiaoDich::create([
            'ma_giao_dich' => $orderCode,
            'user_id'      => $user->id,
            'goi_tin_id'   => $goiTin->id,
            'so_tien'      => $goiTin->gia,
            'trang_thai'   => 'pending'
        ]);

        // Tạo URL thanh toán
        $paymentUrl = $this->vnPay->createPaymentUrl(
            $orderCode,
            $goiTin->gia,
            $this->vnPay->getIpAddress(),
            "Thanh toan goi tin: {$goiTin->ten_goi}"
        );

        return response()->json([
            'status' => true,
            'data' => [
                'payment_url' => $paymentUrl,
                'order_code'  => $orderCode
            ]
        ]);
    }

    /**
     * 2. Xử lý Callback/IPN từ VNPay
     * GET /api/payment/vnpay-ipn
     */
    public function handleVnPayCallback(Request $request)
    {
        // 1. Verify chữ ký
        if (!$this->vnPay->verifySignature($request->all())) {
            \Log::error('VNPay IPN: Invalid signature', $request->all());
            return response()->json(['RspCode' => '97', 'Message' => 'Invalid signature'], 400);
        }

        $orderCode = $request->input('vnp_TxnRef');
        $amount    = $request->input('vnp_Amount') / 100; // Chia lại 100
        $responseCode = $request->input('vnp_ResponseCode');
        $vnpTxnRef = $request->input('vnp_TransactionNo');

        // 2. Tìm đơn hàng
        $transaction = GiaoDich::where('ma_giao_dich', $orderCode)->first();
        if (!$transaction) {
            return response()->json(['RspCode' => '01', 'Message' => 'Order not found'], 404);
        }

        // 3. Chống xử lý trùng (Idempotency)
        if ($transaction->trang_thai === 'success') {
            return response()->json(['RspCode' => '02', 'Message' => 'Already processed'], 200);
        }

        // 4. Kiểm tra số tiền khớp
        if ($amount != $transaction->so_tien) {
            $transaction->update(['trang_thai' => 'failed']);
            return response()->json(['RspCode' => '04', 'Message' => 'Amount mismatch'], 400);
        }

        // 5. Xử lý thanh toán thành công
        if ($responseCode == '00') {
            DB::transaction(function () use ($transaction, $vnpTxnRef) {
                $transaction->update([
                    'trang_thai'     => 'success',
                    'ma_vnp_txn_ref' => $vnpTxnRef
                ]);

                $goiTin = GoiTin::find($transaction->goi_tin_id);
                $user   = MoiGioi::find($transaction->user_id);

                // ✅ Kích hoạt gói tin
                $user->so_tin_con_lai   = ($user->so_tin_con_lai ?? 0) + $goiTin->so_luong_tin;
                $user->ngay_het_han_goi = Carbon::now()->addDays($goiTin->so_ngay);
                $user->save();
            });

            return response()->json(['RspCode' => '00', 'Message' => 'Success'], 200);
        }

        // 6. Thanh toán thất bại/hủy
        $transaction->update(['trang_thai' => 'failed']);
        return response()->json(['RspCode' => '00', 'Message' => 'Payment failed'], 200);
    }
}
