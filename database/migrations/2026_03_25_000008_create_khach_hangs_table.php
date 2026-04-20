<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('khach_hangs', function (Blueprint $table) {
            $table->id();
            $table->string('ten')->index();
            $table->string('email')->unique();
            $table->string('so_dien_thoai')->index();
            $table->string('password');
            $table->boolean('is_active')->default(true)->index();
            $table->string('trang_thai')->default('active')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('khach_hangs');
    }
};
