<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    Schema::table('wellbeing_checks', function (Blueprint $table) {
        $columnsToDrop = [
            'emotional_score',
            'behavioural_score',
            'physical_score',
            'safety_score',
            'school_score',
            'relationship_score',
            'journal_notes',
            'tag_summary',
            'safeguarding_flag',
        ];

        $existing = array_filter(
            $columnsToDrop,
            fn($col) => Schema::hasColumn('wellbeing_checks', $col)
        );

        if (!empty($existing)) {
            $table->dropColumn($existing);
        }
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
