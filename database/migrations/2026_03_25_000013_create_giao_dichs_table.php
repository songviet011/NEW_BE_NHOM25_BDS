<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('giao_dichs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('moi_gioi_id')->constrained('moi_giois')->cascadeOnDelete();
            $table->foreignId('goi_tin_id')->constrained('goi_tins')->cascadeOnDelete();
            $table->decimal('so_tien', 15, 0);
            $table->string('phuong_thuc')->index();
            $table->string('trang_thai')->index();
            $table->timestamp('paid_at')->nullable();
            $table->string('ma_giao_dich')->nullable()->unique();
            $table->string('ma_sepay_txn_ref')->nullable()->unique();
            $table->timestamps();

            $table->index(['moi_gioi_id', 'trang_thai']);
            $table->index(['goi_tin_id', 'trang_thai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('giao_dichs');
    }
};
