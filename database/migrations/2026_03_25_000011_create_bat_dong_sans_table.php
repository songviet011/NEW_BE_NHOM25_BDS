<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bat_dong_sans', function (Blueprint $table) {
            $table->id();
            $table->string('tieu_de')->index();
            $table->text('mo_ta')->nullable();
            $table->decimal('gia', 15, 0);
            $table->float('dien_tich');
            $table->foreignId('loai_id')->constrained('loai_bat_dong_sans')->cascadeOnDelete();
            $table->foreignId('trang_thai_id')->constrained('trang_thai_bat_dong_sans')->cascadeOnDelete();
            $table->foreignId('moi_gioi_id')->constrained('moi_giois')->cascadeOnDelete();
            $table->foreignId('dia_chi_id')->constrained('dia_chis')->cascadeOnDelete();
            $table->integer('so_phong_ngu')->nullable();
            $table->integer('so_phong_tam')->nullable();
            $table->boolean('is_duyet')->default(false)->index();
            $table->boolean('is_noi_bat')->default(false)->index();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['moi_gioi_id', 'trang_thai_id']);
            $table->index(['loai_id', 'trang_thai_id']);
            $table->index(['dia_chi_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bat_dong_sans');
    }
};
