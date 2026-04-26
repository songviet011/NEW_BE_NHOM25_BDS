<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('moi_giois', function (Blueprint $table) {
            $table->id();
            $table->string('ten')->index();
            $table->string('email')->unique();
            $table->string('so_dien_thoai')->index();
            $table->string('password');
            $table->string('avatar')->nullable();
            $table->text('mo_ta')->nullable();
            $table->string('zalo_link')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->string('trang_thai')->default('active')->index();
            $table->integer('so_tin_con_lai')->default(0);
            $table->dateTime('ngay_het_han_goi')->nullable();
            $table->foreignId('goi_tin_id')
                ->nullable()
                ->constrained('goi_tins')
                ->nullOnDelete();
            $table->timestamps();
            $table->index(['is_active', 'trang_thai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moi_giois');
    }
};
