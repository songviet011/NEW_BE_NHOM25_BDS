<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('unmatched_payments', function (Blueprint $table) {
            $table->id();
            $table->string('sepayer_reference')->nullable();
            $table->string('order_code_from_sepay')->nullable();
            $table->decimal('so_tien', 15, 2);
            $table->json('payload');
            $table->enum('status', ['unmatched', 'matched', 'ignored'])->default('unmatched');
            $table->foreignId('giao_dich_id')->nullable()->constrained('giao_dichs')->nullOnDelete();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('unmatched_payments');
    }
};
