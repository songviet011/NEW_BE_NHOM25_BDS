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
            $table->foreignId('moi_gioi_id')->constrained('moi_giois')->cascadeOnDelete();
            $table->foreignId('khach_hang_id')->nullable()->constrained('khach_hangs')->nullOnDelete();
            $table->foreignId('bat_dong_san_id')->constrained('bat_dong_sans')->cascadeOnDelete();
            $table->string('tieu_de');
            $table->text('noi_dung');
            $table->tinyInteger('trang_thai')->default(0)->index();
            $table->timestamps();

            $table->index(['moi_gioi_id', 'trang_thai']);
            $table->index(['khach_hang_id']);
            $table->index(['bat_dong_san_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thong_baos');
    }
};
