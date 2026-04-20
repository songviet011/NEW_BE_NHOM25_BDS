<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lich_su_dinh_gias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('a_i_dinh_gia_id')->constrained('a_i_dinh_gias')->cascadeOnDelete();
            $table->text('ket_qua')->nullable();
            $table->timestamps();

            $table->index(['a_i_dinh_gia_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lich_su_dinh_gias');
    }
};
