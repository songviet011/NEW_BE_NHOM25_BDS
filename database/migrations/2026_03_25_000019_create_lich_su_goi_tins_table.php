<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lich_su_goi_tins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('moi_gioi_id')->constrained('moi_giois')->cascadeOnDelete();
            $table->foreignId('goi_tin_id')->constrained('goi_tins')->cascadeOnDelete();
            $table->foreignId('giao_dich_id')->nullable()->constrained('giao_dichs')->nullOnDelete();
            $table->timestamp('bat_dau')->nullable();
            $table->timestamp('ket_thuc')->nullable();
            $table->timestamps();

            $table->index(['moi_gioi_id', 'goi_tin_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lich_su_goi_tins');
    }
};
