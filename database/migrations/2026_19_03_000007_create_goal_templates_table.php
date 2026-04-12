<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Goal templates represent predefined wellbeing improvement patterns
     * that the system can suggest automatically based on detected tag patterns
     * and domain score trends.
     *
     * Examples: sleep_improvement, social_engagement, school_attendance,
     * emotional_regulation, peer_relationship_support, physical_activity.
     *
     * When the system detects a negative pattern (via tags or domain scores),
     * it matches that pattern to a template and surfaces a goal suggestion
     * to the social worker for approval — consistent with NCB (2017) guidance
     * that assessment value is realised through intervention.
     */
    public function up(): void
    {
        Schema::create('goal_templates', function (Blueprint $table) {
            $table->id();

            $table->string('title', 255);
            $table->text('description')->nullable();

            // The primary domain this template addresses
            $table->unsignedBigInteger('source_domain_id')->nullable();

            $table->timestamps();

            $table->foreign('source_domain_id')
                  ->references('id')->on('domains')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goal_templates');
    }
};
