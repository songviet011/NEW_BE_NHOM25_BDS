<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('goi_tins', function (Blueprint $table) {
            $table->id();
            $table->string('ten_goi');
            $table->decimal('gia', 15, 0);
            $table->integer('so_ngay');
            $table->integer('so_luong_tin');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goi_tins');
    }
};
