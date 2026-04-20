<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loai_bat_dong_sans', function (Blueprint $table) {
            $table->id();
            $table->string('ten_loai')->index();
            $table->text('mo_ta')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loai_bat_dong_sans');
    }
};
