<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tags', function (Blueprint $table) {
            // Replace the boolean is_safeguarding with a richer classification
            $table->dropColumn('is_safeguarding');

            // What this tag's purpose is in the system
            $table->enum('category', [
                'safeguarding',   // immediate alert, bypasses scoring pipeline
                'clinical',       // flag for professional review
                'goal_mapping',   // drives automated goal suggestions
                'pattern',        // trend detection and longitudinal analysis only
            ])->default('goal_mapping')->after('name');

            // If true, a sufficiently negative response fires an alert regardless
            // of overall wellbeing score — used for self_harm, abuse, neglect tags
            $table->boolean('alert_override')->default(false)->after('category');

            // Wellbeing score below this value triggers an alert when alert_override is true.
            // NULL means the tag is evaluated purely by goal_mapping logic.
            $table->smallInteger('alert_threshold')->unsigned()->nullable()->after('alert_override');
        });
    }

    public function down(): void
    {
        Schema::table('tags', function (Blueprint $table) {
            $table->dropColumn(['category', 'alert_override', 'alert_threshold']);
            $table->boolean('is_safeguarding')->default(false);
        });
    }
};
