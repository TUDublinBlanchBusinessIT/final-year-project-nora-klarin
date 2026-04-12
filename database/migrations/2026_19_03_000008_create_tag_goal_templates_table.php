<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Maps tags to goal templates with a trigger threshold.
     *
     * When a response fires a tag AND the associated wellbeing score
     * falls below trigger_threshold, the linked goal template is added
     * to the suggestion queue for social worker review.
     *
     * A single tag can map to multiple templates (e.g. the 'sleep_disruption'
     * tag might suggest both a 'sleep_improvement' goal and a
     * 'stress_management' goal). A template can also be triggered by
     * multiple different tags.
     */
    public function up(): void
    {
        Schema::create('tag_goal_templates', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('tag_id');
            $table->unsignedBigInteger('goal_template_id');

            // Suggest this goal when the response wellbeing score drops below this value.
            // Default 40 — consistent with the domain alert threshold.
            $table->smallInteger('trigger_threshold')->unsigned()->default(40);

            $table->timestamp('created_at')->nullable();

            $table->foreign('tag_id')
                  ->references('id')->on('tags')
                  ->onDelete('cascade');

            $table->foreign('goal_template_id')
                  ->references('id')->on('goal_templates')
                  ->onDelete('cascade');

            $table->unique(['tag_id', 'goal_template_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tag_goal_templates');
    }
};
