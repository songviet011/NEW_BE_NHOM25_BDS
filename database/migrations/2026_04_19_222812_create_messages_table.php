<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conversation_id');
            $table->unsignedBigInteger('sender_id');
            $table->foreign('conversation_id')->references('id')->on('conversations')->cascadeOnDelete();
            $table->string('sender_type');
            $table->text('content');
            $table->string('type')->default('text');
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->index(['conversation_id', 'is_read']);
            $table->index(['sender_id', 'sender_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
