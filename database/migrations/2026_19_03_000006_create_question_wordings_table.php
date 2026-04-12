<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Age-banded wording variants for each question.
     *
     * The questions table stores a default text used as a fallback.
     * This table stores age-appropriate rephrasing of the same question
     * construct, as recommended by Deighton et al. (2014) and the HBSC
     * protocol, which both specify that assessment language must reflect
     * developmental stage and comprehension ability.
     *
     * Example:
     *   Default:        "How often do you feel worried or anxious?"
     *   Ages 6–9:       "Do you ever feel worried or scared a lot?"
     *   Ages 10–13:     "How often do you feel anxious or nervous?"
     *   Ages 14–17:     "How often do you experience feelings of anxiety?"
     */
    public function up(): void
    {
        Schema::create('question_wordings', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('question_id');

            // Age band this wording applies to (inclusive range)
            $table->smallInteger('age_min')->unsigned();
            $table->smallInteger('age_max')->unsigned();

            // The age-appropriate phrasing of the question
            $table->text('text');

            $table->timestamps();

            $table->foreign('question_id')
                  ->references('id')->on('questions')
                  ->onDelete('cascade');

            // A question should have at most one wording per age band
            $table->unique(['question_id', 'age_min', 'age_max']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_wordings');
    }
};
