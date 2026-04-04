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
        Schema::table('a_i_dinh_gias', function (Blueprint $table) {
            $table->foreignId('bat_dong_san_id')->nullable()->after('id')->constrained('bat_dong_sans')->nullOnDelete();
            $table->decimal('gia_du_doan', 15, 0)->nullable()->after('bat_dong_san_id');
            $table->decimal('do_tin_cay', 5, 2)->nullable()->after('gia_du_doan');
            $table->text('ly_do')->nullable()->after('do_tin_cay');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('a_i_dinh_gias', function (Blueprint $table) {
            $table->dropForeign(['bat_dong_san_id']);
            $table->dropColumn([
                'bat_dong_san_id',
                'gia_du_doan',
                'do_tin_cay',
                'ly_do',
            ]);
        });
    }
};
