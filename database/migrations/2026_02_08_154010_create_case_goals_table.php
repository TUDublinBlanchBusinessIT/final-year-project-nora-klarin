<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
Schema::create('case_goals', function (Blueprint $table) {
    $table->id();

    $table->foreignId('case_file_id')
          ->constrained()
          ->cascadeOnDelete();

    $table->foreignId('goal_id')
          ->constrained()
          ->cascadeOnDelete();

    $table->enum('status', ['pending', 'in_progress', 'completed'])
          ->default('pending');

    $table->date('due_date')->nullable();

    $table->timestamps();
});


    }

    public function down(): void
    {
        Schema::dropIfExists('case_goals');
    }
};
