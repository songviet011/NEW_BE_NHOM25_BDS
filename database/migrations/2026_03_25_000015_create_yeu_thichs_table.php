<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('yeu_thichs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('moi_gioi_id')->constrained('moi_giois')->cascadeOnDelete();
            $table->foreignId('khach_hang_id')->constrained('khach_hangs')->cascadeOnDelete();
            $table->foreignId('bds_id')->nullable()->constrained('bat_dong_sans')->cascadeOnDelete();
            $table->string('noi_dung');
            $table->boolean('is_read')->default(false)->index();
            $table->timestamps();

            $table->index(['moi_gioi_id', 'khach_hang_id']);
            $table->index(['bds_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('yeu_thichs');
    }
};
