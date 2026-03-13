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
Schema::create('wellbeing_checks', function (Blueprint $table) {
    $table->id();

    $table->foreignId('case_file_id')->constrained()->cascadeOnDelete();

    $table->unsignedTinyInteger('overall_score')->nullable();
    $table->unsignedTinyInteger('emotional_score')->nullable();
    $table->unsignedTinyInteger('behavioural_score')->nullable();
    $table->unsignedTinyInteger('physical_score')->nullable();
    $table->unsignedTinyInteger('safety_score')->nullable();
    $table->unsignedTinyInteger('school_score')->nullable();
    $table->unsignedTinyInteger('relationship_score')->nullable();

    $table->text('journal_notes')->nullable();

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wellbeing_checks');
    }
};
