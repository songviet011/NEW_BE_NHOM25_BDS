<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hinh_anh_bat_dong_sans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bds_id')->constrained('bat_dong_sans')->cascadeOnDelete();
            $table->string('url');
            $table->integer('thu_tu')->default(0)->index();
            $table->boolean('is_anh_dai_dien')->default(false)->index();
            $table->timestamps();

            $table->index(['bds_id', 'thu_tu']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hinh_anh_bat_dong_sans');
    }
};
