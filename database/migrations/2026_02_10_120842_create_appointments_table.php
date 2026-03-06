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
Schema::create('appointments', function (Blueprint $table) {
    $table->id();

    // Context
    $table->foreignId('case_file_id')
        ->nullable()
        ->constrained('case_files')
        ->nullOnDelete();

    // Primary actors
    $table->foreignId('created_by') // social worker
        ->constrained('users');

    $table->foreignId('young_person_id')
        ->constrained('users');

    // Timing
    $table->dateTime('start_time');
    $table->dateTime('end_time')->nullable();

    // Meta
    $table->string('title');
    $table->string('location')->nullable();
    $table->text('notes')->nullable();

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
