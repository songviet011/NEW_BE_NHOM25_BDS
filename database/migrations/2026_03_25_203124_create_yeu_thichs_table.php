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
        Schema::create('yeu_thichs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('moi_gioi_id');     // người nhận
            $table->foreignId('khach_hang_id');   // người thực hiện hành động

            $table->foreignId('bds_id')->nullable();

            $table->string('noi_dung');
            $table->boolean('is_read')->default(false);
            $table->foreignId('khach_hang_id');
            $table->foreignId('bds_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('yeu_thichs');
    }
};
