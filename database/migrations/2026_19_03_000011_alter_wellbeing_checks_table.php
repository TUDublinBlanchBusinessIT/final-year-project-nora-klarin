<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wellbeing_checks', function (Blueprint $table) {
            // Drop hardcoded domain score columns — replaced by wellbeing_domain_scores
            $table->dropColumn([
                'emotional_score',
                'behavioural_score',
                'physical_score',
                'safety_score',
                'school_score',
                'relationship_score',
                'journal_notes',
                'tag_summary',
                'safeguarding_flag',
            ]);


            // Computed scores (stored for performance, derived from responses)
            $table->decimal('overall_wb_score', 5, 2)->nullable()->after('overall_score');
            $table->decimal('overall_risk_score', 8, 2)->nullable()->after('overall_wb_score');

            // Check metadata
            $table->enum('check_type', ['scheduled', 'intake', 'triggered'])
                  ->default('scheduled')
                  ->after('overall_risk_score');

            $table->enum('game_mode', ['slider', 'emoji', 'scenario', 'safety_focused'])
                  ->nullable()
                  ->after('check_type');

            $table->timestamp('completed_at')->nullable()->after('game_mode');
        });
    }

    public function down(): void
    {
        Schema::table('wellbeing_checks', function (Blueprint $table) {
            $table->dropForeign(['young_person_id']);
            $table->dropColumn([
                'young_person_id',
                'overall_wb_score',
                'overall_risk_score',
                'check_type',
                'game_mode',
                'completed_at',
            ]);

            // Restore dropped columns
            $table->tinyInteger('emotional_score')->unsigned()->nullable();
            $table->tinyInteger('behavioural_score')->unsigned()->nullable();
            $table->tinyInteger('physical_score')->unsigned()->nullable();
            $table->tinyInteger('safety_score')->unsigned()->nullable();
            $table->tinyInteger('school_score')->unsigned()->nullable();
            $table->tinyInteger('relationship_score')->unsigned()->nullable();
            $table->text('journal_notes')->nullable();
            $table->json('tag_summary')->nullable();
            $table->boolean('safeguarding_flag')->default(false);
        });
    }
};
