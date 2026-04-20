<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trang_thai_bat_dong_sans', function (Blueprint $table) {
            $table->id();
            $table->string('ten_trang_thai')->index();
            $table->text('mo_ta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trang_thai_bat_dong_sans');
    }
};
