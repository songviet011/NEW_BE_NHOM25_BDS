<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quan_huyens', function (Blueprint $table) {
            $table->id();
            $table->string('ten')->index();
            $table->foreignId('tinh_id')->constrained('tinh_thanhs')->cascadeOnDelete();
            $table->timestamps();
            $table->index(['tinh_id', 'ten']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quan_huyens');
    }
};
