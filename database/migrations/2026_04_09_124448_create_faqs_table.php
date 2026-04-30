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
        Schema::create('faqs', function (Blueprint $table) {

            // Primary key
            $table->id();

            // Main FAQ content
            $table->string('question'); // The user question
            $table->text('answer');     // The chatbot response

            // Category (Housing, Jobs, etc.)
            $table->string('category')->nullable()->index();

            // Keywords for matching (comma-separated)
            $table->text('keywords')->nullable();

            // Priority (higher = more important in matching)
            $table->integer('priority')->default(0);

            // Flag for emergency-related FAQs
            $table->boolean('is_emergency')->default(false)->index();

            // Timestamps (created_at, updated_at)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faqs');
    }
};