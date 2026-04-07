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
        Schema::table('moi_giois', function (Blueprint $table) {
            $table->integer('so_tin_con_lai')->default(0)->after('is_active');
            $table->dateTime('ngay_het_han_goi')->nullable()->after('so_tin_con_lai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('moi_giois', function (Blueprint $table) {
            $table->dropColumn(['so_tin_con_lai', 'ngay_het_han_goi']);
        });
    }
};
