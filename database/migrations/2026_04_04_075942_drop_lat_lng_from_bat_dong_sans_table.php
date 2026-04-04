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
        $table->dropColumn(['vi_do', 'kinh_do']);
    });
}

public function down(): void
{
    Schema::table('bat_dong_sans', function (Blueprint $table) {
        $table->decimal('vi_do', 10, 7)->nullable();
        $table->decimal('kinh_do', 10, 7)->nullable();
    });
}
};
