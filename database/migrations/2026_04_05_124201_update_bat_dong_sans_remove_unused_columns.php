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
         Schema::table('bat_dong_sans', function (Blueprint $table) {

        if (Schema::hasColumn('bat_dong_sans', 'tinh_id')) {
            $table->dropColumn('tinh_id');
        }

        if (Schema::hasColumn('bat_dong_sans', 'quan_id')) {
            $table->dropColumn('quan_id');
        }

        if (Schema::hasColumn('bat_dong_sans', 'vi_do')) {
            $table->dropColumn('vi_do');
        }

        if (Schema::hasColumn('bat_dong_sans', 'kinh_do')) {
            $table->dropColumn('kinh_do');
        }
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bat_dong_sans', function (Blueprint $table) {
            $table->unsignedBigInteger('tinh_id')->nullable();
            $table->unsignedBigInteger('quan_id')->nullable();
            $table->decimal('vi_do', 10, 7)->nullable();
            $table->decimal('kinh_do', 10, 7)->nullable();
        });
    }
};
