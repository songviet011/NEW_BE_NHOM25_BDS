<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('phan_quyens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_chuc_vu')->constrained('chuc_vus')->cascadeOnDelete();
            $table->foreignId('id_chuc_nang')->constrained('chuc_nangs')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['id_chuc_vu', 'id_chuc_nang']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phan_quyens');
    }
};
