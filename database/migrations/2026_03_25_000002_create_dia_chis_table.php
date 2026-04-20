<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dia_chis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tinh_id')->constrained('tinh_thanhs')->cascadeOnDelete();
            $table->foreignId('quan_id')->constrained('quan_huyens')->cascadeOnDelete();
            $table->string('dia_chi_chi_tiet');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();
            $table->index(['tinh_id', 'quan_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dia_chis');
    }
};
