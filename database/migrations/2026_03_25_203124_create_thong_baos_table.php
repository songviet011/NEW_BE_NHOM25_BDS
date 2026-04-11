<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('thong_baos', function (Blueprint $table) {
            $table->id();

            // ✅ Người nhận thông báo (Môi giới)
            $table->foreignId('moi_gioi_id')->constrained('moi_giois')->onDelete('cascade');

            // ✅ Người gây ra thông báo (Khách hàng - nullable vì có thể là system notification)
            $table->foreignId('khach_hang_id')->nullable()->constrained('khach_hangs')->onDelete('set null');

            // ✅ BĐS liên quan
            $table->foreignId('bat_dong_san_id')->constrained('bat_dong_sans')->onDelete('cascade');

            // ✅ Nội dung thông báo
            $table->string('tieu_de');
            $table->text('noi_dung');

            // ✅ Trạng thái: 0 = Chưa đọc, 1 = Đã đọc
            $table->tinyInteger('trang_thai')->default(0);

            $table->timestamps();

            // Index để query nhanh
            $table->index(['moi_gioi_id', 'trang_thai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thong_baos');
    }
};
