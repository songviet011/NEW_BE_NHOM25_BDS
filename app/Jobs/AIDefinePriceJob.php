<?php

namespace App\Jobs;

use App\Models\BatDongSan;
use App\Models\AIDinhGia;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

// ============================================================
// JOB: AIDefinePriceJob
// Mục đích: Gọi AI API để định giá BDS (ASYNC - không block API)
//
// Cách sử dụng:
//   AIDefinePriceJob::dispatch($batDongSan);
//   hoặc AIDefinePriceJob::dispatchAfterResponse($batDongSan); // dispatch sau response
//
// Flow:
//   1. Controller tạo/update BDS
//   2. Dispatch job: AIDefinePriceJob::dispatch($bds)
//   3. Job được lưu vào queue (database)
//   4. Queue worker xử lý (php artisan queue:work)
//   5. Job gọi AI API, lưu kết quả vào bảng ai_dinh_gias
//   6. Cập nhật BDS với giá AI nếu cần
//
// TODO: Config API AI (key, endpoint, v.v) trong .env
//   AI_API_KEY=
//   AI_API_ENDPOINT=https://api.ai-pricing.com/predict
// ============================================================
class AIDefinePriceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // BDS cần định giá
    protected BatDongSan $batDongSan;

    // Số lần retry nếu fail
    public int $tries = 3;

    // Thời gian chờ giữa các lần retry (giây)
    public int $backoff = 60;

    /**
     * Constructor - lưu BDS cần định giá
     */
    public function __construct(BatDongSan $batDongSan)
    {
        $this->batDongSan = $batDongSan;
    }

    /**
     * Handle: Logic chính của job - gọi AI API để định giá
     */
    public function handle(): void
    {
        try {
            // TODO: Validate dữ liệu BDS trước khi gọi AI
            // Cần có: diện tích, địa chỉ, loại, số phòng, etc
            if (!$this->validateBatDongSan()) {
                logger()->warning("AIDefinePriceJob: BDS {$this->batDongSan->id} không đủ dữ liệu để định giá");
                return;
            }

            // Gọi AI API để lấy giá dự đoán
            $predictedPrice = $this->callAIAPI();

            // Lưu kết quả vào bảng ai_dinh_gias
            $this->saveAIPriceResult($predictedPrice);

            // TODO: Nếu muốn, cập nhật luôn giá BDS
            // $this->batDongSan->update(['gia' => $predictedPrice]);

            logger()->info("AIDefinePriceJob: Định giá thành công BDS {$this->batDongSan->id} = {$predictedPrice}");
        } catch (\Exception $e) {
            logger()->error("AIDefinePriceJob failed for BDS {$this->batDongSan->id}: " . $e->getMessage());
            // Job sẽ tự động retry theo $tries & $backoff
            throw $e;
        }
    }

    /**
     * Validate - kiểm tra BDS có đủ thông tin không
     * @return bool
     */
    private function validateBatDongSan(): bool
    {
        // TODO: Điều chỉnh theo logic thực tế
        // Hiện tại chỉ kiểm tra fields quan trọng

        return !empty($this->batDongSan->dien_tich)
            && !empty($this->batDongSan->loai_id)
            && !empty($this->batDongSan->tinh_id);
    }

    /**
     * Gọi AI API để lấy giá dự đoán
     * @return float Giá dự đoán
     */
    private function callAIAPI(): float
    {
        // TODO: Implement gọi API thực tế
        // Ví dụ:
        //   $response = Http::post(env('AI_API_ENDPOINT'), [
        //       'dien_tich' => $this->batDongSan->dien_tich,
        //       'loai_id' => $this->batDongSan->loai_id,
        //       'tinh_id' => $this->batDongSan->tinh_id,
        //       'so_phong_ngu' => $this->batDongSan->so_phong_ngu,
        //       'so_phong_tam' => $this->batDongSan->so_phong_tam,
        //   ]);
        //   return $response->json('predicted_price', 0);

        // Dummy: trả về 1 số random (để test)
        return rand(1000000000, 5000000000);
    }

    /**
     * Lưu kết quả định giá vào bảng ai_dinh_gias
     * @param float $predictedPrice Giá AI dự đoán
     */
    private function saveAIPriceResult(float $predictedPrice): void
    {
        // Lưu record vào bảng lịch sử định giá
        AIDinhGia::create([
            'bat_dong_san_id' => $this->batDongSan->id,
            'gia_du_doan' => $predictedPrice,
            'do_tin_cay' => 0.85, // TODO: confidence score từ AI API
            'ly_do' => 'AI auto predict', // TODO: lý do từ AI
            'created_at' => now(),
        ]);
    }

    /**
     * Fail - nếu job fail sau retry hết
     * @param \Throwable $exception
     */
    public function failed(\Throwable $exception): void
    {
        logger()->error(
            "AIDefinePriceJob FAILED final for BDS {$this->batDongSan->id}",
            ['error' => $exception->getMessage()]
        );

        // TODO: Gửi notification admin về job fail nếu cần
    }
}
