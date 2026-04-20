<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Records which questions were presented in each wellbeing check.
     *
     * This table is the foundation of the question rotation algorithm.
     * Before selecting questions for a new check, the system queries this
     * table to exclude any question asked in the child's previous N checks,
     * preventing repetition while maintaining domain coverage.
     *
     * Query pattern used by selection algorithm:
     *
     *   SELECT question_id FROM check_question_log
     *   WHERE wellbeing_check_id IN (
     *       SELECT id FROM wellbeing_checks
     *       WHERE young_person_id = ?
     *       ORDER BY completed_at DESC
     *       LIMIT 2
     *   )
     *
     * The result set is then excluded from the candidate question pool.
     */
    public function up(): void
    {
        Schema::create('check_question_log', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('wellbeing_check_id');
            $table->unsignedBigInteger('question_id');

            $table->timestamp('created_at')->nullable();

            $table->foreign('wellbeing_check_id')
                  ->references('id')->on('wellbeing_checks')
                  ->onDelete('cascade');

            $table->foreign('question_id')
                  ->references('id')->on('questions')
                  ->onDelete('cascade');

            // Index on question_id for fast exclusion lookups
            $table->index('question_id');

            // Index for pulling all questions from a specific check
            $table->index('wellbeing_check_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('check_question_log');
    }
};
