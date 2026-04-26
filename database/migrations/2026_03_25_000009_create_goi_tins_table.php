<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goi_tins', function (Blueprint $table) {
            $table->id();
            $table->string('ten_goi')->index();
            $table->text('mo_ta')->nullable();
            $table->decimal('gia', 15, 0);
            $table->integer('so_ngay');
            $table->integer('so_luong_tin');
            $table->boolean('gan_nhan_vip')->default(false);
            $table->integer('uu_tien_hien_thi')->default(0);
            $table->string('trang_thai')->default('active')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goi_tins');
    }
};
