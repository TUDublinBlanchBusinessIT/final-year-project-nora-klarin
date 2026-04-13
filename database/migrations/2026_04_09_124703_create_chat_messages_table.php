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
        Schema::create('chat_messages', function (Blueprint $table) {

            // Primary key
            $table->id();

            // Optional user (if logged in)
            $table->unsignedBigInteger('user_id')->nullable()->index();

            // Messages
            $table->text('user_message'); // What user typed
            $table->text('bot_reply');    // Chatbot response

            // Category matched (Housing, Jobs, etc.)
            $table->string('matched_category')->nullable()->index();

            // Emergency flag
            $table->boolean('is_emergency')->default(false)->index();

            // Timestamps
            $table->timestamps();

            // Optional: Foreign key (ONLY if you have users table)
            // Uncomment if using Laravel auth
            /*
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();
            */
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};