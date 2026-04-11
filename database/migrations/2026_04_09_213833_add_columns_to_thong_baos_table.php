<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('thong_baos', function (Blueprint $table) {
            // ✅ Thêm các cột còn thiếu
            $table->foreignId('moi_gioi_id')->nullable()->after('id')->constrained('moi_giois')->onDelete('cascade');
            $table->foreignId('khach_hang_id')->nullable()->after('moi_gioi_id')->constrained('khach_hangs')->onDelete('set null');
            $table->foreignId('bat_dong_san_id')->nullable()->after('khach_hang_id')->constrained('bat_dong_sans')->onDelete('cascade');
            
            $table->string('tieu_de')->nullable()->after('bat_dong_san_id');
            $table->text('noi_dung')->nullable()->after('tieu_de');
            $table->tinyInteger('trang_thai')->default(0)->after('noi_dung');
            
            $table->index(['moi_gioi_id', 'trang_thai']);
        });
    }

    public function down(): void
    {
        Schema::table('thong_baos', function (Blueprint $table) {
            $table->dropIndex(['moi_gioi_id', 'trang_thai']);
            $table->dropForeign(['moi_gioi_id']);
            $table->dropForeign(['khach_hang_id']);
            $table->dropForeign(['bat_dong_san_id']);
            $table->dropColumn(['moi_gioi_id', 'khach_hang_id', 'bat_dong_san_id', 'tieu_de', 'noi_dung', 'trang_thai']);
        });
    }
};