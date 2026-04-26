<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GiaoDichController; // Import để dùng lại hàm activatePackage
use App\Models\GiaoDich;
use App\Models\UnmatchedPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UnmatchedPaymentController extends Controller
{
    public function index()
    {
        $unmatched = UnmatchedPayment::where('status', 'unmatched')
            ->latest()
            ->paginate(10);

        // Lấy danh sách giao dịch pending để admin chọn khớp
        $pendingTransactions = GiaoDich::where('trang_thai', GiaoDich::STATUS_PENDING)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.unmatched-payments.index', compact('unmatched', 'pendingTransactions'));
    }

    public function match(Request $request, $id)
    {
        $request->validate(['giao_dich_id' => 'required|exists:giao_dich,id']);

        DB::beginTransaction();
        try {
            $unmatched = UnmatchedPayment::findOrFail($id);
            $transaction = GiaoDich::findOrFail($request->giao_dich_id);

            // 1. Update giao dịch thành công
            $transaction->update([
                'trang_thai' => GiaoDich::STATUS_SUCCESS,
                'paid_at' => now(),
                'ma_sepay_txn_ref' => $unmatched->sepayer_reference,
                'ghi_chu' => "Manual match by Admin from unmatched payment #{$id}"
            ]);

            // 2. Kích hoạt gói (Gọi lại hàm trong GiaoDichController)
            // Lưu ý: Vì hàm activatePackage là private, ta cần gọi trực tiếp logic hoặc chuyển nó sang public/protected
            // Ở đây mình viết lại logic ngắn gọn để đảm bảo chạy được:
            $goiTin = \App\Models\GoiTin::find($transaction->goi_tin_id);
            $user = \App\Models\MoiGioi::find($transaction->moi_gioi_id);
            if ($goiTin && $user) {
                $baseDate = $user->ngay_het_han_goi && $user->ngay_het_han_goi->isFuture() ? $user->ngay_het_han_goi->copy() : now();
                $user->update([
                    'goi_tin_id' => $goiTin->id,
                    'so_tin_con_lai' => $user->so_tin_con_lai + $goiTin->so_luong_tin,
                    'ngay_het_han_goi' => $baseDate->addDays($goiTin->so_ngay),
                ]);
            }

            // 3. Đánh dấu unmatched payment đã xử lý
            $unmatched->update([
                'status' => 'matched',
                'giao_dich_id' => $transaction->id,
                'admin_notes' => "Matched by " . auth()->user()->email
            ]);

            DB::commit();
            return back()->with('success', '✅ Đã khớp thủ công và kích hoạt gói tin thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Manual match failed: ' . $e->getMessage());
            return back()->with('error', '❌ Lỗi: ' . $e->getMessage());
        }
    }

    public function ignore($id)
    {
        UnmatchedPayment::findOrFail($id)->update([
            'status' => 'ignored',
            'admin_notes' => "Ignored by " . auth()->user()->email
        ]);
        return back()->with('success', 'Đã bỏ qua giao dịch.');
    }
}