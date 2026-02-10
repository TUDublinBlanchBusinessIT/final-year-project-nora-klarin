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

    $table->foreignId('case_file_id')->constrained()->cascadeOnDelete();
    $table->foreignId('created_by')->constrained('users');

    $table->string('title');
    $table->text('description')->nullable();
    $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');

    $table->timestamps();
});

    }

    public function down(): void
    {
        Schema::dropIfExists('case_goals');
    }
};
