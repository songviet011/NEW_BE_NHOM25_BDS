<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('a_i_dinh_gias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('moi_gioi_id')->nullable()->constrained('moi_giois')->nullOnDelete();
            $table->string('dia_chi')->nullable();
            $table->decimal('gia_ao', 15, 0)->nullable();
            $table->decimal('gia_thap', 15, 0)->nullable();
            $table->decimal('gia_cao', 15, 0)->nullable();
            $table->string('trang_thai')->default('pending')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('a_i_dinh_gias');
    }
};
