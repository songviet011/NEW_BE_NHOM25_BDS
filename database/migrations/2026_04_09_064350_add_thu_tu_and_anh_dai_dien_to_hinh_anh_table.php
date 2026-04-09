<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('hinh_anh_bat_dong_sans', function (Blueprint $table) {
            $table->integer('thu_tu')->default(0)->after('url');
            $table->boolean('is_anh_dai_dien')->default(false)->after('thu_tu');
            $table->index(['bds_id', 'thu_tu']);
            $table->index(['bds_id', 'is_anh_dai_dien']);
        });
    }

    public function down(): void
    {
        Schema::table('hinh_anh_bat_dong_sans', function (Blueprint $table) {
            $table->dropIndex(['bds_id', 'thu_tu']);
            $table->dropIndex(['bds_id', 'is_anh_dai_dien']);
            $table->dropColumn(['thu_tu', 'is_anh_dai_dien']);
        });
    }
};
