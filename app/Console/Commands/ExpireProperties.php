<?php

namespace App\Console\Commands;

use App\Models\BatDongSan;
use App\Events\PropertyExpired;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ExpireProperties extends Command
{
    protected $signature = 'properties:expire';
    protected $description = 'Tự động ẩn tin đăng hết hạn sau 15 ngày';

    public function handle()
    {
        $expiredDate = Carbon::now()->subDays(15);

        $expiredProperties = BatDongSan::where('trang_thai_id', 2)
            ->where('is_duyet', 1)
            ->where('created_at', '<=', $expiredDate)
            ->where('trang_thai_id', '!=', 6)
            ->get();

        $count = 0;
        foreach ($expiredProperties as $property) {
            $property->update([
                'is_duyet' => 0,
                'trang_thai_id' => 6,
            ]);

            event(new PropertyExpired($property));
            $count++;
        }

        $this->info("✅ Đã ẩn {$count} tin hết hạn.");
        return Command::SUCCESS;
    }
}