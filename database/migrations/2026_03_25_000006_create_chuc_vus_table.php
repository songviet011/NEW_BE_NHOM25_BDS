<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chuc_vus', function (Blueprint $table) {
            $table->id();
            $table->string('ten_chuc_vu')->index();
            $table->string('slug_chuc_vu')->unique();
            $table->text('mo_ta')->nullable();
            $table->tinyInteger('tinh_trang')->default(1)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chuc_vus');
    }
};
