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
        Schema::table('giao_dichs', function (Blueprint $table) {
            $table->string('ma_vnp_txn_ref')->nullable()->after('ma_giao_dich');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('giao_dichs', function (Blueprint $table) {
             $table->dropColumn('ma_vnp_txn_ref');
        });
    }
};
