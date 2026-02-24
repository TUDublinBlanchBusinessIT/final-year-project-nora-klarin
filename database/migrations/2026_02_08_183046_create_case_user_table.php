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
Schema::create('case_user', function (Blueprint $table) {
    $table->id();

    $table->foreignId('case_file_id')
          ->constrained('case_files')
          ->cascadeOnDelete();

    $table->foreignId('user_id')
          ->constrained()
          ->cascadeOnDelete();

    $table->enum('role', ['social_worker', 'carer']);

    $table->timestamp('assigned_at')->nullable();

    $table->timestamps();

    $table->unique(['case_file_id', 'user_id']);
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_user');
    }
};
