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
Schema::create('case_files', function (Blueprint $table) {
    $table->id();

    $table->foreignId('young_person_id')
          ->constrained('users')
          ->cascadeOnDelete();

    $table->enum('status', ['open', 'closed', 'review'])
          ->default('open');

    $table->enum('risk_level', ['low', 'medium', 'high'])
          ->default('low');

    $table->string('placement_type')->nullable();
    $table->string('placement_location')->nullable();

    $table->timestamp('opened_at')->nullable();
    $table->timestamp('closed_at')->nullable();
    $table->timestamp('last_reviewed_at')->nullable();

    $table->text('summary')->nullable();

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_files');
    }
};
