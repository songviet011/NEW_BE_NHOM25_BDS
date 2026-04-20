<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chuc_nangs', function (Blueprint $table) {
            $table->id();
            $table->string('ten_chuc_nang')->index();
            $table->string('url_chuc_nang')->nullable();
            $table->string('methods_chuc_nang')->nullable();
            $table->text('mo_ta_chuc_nang')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chuc_nangs');
    }
};
